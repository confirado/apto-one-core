import { Component, OnInit } from '@angular/core';
import { setNextStep, setPrevStep } from '@apto-catalog-frontend/store/configuration/configuration.actions';
import {
  ElementState,
  ProgressElement,
  ProgressState, SectionTypes,
} from '@apto-catalog-frontend/store/configuration/configuration.model';
import {
  configurationIsValid,
  selectConfiguration, selectCurrentProductElements, selectCurrentStateElements,
  selectProgress,
  selectProgressState,
} from '@apto-catalog-frontend/store/configuration/configuration.selectors';
import { selectProduct } from '@apto-catalog-frontend/store/product/product.selectors';
import { Store } from '@ngrx/store';
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
  protected stepPositions: number[] = [];
  protected readonly SectionTypes = SectionTypes;

	public constructor(private store: Store) {}

  public ngOnInit(): void {
    this.currentStateElements$.subscribe((next: ElementState[]) => {
      this.currentStateElements = next;
    });

		this.progressState$.subscribe((next: ProgressState) => {
			this.progressState = next;

      this.stepPositions = [];
      for (const step of this.progressState.steps) {
        this.stepPositions.push(step.section.position);
      }
		});
  }

  protected get currentPosition(): number {
    return this.progressState.currentStep.section.position;
  }

  protected get minStepPosition(): number {
    return Math.min(...this.stepPositions) || 0;
  }

  public isElementDisabled(elementId: string, sectionRepetition: number): boolean {
    const state = this.currentStateElements.filter(e => e.id === elementId);
    return state.length > 0 ? state[sectionRepetition].disabled : false;
  }

  public getProgressElement(elementId: string): ProgressElement | null {
    const element = this.progressState.currentStep.elements.filter(e => e.element.id === elementId);
    return element.length > 0 ? element[0] : null;
  }

	public lastSection(state: ProgressState): boolean {
		return state.afterSteps.length === 0;
	}

	public prevStep(state: ProgressState): void {
    const step = state.beforeSteps.length ? state.beforeSteps[0] : state.currentStep;

		this.store.dispatch(setPrevStep({
      payload: {
        id: step.section.id, repetition: step.section.repetition,
      },
    }));
	}

	public nextStep(state: ProgressState, nextStepScrollTarget: HTMLElement): void {
    if (nextStepScrollTarget) {
      nextStepScrollTarget.scrollIntoView({behavior: 'smooth'});
    }

    const step = state.afterSteps.length ? state.afterSteps[0] : state.currentStep;

    this.store.dispatch(setNextStep({
      payload: {
        id: step.section.id, repetition: step.section.repetition,
      },
    }));
	}

  protected get sectionIndex(): string {
    return this.progressState.currentStep.section.repeatableType === SectionTypes.WIEDERHOLBAR ? `${this.progressState.currentStep.section.repetition + 1}` : '';
  }

  // todo do we need this?
  getZoomFunction(isZoomable: boolean): any {
     if (true === isZoomable) {
       return ElementZoomFunctionEnum.IMAGE_PREVIEW;
     }
     return ElementZoomFunctionEnum.DEACTIVATED;
  }
}
