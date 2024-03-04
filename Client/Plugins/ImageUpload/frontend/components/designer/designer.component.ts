import {
  AfterViewInit, Component, ElementRef, HostBinding, Input, OnChanges, OnDestroy, OnInit, SimpleChanges,
  ViewChild,
} from '@angular/core';
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

import { selectContentSnippet } from '@apto-base-frontend/store/content-snippets/content-snippets.selectors';

import { RenderImageService } from '@apto-catalog-frontend/services/render-image.service';
import { selectProduct } from '@apto-catalog-frontend/store/product/product.selectors';
import { Product } from '@apto-catalog-frontend/store/product/product.model';

import { selectCanvas } from '@apto-image-upload-frontend/store/canvas/canvas.selectors';
import { CanvasState } from '@apto-image-upload-frontend/store/canvas/canvas.reducer';
import { FabricCanvasService } from '@apto-image-upload-frontend/services/fabric-canvas.service';
import { CanvasStyle, Font, PrintArea } from '@apto-image-upload-frontend/store/canvas/canvas.model';
import { UntilDestroy, untilDestroyed } from '@ngneat/until-destroy';

@UntilDestroy()
@Component({
  selector: 'apto-designer',
  templateUrl: './designer.component.html',
  styleUrls: ['./designer.component.scss'],
  providers: [RenderImageService]
})
export class DesignerComponent implements OnInit, AfterViewInit, OnDestroy {
  @ViewChild('htmlCanvas') htmlCanvas: ElementRef;
  @ViewChild('htmlRenderImage') htmlRenderImage: ElementRef;
  @ViewChild('htmlMiddle') htmlMiddle: ElementRef;

  private initStarted: boolean = false;
  private subscriptions: Subscription[] = [];

  public mediaUrl: string = environment.api.media;
  public renderImage: any = null;
  public imageUploadControl: FormControl;
  public imageUploadErrors: Array<any> = [];

  public readonly contentSnippet$ = this.store.select(selectContentSnippet('plugins.imageUpload'));

  public printAreas: PrintArea[] = [];
  public canvasStyle: CanvasStyle = { width: '0px', height: '0px', left: '0px' };

  @Input()
  public currentPerspective: string;

  @Input()
  public locale: string;

  @Input()
  public canvas: CanvasState;

  @Input()
  public product: Product;

