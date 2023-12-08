import { Component, OnInit } from '@angular/core';
import { setNextStep, setPrevStep } from '@apto-catalog-frontend/store/configuration/configuration.actions';
import {
	ElementState,
	ProgressElement,
	ProgressState,
} from '@apto-catalog-frontend/store/configuration/configuration.model';
import {
  configurationIsValid,
  selectConfiguration, selectCurrentProductElements, selectCurrentStateElements,
  selectProgress,
  selectProgressState,
} from '@apto-catalog-frontend/store/configuration/configuration.selectors';
import { selectProduct } from '@apto-catalog-frontend/store/product/product.selectors';
import { Store } from '@ngrx/store';
import { distinctUntilChanged, filter, map } from 'rxjs';
import { ElementZoomFunctionEnum } from '@apto-catalog-frontend/store/product/product.model';

@Component({
	selector: 'apto-sbs-elements',
	templateUrl: './sbs-elements.component.html',
	styleUrls: ['./sbs-elements.component.scss'],
})
export class SbsElementsComponent implements OnInit{
	public readonly progressState$ = this.store.select(selectProgressState);
	public readonly progress$ = this.store.select(selectProgress);
	public readonly product$ = this.store.select(selectProduct);
	public readonly configuration$ = this.store.select(selectConfiguration);
  public readonly currentProductElements$ = this.store.select(selectCurrentProductElements);
  public readonly currentStateElements$ = this.store.select(selectCurrentStateElements);
  public readonly configurationIsValid$ = this.store.select(configurationIsValid);
  private currentStateElements: ElementState[] = null;
	private progressState: ProgressState = null;

	public constructor(private store: Store) {
		this.progressState$
			.pipe(
				filter((s) => s.currentStep?.section.id !== undefined),
				map((s) => s.currentStep?.section.id),
				distinctUntilChanged()
			);
	}

  public ngOnInit() {
    this.currentStateElements$.subscribe((next: ElementState[]) => {
      this.currentStateElements = next;
    });
		this.progressState$.subscribe((next: ProgressState) => {
			this.progressState = next;
		});
  }

  public isElementDisabled(elementId: string) {
    const state = this.currentStateElements.filter(e => e.id === elementId);
    if (state.length > 0) {
      return state[0].disabled;
    }
    return false;
  }

  public getProgressElement(elementId: string): ProgressElement | null{
    const element = this.progressState.currentStep.elements.filter(e => e.element.id === elementId);
		if (element.length > 0) {
			return element[0];
		}
		return null;
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
