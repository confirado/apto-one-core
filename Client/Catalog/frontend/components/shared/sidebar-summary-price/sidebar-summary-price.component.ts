import { Component, Input } from '@angular/core';
import { selectContentSnippet } from '@apto-base-frontend/store/content-snippets/content-snippets.selectors';
import { Product } from '@apto-catalog-frontend/store/product/product.model';
import {select, Store} from '@ngrx/store';
import { FormControl, FormGroup } from '@angular/forms';

@Component({
	selector: 'apto-sidebar-summary-price',
	templateUrl: './sidebar-summary-price.component.html',
	styleUrls: ['./sidebar-summary-price.component.scss'],
})
export class SidebarSummaryPriceComponent {
	@Input()
	public sumPrice: string | null | undefined;
	@Input()
	public progress: number | null | undefined;
	@Input()
	public product: Product | null | undefined;
	@Input()
	public sumPseudoPrice: string | null | undefined;
	@Input()
	public discount: number | undefined;

  public quantityInputGroup = new FormGroup({
    quantityInput: new FormControl<number>(1),
  });

	public readonly contentSnippets$ = this.store.select(selectContentSnippet('aptoSummary'));
  public readonly sidebarSummary$ = this.store.select(selectContentSnippet('sidebarSummary'));
	public constructor(private store: Store) {}
}
