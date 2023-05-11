import { AfterViewInit, Component, ElementRef, OnInit, ViewChild } from '@angular/core';
import { Store } from '@ngrx/store';
import {
  setHideOnePage,
  updateConfigurationState,
} from '@apto-catalog-frontend/store/configuration/configuration.actions';
import { selectCanvas } from '@apto-image-upload-frontend/store/canvas/canvas.selectors';
import { ResizedEvent } from 'angular-resize-event';
import { CanvasState } from '@apto-image-upload-frontend/store/canvas/canvas.reducer';
import { fabric } from 'fabric';

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
  public printArea: { width: number, height: number, left: number, top: number } = { width: 0, height: 0, left: 0, top: 0 };
  public middleWidth: number = 0;
  public canvasWidth: number = 0;
  public canvasHeight: number = 0;
  public canvasLeft: number = 0;
  public factor: number = 0;
  public fabricCanvas: any = null;
  public rect1: any = null;
  public rect2: any = null;
  public canvasStyle: { width: string, height: string, left: string } = { width: '0px', height: '0px', left: '0px' };

  public constructor(private store: Store) {
  }

  public ngOnInit(): void {
    this.store.select(selectCanvas).subscribe((next) => {
      this.canvas = next;
    });
  }

  ngAfterViewInit(): void {
    setTimeout(() => {
      this.updateCanvasStyle();
      this.calculatePrintArea();
      let element = this.getElementPayload();

      this.fabricCanvas = new fabric.Canvas(this.htmlCanvas.nativeElement, {
        preserveObjectStacking: true,
        selection: false
      });

      if (!element) {
        this.rect1 = new fabric.Rect({ width: 600, height: 300, top: 162, left: 200, fill: '#ff0000', type: 'rect' });
        this.rect2 = new fabric.Rect({ width: 600, height: 300, top: 162, left: 200, fill: '#0000ff', type: 'rect' });

        this.fabricCanvas.add(this.rect1, this.rect2);
        this.setCanvasSize();
      } else {
        this.fabricCanvas.loadFromJSON(element.json, () => {
          this.setCanvasSize();
        });
      }
    });
  }

  getElementPayload() {
    let payload = this.canvas.element.state.payload;
    if (payload === null || !payload.elements) {
      return null;
    }

    const element = payload.elements.find((element) => element.elementId === this.canvas.element.elementId);
    if (element) {
      return element;
    }

    return null;
  }

  getUpdatedPayload(): any {
    // init payload
    let payload = {
      elements: []
    }

    // re-add all other elements to payload
    if (this.canvas.element.state.payload && this.canvas.element.state.payload.elements) {
      this.canvas.element.state.payload.elements.forEach((element) => {
        if (element.elementId !== this.canvas.element.elementId) {
          payload.elements.push(element);
        }
      })
    }

    // add current element to payload
    payload.elements.push({
      elementId: this.canvas.element.elementId,
      json: JSON.stringify(this.fabricCanvas)
    });

    // return payload
    return payload;
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
    let factor = this.canvasWidth / this.canvas.element.renderImage.width;
    this.printArea.width = this.canvas.element.staticValues.background.area.width * factor;
    this.printArea.height = this.canvas.element.staticValues.background.area.height * factor;
    this.printArea.left = this.canvas.element.staticValues.background.area.left * factor;
    this.printArea.top = this.canvas.element.staticValues.background.area.top * factor;
  }

  setCanvasSize() {
    if (!this.fabricCanvas || !this.canvas || this.canvasWidth < 1 || this.canvasHeight < 1) {
      return;
    }
    let factor = this.canvasWidth / this.canvas.element.renderImage.width;
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

  public cancel() {
    this.store.dispatch(
      setHideOnePage({
        payload: false
      })
    );
  }

  public finish(): void {
    this.store.dispatch(
      updateConfigurationState({
        updates: {
          set: [
            {
              sectionId: this.canvas.element.sectionId,
              elementId: this.canvas.element.elementId,
              property: 'aptoElementDefinitionId',
              value: 'apto-element-image-upload',
            },
            {
              sectionId: this.canvas.element.sectionId,
              elementId: this.canvas.element.elementId,
              property: 'payload',
              value: this.getUpdatedPayload(),
            },
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
}
