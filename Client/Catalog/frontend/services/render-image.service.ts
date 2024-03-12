import { Injectable, OnDestroy } from '@angular/core';
import { Subject, Subscription } from 'rxjs';
import { Store } from '@ngrx/store';
import { environment } from '@apto-frontend/src/environments/environment';
import { ElementState, RenderImageData } from '@apto-catalog-frontend/store/configuration/configuration.model';
import {
  selectCurrentRenderImages,
  selectCurrentStateElements, selectStateActiveElements,
} from '@apto-catalog-frontend/store/configuration/configuration.selectors';
import { selectElement, selectElements } from '@apto-catalog-frontend/store/product/product.selectors';
import { CustomProperty } from '@apto-base-core/store/custom-property/custom-property.model';
import { ReplaceColorData } from '@apto-catalog-frontend/models/replace-color-data';

@Injectable({
  providedIn: 'root',
})
export class RenderImageService implements OnDestroy {
  private subscriptions: Subscription[] = [];
  private canvas: HTMLCanvasElement;
  private ctx: CanvasRenderingContext2D;
  private scale = 1;
  private renderImages = [];
  public outputImageSrc: string = '';
  private mediaUrl: string = environment.api.media;

  public canvasWidth: number | string;
  public canvasHeight: number | string;
  public imageWidth: number | string;
  public imageHeight: number | string;

  public readonly renderImages$ = this.store.select(selectCurrentRenderImages);

  public outputSrcSubject = new Subject();

  constructor(private store: Store) {
    this.init();
  }

  public init() {
    this.ngOnDestroy();
    this.subscriptions.push(
      // data for renderImage$ is sent from multiple directions, so to call all this stuff only once we put a debounce time here
      this.renderImages$.subscribe((data) => {
        this.renderImages = data;

        if (this.renderImages.length > 0 && this.firstImageWidth && this.firstImageHeight) {
          this.canvas = document.createElement('canvas');
          this.ctx = this.canvas.getContext('2d');
          this.drawImages();
        } else {
          this.outputSrcSubject.next(null);
        }
      })
    );
  }

  public resize(img: any, width: number) {
    const canvas = document.createElement('canvas');
    const ctx = canvas.getContext('2d');
    const height = Math.floor(width / (img.width / img.height));

    canvas.width = width;
    canvas.height = height;

    return new Promise(resolve => {
      var image = new Image();
      image.onload = function() {
        // draw source image into the off-screen canvas:
        ctx.drawImage(image, 0, 0, width, height);

        // encode image to data-uri with base64 version of compressed image
        resolve({
          src: canvas.toDataURL(),
          height: height,
          width: width
        })
      };
      image.src = img.src;
    });
  }

  /**
   * This is the main function that draws the image first on hidden canvas then converts to data-url image
   *
   * @private
   */
  private drawImages(): void {
    this.clearCanvas();
    this.adjustCanvasSize();
    this.loadImages()
      .then((loadedImages) => {
        this.drawImagesOnCanvas(loadedImages);

        // we need to give some time the canvas to finish drawing, and only then convert the image, without setTimeout not working
        setTimeout(() => {
          this.generateImageFromCanvas();
          this.adjustImageSize();
          //this.afterCanvasReady.emit();
          // this.ctx.scale(this.scale, this.scale);
        }, 0);
      });
  }

  private clearCanvas(): void {
    if (this.canvas.width && this.canvas.height) {
      this.ctx.clearRect(0, 0, this.canvas.width, this.canvas.height);
    }
  }

  /**
   * Takes canvas dimension from first image
   *
   * @private
   */
  private adjustCanvasSize(): void {
    this.canvas.width = this.firstImageWidth;
    this.canvas.height = this.firstImageHeight;
    this.scale = 1;
  }

  private async loadImages(): Promise<any[]> {
    try {
      return await Promise.all(this.renderImages.map((data) => this.createHtmlImage(data)));
    } catch (error) {
      console.error('Error loading images:', error);
      throw error;
    }
  }

