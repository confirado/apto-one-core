import { Component, OnInit } from '@angular/core';
import { ContentSnippet } from '@apto-base-frontend/store/content-snippets/content-snippet.model';
import { selectContentSnippet } from '@apto-base-frontend/store/content-snippets/content-snippets.selectors';
import { loadProductList } from '@apto-catalog-frontend/store/product/product.actions';
import { Category, Product } from '@apto-catalog-frontend/store/product/product.model';
import { selectProductList, selectProductListLoading } from '@apto-catalog-frontend/store/product/product.selectors';
import { Store } from '@ngrx/store';
import { Observable } from 'rxjs';
import { FormArray, FormBuilder, FormControl, FormGroup } from '@angular/forms';
import { LoadingIndicatorTypes } from '@apto-base-core/components/common/loading-indicator/loading-indicator.component';
import { Router } from '@angular/router';
import { environment } from '@apto-frontend/src/environments/environment';
import { MessageBusService } from '@apto-base-core/services/message-bus.service';

@Component({
    selector: 'apto-product-list',
    templateUrl: './product-list.component.html',
    styleUrls: ['./product-list.component.scss']
})
export class ProductListComponent implements OnInit {

    protected readonly productList$: Observable<Product[]>;
    protected readonly contentSnippets$: Observable<ContentSnippet | null>;
    protected readonly selectProductListLoading$ = this.store.select(selectProductListLoading);
    protected loadingIndicatorTypes = LoadingIndicatorTypes;

    public productList: Product[] = [];
    public shownProductList: Product[] = [];
    public categories: Category[] = [];
    public categoryForm: FormGroup;

    public constructor(
        private readonly store: Store,
        private readonly router: Router,
        private readonly messageBus: MessageBusService,
        private readonly fb: FormBuilder
    ) {
        this.productList$ = this.store.select(selectProductList);
        this.contentSnippets$ = this.store.select(selectContentSnippet('aptoProductList'));
        this.store.dispatch(loadProductList({ payload: { searchString: this.formElement.value || '' } }));
    }

    protected formElement = new FormControl();

    public ngOnInit(): void {
        this.categoryForm = this.fb.group({
            categories: this.fb.array<boolean>([])
        });

        this.categoryForm.valueChanges.subscribe((value) => {
            const categoriesSelectedValues = value.categories;
            const selectedCategories = this.getSelectedCategories(categoriesSelectedValues, this.categories);

            if (selectedCategories.length > 0) {
                this.shownProductList = this.productList.filter(product => this.categoriesAreFoundInProduct(product, selectedCategories));
            } else {
                this.shownProductList = this.productList;
            }
        });

        if (environment.aptoInline) {
            this.router.navigate(['product', environment.aptoInline.productId]);
        }

        this.formElement.valueChanges.subscribe((value) => {
            this.store.dispatch(loadProductList({ payload: { searchString: value } }));
        });

        this.productList$.subscribe(products => {
            this.productList = products;
            this.shownProductList = this.productList;
        });

        this.messageBus.query<Category[]>('FindCategoryTree', ['superadmin']).subscribe(response => {
            this.categories = response.result == null ? [] : response.result;
            this.categories.forEach(_ => {
                this.categoryFormArray.push(new FormControl(false));
            });
        });
    }

    public get categoryFormArray(): FormArray {
        return this.categoryForm.get('categories') as FormArray;
    }

    private getSelectedCategories(categoriesSelectedValues: boolean[], categories: Category[]): Category[] {
        const selectedCategories = [];
        for (let i = 0; i < categoriesSelectedValues.length; i++) {
            if (categoriesSelectedValues[i]) {
                selectedCategories.push(categories[i]);
            }
        }
        return selectedCategories;
    }

    private categoriesAreFoundInProduct(product: Product, selectedCategories: Category[]): boolean {
        if (!product.categories?.length) {
            return false;
        }

        let retval = false;

        product.categories.forEach(category => {
            retval = selectedCategories.some(cat => cat.id === category.id);
        });

        return retval;
    }


}
