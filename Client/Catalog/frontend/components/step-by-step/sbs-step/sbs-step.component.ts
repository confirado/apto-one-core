import { Component, Input, OnDestroy, OnInit } from '@angular/core';
import { ActivatedRoute, Router } from '@angular/router';
import { Observable, Subject, takeUntil } from 'rxjs';
import { Store } from '@ngrx/store';

import { environment } from '@apto-frontend/src/environments/environment';
import { DialogSizesEnum } from '@apto-frontend/src/configs-static/dialog-sizes-enum';
import { translate, TranslatedValue } from '@apto-base-core/store/translated-value/translated-value.model';
import { selectContentSnippet } from '@apto-base-frontend/store/content-snippets/content-snippets.selectors';
import { selectLocale } from '@apto-base-frontend/store/language/language.selectors';
import { ContentSnippet } from '@apto-base-frontend/store/content-snippets/content-snippet.model';
import { setSectionTouched, setStep } from '@apto-catalog-frontend-configuration-actions';
import { ProgressElement, ProgressState, ProgressStatuses, ProgressStep, SectionTypes, TempStateItem } from '@apto-catalog-frontend-configuration-model';
import { selectTempState, selectElementValues, selectSectionIsValid } from '@apto-catalog-frontend-configuration-selectors';
import { Element, Product } from '@apto-catalog-frontend/store/product/product.model';
import { DialogService } from '@apto-catalog-frontend/components/common/dialogs/dialog-service';

@Component({
	selector: 'apto-sbs-step',
	templateUrl: './sbs-step.component.html',
	styleUrls: ['./sbs-step.component.scss'],
})
export class SbsStepComponent implements OnInit, OnDestroy {
	@Input()
	public section: ProgressStep | undefined;

	@Input()
	public index: number | undefined;

	@Input()
	public status: ProgressStatuses | undefined;

	@Input()
	public description: string | undefined;

	@Input()
	public last: boolean | undefined;

	@Input()
	public product: Product | null | undefined;

	@Input()
	public elements: ProgressElement[] | undefined | null;

	@Input()
	public active: boolean | undefined;

	@Input()
	public state: ProgressState | undefined;

  public readonly popUp$ = this.store.select(selectContentSnippet('confirmSelectSectionDialog'));
  public locale: string;
  public panelOpenState: boolean = false;
  public isActive: boolean = false;
  public sectionIsValid: boolean = false;
  protected tempState: TempStateItem[];
  protected readonly SectionTypes = SectionTypes;
  private destroy$ = new Subject<void>();

  private csPopUp: {
    title: string,
    message: string,
    button: {
      cancel: string,
      accept: string
    }
  } = null;


	public constructor(private store: Store, private activatedRoute: ActivatedRoute, private dialogService: DialogService, private router: Router) {
    this.locale = environment.defaultLocale;
  }

  public ngOnInit(): void {
    this.store.select(selectLocale).subscribe((locale: string) => {
      this.onLocalChange(locale);
    });

    this.popUp$.pipe(
      takeUntil(this.destroy$)
    ).subscribe((next: ContentSnippet) => {
      this.onCsPopUpChange(next);
    });

    this.store.select(selectSectionIsValid(this.section.section.id, this.section.section.repetition)).pipe(
      takeUntil(this.destroy$)
    ).subscribe(next => this.sectionIsValid = next);

    this.store.select(selectTempState).pipe(
      takeUntil(this.destroy$)
    ).subscribe((next) => {
      this.tempState = next;
    });
  }

  protected isSectionTouched(sectionId: string, repetition: number) {
    const tempItem = this.tempState.find(item => item.sectionId === sectionId && item.repetition === repetition);
    return tempItem?.touched ?? false;
  }

  public setStep(section: ProgressStep | undefined, seoUrl: string, isStepByStep: boolean): void {
    if (this.product.keepSectionOrder) {
      if (section && !this.state?.afterSteps.includes(section)) {
        if (isStepByStep === false) {
          return;
        }

        if (!this.csPopUp.title || !this.csPopUp.message) {
          this.updateStore(section);
          return;
        }

        this.openPopUp()
          .pipe(takeUntil(this.destroy$))
          .subscribe((next: boolean) => {
            if (next === true) {
              this.updateStore(section);
            }
          });
      }
    } else { // we want to move between configuration section without restrictions
      if (section && isStepByStep) {
        this.updateStore(section);
      }
    }
  }

  private updateStore(section: ProgressStep | undefined): void {
    this.store.dispatch(
      setStep({
        payload: {
          id: section.section.id, repetition: section.section.repetition,
        },
      })
    );

    this.store.dispatch(
      setSectionTouched({
        payload: {
          sectionId: section.section.id,
          repetition: section.section.repetition,
          touched: true,
        },
      })
    )
  }

  /**
   * If we don't want to keep section order then step by step is functioning like one page, where you can freely switch between sections
   */
  protected isSectionSelectable(): boolean {
    if (this.product.keepSectionOrder) {
      return this.status === ProgressStatuses.COMPLETED || this.status === ProgressStatuses.CURRENT;
    }
    return true;
  }

  protected get sectionIndex(): string {
    return this.section.section.repeatableType === SectionTypes.WIEDERHOLBAR ? `${this.section.section.repetition + 1}` : '';
  }

  public getElementValues(element: Element, section: ProgressStep): Observable<TranslatedValue[] | null | undefined> {
    const elementWithRepetition = { ...element, ...{ sectionRepetition: section.section.repetition } };

    return this.store.select(selectElementValues(elementWithRepetition));
  }

	public togglePanel(): void {
		this.panelOpenState = !this.panelOpenState;
	}

	public isActiveSection(): void {
		this.isActive = true;
	}

  private openPopUp() {
    return this.dialogService
      .openWarningDialog(
        DialogSizesEnum.md,
        this.csPopUp.title,
        this.csPopUp.message,
        this.csPopUp.button.cancel,
        this.csPopUp.button.accept
      )
      .afterClosed();
  }

  private onLocalChange(locale: string) {
    if (locale === null) {
      this.locale = environment.defaultLocale;
    } else {
      this.locale = locale;
    }
  }

  private onCsPopUpChange(next: ContentSnippet) {
    this.csPopUp = {
      title: '',
      message: '',
      button: {
        cancel: '',
        accept: ''
      }
    }

    if (null === next) {
      return;
    }

    next.children.forEach((value: ContentSnippet) => {
      if (value.name === 'title') {
        this.csPopUp.title = translate(value.content, this.locale);
      }
      if (value.name === 'message') {
        this.csPopUp.message = translate(value.content, this.locale);
      }
      if (value.name === 'buttonCancel') {
        this.csPopUp.button.cancel = translate(value.content, this.locale);
      }
      if (value.name === 'buttonAccept') {
        this.csPopUp.button.accept = translate(value.content, this.locale);
      }
    })
  }

  public ngOnDestroy(): void {
    this.destroy$.next();
    this.destroy$.complete();
  }
}