  /**
   * We have 3 different algorithms for drawing the images on canvas
   *
   * @param loadedImages
   * @private
   */
  private async drawImagesOnCanvas(loadedImages): Promise<void> {
    const colorsToReplace = await this.findAllCustomPropertiesValues('overlayProductColor');
    for (const img of loadedImages) {
      this.drawImageByRepeating(img.imageHtml, img.imgObj, colorsToReplace);
      // this.drawImageByScaling(img.imageHtml, img.imgObj);
      // this.drawImageByScalingAndRepeating(img.imageHtml, img.imgObj);
    }
  }

  /**
   * In element's custom properties we expect value with key "overlayColor" and as value 2 hexadecimal color values separated by comma: #CCCCCC,#000000
   *
   * @param key
   * @private
   */
  private findAllCustomPropertiesValues(key: string): Promise<ReplaceColorData[]> {
    return new Promise((resolve) => {
      this.store.select(selectStateActiveElements).subscribe((result: ElementState[]) => {
        const data: ReplaceColorData[] = [];
        const elementIds = result.map((e: ElementState) => e.id);
        this.store.select(selectElements(elementIds)).subscribe((elements) => {
          for (const singleElement of elements) {
            const customPropertyColor: CustomProperty = singleElement.customProperties.find((e) => e.key === key);
            if (customPropertyColor) {
              // value in the format e.g.	#E7E7E7,#800080
              const colors = (customPropertyColor.value as string).split(',');
              const colorData = data.find((c) => c.search === colors[0]);
              if (colorData) {
                colorData.replace = colors[1];
              } else {
                data.push({
                  search: colors[0],
                  replace: colors[1],
                });
              }
            }
          }

          return resolve(data);
        });
      });
    });
  }

