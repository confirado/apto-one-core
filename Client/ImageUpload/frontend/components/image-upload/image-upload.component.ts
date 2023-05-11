import { Component, ElementRef, Input, OnInit, ViewChild } from '@angular/core';
import { selectContentSnippet } from '@apto-base-frontend/store/content-snippets/content-snippets.selectors';
import { ProgressElement, RenderImage } from '@apto-catalog-frontend/store/configuration/configuration.model';
import { Store } from '@ngrx/store';
import { Product } from '@apto-catalog-frontend/store/product/product.model';
import { setHideOnePage } from '@apto-catalog-frontend/store/configuration/configuration.actions';
import { setCanvasElement } from '@apto-image-upload-frontend/store/canvas/canvas.actions';
import { selectRenderImageByPerspective } from '@apto-catalog-frontend/store/configuration/configuration.selectors';

@Component({
  selector: 'apto-image-upload',
  templateUrl: './image-upload.component.html',
  styleUrls: ['./image-upload.component.scss'],
})
export class ImageUploadComponent implements OnInit {
  @Input()
  public element: ProgressElement | undefined | null;

  @Input()
  public product: Product | null | undefined;

  public readonly contentSnippet$ = this.store.select(selectContentSnippet('aptoDefaultElementDefinition'));
  public renderImage: RenderImage | null = null;

  public constructor(private store: Store) {}

  public ngOnInit(): void {
    this.store.select(selectRenderImageByPerspective(this.element?.element.definition.staticValues.background.perspective)).subscribe((next) => {
      this.renderImage = next;
    })
  }

  public hideOnePage(): void {
    this.store.dispatch(
      setCanvasElement({
        payload: {
          elementId: this.element?.element.id,
          sectionId: this.element?.element.sectionId,
          staticValues: this.element?.element.definition.staticValues,
          state: this.element?.state.values,
          renderImage: this.renderImage
        }
      })
    );

    this.store.dispatch(
      setHideOnePage({
        payload: true
      })
    );
  }
}
