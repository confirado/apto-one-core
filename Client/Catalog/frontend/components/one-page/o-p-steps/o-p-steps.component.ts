import { Component } from '@angular/core';
import { ProgressStep } from '@apto-catalog-frontend/store/configuration/configuration.model';
import { selectProgressState } from '@apto-catalog-frontend/store/configuration/configuration.selectors';
import { selectProduct } from '@apto-catalog-frontend/store/product/product.selectors';
import { Store } from '@ngrx/store';

@Component({
	selector: 'apto-o-p-steps',
	templateUrl: './o-p-steps.component.html',
	styleUrls: ['./o-p-steps.component.scss'],
})
export class OPStepsComponent {
	public product$ = this.store.select(selectProduct);

	public readonly steps$ = this.store.select(selectProgressState);

	public constructor(private store: Store) {}

	public stepTrackBy(index: number, section: ProgressStep): string {
		return section.section.id;
	}
}
