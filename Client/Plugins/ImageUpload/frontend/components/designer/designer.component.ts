import { AfterViewInit, Component, ElementRef, OnDestroy, OnInit, ViewChild } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { FormControl, Validators } from '@angular/forms';
import { Observable, Subscription } from 'rxjs';
import { Store } from '@ngrx/store';
import { ResizedEvent } from 'angular-resize-event';
import { Color, stringInputToObject } from '@angular-material-components/color-picker';
import { MaxSizeValidator } from '@angular-material-components/file-input';
import { fabric } from 'fabric';
import FontFaceObserver from 'fontfaceobserver';
import { v4 as uuidv4 } from 'uuid';

import { environment } from '@apto-frontend/src/environments/environment';
import { DialogSizesEnum } from '@apto-frontend/src/configs-static/dialog-sizes-enum';
import { sha1 } from '@apto-base-core/helper/encrypt';
import { translate } from '@apto-base-core/store/translated-value/translated-value.model';

import { selectContentSnippet } from '@apto-base-frontend/store/content-snippets/content-snippets.selectors';
import { selectLocale } from '@apto-base-frontend/store/language/language.selectors';
import { setHideOnePage, updateConfigurationState } from '@apto-catalog-frontend/store/configuration/configuration.actions';
import { DialogService } from '@apto-catalog-frontend/components/common/dialogs/dialog-service';
import { RenderImageService } from '@apto-catalog-frontend/services/render-image.service';
import { selectProduct } from '@apto-catalog-frontend/store/product/product.selectors';
import { Product } from '@apto-catalog-frontend/store/product/product.model';

import { selectCanvas } from '@apto-image-upload-frontend/store/canvas/canvas.selectors';
import { CanvasState } from '@apto-image-upload-frontend/store/canvas/canvas.reducer';
import { FabricCanvasService } from '@apto-image-upload-frontend/services/fabric-canvas.service';
import { CanvasStyle, Font, PrintArea } from '@apto-image-upload-frontend/store/canvas/canvas.model';

@Component({
  selector: 'apto-designer',
  templateUrl: './designer.component.html',
  styleUrls: ['./designer.component.scss'],
})
export class DesignerComponent implements OnInit, AfterViewInit, OnDestroy {
  @ViewChild('htmlCanvas') htmlCanvas: ElementRef;
  @ViewChild('htmlRenderImage') htmlRenderImage: ElementRef;
  @ViewChild('htmlMiddle') htmlMiddle: ElementRef;

  private locale: string;
  private initStarted: boolean = false;
  private subscriptions: Subscription[] = [];

  public canvas: CanvasState | null = null;
  public mediaUrl: string = environment.api.media;
  public renderImage: any = null;
  public imageUploadControl: FormControl;
  public imageUploadErrors: Array<any> = [];

  public readonly contentSnippet$ = this.store.select(selectContentSnippet('plugins.imageUpload'));
  public readonly cancelMessage$ = this.store.select(selectContentSnippet('plugins.imageUpload.upload.cancelMessage'));
  public readonly resetMessage$ = this.store.select(selectContentSnippet('plugins.imageUpload.upload.resetMessage'));

  public printAreas: PrintArea[] = [];
  public canvasStyle: CanvasStyle = { width: '0px', height: '0px', left: '0px' };
  public product: Product;

  public middleWidth: number = 0;
  public canvasWidth: number = 0;
  public canvasHeight: number = 0;
  public canvasLeft: number = 0;

  public fabricCanvas: any = null;
  public fabricSelectedObject: any = null;
  public fabricTextBoxes: any[] = [];
  public controlOptionsLocked = {
    selectable: false,
    editable: false,
    hasControls: false,
    lockMovementX: true,
    lockMovementY: true,
    lockRotation: true,
    lockScalingFlip: true,
    lockScalingX: true,
    lockScalingY: true,
    lockSkewingX: true,
    lockSkewingY: true
  };

  public controlOptionsEditable = {
    selectable: true,
    editable: true,
    hasControls: true,
    lockMovementX: false,
    lockMovementY: false,
    lockRotation: false,
    lockScalingFlip: false,
    lockScalingX: false,
    lockScalingY: false,
    lockSkewingX: true,
    lockSkewingY: true
  };

  public controlVisibility = {
    mtr: true, // middle top rotate
    tl: true, // top left
    bl: true, // bttom left
    tr: true, // top right
    br: true, // bottom right
    mt: false, // middle top
    mb: false, // midle bottom
    ml: false, // middle left
    mr: false, // middle right
  }

