import { Component, Input, OnDestroy, OnInit } from '@angular/core';
import { ActivatedRoute, Router } from '@angular/router';
import { Observable, of, Subject, Subscription, takeUntil } from 'rxjs';
import { Store } from '@ngrx/store';

import { environment } from '@apto-frontend/src/environments/environment';
import { DialogSizesEnum } from '@apto-frontend/src/configs-static/dialog-sizes-enum';
import { translate, TranslatedValue } from '@apto-base-core/store/translated-value/translated-value.model';
import { selectContentSnippet } from '@apto-base-frontend/store/content-snippets/content-snippets.selectors';
import { selectLocale } from '@apto-base-frontend/store/language/language.selectors';
import { ContentSnippet } from '@apto-base-frontend/store/content-snippets/content-snippet.model';
import { setStep } from '@apto-catalog-frontend/store/configuration/configuration.actions';
import { ProgressElement, ProgressState, ProgressStep } from '@apto-catalog-frontend/store/configuration/configuration.model';
import { selectElementValues } from '@apto-catalog-frontend/store/configuration/configuration.selectors';
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
	public status: string | undefined;

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
  private destroy$ = new Subject<void>();

  private popupSubscription: Subscription = null;
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
  }

	public setStep(section: ProgressStep | undefined, seoUrl: string, isStepByStep: boolean): void {
		// eslint-disable-next-line no-restricted-globals
		if (section && !this.state?.afterSteps.includes(section)) {
      if (false === isStepByStep) {
        return;
      }

      if (!this.csPopUp.title || !this.csPopUp.message) {
        this.updateStore(section);
        return;
      }

      this.popupSubscription = this.openPopUp().subscribe((next: boolean) => {
        this.popupSubscription.unsubscribe();

        if (true !== next) {
          return;
        }

        this.updateStore(section);
      });
		}
	}

  private updateStore(section: ProgressStep | undefined): void {
    this.store.dispatch(
      setStep({
        payload: {
          id: section.section.id,
        },
      })
    );
  }

  public getElementValues(element: Element): Observable<TranslatedValue[] | null | undefined> {
    return this.store.select(selectElementValues(element));
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
