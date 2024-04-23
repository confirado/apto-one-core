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

  @Input()
  public gallery: any[] = [];

  public imgStyles: string = '';

  public isOpen = false;

  public currentImageIndex = 0;

  constructor() { }

  public ngOnInit(): void {
    if (this.previewImage && this.gallery.length > 0) {
      this.initializeGallery();
    }
    if (this.width) {
      this.calculateImgStyle();
    }
  }

  public get currentImagePath(): string {
    return this.updatePreviewImage();
  }

  public initializeGallery(): void {
    if (this.previewImage && this.gallery) {
      this.gallery = [{ path: this.previewImage }, ...this.gallery];
    }
  }


  public calculateImgStyle() {
    this.imgStyles = [
      'min-width: calc(' + this.width + '/4)',
      'min-height: calc(' + this.width + '/4)',
      'max-width: ' + this.width,
      'max-height: calc(' + this.width + ' + ' + this.width + '/2)'
    ].join(';');
  }

  public zoom(event: Event) {
    this.isOpen = !this.isOpen;
    event.stopPropagation();
  }

  public isZoomEnabled(): boolean {
    return this.zoomFunction == ElementZoomFunctionEnum.IMAGE_PREVIEW;
  }

  public nextImage(event: Event): void {
    event.stopPropagation();
    if (this.gallery.length === 0) return;
    this.currentImageIndex = (this.currentImageIndex + 1) % this.gallery.length;
    this.previewImage = this.updatePreviewImage();
  }

  public previousImage(event: Event): void {
    event.stopPropagation();
    if (this.gallery.length === 0) return;
    this.currentImageIndex = (this.currentImageIndex - 1 + this.gallery.length) % this.gallery.length;
    this.previewImage = this.updatePreviewImage();
  }

  private updatePreviewImage(): string {
    const baseUrl = 'http://grobi.projektversion.de/apto-one-template/web/public/media/';
    const item = this.gallery[this.currentImageIndex];
    let imageUrl = item.path || '';
    const baseIncluded = imageUrl.startsWith(baseUrl);
    if (item.mediaFile && item.mediaFile.length > 0) {
      const media = item.mediaFile[0];
      if (media.filename && media.extension) {
        imageUrl = baseIncluded ? `${media.path}/${media.filename}.${media.extension}`
          : `${baseUrl}${media.path}/${media.filename}.${media.extension}`;
      } else {
        imageUrl = baseIncluded ? imageUrl : baseUrl + imageUrl;
      }
    } else {
      imageUrl = baseIncluded ? imageUrl : baseUrl + imageUrl;
    }
    return imageUrl;
  }
}