  public fonts: Font[] = [];
  public selectedFont: Font | null = null;
  public file: File | null;

  public constructor(private store: Store, private fabricCanvasService: FabricCanvasService, private http: HttpClient, private dialogService: DialogService, public renderImageService: RenderImageService) {
    // set default locale
    this.locale = environment.defaultLocale;
    this.imageUploadControl = new FormControl(this.file, [
      Validators.required,
      // 1024 * 1024 equals to 1MB, max value must be given in byte
      MaxSizeValidator(1024 * 1024)
    ]);
  }

  public ngOnInit(): void {
    // subscribe for locale store value
    this.store.select(selectLocale).subscribe((locale: string) => {
      if (locale === null) {
        this.locale = environment.defaultLocale;
      } else {
        this.locale = locale;
      }
    });

    this.store.select(selectProduct).subscribe((next: Product) => {
      this.product = next;
    });

    // subscribe for canvas
    this.store.select(selectCanvas).subscribe((next: CanvasState) => {
      this.canvas = next;
      this.imageUploadControl.setValidators([
        Validators.required,
        MaxSizeValidator(this.canvas.element.staticValues.image.maxFileSize * 1024 * 1024)
      ]);
    });

    // subscribe for image upload
    this.imageUploadControl.valueChanges.subscribe((file) => {
      this.imageUploadErrors = [];
      if (!this.imageUploadControl.errors) {
        this.addImageFromFile(file);
        return;
      }

      if (this.imageUploadControl.errors.hasOwnProperty('maxSize')) {
        this.imageUploadErrors.push({
          type: 'maxSize'
        });
      }
    })
  }

  public ngAfterViewInit(): void {
    this.renderImageService.init();
    this.subscriptions.push(
      this.renderImageService.outputSrcSubject.subscribe((next) => {
        this.renderImage = next;

        if (null !== next && this.initStarted === false) {
          this.initStarted = true;
          this.init();
        }
      })
    );
  }

  public init() {
    this.initFonts().then(() => {
      setTimeout(() => {
        this.updateCanvasStyle();
        this.calculatePrintAreas();

        this.fabricCanvas = new fabric.Canvas(this.htmlCanvas.nativeElement, {
          preserveObjectStacking: true,
          selection: false
        });

        this.fabricCanvas.on({
          'selection:created': this.selectionUpdated.bind(this),
          'selection:updated': this.selectionUpdated.bind(this),
          'selection:cleared': this.selectionCleared.bind(this),
        });

        if (!this.canvas.element.state.payload) {
          this.initTextBoxes();
        } else {
          this.initState(() => {
          });
        }
      });
    });
  }

  initFonts() {
    this.fonts = [];
    this.selectedFont = null;
    let promises = [(new FontFaceObserver('Montserrat')).load()];

    if (!this.canvas.element.staticValues.text.fonts) {
      return Promise.all(promises);
    }

    for (let i = 0; i < this.canvas.element.staticValues.text.fonts.length; i++) {
      const font = this.canvas.element.staticValues.text.fonts[i];
      if (font.isActive) {

        this.fonts.push({
          family: font.name,
          url: this.mediaUrl + font.file,
        });

        if (font.isDefault) {
          this.selectedFont = this.fonts[this.fonts.length - 1];
        }

        promises.push((new FontFaceObserver(font.name)).load());
      }
    }

    return Promise.all(promises);
  }

  initTextBoxes() {
    if (!this.canvas.element.staticValues.text.active) {
      return;
    }

    this.canvas.element.staticValues.text.boxes.forEach((box) => {
      let textOptions = {
        identifier: box.identifier,
        fontSize: box.fontSize,
        fill: box.fill,
        textAlign: box.textAlign,
        left: box.left,
        top: box.top,
        fontFamily: this.selectedFont ? this.selectedFont.family : 'Montserrat',
        originX: "center",
        originY: "center",
        payload: {
          box: box,
          type: 'text'
        }
      }

      // cant use IText actually, see: https://github.com/fabricjs/fabric.js/issues/8865
      let fabricText = new fabric.Text(box.default, {
        ...textOptions,
        ...this.getTextBoxControlOptions(box)
      }).setControlsVisibility(this.controlVisibility);

      if (box.radius > 0) {
        this.fabricCanvasService.updateTextElementForBending(fabricText, box.radius);
      }

      this.fabricCanvas.add(fabricText);
      this.fabricTextBoxes.push(fabricText);
    });
    this.setCanvasSize();
  }

