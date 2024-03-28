import { Component, OnInit } from '@angular/core';
import { ContentSnippet } from '@apto-base-frontend/store/content-snippets/content-snippet.model';
import { selectContentSnippet } from '@apto-base-frontend/store/content-snippets/content-snippets.selectors';
import { loadProductList } from '@apto-catalog-frontend/store/product/product.actions';
import { Product } from '@apto-catalog-frontend/store/product/product.model';
import { selectProductList, selectProductListLoading } from '@apto-catalog-frontend/store/product/product.selectors';
import { Store } from '@ngrx/store';
import { Observable } from 'rxjs';
import { FormControl } from '@angular/forms';
import { LoadingIndicatorTypes } from '@apto-base-core/components/common/loading-indicator/loading-indicator.component';
import { Router } from '@angular/router';
import { environment } from '@apto-frontend/src/environments/environment';

@Component({
  selector: 'apto-product-list',
  templateUrl: './product-list.component.html',
  styleUrls: ['./product-list.component.scss'],
})
export class ProductListComponent implements OnInit {

  protected readonly productList$: Observable<Product[]>;
  protected readonly contentSnippets$: Observable<ContentSnippet | null>;
  protected readonly selectProductListLoading$ = this.store.select(selectProductListLoading);

  protected loadingIndicatorTypes = LoadingIndicatorTypes;

  public constructor(
    private store: Store,
    private router: Router
  ) {
    this.productList$ = this.store.select(selectProductList);
    this.contentSnippets$ = this.store.select(selectContentSnippet('aptoProductList'));
    this.store.dispatch(loadProductList({ payload: { searchString: this.formElement.value || '' } }));
  }

  protected formElement = new FormControl();

  public ngOnInit(): void {
    if (environment.aptoInline) {
      this.router.navigate(['product', environment.aptoInline.productId]);
    }

    this.formElement.valueChanges.subscribe((value) => {
      this.store.dispatch(loadProductList({ payload: { searchString: value } }));
    });
  }
}
