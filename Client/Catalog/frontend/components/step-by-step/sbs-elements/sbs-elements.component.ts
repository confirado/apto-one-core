import { Component, OnInit } from '@angular/core';
import { ActivatedRoute } from "@angular/router";
import { distinctUntilChanged, take } from 'rxjs';
import { UntilDestroy, untilDestroyed } from "@ngneat/until-destroy";
import { Store } from '@ngrx/store';
import { Actions, ofType } from "@ngrx/effects";
import { environment } from "@apto-frontend/src/environments/environment";
import { selectContentSnippet } from "@apto-base-frontend/store/content-snippets/content-snippets.selectors";
import { RenderImageService } from "@apto-catalog-frontend/services/render-image.service";
import { ElementZoomFunctionEnum } from '@apto-catalog-frontend/store/product/product.model';
import {
  ElementState,
  ProgressElement,
  ProgressState, SectionTypes,
} from '@apto-catalog-frontend/store/configuration/configuration.model';
import {
  selectProduct
} from '@apto-catalog-frontend/store/product/product.selectors';
import {
  configurationIsValid,
  selectCurrentPerspective,
  selectCurrentProductElements,
  selectCurrentStateElements,
  selectProgressState,
} from '@apto-catalog-frontend/store/configuration/configuration.selectors';
import {
  addToBasket,
  addToBasketSuccess,
  setSectionTouched,
  setStep
} from '@apto-catalog-frontend/store/configuration/configuration.actions';

@UntilDestroy()
@Component({
	selector: 'apto-sbs-elements',
	templateUrl: './sbs-elements.component.html',
	styleUrls: ['./sbs-elements.component.scss'],
})
export class SbsElementsComponent implements OnInit{
	public readonly progressState$ = this.store.select(selectProgressState);
	public readonly product$ = this.store.select(selectProduct);
  public readonly currentProductElements$ = this.store.select(selectCurrentProductElements);
  public readonly currentStateElements$ = this.store.select(selectCurrentStateElements);
  public readonly configurationIsValid$ = this.store.select(configurationIsValid);
  public readonly contentSnippets$ = this.store.select(selectContentSnippet('aptoStepByStep.elementsContainer'));
  private currentStateElements: ElementState[] = null;
	private progressState: ProgressState = null;
  protected stepPositions: number[] = [];

  public readonly isInline = !!environment.aptoInline;
  public renderImage = null;
  public sw6CartButtonDisabled: boolean = false;

	public constructor(
    private store: Store,
    private renderImageService: RenderImageService,
    private activatedRoute: ActivatedRoute,
    private readonly actions$: Actions
  ) {
    this.store.select(selectCurrentPerspective).pipe(untilDestroyed(this)).subscribe(async (result: string) => {
      this.renderImage = await this.renderImageService.drawImageForPerspective(result);
    });
  }

  public ngOnInit(): void {
    this.currentStateElements$.subscribe((next: ElementState[]) => {
      this.currentStateElements = next;
    });

		this.progressState$.pipe(
      distinctUntilChanged(),
    ).subscribe((next: ProgressState) => {
			this.progressState = next;

      this.stepPositions = [];
      for (const step of this.progressState.steps) {
        this.stepPositions.push(step.section.position);
      }
		});

    /*  With this we set as touched first section as on page load we are on that section. this is needed otherwise when go to next section, first
        section remains as untouched, and we can not then display the correct icon for it.   */
    this.store.dispatch(
      setSectionTouched({
        payload: {
          sectionId: this.progressState.steps[0].section.id,
          repetition: this.progressState.steps[0].section.repetition,
          touched: true,
        },
      })
    );
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
    const step = state.beforeSteps.length ? state.beforeSteps[state.beforeSteps.length - 1] : state.currentStep;

		this.store.dispatch(setStep({
      payload: {
        id: step.section.id, repetition: step.section.repetition,
      },
    }));

    this.store.dispatch(
      setSectionTouched({
        payload: {
          sectionId: step.section.id,
          repetition: step.section.repetition,
          touched: true,
        },
      })
    )
	}

	public nextStep(state: ProgressState, nextStepScrollTarget: HTMLElement): void {
    if (nextStepScrollTarget) {
      nextStepScrollTarget.scrollIntoView({behavior: 'smooth'});
    }

    const step = state.afterSteps.length ? state.afterSteps[0] : state.currentStep;

    this.store.dispatch(setStep({
      payload: {
        id: step.section.id, repetition: step.section.repetition,
      },
    }));

    this.store.dispatch(
      setSectionTouched({
        payload: {
          sectionId: step.section.id,
          repetition: step.section.repetition,
          touched: true,
        },
      })
    )
	}

  public openShopware6Cart() {
    this.sw6CartButtonDisabled = true;
    this.actions$.pipe(
      ofType(addToBasketSuccess),
      untilDestroyed(this),
      take(1)
    ).subscribe((next) => {
      const offCanvasCartInstances: any = window.PluginManager.getPluginInstances('OffCanvasCart');
      for (let i = 0; i < offCanvasCartInstances.length; i++) {
        offCanvasCartInstances[i].openOffCanvas(window.router['frontend.cart.offcanvas'], false);
      }
      this.sw6CartButtonDisabled = false;
    });

    if (this.renderImage) {
      this.renderImageService.resize(this.renderImage, 800).then((image: any) => {
        this.store.dispatch(
          addToBasket({
            payload: {
              type: 'ADD_TO_BASKET',
              productImage: image.src,
              configurationId: this.configurationId,
              configurationType: this.configurationType,
            },
          })
        );
      });
    } else {
      this.store.dispatch(
        addToBasket({
          payload: {
            type: 'ADD_TO_BASKET',
            productImage: null,
            configurationId: this.configurationId,
            configurationType: this.configurationType,
          },
        })
      );
    }
  }

  protected get sectionIndex(): string {
    return this.progressState.currentStep.section.repeatableType === SectionTypes.WIEDERHOLBAR ? `${this.progressState.currentStep.section.repetition + 1}` : '';
  }

  private get configurationId(): string {
    return this.activatedRoute.snapshot.params['configurationId'];
  }

  private get configurationType(): string {
    return this.activatedRoute.snapshot.params['configurationType'];
  }

  // todo do we need this?
  getZoomFunction(isZoomable: boolean): any {
     if (true === isZoomable) {
       return ElementZoomFunctionEnum.IMAGE_PREVIEW;
     }
     return ElementZoomFunctionEnum.DEACTIVATED;
  }
}
