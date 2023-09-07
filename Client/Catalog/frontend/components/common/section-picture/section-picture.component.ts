import {Component, Input, OnInit} from '@angular/core';

@Component({
  selector: 'apto-section-picture',
  templateUrl: './section-picture.component.html',
  styleUrls: ['./section-picture.component.scss']
})
export class SectionPictureComponent implements OnInit {

  @Input()
  public previewImage: string | null;

  @Input()
  public width: string;

  @Input()
  public isZoomable: boolean;

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
    return this.isZoomable;
  }
}