  initState(callback) {
    this.fabricCanvas.loadFromJSON(this.canvas.element.state.payload.json, () => {
      this.setCanvasSize();

      this.fabricCanvas.getObjects().forEach((object) => {
        const payload = object.get('payload');
        if (payload.type === 'text') {
          object.setOptions(this.getTextBoxControlOptions(payload.box));
          this.fabricTextBoxes.push(object);
        }

        if (payload.type === 'motive') {
          object.setOptions(this.controlOptionsLocked);
          object.sendToBack();
        }

        if (payload.type === 'image') {
          object.setOptions(this.controlOptionsEditable);
        }

        object.setControlsVisibility(this.controlVisibility);
      });
      callback();
    });
  }

  onResizedBackground(event: ResizedEvent) {
    if (event.isFirst) {
      return;
    }
    this.updateCanvasStyle();
    this.calculatePrintAreas();
    this.setCanvasSize();
  }

  onResizedMiddle(event: ResizedEvent) {
    if (event.isFirst) {
      return;
    }
    this.updateCanvasStyle();
    this.calculatePrintAreas();
    this.setCanvasSize();
  }

  calculatePrintAreas() {
    if (!this.canvas) {
      return;
    }
    this.printAreas = [];
    let factor = this.canvasWidth / this.renderImage.width;

    this.canvas.element.staticValues.area.forEach((area) => {
      this.printAreas.push({
        width: area.width * factor,
        height: area.height * factor,
        left: area.left * factor,
        top: area.top * factor
      });
    });
  }

  setCanvasSize() {
    if (!this.fabricCanvas || !this.canvas || this.canvasWidth < 1 || this.canvasHeight < 1) {
      return;
    }
    let factor = this.canvasWidth / this.renderImage.width;
    this.fabricCanvas.setWidth(this.canvasWidth);
    this.fabricCanvas.setHeight(this.canvasHeight);
    this.fabricCanvas.setZoom(factor);
  }

  updateCanvasStyle() {
    if (!this.htmlRenderImage) {
      return;
    }
    this.canvasWidth = this.htmlRenderImage.nativeElement.clientWidth;
    this.canvasHeight = this.htmlRenderImage.nativeElement.clientHeight;
    this.middleWidth = this.htmlMiddle.nativeElement.clientWidth;
    this.canvasLeft = (this.middleWidth - this.canvasWidth) / 2;

    this.canvasStyle.width = this.canvasWidth + 'px';
    this.canvasStyle.height = this.canvasHeight + 'px';
    this.canvasStyle.left = this.canvasLeft + 'px';
  }

  public addImage(url, options, layer = null, center = false) {
    fabric.Image.fromURL(url, (fabricImage) => {
      fabricImage.setOptions(options);
      fabricImage.setControlsVisibility(this.controlVisibility);

      this.fabricCanvas.add(fabricImage);

      switch (layer) {
        case 'back': {
          fabricImage.sendToBack();
          break;
        }
        case 'front': {
          fabricImage.bringToFront();
          break;
        }
      }

      if (true === center) {
        this.fabricCanvas.viewportCenterObject(fabricImage);
      }
    });
  }

  public addMotive(file) {
    let fileIsAlreadySelected: true | false = false;
    const url = this.mediaUrl + file.path;
    const canvasObjects = this.fabricCanvas.getObjects();

    for (let i = 0; i < canvasObjects.length; i++) {
      if (canvasObjects[i].payload.type !== 'motive') {
        continue;
      }

      this.fabricCanvas.remove(canvasObjects[i]);
      fileIsAlreadySelected = canvasObjects[i].payload.file.url === file.url;
    }

    if (true === fileIsAlreadySelected) {
      return;
    }

    const options = {
      ...this.controlOptionsLocked,
      left: this.canvas.element.staticValues.motive.left,
      top: this.canvas.element.staticValues.motive.top,
      payload: {
        type: 'motive',
        file: file,
        name: this.getNameFromFileName(file.name),
        url: url
      }
    };

    this.addImage(url, options, 'back');
  }