  @HostBinding('class.visible') @Input() visible: boolean;

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
    lockSkewingY: true,
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
    lockSkewingY: true,
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
  };

  public fonts: Font[] = [];
  public selectedFont: Font | null = null;
  public file: File | null;

  public constructor(private store: Store, private fabricCanvasService: FabricCanvasService, public renderImageService: RenderImageService) {
  }

  public ngOnInit(): void {
    this.imageUploadControl = new FormControl(this.file, [
      Validators.required,
      // 1024 * 1024 equals to 1MB, max value must be given in byte
      MaxSizeValidator(this.canvas.element.staticValues.image[this.currentPerspective]?.maxFileSize * 1024 * 1024)
    ]);

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
    this.renderImageService.initForPerspective(this.currentPerspective);
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

  public init(): void {
    this.initFonts().then(() => {
      setTimeout(() => {
        this.fabricCanvas = new fabric.Canvas(this.htmlCanvas.nativeElement, {
          preserveObjectStacking: true,
          selection: false
        });

        this.initCanvasSize();

        this.fabricCanvas.on({
          'selection:created': this.selectionUpdated.bind(this),
          'selection:updated': this.selectionUpdated.bind(this),
          'selection:cleared': this.selectionCleared.bind(this),
        });

        if (this.canvas.element.state.payload === null) {
          this.initTextBoxes();
          this.fabricCanvas.requestRenderAll();
        } else {
          this.initState(() => {
            this.fabricCanvas.requestRenderAll();
          });
        }
      });
    });
  }

  private initFonts(): Promise<any> {
    this.fonts = [];
    this.selectedFont = null;
    const promises = [(new FontFaceObserver('Montserrat')).load()];

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

  private initCanvasSize(): void {
    this.updateCanvasStyle();
    this.calculatePrintAreas();
    this.setCanvasSize();
  }

  private initTextBoxes(): void {
    this.fabricTextBoxes = [];
    if (!this.canvas.element.staticValues.text.active) {
      return;
    }

    this.canvas.element.staticValues.text.boxes.forEach((box) => {
      if (box.perspective !== this.currentPerspective) {
        return;
      }

      const textOptions = {
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
      const fabricText = new fabric.Text(box.default, {
        ...textOptions,
        ...this.getTextBoxControlOptions(box)
      }).setControlsVisibility(this.controlVisibility);

      if (box.radius > 0) {
        this.fabricCanvasService.updateTextElementForBending(fabricText, box.radius);
      }

      this.fabricCanvas.add(fabricText);
      this.fabricTextBoxes.push(fabricText);
    });
  }

  private initState(callback): void {
    this.fabricCanvas.loadFromJSON(this.canvas.element.state.payload.json, () => {
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

  public onResizedBackground(event: ResizedEvent): void {
    if (event.isFirst) {
      return;
    }
    this.initCanvasSize();
  }

  public onResizedMiddle(event: ResizedEvent): void {
    if (event.isFirst) {
      return;
    }
    this.initCanvasSize();
  }

  private calculatePrintAreas(): void {
    if (!this.canvas) {
      return;
    }
    this.printAreas = [];
    const factor = this.canvasWidth / this.renderImage.width;

    this.canvas.element.staticValues.area.forEach((area) => {
      if (area.perspective === this.currentPerspective) {
        this.printAreas.push({
          width: area.width * factor,
          height: area.height * factor,
          left: area.left * factor,
          top: area.top * factor
        });
      }
    });
  }

  private setCanvasSize(): void {
    if (!this.fabricCanvas || !this.canvas || this.canvasWidth < 1 || this.canvasHeight < 1) {
      return;
    }
    const factor = this.canvasWidth / this.renderImage.width;
    this.fabricCanvas.setWidth(this.canvasWidth);
    this.fabricCanvas.setHeight(this.canvasHeight);
    this.fabricCanvas.setZoom(factor);
  }

  private updateCanvasStyle(): void {
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

  public addImage(url, options, layer = null, center = false): void {
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

  public addMotive(file): void {
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

    if (fileIsAlreadySelected) {
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

  public addImageFromFile(file): void {
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
        const scale = this.getImageScale(this.canvas.element.staticValues.image[this.currentPerspective]?.previewSize, dimensions);
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

  public selectionUpdated(event): void {
    this.fabricSelectedObject = null;
    if (event.selected.length > 0) {
      this.fabricSelectedObject = event.selected[0];
    }
  }

  public selectionCleared(event): void {
    this.fabricSelectedObject = null;
  }

  public removeSelectedObject(): void {
    if (null === this.fabricSelectedObject) {
      return;
    }

    if (this.fabricSelectedObject.payload.type === 'image') {
      this.imageUploadControl.reset();
    }

    this.fabricCanvas.remove(this.fabricSelectedObject);
    this.fabricSelectedObject = null;
  }

  public updateText(event, identifier): void {
    this.updateTextPropery(identifier, 'text', event.target.value);
  }

  public removeDefaultText(box): void {
    if (box.get('text') === box.payload.box.default) {
      this.updateTextPropery(box.payload.box.identifier, 'text', '');
    }
  }

  public updateTextColor(event, identifier): void {
    this.updateTextPropery(identifier, 'fill', '#' + event.value.hex, { color: event.value });
  }

  public updateTextFont(event): void {
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

  public getObjectValue(type, identifier, property): Color {
    const object = this.fabricCanvas.getObjects().find((o) => o.get('type') === type && o.get('identifier') === identifier);
    if (!object || !object.hasOwnProperty(property)) {
      return null;
    }
    return object[property];
  }

  public getColorFromHex(hex): Color {
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
    return this.canvas.element.staticValues.image[this.currentPerspective]?.allowedMimeTypes.join(',')
  }

  public getPrintAreaId(area): string {
    return area.perspective + area.layer + area.width + area.height + area.left + area.top;
  }

  private getTextBoxControlOptions(box) {
    return box.locked ? this.controlOptionsLocked : this.controlOptionsEditable;
  }

  private getExtensionFromFileName(fileName): string {
    const extension = fileName.split('.');
    if (extension.length === 1 || (extension[0] === "" && extension.length === 2)) {
      return '';
    }
    return ('' + extension.pop()).toLowerCase();
  }

  public getNameFromFileName(fileName): string {
    return fileName.replace('.' + this.getExtensionFromFileName(fileName), '');
  }

  private getImageScale(maxWidth, dimensions): number {
    return maxWidth / dimensions.width;
  }

  public ngOnDestroy(): void {
    this.subscriptions.forEach((subscription: Subscription) => {
      subscription.unsubscribe();
    });
    this.renderImageService.ngOnDestroy();
  }

  private updateTextPropery(identifier, property, value, payload = {}): void {
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

  private assertValidDimensions(dimensions): boolean {
    const minWidth = this.canvas.element.staticValues.image[this.currentPerspective]?.minWidth;
    const minHeight = this.canvas.element.staticValues.image[this.currentPerspective]?.minHeight;

    if ((minWidth > 0 && dimensions.width < minWidth) || (minHeight > 0 && dimensions.width < minHeight)) {
      return false;
    }

    return true;
  }
}
