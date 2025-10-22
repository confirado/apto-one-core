import { Component, Input } from '@angular/core';
import { selectContentSnippet } from '@apto-base-frontend/store/content-snippets/content-snippets.selectors';
import { ProgressElement } from '@apto-catalog-frontend-configuration-model';
import { Store } from '@ngrx/store';
import { Product } from '@apto-catalog-frontend/store/product/product.model';
import { setHideOnePage } from '@apto-catalog-frontend-configuration-actions';
import { findEditableRenderImage, setCanvasElement } from '@apto-image-upload-frontend/store/canvas/canvas.actions';

@Component({
  selector: 'apto-image-upload',
  templateUrl: './image-upload.component.html',
  styleUrls: ['./image-upload.component.scss'],
})
export class ImageUploadComponent {
  @Input()
  public element: ProgressElement | undefined | null;

  @Input()
  public product: Product | null | undefined;

  public readonly contentSnippet$ = this.store.select(selectContentSnippet('aptoDefaultElementDefinition'));

  public constructor(private store: Store) {}

  protected get hasAttachments(): boolean {
    return this.element.element.attachments?.length !== 0;
  }

  public showDesigner(): void {
    this.store.dispatch(
      setCanvasElement({
        payload: {
          element: {
            elementId: this.element?.element.id,
            sectionId: this.element?.element.sectionId,
            sectionRepetition: this.element?.state.sectionRepetition,
            staticValues: this.element?.element.definition.staticValues,
            state: this.element?.state.values,
          },
        }
      })
    );

    if (this.element?.state.values.payload && this.element?.state.values.payload.renderImages) {
      const renderImageIds = [];
      this.element?.state.values.payload.renderImages.forEach((renderImage) => {
        renderImageIds.push(renderImage.renderImageId);
      });

      this.store.dispatch(findEditableRenderImage({
        payload: {
          perspective: this.element?.state.values.payload.renderImages[0].perspective,
          renderImageIds
        }
      }));
    }

    this.store.dispatch(setHideOnePage({ payload: true }));
  }
}
