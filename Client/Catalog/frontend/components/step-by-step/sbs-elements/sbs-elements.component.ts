import { Component } from '@angular/core';
import { setNextStep, setPrevStep } from '@apto-catalog-frontend/store/configuration/configuration.actions';
import { ProgressState } from '@apto-catalog-frontend/store/configuration/configuration.model';
import {
	selectConfiguration,
	selectProgress,
	selectProgressState,
} from '@apto-catalog-frontend/store/configuration/configuration.selectors';
import { selectProduct } from '@apto-catalog-frontend/store/product/product.selectors';
import { Store } from '@ngrx/store';
import { distinctUntilChanged, filter, map } from 'rxjs';
import {ElementZoomFunctionEnum} from "@apto-catalog-frontend/store/product/product.model";

@Component({
	selector: 'apto-sbs-elements',
	templateUrl: './sbs-elements.component.html',
	styleUrls: ['./sbs-elements.component.scss'],
})
export class SbsElementsComponent {
	public readonly progressState$ = this.store.select(selectProgressState);
	public readonly progress$ = this.store.select(selectProgress);
	public readonly product$ = this.store.select(selectProduct);
	public readonly configuration$ = this.store.select(selectConfiguration);

	public constructor(private store: Store) {
		this.progressState$
			.pipe(
				filter((s) => s.currentStep?.section.id !== undefined),
				map((s) => s.currentStep?.section.id),
				distinctUntilChanged()
			);
	}

	public lastSection(state: ProgressState): boolean {
		return state.afterSteps.length === 0;
	}

	public prevStep(): void {
		this.store.dispatch(setPrevStep());
	}

	public nextStep(): void {
		this.store.dispatch(setNextStep());
	}

  getZoomFunction(isZoomable: boolean): any {
     if (true === isZoomable) {
       return ElementZoomFunctionEnum.IMAGE_PREVIEW;
     }
     return ElementZoomFunctionEnum.DEACTIVATED;
  }
}
