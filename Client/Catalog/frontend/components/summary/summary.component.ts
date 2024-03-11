import { Component } from '@angular/core';
import { ActivatedRoute, Router } from '@angular/router';
import { BasketService } from '@apto-base-frontend/services/basket.service';
import { selectContentSnippet } from '@apto-base-frontend/store/content-snippets/content-snippets.selectors';
import { addToBasket } from '@apto-catalog-frontend/store/configuration/configuration.actions';
import {
  selectConfiguration, selectCurrentPerspective,
  selectSumPrice,
} from '@apto-catalog-frontend/store/configuration/configuration.selectors';
import { selectProduct } from '@apto-catalog-frontend/store/product/product.selectors';
import { Store } from '@ngrx/store';
import { FormControl, FormGroup } from '@angular/forms';
import { RenderImageService } from '@apto-catalog-frontend/services/render-image.service';
import { environment } from '@apto-frontend/src/environments/environment';
import { UntilDestroy, untilDestroyed } from '@ngneat/until-destroy';

@UntilDestroy()
@Component({
	selector: 'apto-summary',
	templateUrl: './summary.component.html',
	styleUrls: ['./summary.component.scss'],
})
export class SummaryComponent {
	public readonly contentSnippet$ = this.store.select(selectContentSnippet('aptoSummary'));
  public readonly sidebarSummary$ = this.store.select(selectContentSnippet('sidebarSummary'));
	public product$ = this.store.select(selectProduct);
	public configuration$ = this.store.select(selectConfiguration);
	public readonly sumPrice$ = this.store.select(selectSumPrice);
  public quantityInputGroup = new FormGroup({
    quantityInput: new FormControl<number>(1),
  });
  public renderImage = null;

  public constructor(
    private store: Store,
    private router: Router,
    private activatedRoute: ActivatedRoute,
    private renderImageService: RenderImageService,
    private basketService: BasketService
  ) {
    this.configuration$.subscribe((c) => {
      if (!c.loading && c.state.sections.length === 0) {
        router.navigate(['..'], { relativeTo: activatedRoute });
      }
    });

    this.store.select(selectCurrentPerspective).pipe(untilDestroyed(this)).subscribe(async (result: string) => {
      this.renderImage = await this.renderImageService.drawImageForPerspective(result);
    });
	}

  private get configurationId(): string {
    return this.activatedRoute.snapshot.params['configurationId'];
  }

  private get configurationType(): string {
    return this.activatedRoute.snapshot.params['configurationType'];
  }

  public get showPrices(): boolean {
    return environment.showPrices;
  }

	public addBasket(): void {
    // @todo make product image size configurable
    if (this.renderImage) {
      this.renderImageService.resize(this.renderImage, 800).then((image: any) => {
        this.store.dispatch(
          addToBasket({
            payload: {
              type: 'ADD_TO_BASKET',
              productImage: image.src,
              configurationId: this.configurationId,
              configurationType: this.configurationType,
            },
          })
        );
      });
    } else {
      this.store.dispatch(
        addToBasket({
          payload: {
            type: 'ADD_TO_BASKET',
            productImage: null,
            configurationId: this.configurationId,
            configurationType: this.configurationType,
          },
        })
      );
    }

		this.basketService.sideBar?.toggle();
	}
}
