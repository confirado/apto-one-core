import { Component, OnInit, SimpleChanges } from '@angular/core';
import { TemplateSlot } from '@apto-base-core/template-slot/template-slot.decorator';
import { TemplateSlotInterface } from '@apto-base-core/template-slot/template-slot.interface';
import {
  selectConfigurationLoading,
  selectHideOnePage,
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
	public readonly product$ = this.store.select(selectProduct);
	public readonly hideOnePage$ = this.store.select(selectHideOnePage);
	public readonly renderImage$ = this.store.select(selectRenderImage);
	public readonly perspectives$ = this.store.select(selectPerspectives);
  public readonly configurationLoading$ = this.store.select(selectConfigurationLoading);

	public constructor(private store: Store) {}

	public ngOnInit(): void {}

	onPropsChanged(changes: SimpleChanges) {}
}
