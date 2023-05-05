import { Component, Input, OnInit } from '@angular/core';
import { Store } from '@ngrx/store';
import { selectContentSnippet } from '@apto-base-frontend/store/content-snippets/content-snippets.selectors';
import { selectProduct } from '@apto-catalog-frontend/store/product/product.selectors';
import {
  selectBasicPrice,
  selectBasicPseudoPrice,
  selectConfiguration,
  selectProgressState,
  selectSectionPrice, selectSumPrice, selectSumPseudoPrice
} from '@apto-catalog-frontend/store/configuration/configuration.selectors';
import { Section } from '@apto-catalog-frontend/store/product/product.model';
import { setStep } from '@apto-catalog-frontend/store/configuration/configuration.actions';
import {ActivatedRoute, Router} from '@angular/router';
import { Observable } from 'rxjs';

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
  @Input() public showPrices: boolean = true;
  constructor(private store: Store, private router: Router, private activatedRoute: ActivatedRoute) { }

  ngOnInit(): void {
  }

  public getSectionPrice(section: Section): Observable<string | null | undefined> {
    return this.store.select(selectSectionPrice(section));
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
      this.router.navigate(['..'], { relativeTo: this.activatedRoute });
    }
  }
}
