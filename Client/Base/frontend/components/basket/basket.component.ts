import { Component } from '@angular/core';
import { BasketService } from '@apto-base-frontend/services/basket.service';
import { selectFullConnector } from '@apto-base-frontend/store/shop/shop.selectors';
import { Store } from '@ngrx/store';
import { selectContentSnippet } from '@apto-base-frontend/store/content-snippets/content-snippets.selectors';

@Component({
	selector: 'apto-basket',
	templateUrl: './basket.component.html',
	styleUrls: ['./basket.component.scss'],
})
export class BasketComponent {
  public readonly csAptoBasket$ = this.store.select(selectContentSnippet('aptoBasket'));

	public toggleSideBar(): void {
		this.basketService.sideBar?.toggle();
	}

	public totalPrice(
		products: {
			name: string;
			price: number;
			currency: string;
			quantity: number;
			image: string;
		}[]
	): string {
		let totalPrice = 0;
		let totalCurrency;
		for (const product of products) {
			const currentPrice = product.quantity * product.price;
			totalPrice += currentPrice;
			totalCurrency = product.currency;
		}
		if (!totalCurrency) {
			return 'no currency found!';
		}
		return `${totalPrice.toString()} ${totalCurrency}`;
	}

	public connector$ = this.store.select(selectFullConnector);

	public constructor(private store: Store, private basketService: BasketService) {}
}
