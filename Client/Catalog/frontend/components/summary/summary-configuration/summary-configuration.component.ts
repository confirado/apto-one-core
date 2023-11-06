import { Component, Input, OnDestroy, OnInit } from '@angular/core';
import { ActivatedRoute, Router } from '@angular/router';
import { Observable, Subject, Subscription, takeUntil } from 'rxjs';
import { Store } from '@ngrx/store';

import { DialogSizesEnum } from '@apto-frontend/src/configs-static/dialog-sizes-enum';
import { environment } from '@apto-frontend/src/environments/environment';
import { translate } from '@apto-base-core/store/translated-value/translated-value.model';
import { selectContentSnippet } from '@apto-base-frontend/store/content-snippets/content-snippets.selectors';
import { selectLocale } from '@apto-base-frontend/store/language/language.selectors';
import { ContentSnippet } from '@apto-base-frontend/store/content-snippets/content-snippet.model';
import { DialogService } from '@apto-catalog-frontend/components/common/dialogs/dialog-service';
import { selectProduct } from '@apto-catalog-frontend/store/product/product.selectors';
import { Section } from '@apto-catalog-frontend/store/product/product.model';
import { setStep } from '@apto-catalog-frontend/store/configuration/configuration.actions';
import {
  selectBasicPrice,
  selectBasicPseudoPrice,
  selectConfiguration,
  selectProgressState,
  selectSectionPrice, selectSectionPseudoPrice,
  selectSumPrice,
  selectSumPseudoPrice,
} from '@apto-catalog-frontend/store/configuration/configuration.selectors';

@Component({
  selector: 'apto-summary-configuration',
  templateUrl: './summary-configuration.component.html',
  styleUrls: ['./summary-configuration.component.scss']
})
export class SummaryConfigurationComponent implements OnInit, OnDestroy {
  public readonly contentSnippet$ = this.store.select(selectContentSnippet('aptoSummary'));
  public product$ = this.store.select(selectProduct);
  public configuration$ = this.store.select(selectConfiguration);
  public readonly basicPseudoPrice$ = this.store.select(selectBasicPseudoPrice);
  public readonly sumPseudoPrice$ = this.store.select(selectSumPseudoPrice);
  public readonly sumPrice$ = this.store.select(selectSumPrice);
  public readonly steps$ = this.store.select(selectProgressState);
  public readonly basicPrice$ = this.store.select(selectBasicPrice);
  public readonly popUp$ = this.store.select(selectContentSnippet('confirmSelectSectionDialog'));
  private destroy$ = new Subject<void>();
  public locale: string;

  private popupSubscription: Subscription = null;
  private csPopUp: {
    title: string,
    message: string,
    button: {
      cancel: string,
      accept: string
    }
  } = null;

  @Input() public showPrices: boolean = true;
  constructor(private store: Store, private router: Router, private activatedRoute: ActivatedRoute, private dialogService: DialogService) {
    this.locale = environment.defaultLocale;
  }

  public ngOnInit(): void {
    // subscribe for locale store value
    this.store.select(selectLocale).subscribe((locale: string) => {
      this.onLocalChange(locale);
    });

    this.popUp$.pipe(
      takeUntil(this.destroy$)
    ).subscribe((next: ContentSnippet) => {
      this.onCsPopUpChange(next);
    });
  }

  public getSectionPrice(section: Section): Observable<string | null | undefined> {
    return this.store.select(selectSectionPrice(section));
  }

  public getSectionPseudoPrice(section: Section): Observable<string | null | undefined> {
    return this.store.select(selectSectionPseudoPrice(section));
  }

  public setStep(section: Section | undefined, seoUrl: string, isStepByStep: boolean): void {
    if (section) {
      if (false === isStepByStep) {
        this.router.navigate(['..'], { relativeTo: this.activatedRoute });
        return;
      }

      if (!this.csPopUp.title || !this.csPopUp.message) {
        this.updateStore(section);
        this.router.navigate(['..'], { relativeTo: this.activatedRoute });
        return;
      }

      this.popupSubscription = this.openPopUp().subscribe((next) => {
        this.popupSubscription.unsubscribe();

        if (true !== next) {
          return;
        }

        this.updateStore(section);

        this.router.navigate(['..'], { relativeTo: this.activatedRoute });
      })
    }
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

  private updateStore(section: Section | undefined): void {
    this.store.dispatch(
      setStep({
        payload: {
          id: section.id, repetition: section.repetition,
        },
      })
    );
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