  public addImageFromFile(file) {
    const date: Date = new Date();
    const fileId = uuidv4();
    const extension = this.getExtensionFromFileName(file.name);
    const directory = '/apto-plugin-image-upload/user-images/' + date.getFullYear() + '/' + (date.getMonth() + 1).toString().padStart(2, '0') + '/';

    const getHeightAndWidthFromDataUrl = dataURL => new Promise(resolve => {
      const img = new Image()
      img.onload = () => {
        resolve({
          height: img.height,
          width: img.width
        })
      }
      img.src = dataURL
    })

    // Get the data URL of the image as a string
    const fileAsDataURL = window.URL.createObjectURL(file)

    // Get the dimensions
    getHeightAndWidthFromDataUrl(fileAsDataURL).then((dimensions) => {
      if (false === this.assertValidDimensions(dimensions)) {
        this.imageUploadErrors.push({
          type: 'minDimensions'
        })
        return;
      }

      this.fabricCanvasService.uploadFile(file, fileId, extension, directory).subscribe((next) => {
        const scale = this.getImageScale(this.canvas.element.staticValues.image.previewSize, dimensions);
        const options = {
          ...this.controlOptionsEditable,
          scaleX: scale,
          scaleY: scale,
          left: 0,
          top: 0,
          payload: {
            type: 'image'
          }
        };

        this.addImage(this.mediaUrl + directory + fileId + '.' + extension, options, 'front', true);
      });
    });
  }

  public selectionUpdated(event) {
    this.fabricSelectedObject = null;
    if (event.selected.length > 0) {
      this.fabricSelectedObject = event.selected[0];
    }
  }

  public selectionCleared(event) {
    this.fabricSelectedObject = null;
  }

  public removeSelectedObject() {
    if (null === this.fabricSelectedObject) {
      return;
    }

    if (this.fabricSelectedObject.payload.type === 'image') {
      this.imageUploadControl.reset();
    }

    this.fabricCanvas.remove(this.fabricSelectedObject);
    this.fabricSelectedObject = null;
  }

  public updateText(event, identifier) {
    this.updateTextPropery(identifier, 'text', event.target.value);
  }

  public removeDefaultText(box) {
    if (box.get('text') === box.payload.box.default) {
      this.updateTextPropery(box.payload.box.identifier, 'text', '');
    }
  }

  public updateTextColor(event, identifier) {
    this.updateTextPropery(identifier, 'fill', '#' + event.value.hex, { color: event.value });
  }

  public updateTextFont(event) {
    this.selectedFont = event.value;
    this.fabricCanvas.getObjects().forEach((object) => {
      if (object.get('type') === 'text') {
        object.setOptions({
          fontFamily: this.selectedFont.family
        });
      }
    });
    this.fabricCanvas.renderAll();
  }

  private updateTextPropery(identifier, property, value, payload = {}) {
    this.fabricCanvas.getObjects().forEach((object) => {
      if (object.get('type') === 'text' && object.get('identifier') === identifier) {
        object.setOptions({
          [property]: value,
          payload: {
            ...object.payload,
            ...payload
          }
        });
        this.fabricCanvas.renderAll();
      }
    });
  }

  public getObjectValue(type, identifier, property): Color {
    const object = this.fabricCanvas.getObjects().find((o) => o.get('type') === type && o.get('identifier') === identifier);
    if (!object || !object.hasOwnProperty(property)) {
      return null;
    }
    return object[property];
  }

  public getColorFromHex(hex) {
    if (hex === null) {
      return new Color(0, 0, 0, 1);
    }
    const { r, g, b, a } = stringInputToObject(hex);
    return new Color(r, g, b, a);
  }

  public getTextBoxProperty(identifier, property) {
    const boxes = this.canvas.element.staticValues.text.boxes;

    for (let i = 0; i < boxes.length; i++) {
      if (identifier !== boxes[i].identifier) {
        continue;
      }

      return boxes[i][property];
    }

    return null;
  }

  public getAcceptedFileTypes(): string {
    return this.canvas.element.staticValues.image.allowedMimeTypes.join(',')
  }

  public reset() {
    let dialogMessage = '';
    this.resetMessage$.subscribe((next) => {
      dialogMessage = translate(next.content, this.locale);
    });

    this.dialogService.openWarningDialog(DialogSizesEnum.md, 'Achtung!', dialogMessage, 'Abbrechen', 'Zurücksetzen' ).afterClosed().subscribe((next) => {
      if (true === next) {
        this.store.dispatch(
          updateConfigurationState({
            updates: {
              remove: [
                {
                  sectionId: this.canvas.element.sectionId,
                  elementId: this.canvas.element.elementId,
                  property: null,
                  value: null
                }
              ],
            },
          })
        );

        this.store.dispatch(
          setHideOnePage({
            payload: false
          })
        );
      }
    });
  }

