import { Component, Input, OnInit } from '@angular/core';
import { ElementZoomFunctionEnum } from "@apto-catalog-frontend/store/product/product.model";
import { environment } from "@apto-frontend/src/environments/environment";

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

  protected mediaUrl = environment.api.media;

  public constructor() { }

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
    return this.zoomFunction === ElementZoomFunctionEnum.IMAGE_PREVIEW ||
      this.zoomFunction === ElementZoomFunctionEnum.GALLERY;
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
    const item = this.gallery[this.currentImageIndex];
    let imageUrl = item.path || '';
    const baseIncluded = imageUrl.startsWith(this.mediaUrl);
    if (item.mediaFile && item.mediaFile.length > 0) {
      const media = item.mediaFile[0];
      if (media.filename && media.extension) {
        imageUrl = baseIncluded ? `${media.path}/${media.filename}.${media.extension}`
          : `${this.mediaUrl}${media.path}/${media.filename}.${media.extension}`;
      } else {
        imageUrl = baseIncluded ? imageUrl : this.mediaUrl + imageUrl;
      }
    } else {
      imageUrl = baseIncluded ? imageUrl : this.mediaUrl + imageUrl;
    }
    return imageUrl;
  }
}
