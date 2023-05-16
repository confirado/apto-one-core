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

  public canvas: CanvasState | null = null;
  private locale: string;

  public readonly contentSnippet$ = this.store.select(selectContentSnippet('plugins.imageUpload'));
  public readonly cancelMessage$ = this.store.select(selectContentSnippet('plugins.imageUpload.upload.cancelMessage'));
  public readonly resetMessage$ = this.store.select(selectContentSnippet('plugins.imageUpload.upload.resetMessage'));

  public fabricCanvas: any = null;
  public printArea: { width: number, height: number, left: number, top: number } = { width: 0, height: 0, left: 0, top: 0 };
  public canvasStyle: { width: string, height: string, left: string } = { width: '0px', height: '0px', left: '0px' };

  public middleWidth: number = 0;
  public canvasWidth: number = 0;
  public canvasHeight: number = 0;
  public canvasLeft: number = 0;

  public fabricText: any = null;
  public textSettings: any = {};

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

  ngAfterViewInit(): void {
    setTimeout(() => {
      this.textSettings = Object.assign({}, this.canvas.element.staticValues.text);
      this.updateCanvasStyle();
      this.calculatePrintArea();
      let controlOptions = {
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
      }

      this.fabricCanvas = new fabric.Canvas(this.htmlCanvas.nativeElement, {
        preserveObjectStacking: true,
        selection: false
      });

      if (!this.canvas.element.state.payload) {
        let textOptions = {
          fontSize: this.textSettings.fontSize,
          fill: this.textSettings.fill,
          textAlign: this.textSettings.textAlign,
          left: this.textSettings.left,
          top: this.textSettings.top,
          fontFamily: 'Montserrat',
          originX: "center",
          originY: "center"
        }

        // cant use IText actually, see: https://github.com/fabricjs/fabric.js/issues/8865
        this.fabricText = new fabric.Text(this.textSettings.default, {
          ...textOptions,
          ...controlOptions
        });

        if (this.textSettings.radius > 0) {
          this.fabricCanvasService.updateTextElementForBending(this.fabricText, this.textSettings.radius);
        }

        this.fabricCanvas.add(this.fabricText);
        this.setCanvasSize();
      } else {
        this.fabricCanvas.loadFromJSON(this.canvas.element.state.payload.json, () => {
          this.setCanvasSize();

          this.fabricCanvas.getObjects().forEach((object) => {
            object.setOptions(controlOptions);
            this.textSettings.default = object.get('text');
          });
        });
      }
    });
  }

  onResizedBackground(event: ResizedEvent) {
    if (event.isFirst) {
      return;
    }
    this.updateCanvasStyle();
    this.calculatePrintArea();
    this.setCanvasSize();
  }

  onResizedMiddle(event: ResizedEvent) {
    if (event.isFirst) {
      return;
    }
    this.updateCanvasStyle();
    this.calculatePrintArea();
    this.setCanvasSize();
  }

  calculatePrintArea() {
    if (!this.canvas) {
      return;
    }
    let factor = this.canvasWidth / this.canvas.renderImage.width;
    this.printArea.width = this.canvas.element.staticValues.background.area.width * factor;
    this.printArea.height = this.canvas.element.staticValues.background.area.height * factor;
    this.printArea.left = this.canvas.element.staticValues.background.area.left * factor;
    this.printArea.top = this.canvas.element.staticValues.background.area.top * factor;
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

  public updateText(text) {
    this.textSettings.default = text;
    this.fabricCanvas.getObjects().forEach((object) => {
      object.setOptions({
        text: this.textSettings.default
      });
      this.fabricCanvas.renderAll();
    });
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
    const fabricCanvasJson = JSON.stringify(this.fabricCanvas);
    const fileName = sha1(fabricCanvasJson);
    const payload = {
      json: fabricCanvasJson,
      renderImage: {
        fileName: fileName,
        renderImageId: fileName,
        productId: 'fe65066c-41b9-46da-a500-044777a1d4b5',
        directory: '/apto-plugin-image-upload/render-images/2023/05/',
        path: '/apto-plugin-image-upload/render-images/2023/05/' + this.canvas.element.elementId + 'png',
        extension: 'png',
        perspective: this.canvas.element.staticValues.background.perspective,
        layer: this.canvas.element.staticValues.background.layer,
        offsetX: this.canvas.element.staticValues.background.area.left * 100 / this.canvas.renderImage.width,
        offsetY: this.canvas.element.staticValues.background.area.top * 100 / this.canvas.renderImage.height
      }
    }

    this.fabricCanvasService.uploadLayerImage(this.fabricCanvas, this.canvas.element.staticValues.background.area, this.canvas.renderImage, fileName, (upload: Observable<any>) => {
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