  private hexToRgb(hex: string): number[] {
    hex = hex.replace(/^#/, '');
    const r = parseInt(hex.substring(0, 2), 16);
    const g = parseInt(hex.substring(2, 4), 16);
    const b = parseInt(hex.substring(4, 6), 16);

    return [r, g, b];
  }

  private drawImageWithColorReplacement(imageHtml: HTMLImageElement, colorSearchHex: string, colorReplaceHex: string): HTMLCanvasElement {
    const imageWidth = imageHtml.width;
    const imageHeight = imageHtml.height;
    // temp canvas for pixels manipulations
    const tempCanvas = document.createElement('canvas');
    tempCanvas.width = imageWidth;
    tempCanvas.height = imageHeight;
    const tempCtx = tempCanvas.getContext('2d');

    tempCtx.drawImage(imageHtml, 0, 0, imageWidth, imageHeight);

    const imageData = tempCtx.getImageData(0, 0, imageWidth, imageHeight);
    const data = imageData.data;
    const colorSearch = this.hexToRgb(colorSearchHex);
    const colorReplace = this.hexToRgb(colorReplaceHex);

    // change colors for each pixel if match criteria
    for (let i = 0; i < data.length; i += 4) {
      if (data[i] === colorSearch[0] && data[i + 1] === colorSearch[1] && data[i + 2] === colorSearch[2]) { // Sprawdzenie, czy piksel jest biały
        data[i] = colorReplace[0];
        data[i + 1] = colorReplace[1];
        data[i + 2] = colorReplace[2];
        data[i + 3] = 255;
      }
    }

    tempCtx.putImageData(imageData, 0, 0);

    return tempCanvas;
  }

  /**
   * This draws images on canvas by repeating the repeatable images, also it does not resize the images and draws them in the original size
   *
   * @param imageHtml
   * @param imgObj
   * @param colorsToReplace
   * @private
   */
  private drawImageByRepeating(imageHtml: HTMLImageElement, imgObj: RenderImageData, colorsToReplace: ReplaceColorData[]): void {
    this.store.select(selectElement(imgObj.elementId)).subscribe((storeElement) => {
      let tempCanvas = null;
      const imageWidth = imageHtml.width;
      const imageHeight = imageHtml.height;
      const imageRepeatWidth = imgObj.realWidth;
      const imageRepeatHeight = imgObj.realHeight;
      const offsetX = imgObj.realOffsetX;
      const offsetY = imgObj.realOffsetY;

      const customPropertyColor: CustomProperty = storeElement.customProperties.find((e) => e.key === 'overlayColor');
      if (customPropertyColor) {
        // value in the format e.g.	#E7E7E7,#800080
        const colors = (customPropertyColor.value as string).split(',');
        colorsToReplace.push({
          search: colors[0],
          replace: colors[1],
        });
      }

      for (const colorReplacement of colorsToReplace) {
        tempCanvas = this.drawImageWithColorReplacement(imageHtml, colorReplacement.search, colorReplacement.replace);
      }

      if (imgObj.renderImageOptions?.renderImageOptions?.type.toLowerCase() === 'wiederholbar') {
        let y = offsetY;
        do {
          let x = offsetX;
          do {
            this.ctx.drawImage(tempCanvas || imageHtml, x, y);
            x += imageWidth;
          } while (x < imageRepeatWidth + offsetX);
          y += imageHeight;
        } while (y < imageRepeatHeight + offsetY);
      } else {
        this.ctx.drawImage(tempCanvas || imageHtml, offsetX, offsetY);
      }
    });
  }

  /**
   * This draws images on canvas by resizing/scaling the repeatable images (instead of repeating them), but does not resize the final image
   * when all images are drawn only then canvas is resized
   *
   * This method much faster than 'drawImageByRepeating', but we did not select it as default as we think
   * that it can have unexpected behavior!
   * If your drawing is slow, consider using this version
   *
   * @param imageHtml
   * @param imgObj
   * @private
   */
  private drawImageByScaling(imageHtml: HTMLImageElement, imgObj: RenderImageData): void {
    const imageWidth = imageHtml.width;
    const imageHeight = imageHtml.height;
    const imageRepeatWidth = imgObj.realWidth;
    const imageRepeatHeight = imgObj.realHeight;

    const x = imgObj.realOffsetX;
    let scaleWidth = imageRepeatWidth;
    if (imageRepeatWidth !== imageWidth) {
      scaleWidth = Math.max(imageWidth, imageRepeatWidth);
    }

    const y = imgObj.realOffsetY;
    let scaleHeight = imageRepeatHeight;
    if (imageRepeatHeight !== imageHeight) {
      scaleHeight = Math.max(imageHeight, imageRepeatHeight);
    }

    this.ctx.drawImage(imageHtml, 0, 0, imageWidth, imageHeight, x, y, scaleWidth, scaleHeight);
  }

  /**
   * This version of drawing images, resizes image first and only then draws on canvas, this version does not require later resize the whole canvas
   *
   * @param imageHtml
   * @param imgObj
   * @private
   */
  private drawImageByScalingAndRepeating(imageHtml: HTMLImageElement, imgObj: RenderImageData): void {
    const imageWidth = imageHtml.width;
    const imageHeight = imageHtml.height;
    const imageRepeatWidth = imgObj.realWidth;
    const imageRepeatHeight = imgObj.realHeight;
    const offsetX = imgObj.realOffsetX;
    const offsetY = imgObj.realOffsetY;

    if (imgObj.renderImageOptions?.renderImageOptions?.type.toLowerCase() === 'wiederholbar') {
      let y = offsetY;
      do {
        let x = offsetX;
        do {
          // if scale is not 1 it means we need to resize the canvas, otherwise draw image as it is
          if (this.scale !== 1) {
            this.ctx.drawImage(imageHtml, 0, 0, imageWidth, imageHeight,
              x * this.scale, y * this.scale, imageWidth * this.scale, imageHeight * this.scale);
          } else {
            this.ctx.drawImage(imageHtml, x, y);
          }
          x += imageWidth;
        } while (x < imageRepeatWidth + offsetX * this.scale);
        y += imageHeight;
      } while (y < imageRepeatHeight + offsetY * this.scale);
    } else {
      // if scale is not 1 it means we need to resize the canvas, otherwise draw image as it is
      if (this.scale !== 1) {
        this.ctx.drawImage(imageHtml, 0, 0, imageWidth, imageHeight,
          offsetX * this.scale, offsetY * this.scale, imageWidth * this.scale, imageHeight * this.scale);
      } else {
        this.ctx.drawImage(imageHtml, offsetX, offsetY);
      }
    }
  }

  /**
   * This scales up or down the resulting images, so that it perfectly fits into available space
   *
   * @private
   */
  private adjustImageSize(): void {
    const { firstImageWidth, firstImageHeight, availableWidth, availableHeight } = this;
    const firstImageRatio = firstImageWidth / firstImageHeight;
    const availableRatio = availableWidth === 0 || availableHeight === 0 ? firstImageRatio : availableWidth / availableHeight;

    // This is the image too big for an available space case,
    // We need to shrink the canvas to fit into the available space if
    if (availableWidth !== 0 && availableHeight !== 0 && (availableWidth < firstImageWidth || availableHeight < firstImageHeight)) {
      if (firstImageRatio > availableRatio) {
        this.scale = availableWidth / firstImageWidth;
      } else {
        this.scale = availableHeight / firstImageHeight;
      }

      this.imageWidth = Math.min(firstImageWidth * this.scale, availableWidth);
      this.imageHeight = Math.min(firstImageHeight * this.scale, availableHeight);
    }
      // this is the case when "canvas-container" element has missed one of dimensions, and we can not calculate the available space,
    // in this cas we take the ratio from the first image and calculate the missing dimension from existing one considering the scale/ratio
    else if (availableWidth === 0 || availableHeight === 0) {
      if (firstImageRatio > availableRatio) {
        this.scale = availableWidth / firstImageWidth;
      } else {
        this.scale = availableHeight / firstImageHeight;
      }

      if (availableWidth === 0) {
        this.imageHeight = Math.min(availableHeight, firstImageHeight);
        this.imageWidth = firstImageRatio > 1 ? this.imageHeight * firstImageRatio : this.imageHeight / firstImageRatio;

      } else if (availableHeight === 0) {
        this.imageWidth = Math.min(availableWidth, firstImageWidth);
        this.imageHeight = firstImageRatio > 1 ? this.imageWidth * firstImageRatio : this.imageWidth / firstImageRatio;
      }
    }
    // this is the image too small case we dont have to shrink it to fit into canvas
    else {
      // we draw the canvas as big as config dimensions are
      this.imageWidth = firstImageWidth;
      this.imageHeight = firstImageHeight;
    }
  }

  private generateImageFromCanvas(type = 'image/png'): void {
    this.outputImageSrc = this.canvas.toDataURL(type);
    let next = null;
    if (this.renderImages.length > 0 && this.firstImageWidth && this.firstImageHeight) {
      next = {
        src: this.outputImageSrc,
        width: this.firstImageWidth,
        height: this.firstImageHeight
      };
    }

    this.outputSrcSubject.next(next);
  }

  /**
   * Creates a html image from the given image path
   *
   * @param image
   * @private
   */
  private createHtmlImage(image: RenderImageData): Promise<any> {
    return new Promise((resolve, reject) => {
      const img = new Image();
      img.src = `${this.mediaUrl + image.path}/${image.filename}.${image.extension}`;
      img.onload = () => resolve({ imageHtml: img, imgObj: image });
      img.onerror = reject;
    });
  }

  /**
   * First image dimensions give the canvas dimensions. So remember this when adding image in backend
   *
   * @private
   */
  private get firstImageWidth(): number {
    return this.renderImages[0].realWidth;
  }

  private get firstImageHeight(): number {
    return this.renderImages[0].realHeight;
  }

  /**
   * "available" dimensions are used for calculating the image proportions
   *
   * @private
   */
  private get availableWidth(): number {
    return this.canvas.clientWidth;
  }

  private get availableHeight(): number {
    return this.canvas.clientHeight;
  }

  public ngOnDestroy() {
    this.subscriptions.forEach((subscription: Subscription) => {
      subscription.unsubscribe();
    })
  }
}
