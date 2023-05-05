import { Component } from '@angular/core';
import { ActivatedRoute, Router } from '@angular/router';
import { BasketService } from '@apto-base-frontend/services/basket.service';
import { selectContentSnippet } from '@apto-base-frontend/store/content-snippets/content-snippets.selectors';
import { addToBasket } from '@apto-catalog-frontend/store/configuration/configuration.actions';
import {
	selectConfiguration,
	selectRenderImage,
	selectSumPrice,
} from '@apto-catalog-frontend/store/configuration/configuration.selectors';
import { selectProduct } from '@apto-catalog-frontend/store/product/product.selectors';
import { Store } from '@ngrx/store';
import { FormControl, FormGroup } from '@angular/forms';

@Component({
	selector: 'apto-summary',
	templateUrl: './summary.component.html',
	styleUrls: ['./summary.component.scss'],
})
export class SummaryComponent {
	public readonly contentSnippet$ = this.store.select(selectContentSnippet('aptoSummary'));
	public product$ = this.store.select(selectProduct);
	public configuration$ = this.store.select(selectConfiguration);
	public readonly renderImage$ = this.store.select(selectRenderImage);
	public readonly sumPrice$ = this.store.select(selectSumPrice);
  public showPrices: boolean = true;
  public quantityInputGroup = new FormGroup({
    quantityInput: new FormControl<number>(1),
  });
  public constructor(private store: Store, private basketService: BasketService, private router: Router, activatedRoute: ActivatedRoute) {
		this.configuration$.subscribe((c) => {
			if (!c.loading && c.state.sections.length === 0) {
				router.navigate(['..'], { relativeTo: activatedRoute });
			}
		});
	}

	public addBasket(): void {
		this.store.dispatch(
			addToBasket({
				payload: {
					type: 'ADD_TO_BASKET'
				},
			})
		);
		this.basketService.sideBar?.toggle();
	}
}
