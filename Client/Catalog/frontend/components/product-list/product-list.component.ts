import { Component } from '@angular/core';
import { ContentSnippet } from '@apto-base-frontend/store/content-snippets/content-snippet.model';
import { selectContentSnippet } from '@apto-base-frontend/store/content-snippets/content-snippets.selectors';
import { loadProductList } from '@apto-catalog-frontend/store/product/product.actions';
import { Product } from '@apto-catalog-frontend/store/product/product.model';
import { selectProductList } from '@apto-catalog-frontend/store/product/product.selectors';
import { Store } from '@ngrx/store';
import { Observable } from 'rxjs';

@Component({
	selector: 'apto-product-list',
	templateUrl: './product-list.component.html',
	styleUrls: ['./product-list.component.scss'],
})
export class ProductListComponent {
	public readonly productList$: Observable<Product[]>;
	public readonly contentSnippets$: Observable<ContentSnippet | null>;

	public constructor(private store: Store) {
		this.productList$ = this.store.select(selectProductList);
		this.contentSnippets$ = this.store.select(selectContentSnippet('aptoProductList'));
		this.store.dispatch(loadProductList());
	}
}
