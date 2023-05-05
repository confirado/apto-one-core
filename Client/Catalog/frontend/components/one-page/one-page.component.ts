import { Component, OnInit, SimpleChanges } from '@angular/core';
import { TemplateSlot } from '@apto-base-core/template-slot/template-slot.decorator';
import { TemplateSlotInterface } from '@apto-base-core/template-slot/template-slot.interface';
import { ConfigurationState } from '@apto-catalog-frontend/store/configuration/configuration.reducer';
import {
	selectConfiguration,
	selectPerspectives,
	selectRenderImage,
} from '@apto-catalog-frontend/store/configuration/configuration.selectors';
import { Product } from '@apto-catalog-frontend/store/product/product.model';
import { selectProduct } from '@apto-catalog-frontend/store/product/product.selectors';
import { Store } from '@ngrx/store';
import { Observable } from 'rxjs';

@Component({
	selector: 'apto-one-page',
	templateUrl: './one-page.component.html',
	styleUrls: ['./one-page.component.scss'],
})
@TemplateSlot({
	slot: 'frontend-content',
})
export class OnePageComponent implements OnInit, TemplateSlotInterface {
	public readonly product$: Observable<Product | null>;
	public readonly configuration$: Observable<ConfigurationState>;
	public readonly renderImage$ = this.store.select(selectRenderImage);
	public readonly perspectives$ = this.store.select(selectPerspectives);

	public constructor(private store: Store) {
		this.product$ = store.select(selectProduct);
		this.configuration$ = store.select(selectConfiguration);
	}

	public ngOnInit(): void {}

	onPropsChanged(changes: SimpleChanges) {}
}
