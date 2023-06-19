import {Component, Input, OnInit} from '@angular/core';
import {Store} from '@ngrx/store';
import {selectContentSnippet} from '@apto-base-frontend/store/content-snippets/content-snippets.selectors';
import {selectProduct} from '@apto-catalog-frontend/store/product/product.selectors';
import {
  selectBasicPrice,
  selectBasicPseudoPrice,
  selectConfiguration,
  selectProgressState,
  selectSectionPrice,
  selectSumPrice,
  selectSumPseudoPrice
} from '@apto-catalog-frontend/store/configuration/configuration.selectors';
import {Section} from '@apto-catalog-frontend/store/product/product.model';
import {setStep} from '@apto-catalog-frontend/store/configuration/configuration.actions';
import {ActivatedRoute, Router} from '@angular/router';
import {Observable} from 'rxjs';
import {DialogSizesEnum} from "@apto-frontend/src/configs-static/dialog-sizes-enum";
import {DialogService} from "@apto-catalog-frontend/components/common/dialogs/dialog-service";
import {translate} from "@apto-base-core/store/translated-value/translated-value.model";
import { environment } from '@apto-frontend/src/environments/environment';
import {selectLocale} from "@apto-base-frontend/store/language/language.selectors";

@Component({
  selector: 'apto-summary-configuration',
  templateUrl: './summary-configuration.component.html',
  styleUrls: ['./summary-configuration.component.scss']
})
export class SummaryConfigurationComponent implements OnInit {
  public readonly contentSnippet$ = this.store.select(selectContentSnippet('aptoSummary'));
  public product$ = this.store.select(selectProduct);
  public configuration$ = this.store.select(selectConfiguration);
  public readonly basicPseudoPrice$ = this.store.select(selectBasicPseudoPrice);
  public readonly sumPseudoPrice$ = this.store.select(selectSumPseudoPrice);
  public readonly sumPrice$ = this.store.select(selectSumPrice);
  public readonly steps$ = this.store.select(selectProgressState);
  public readonly basicPrice$ = this.store.select(selectBasicPrice);
  public readonly popUp$ = this.store.select(selectContentSnippet('aptoSummary.confirmSelectSectionDialog'))
  public locale: string;
  @Input() public showPrices: boolean = true;
  constructor(private store: Store, private router: Router, private activatedRoute: ActivatedRoute, private dialogService: DialogService) {
    this.locale = environment.defaultLocale;
  }

  ngOnInit(): void {
    // subscribe for locale store value
    this.store.select(selectLocale).subscribe((locale) => {
      if (locale === null) {
        this.locale = environment.defaultLocale;
      } else {
        this.locale = locale;
      }
    });
  }

  public getSectionPrice(section: Section): Observable<string | null | undefined> {
    return this.store.select(selectSectionPrice(section));
  }

  public openPopUp() {
    let dialogMessage = '';
    let dialogTitle = '';
    let dialogButtonCancel = '';
    let dialogButtonAccept = '';

    this.popUp$.subscribe((next) => {
      if (next === null) {
        this.router.navigate(['..'], { relativeTo: this.activatedRoute });
        return;
      }
      next.children.forEach((value) => {
        if (value.name === 'title') {
          dialogTitle = translate(value.content, this.locale);
        }
        if (value.name === 'message') {
          dialogMessage = translate(value.content, this.locale);
        }
        if (value.name === 'buttonCancel') {
          dialogButtonCancel = translate(value.content, this.locale);
        }
        if (value.name === 'buttonAccept') {
          dialogButtonAccept = translate(value.content, this.locale);
        }
      })
      this.dialogService.openWarningDialog(DialogSizesEnum.md, dialogTitle, dialogMessage, dialogButtonCancel, dialogButtonAccept).afterClosed().subscribe((next) => {
        if (next === true) {
          this.router.navigate(['..'], { relativeTo: this.activatedRoute });
        }
      })
    })
  }

  public setStep(section: Section | undefined, seoUrl: string, isStepByStep: boolean): void {
    if (section) {
      if (isStepByStep) {
        this.store.dispatch(
          setStep({
            payload: {
              id: section.id,
            },
          })
        );
      }
      this.openPopUp();
    }
  }
}
