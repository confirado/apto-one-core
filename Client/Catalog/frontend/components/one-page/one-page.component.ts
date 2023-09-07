import { Component, OnDestroy, OnInit, SimpleChanges } from '@angular/core';
import { Store } from '@ngrx/store';
import { Observable, Subscription } from 'rxjs';
import { TemplateSlot } from '@apto-base-core/template-slot/template-slot.decorator';
import { TemplateSlotInterface } from '@apto-base-core/template-slot/template-slot.interface';
import {
  selectConfigurationLoading, selectCurrentRenderImages,
  selectHideOnePage,
  selectPerspectives,
  selectRenderImage,
} from '@apto-catalog-frontend/store/configuration/configuration.selectors';
import { Product } from '@apto-catalog-frontend/store/product/product.model';
import { selectProduct } from '@apto-catalog-frontend/store/product/product.selectors';
import { RenderImageService } from '@apto-catalog-frontend/services/render-image.service';

@Component({
	selector: 'apto-one-page',
	templateUrl: './one-page.component.html',
	styleUrls: ['./one-page.component.scss'],
})
@TemplateSlot({
	slot: 'frontend-content',
})
export class OnePageComponent implements OnInit, TemplateSlotInterface, OnDestroy {
  private subscriptions: Subscription[] = [];

	public readonly product$ = this.store.select(selectProduct);
	public readonly hideOnePage$ = this.store.select(selectHideOnePage);
	public readonly perspectives$ = this.store.select(selectPerspectives);
  public readonly configurationLoading$ = this.store.select(selectConfigurationLoading);

  public renderImage = null;

	public constructor(private store: Store, private renderImageService: RenderImageService) {
    this.renderImageService.init();
    this.subscriptions.push(
      this.renderImageService.outputSrcSubject.subscribe((next) => {
        this.renderImage = next;
      })
    );
  }

	public ngOnInit(): void {}

	onPropsChanged(changes: SimpleChanges) {

  }

  public ngOnDestroy() {
    this.subscriptions.forEach((subscription: Subscription) => {
      subscription.unsubscribe();
    });
  }
}
