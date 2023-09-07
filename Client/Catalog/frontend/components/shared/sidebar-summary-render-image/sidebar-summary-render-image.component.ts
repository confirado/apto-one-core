import { Component, Input, OnDestroy } from '@angular/core';
import { Store } from '@ngrx/store';
import { Subscription } from 'rxjs';
import { setNextPerspective, setPrevPerspective } from '@apto-catalog-frontend/store/configuration/configuration.actions';
import { RenderImage } from '@apto-catalog-frontend/store/configuration/configuration.model';
import { Product } from '@apto-catalog-frontend/store/product/product.model';
import { RenderImageService } from '@apto-catalog-frontend/services/render-image.service';

@Component({
	selector: 'apto-sidebar-summary-render-image',
	templateUrl: './sidebar-summary-render-image.component.html',
	styleUrls: ['./sidebar-summary-render-image.component.scss'],
})
export class SidebarSummaryRenderImageComponent implements OnDestroy{
  private subscriptions: Subscription[] = [];

	@Input()
	public perspectives: string[] | undefined | null;

	@Input()
	public product: Product | null | undefined;

  public renderImage = null;

	public constructor(private store: Store, private renderImageService: RenderImageService) {
    this.renderImageService.init();
    this.subscriptions.push(
      this.renderImageService.outputSrcSubject.subscribe((next) => {
        this.renderImage = next;
      })
    );
  }

	public prevRenderImage(): void {
		this.store.dispatch(setPrevPerspective());
	}

	public nextRenderImage(): void {
		this.store.dispatch(setNextPerspective());
	}

  public ngOnDestroy() {
    this.subscriptions.forEach((subscription: Subscription) => {
      subscription.unsubscribe();
    })
  }
}
