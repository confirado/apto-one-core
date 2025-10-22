import { Component } from '@angular/core';
import { ActivatedRoute, Router } from '@angular/router';
import { BasketService } from '@apto-base-frontend/services/basket.service';
import { selectContentSnippet } from '@apto-base-frontend/store/content-snippets/content-snippets.selectors';
import { addToBasket } from '@apto-catalog-frontend-configuration-actions';
import {
  selectConfiguration,
  selectCurrentPerspective,
  selectPerspectives,
  selectSumPrice,
} from '@apto-catalog-frontend-configuration-selectors';
import { selectProduct } from '@apto-catalog-frontend/store/product/product.selectors';
import { Store } from '@ngrx/store';
import { FormControl, FormGroup } from '@angular/forms';
import { RenderImageService } from '@apto-catalog-frontend/services/render-image.service';
import { environment } from '@apto-frontend/src/environments/environment';
import { selectLocale } from '@apto-base-frontend/store/language/language.selectors';
import { UntilDestroy, untilDestroyed } from '@ngneat/until-destroy';

@UntilDestroy()
@Component({
	selector: 'apto-summary',
	templateUrl: './summary.component.html',
	styleUrls: ['./summary.component.scss'],
})
export class SummaryComponent {
	public readonly contentSnippet$ = this.store.select(selectContentSnippet('aptoSummary'));
  public readonly sidebarSummary$ = this.store.select(selectContentSnippet('aptoStepByStep.sidebarSummary'));
  protected readonly AptoOfferConfigurationDialog$ = this.store.select(selectContentSnippet('AptoOfferConfigurationDialog'));
  protected readonly locale$ = this.store.select(selectLocale);
  public readonly perspectives$ = this.store.select(selectPerspectives);
  private locale = 'de_DE';
  protected isOfferConfigurationEnabled = false;
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

    this.locale$.subscribe((next) => {
      this.locale = next;
    });

    this.AptoOfferConfigurationDialog$.subscribe((data) => {
      data.children.forEach((dialogItem) => {
        if (dialogItem.name === 'enabled') {
          this.isOfferConfigurationEnabled = dialogItem.content[this.locale] === 'true';
        }
      });
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

	public addToBasket(): void {
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
              additionalData: {
              },
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
            additionalData: {
            },
          },
        })
      );
    }

		this.basketService.sideBar?.toggle();
	}
}
