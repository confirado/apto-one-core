import { Component, OnDestroy, OnInit } from '@angular/core';
import { ActivatedRoute, Router } from '@angular/router';
import { Subscription } from 'rxjs';
import { Store } from '@ngrx/store';
import { Actions, ofType } from '@ngrx/effects';
import { FormControl, FormGroup } from '@angular/forms';
import { ViewportScroller } from '@angular/common';
import { selectContentSnippet } from '@apto-base-frontend/store/content-snippets/content-snippets.selectors';
import { addToBasket, addToBasketSuccess, onError } from '@apto-catalog-frontend/store/configuration/configuration.actions';
import { selectConfiguration, selectRenderImage, selectSumPrice } from '@apto-catalog-frontend/store/configuration/configuration.selectors';
import { selectProduct } from '@apto-catalog-frontend/store/product/product.selectors';
import { selectHumanReadableState } from '@apto-request-form-frontend/store/human-readable-state.selectors';
import { RenderImageService } from '@apto-catalog-frontend/services/render-image.service';
import { HumanReadableState } from '@apto-catalog-frontend/store/configuration/configuration.model';
import { environment } from '@apto-frontend/src/environments/environment';

@Component({
  selector: 'apto-summary',
  templateUrl: './summary.component.html',
  styleUrls: ['./summary.component.scss'],
})
export class SummaryComponent implements OnInit, OnDestroy {
  private subscriptions: Subscription[] = [];
  public readonly contentSnippet$ = this.store.select(selectContentSnippet('aptoSummary'));
  public readonly sidebarSummary$ = this.store.select(selectContentSnippet('sidebarSummary'));
  public readonly requestForm$ = this.store.select(selectContentSnippet('plugins.requestForm'));
  public product$ = this.store.select(selectProduct);
  public configuration$ = this.store.select(selectConfiguration);
  public readonly sumPrice$ = this.store.select(selectSumPrice);
  private humanReadableState: HumanReadableState;
  public quantityInputGroup = new FormGroup({
    quantityInput: new FormControl<number>(1),
  });
  public renderImage = null;
  public requestForm: FormGroup | null = null;
  public requestState: { sending: boolean, success: boolean, error: boolean } = {
    sending: false,
    success: false,
    error: false,
  };
  public readonly showRequestFormOnBottom = environment.hasOwnProperty('showRequestFormOnBottom') ? environment['showRequestFormOnBottom'] : false;

  public constructor(
    private store: Store,
    private router: Router,
    private activatedRoute: ActivatedRoute,
    private renderImageService: RenderImageService,
    private actions$: Actions,
    private scroller: ViewportScroller
  ) {
    this.configuration$.subscribe((c) => {
      if (!c.loading && c.state.sections.length === 0) {
        router.navigate(['..'], { relativeTo: activatedRoute });
      }
    });

    this.renderImageService.init();
    this.subscriptions.push(
      this.renderImageService.outputSrcSubject.subscribe((next) => {
        this.renderImage = next;
      })
    );

    this.store.select(selectHumanReadableState).subscribe((result) => {
      this.humanReadableState = result;
    });
  }

  public ngOnInit():void {
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

  public get showPrices(): boolean {
    return environment.showPrices;
  }

  public onSendRequestForm(): void {
    if (this.requestForm?.invalid) {
      return;
    }

    this.requestState.sending = true;
    this.scroller.scrollToAnchor('summary-request-form');
    // @todo make product image size configurable
    if (this.renderImage) {
      this.renderImageService.resize(this.renderImage, 800).then((image: any) => {
        this.store.dispatch(
          addToBasket({
            payload: {
              type: 'REQUEST_FORM',
              formData: this.requestForm?.value,
              humanReadableState: this.humanReadableState,
              productImage: image.src,
            },
          }),
        );
      });
    } else {
      this.store.dispatch(
        addToBasket({
          payload: {
            type: 'REQUEST_FORM',
            formData: this.requestForm?.value,
            humanReadableState: this.humanReadableState,
            productImage: null,
          },
        }),
      );
    }
  }

  public onRequestFormChanged(requestForm: FormGroup): void {
    this.requestForm = requestForm;
  }

  public ngOnDestroy() {
    this.subscriptions.forEach((subscription: Subscription) => {
      subscription.unsubscribe();
    })
  }
}
