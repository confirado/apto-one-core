import { Component, OnInit } from '@angular/core';
import { addStep, removeStep, setNextStep, setPrevStep, updateConfigurationState } from '@apto-catalog-frontend/store/configuration/configuration.actions';
import {
  ElementState,
  ProgressElement,
  ProgressState, SectionTypes, StateItemTypes,
} from '@apto-catalog-frontend/store/configuration/configuration.model';
import {
  configurationIsValid,
  selectConfiguration, selectCurrentProductElements, selectCurrentStateElements,
  selectProgress,
  selectProgressState, selectRepetitions,
} from '@apto-catalog-frontend/store/configuration/configuration.selectors';
import { selectProduct } from '@apto-catalog-frontend/store/product/product.selectors';
import { Store } from '@ngrx/store';
import { ElementZoomFunctionEnum } from '@apto-catalog-frontend/store/product/product.model';
import { selectContentSnippet } from '@apto-base-frontend/store/content-snippets/content-snippets.selectors';
import { ConfigurationState } from '@apto-catalog-frontend/store/configuration/configuration.reducer';

@Component({
	selector: 'apto-sbs-elements',
	templateUrl: './sbs-elements.component.html',
	styleUrls: ['./sbs-elements.component.scss'],
})
export class SbsElementsComponent implements OnInit{
	public readonly progressState$ = this.store.select(selectProgressState);
	public readonly progress$ = this.store.select(selectProgress);
	public readonly product$ = this.store.select(selectProduct);
	public readonly repetitions$ = this.store.select(selectRepetitions);
	public readonly configuration$ = this.store.select(selectConfiguration);
  public readonly currentProductElements$ = this.store.select(selectCurrentProductElements);
  public readonly currentStateElements$ = this.store.select(selectCurrentStateElements);
  public readonly configurationIsValid$ = this.store.select(configurationIsValid);
  public readonly contentSnippets$ = this.store.select(selectContentSnippet('aptoStepByStep.elementsContainer'));

  private currentStateElements: ElementState[] = null;
	private progressState: ProgressState = null;
  protected stepPositions: number[] = [];
  protected readonly SectionTypes = SectionTypes;
  protected configurationState: ConfigurationState;
  protected stepAction = '';

	public constructor(private store: Store) {}

  public ngOnInit(): void {
    this.currentStateElements$.subscribe((next: ElementState[]) => {
      this.currentStateElements = next;
    });

    this.configuration$.subscribe((next: ConfigurationState) => {
       this.configurationState = next;
    });

    this.repetitions$.subscribe((repetitions: ConfigurationState) => {
      if (this.progressState) {
        if (this.stepAction === 'addStep') {
          this.store.dispatch(
            addStep({
              payload: {
                id: this.progressState.steps[this.progressState.steps.length - 1].section.id,
                repetition: this.progressState.steps[this.progressState.steps.length - 1].section.repetition + 1,
              },
            })
          );
        }
      }
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
        id: step.section.id,
        repetition: step.section.repetition,
      },
    }));
	}

	public nextStep(state: ProgressState): void {
    const step = state.afterSteps.length ? state.afterSteps[0] : state.currentStep;

    this.store.dispatch(setNextStep({
      payload: {
        id: step.section.id, repetition: step.section.repetition,
      },
    }));
	}

  protected get sectionCountInCurrentRepetition() {
    return this.configurationState.state.sections
      .filter(section => section.id === this.progressState.currentStep.section.id).length;
  }

	public removeStep(): void {
    let currentSections = this.configurationState.state.sections.filter(section => section.id === this.progressState.currentStep.section.id);

    // go back 2 positions but remove one position
    this.store.dispatch(
      removeStep({
        payload: {
          id: this.progressState.currentStep.section.id,
          repetition: currentSections.length - 2, // repetition is zero index
        },
      })
    );

    this.store.dispatch(
      updateConfigurationState({
        updates: {
          set: [
            {
              [StateItemTypes.REPETITIONS]: currentSections.length - 1 > 1 ? currentSections.length - 1 : 1,
              sectionId: this.progressState.currentStep.section.id,
            },
          ],
        },
      })
    );
  };

	public addStep(): void {
    let currentSections = this.configurationState.state.sections.filter(section => section.id === this.progressState.currentStep.section.id);

    this.stepAction = 'addStep';

    this.store.dispatch(
      updateConfigurationState({
        updates: {
          set: [
            {
              [StateItemTypes.REPETITIONS]: currentSections.length + 1,
              sectionId: this.progressState.currentStep.section.id,
            },
          ],
        },
      })
    );
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
