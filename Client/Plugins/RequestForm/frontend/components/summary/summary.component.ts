import {Component, OnInit} from '@angular/core';
import { ActivatedRoute, Router } from '@angular/router';
import { Store } from '@ngrx/store';
import { Actions, ofType } from "@ngrx/effects";
import { FormControl, FormGroup } from '@angular/forms';
import {ViewportScroller} from "@angular/common";
import { selectContentSnippet } from '@apto-base-frontend/store/content-snippets/content-snippets.selectors';
import {
  addToBasket,
  addToBasketSuccess,
  onError
} from '@apto-catalog-frontend/store/configuration/configuration.actions';
import {
  selectConfiguration,
  selectRenderImage,
  selectSumPrice,
} from '@apto-catalog-frontend/store/configuration/configuration.selectors';
import { selectProduct } from '@apto-catalog-frontend/store/product/product.selectors';
import { selectHumanReadableState } from '@apto-request-form-frontend/store/human-readable-state.selectors';

@Component({
  selector: 'apto-summary',
  templateUrl: './summary.component.html',
  styleUrls: ['./summary.component.scss'],
})
export class SummaryComponent implements OnInit{
  public readonly contentSnippet$ = this.store.select(selectContentSnippet('aptoSummary'));
  public readonly sidebarSummary$ = this.store.select(selectContentSnippet('sidebarSummary'));
  public product$ = this.store.select(selectProduct);
  public configuration$ = this.store.select(selectConfiguration);
  public readonly renderImage$ = this.store.select(selectRenderImage);
  public readonly sumPrice$ = this.store.select(selectSumPrice);
  private humanReadableState: any;
  public showPrices: boolean = true;
  public quantityInputGroup = new FormGroup({
    quantityInput: new FormControl<number>(1),
  });
  public requestForm: FormGroup | null = null;
  public requestState: { sending: boolean, success: boolean, error: boolean } = { sending: false, success: false, error: false };
  public constructor(private store: Store, private router: Router, activatedRoute: ActivatedRoute, private actions$: Actions, private scroller: ViewportScroller) {
    this.configuration$.subscribe((c) => {
      if (!c.loading && c.state.sections.length === 0) {
        router.navigate(['..'], { relativeTo: activatedRoute });
      }
    });

    this.store.select(selectHumanReadableState).subscribe((result) => {
      this.humanReadableState = result;
    })
  }

  ngOnInit() {
    this.actions$.pipe(ofType(onError)).subscribe((result) => {
      if (result.message.messageName === 'AddBasketConfiguration') {
        this.requestState.sending = false;
        this.requestState.error = true;
      }
    });
    this.actions$.pipe(ofType(addToBasketSuccess)).subscribe((result) => {
      this.requestState.sending = false;
      this.requestState.success = true;
    });
  }

  public onSendRequestForm(): void {
    if (this.requestForm?.invalid) {
      return;
    }

    this.requestState.sending = true;
    this.scroller.scrollToAnchor('summary-request-form');
    this.store.dispatch(
      addToBasket({
        payload: {
          type: 'REQUEST_FORM',
          formData: this.requestForm?.value,
          humanReadableState: this.humanReadableState
        },
      })
    );
  }

  public onRequestFormChanged(requestForm: FormGroup) {
    this.requestForm = requestForm;
  }
}
