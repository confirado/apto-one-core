import { Component, Input, OnDestroy } from '@angular/core';
import { Subscription } from 'rxjs';
import { Store } from '@ngrx/store';
import { setNextPerspective, setPrevPerspective } from '@apto-catalog-frontend/store/configuration/configuration.actions';
import { RenderImage } from '@apto-catalog-frontend/store/configuration/configuration.model';
import { Product } from '@apto-catalog-frontend/store/product/product.model';
import { RenderImageService } from '@apto-catalog-frontend/services/render-image.service';

@Component({
	selector: 'apto-o-p-full-screen',
	templateUrl: './o-p-full-screen.component.html',
	styleUrls: ['./o-p-full-screen.component.scss'],
})
export class OPFullScreenComponent implements OnDestroy{
	@Input()
	public product: Product | null | undefined;

	@Input()
	public perspectives: string[] | undefined | null;

  private subscriptions: Subscription[] = [];

	public isOpen = false;
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
