import { AfterViewInit, Component, ElementRef, OnInit, ViewChild } from '@angular/core';
import { Store } from '@ngrx/store';
import { HttpClient } from '@angular/common/http';
import { Observable } from 'rxjs';
import { fabric } from 'fabric';
import { ResizedEvent } from 'angular-resize-event';

import { environment } from '@apto-frontend/src/environments/environment';
import { sha1 } from '@apto-base-core/helper/encrypt';

import {
  setHideOnePage,
  updateConfigurationState,
} from '@apto-catalog-frontend/store/configuration/configuration.actions';

import { selectCanvas } from '@apto-image-upload-frontend/store/canvas/canvas.selectors';
import { selectContentSnippet } from '@apto-base-frontend/store/content-snippets/content-snippets.selectors';
import { selectLocale } from '@apto-base-frontend/store/language/language.selectors';

import { DialogSizesEnum } from '@apto-frontend/src/configs-static/dialog-sizes-enum';
import { CanvasState } from '@apto-image-upload-frontend/store/canvas/canvas.reducer';

import { FabricCanvasService } from '@apto-image-upload-frontend/services/fabric-canvas.service';
import { DialogService } from '@apto-catalog-frontend/components/common/dialogs/dialog-service';
import { translate } from '@apto-base-core/store/translated-value/translated-value.model';

@Component({
  selector: 'apto-designer',
  templateUrl: './designer.component.html',
  styleUrls: ['./designer.component.scss'],
})
export class DesignerComponent implements OnInit, AfterViewInit {
  @ViewChild('htmlCanvas') htmlCanvas: ElementRef;
  @ViewChild('htmlRenderImage') htmlRenderImage: ElementRef;
  @ViewChild('htmlMiddle') htmlMiddle: ElementRef;

  private locale: string;

  public canvas: CanvasState | null = null;
  public mediaUrl: string = environment.api.media;
  public currentArea = 0;

  public readonly contentSnippet$ = this.store.select(selectContentSnippet('plugins.imageUpload'));
  public readonly cancelMessage$ = this.store.select(selectContentSnippet('plugins.imageUpload.upload.cancelMessage'));
  public readonly resetMessage$ = this.store.select(selectContentSnippet('plugins.imageUpload.upload.resetMessage'));

  public fabricCanvas: any = null;
  public printAreas: { width: number, height: number, left: number, top: number }[] = [];
  public canvasStyle: { width: string, height: string, left: string } = { width: '0px', height: '0px', left: '0px' };

  public middleWidth: number = 0;
  public canvasWidth: number = 0;
  public canvasHeight: number = 0;
  public canvasLeft: number = 0;

  public fabricTextBoxes: any[] = [];
  public fabricMotive: any = null;
  public controlOptions = {
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

  public constructor(private store: Store, private fabricCanvasService: FabricCanvasService, private http: HttpClient, private dialogService: DialogService) {
    // set default locale
    this.locale = environment.defaultLocale;
  }

  public ngOnInit(): void {
    // subscribe for locale store value
    this.store.select(selectLocale).subscribe((locale) => {
      if (locale === null) {
        this.locale = environment.defaultLocale;
      } else {
        this.locale = locale;
      }
    });

    // subscribe for canvas
    this.store.select(selectCanvas).subscribe((next) => {
      this.canvas = next;
    });
  }

  public ngAfterViewInit(): void {
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
            originY: "center"
          }

          // cant use IText actually, see: https://github.com/fabricjs/fabric.js/issues/8865
          let fabricText = new fabric.Text(box.default, {
            ...textOptions,
            ...this.controlOptions
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
              object.setOptions(this.controlOptions);
              this.fabricTextBoxes.push(object);
            }

            if (object.get('type') === 'image') {
              this.fabricMotive = object;
              this.fabricMotive.setOptions(this.controlOptions);
            }
          });
        });
      }
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
        ...this.controlOptions
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
    let factor = this.canvasWidth / this.canvas.renderImage.width;

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
    let factor = this.canvasWidth / this.canvas.renderImage.width;
    this.fabricCanvas.setWidth(this.canvasWidth);
    this.fabricCanvas.setHeight(this.canvasHeight);
    this.fabricCanvas.setZoom(factor);
  }

  updateCanvasStyle() {
    this.canvasWidth = this.htmlRenderImage.nativeElement.clientWidth;
    this.canvasHeight = this.htmlRenderImage.nativeElement.clientHeight;
    this.middleWidth = this.htmlMiddle.nativeElement.clientWidth;
    this.canvasLeft = (this.middleWidth - this.canvasWidth) / 2;

    this.canvasStyle.width = this.canvasWidth + 'px';
    this.canvasStyle.height = this.canvasHeight + 'px';
    this.canvasStyle.left = this.canvasLeft + 'px';
  }

  public updateText(event, identifier) {
    this.fabricCanvas.getObjects().forEach((object) => {
      if (object.get('type') === 'text' && object.get('identifier') === identifier) {
        object.setOptions({
          text: event.target.value
        });
      }

      this.fabricCanvas.renderAll();
    });
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
    let fabricCanvas = this.fabricCanvas.toJSON(['identifier']);
    fabricCanvas.objects = [];

    this.fabricCanvas.getObjects().forEach((object) => {
      fabricCanvas.objects.push(object.toJSON(['identifier']));
    });

    const fabricCanvasJson = JSON.stringify(fabricCanvas);
    const productId = 'fe65066c-41b9-46da-a500-044777a1d4b5';
    const directory = '/apto-plugin-image-upload/render-images/2023/05/';
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
        offsetX: area.left * 100 / this.canvas.renderImage.width,
        offsetY: area.top * 100 / this.canvas.renderImage.height
      });
    });

    this.fabricCanvasService.uploadLayerImage(this.fabricCanvas, this.canvas.element.staticValues.area, this.canvas.renderImage, fileName, (upload: Observable<any>) => {
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
}