  public cancel() {
    let dialogMessage = '';
    this.cancelMessage$.subscribe((next) => {
      dialogMessage = translate(next.content, this.locale);
    });

    this.dialogService.openWarningDialog(DialogSizesEnum.md, 'Achtung!', dialogMessage, 'Abbrechen', 'Verwerfen' ).afterClosed().subscribe((next) => {
      if (true === next) {
        this.store.dispatch(
          setHideOnePage({
            payload: false
          })
        );
      }
    });
  }

  public save(): void {
    let fabricCanvas = this.fabricCanvas.toJSON(['identifier', 'payload']);
    fabricCanvas.objects = [];

    this.fabricCanvas.getObjects().forEach((object) => {
      fabricCanvas.objects.push(object.toJSON(['identifier', 'payload']));
    });

    const date: Date = new Date();
    const fabricCanvasJson = JSON.stringify(fabricCanvas);
    const productId = this.product.id;
    const directory = '/apto-plugin-image-upload/render-images/' + date.getFullYear() + '/' + (date.getMonth() + 1).toString().padStart(2, '0') + '/';
    const fileName = sha1(fabricCanvasJson);

    const payload = {
      json: fabricCanvasJson,
      renderImages: []
    }

    this.canvas.element.staticValues.area.forEach((area) => {
      payload.renderImages.push({
        fileName: fileName + '-' + area.identifier,
        renderImageId: fileName + '-' + area.identifier,
        productId: productId,
        directory: directory,
        path: directory + fileName + '-' + area.identifier + '.png',
        extension: 'png',
        perspective: area.perspective,
        layer: area.layer,
        offsetX: area.left * 100 / this.renderImage.width,
        offsetY: area.top * 100 / this.renderImage.height
      });
    });

    this.fabricCanvasService.uploadLayerImage(this.fabricCanvas, this.canvas.element.staticValues.area, this.renderImage, fileName, directory, (upload: Observable<any>) => {
      upload.subscribe((next) => {
        if (next.message.error === false) {
          this.store.dispatch(
            updateConfigurationState({
              updates: {
                set: [{
                  sectionId: this.canvas.element.sectionId,
                  elementId: this.canvas.element.elementId,
                  property: 'aptoElementDefinitionId',
                  value: 'apto-element-image-upload',
                }, {
                  sectionId: this.canvas.element.sectionId,
                  elementId: this.canvas.element.elementId,
                  property: 'payload',
                  value: payload,
                }],
              },
            })
          );

          this.store.dispatch(
            setHideOnePage({
              payload: false
            })
          );
        }
      })
    });
  }

  public getPrintAreaId(area) {
    return area.perspective + area.layer + area.width + area.height + area.left + area.top;
  }

  private getTextBoxControlOptions(box) {
    return box.locked ? this.controlOptionsLocked : this.controlOptionsEditable;
  }

  private getExtensionFromFileName(fileName) {
    let extension = fileName.split('.');
    if (extension.length === 1 || (extension[0] === "" && extension.length === 2)) {
      return '';
    }
    return ('' + extension.pop()).toLowerCase();
  }

  public getNameFromFileName(fileName) {
    return fileName.replace('.' + this.getExtensionFromFileName(fileName), '')
  }

  private getImageScale(maxWidth, dimensions) {
    let width = null, height = null;

    if (dimensions.width >= dimensions.height) {
      width = maxWidth;
      height = Math.floor(maxWidth / (dimensions.width / dimensions.height));
    }

    if (dimensions.height > dimensions.width) {
      width = Math.floor(maxWidth / (dimensions.height / dimensions.width));
      height = maxWidth;
    }

    return  maxWidth / dimensions.width;
  }

  private assertValidDimensions(dimensions) {
    const minWidth = this.canvas.element.staticValues.image.minWidth;
    const minHeight = this.canvas.element.staticValues.image.minHeight;

    if (minWidth > 0 && dimensions.width < minWidth) {
      return false;
    }

    if (minHeight > 0 && dimensions.width < minHeight) {
      return false;
    }

    return true;
  }

  public ngOnDestroy() {
    this.subscriptions.forEach((subscription: Subscription) => {
      subscription.unsubscribe();
    })
  }
}
