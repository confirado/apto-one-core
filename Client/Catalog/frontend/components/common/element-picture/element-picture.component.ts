import {Component, Input, OnInit} from '@angular/core';
import {ElementZoomFunctionEnum} from "@apto-catalog-frontend/store/product/product.model";

@Component({
  selector: 'apto-element-picture',
  templateUrl: './element-picture.component.html',
  styleUrls: ['./element-picture.component.scss']
})
export class ElementPictureComponent implements OnInit {

  @Input()
  public previewImage: string | null;

  @Input()
  public width: string;

  @Input()
  public zoomFunction: ElementZoomFunctionEnum;

  public imgStyles: string = '';

  public isOpen = false;

  constructor() { }

  ngOnInit(): void {
    if (this.width) {
      this.calculateImgStyle();
    }
  }

  calculateImgStyle() {
    this.imgStyles = [
      'min-width: calc(' + this.width + '/4)',
      'min-height: calc(' + this.width + '/4)',
      'max-width: ' + this.width,
      'max-height: calc(' + this.width + ' + ' + this.width + '/2)'
    ].join(';');
  }

  zoom(event: Event) {
    this.isOpen = !this.isOpen;
    event.stopPropagation();
  }

  isZoomEnabled(): boolean {
    return this.zoomFunction == ElementZoomFunctionEnum.IMAGE_PREVIEW;
  }
}
