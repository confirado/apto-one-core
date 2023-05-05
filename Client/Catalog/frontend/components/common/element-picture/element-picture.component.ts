import { Component, Input, OnInit } from '@angular/core';

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
  public isZoomable: boolean = true;

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
}
