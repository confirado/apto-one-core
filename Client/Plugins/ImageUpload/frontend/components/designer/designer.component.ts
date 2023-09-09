import { AfterViewInit, Component, ElementRef, OnDestroy, OnInit, ViewChild } from '@angular/core';
import { Store } from '@ngrx/store';
import { HttpClient } from '@angular/common/http';
import { Observable, Subscription } from 'rxjs';
import { ResizedEvent } from 'angular-resize-event';
import { fabric } from 'fabric';
import { Color, stringInputToObject } from '@angular-material-components/color-picker';

import { environment } from '@apto-frontend/src/environments/environment';
import { DialogSizesEnum } from '@apto-frontend/src/configs-static/dialog-sizes-enum';
import { sha1 } from '@apto-base-core/helper/encrypt';
import { translate } from '@apto-base-core/store/translated-value/translated-value.model';

import { selectContentSnippet } from '@apto-base-frontend/store/content-snippets/content-snippets.selectors';
import { selectLocale } from '@apto-base-frontend/store/language/language.selectors';
import {
  setHideOnePage,
  updateConfigurationState,
} from '@apto-catalog-frontend/store/configuration/configuration.actions';
import { DialogService } from '@apto-catalog-frontend/components/common/dialogs/dialog-service';
import { RenderImageService } from '@apto-catalog-frontend/services/render-image.service';
import { selectProduct } from '@apto-catalog-frontend/store/product/product.selectors';
import { Product } from '@apto-catalog-frontend/store/product/product.model';

import { selectCanvas } from '@apto-image-upload-frontend/store/canvas/canvas.selectors';
import { CanvasState } from '@apto-image-upload-frontend/store/canvas/canvas.reducer';
import { FabricCanvasService } from '@apto-image-upload-frontend/services/fabric-canvas.service';

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
  private initialized: boolean = false;
  private subscriptions: Subscription[] = [];

  public canvas: CanvasState | null = null;
  public mediaUrl: string = environment.api.media;
  public currentArea = 0;
  public renderImage: any = null;

  public readonly contentSnippet$ = this.store.select(selectContentSnippet('plugins.imageUpload'));
  public readonly cancelMessage$ = this.store.select(selectContentSnippet('plugins.imageUpload.upload.cancelMessage'));
  public readonly resetMessage$ = this.store.select(selectContentSnippet('plugins.imageUpload.upload.resetMessage'));

  public fabricCanvas: any = null;
  public printAreas: { width: number, height: number, left: number, top: number }[] = [];
  public canvasStyle: { width: string, height: string, left: string } = { width: '0px', height: '0px', left: '0px' };
  public product: Product;

  public middleWidth: number = 0;
  public canvasWidth: number = 0;
  public canvasHeight: number = 0;
  public canvasLeft: number = 0;

  public fabricTextBoxes: any[] = [];
  public fabricMotive: any = null;
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
    lockSkewingX: false,
    lockSkewingY: false
  };

  public constructor(private store: Store, private fabricCanvasService: FabricCanvasService, private http: HttpClient, private dialogService: DialogService, public renderImageService: RenderImageService) {
    // set default locale
    this.locale = environment.defaultLocale;
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
    });
  }

  public ngAfterViewInit(): void {
    this.renderImageService.init();
    this.subscriptions.push(
      this.renderImageService.outputSrcSubject.subscribe((next) => {
        this.renderImage = next;
        if (null !== next && this.initialized === false) {
          this.init();
        }
      })
    );

    if (this.renderImage && this.initialized === false) {
      this.init();
    }
  }

  public init() {
    setTimeout(() => {
      this.updateCanvasStyle();
      this.calculatePrintAreas();

      this.fabricCanvas = new fabric.Canvas(this.htmlCanvas.nativeElement, {
        preserveObjectStacking: true,
        selection: false
      });

      if (!this.canvas.element.state.payload) {
        this.canvas.element.staticValues.text.boxes.forEach((box) => {
          let textOptions = {
            identifier: box.identifier,
            fontSize: box.fontSize,
            fill: box.fill,
            textAlign: box.textAlign,
            left: box.left,
            top: box.top,
            fontFamily: 'Montserrat',
            originX: "center",
            originY: "center",
            payload: {
              box: box
            }
          }

          // cant use IText actually, see: https://github.com/fabricjs/fabric.js/issues/8865
          let fabricText = new fabric.Text(box.default, {
            ...textOptions,
            ...this.getTextBoxControlOptions(box)
          });

          if (box.radius > 0) {
            this.fabricCanvasService.updateTextElementForBending(fabricText, box.radius);
          }

          this.fabricCanvas.add(fabricText);
          this.fabricTextBoxes.push(fabricText);
        });
        this.setCanvasSize();
      } else {
        this.fabricCanvas.loadFromJSON(this.canvas.element.state.payload.json, () => {
          this.setCanvasSize();

          this.fabricCanvas.getObjects().forEach((object) => {
            if (object.get('type') === 'text') {
              object.setOptions(this.getTextBoxControlOptions(object.payload.box));
              this.fabricTextBoxes.push(object);
            }

            if (object.get('type') === 'image') {
              this.fabricMotive = object;
              this.fabricMotive.setOptions(this.controlOptionsLocked);
            }
          });
        });
      }

      this.initialized = true;
    });
  }

  public setPrintArea(index) {
    this.currentArea = index;
  }

  public getPrintAreaId(area) {
    return area.perspective + area.layer + area.width + area.height + area.left + area.top;
  }

  public addImage(url) {
    fabric.Image.fromURL(url, (fabricImage) => {
      if (null !== this.fabricMotive) {
        this.fabricCanvas.remove(this.fabricMotive);
      }

      const imageOptions = {
        left: this.canvas.element.staticValues.motive.left,
        top: this.canvas.element.staticValues.motive.top
      }

      this.fabricMotive = fabricImage;
      this.fabricMotive.setOptions({
        ...imageOptions,
        ...this.controlOptionsLocked
      });
      this.fabricCanvas.add(this.fabricMotive);
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

  public updateText(event, identifier) {
    this.updateTextPropery(identifier, 'text', event.target.value);
  }

  public updateTextColor(event, identifier) {
    this.updateTextPropery(identifier, 'fill', '#' + event.value.hex, { color: event.value });
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

  private getTextBoxControlOptions(box) {
    return box.locked ? this.controlOptionsLocked : this.controlOptionsEditable;
  }

  public ngOnDestroy() {
    this.subscriptions.forEach((subscription: Subscription) => {
      subscription.unsubscribe();
    })
  }
}
