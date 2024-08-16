import { Component } from '@angular/core';
import { FormControl, FormGroup } from '@angular/forms';
import { Store } from '@ngrx/store';
import { HttpClient } from '@angular/common/http';
import { take } from 'rxjs';
import { UntilDestroy, untilDestroyed } from '@ngneat/until-destroy';
import { environment } from '@apto-frontend/src/environments/environment';
import { translate } from '@apto-base-core/store/translated-value/translated-value.model';
import { selectContentSnippet } from '@apto-base-frontend/store/content-snippets/content-snippets.selectors';
import { initShop } from '@apto-base-frontend/store/shop/shop.actions';
import { selectFullConnector, selectShop } from '@apto-base-frontend/store/shop/shop.selectors';
import { Connector, Shop } from '@apto-base-frontend/store/shop/shop.model';
import { selectLocale } from '@apto-base-frontend/store/language/language.selectors';

@UntilDestroy()
@Component({
	selector: 'apto-frontend-shop-login',
	templateUrl: './frontend-shop-login.component.html',
	styleUrls: ['./frontend-shop-login.component.scss'],
})
export class FrontendShopLoginComponent {
  public readonly contentSnippets$ = this.store.select(selectContentSnippet('plugins.frontendUsers'));
  public connector: Connector | null;
  public shop: Shop | null;
  public error: boolean = false;
  private locale: string = environment.defaultLocale;
  public formGroup = new FormGroup({
    username: new FormControl<string>('', { nonNullable: true }),
    password: new FormControl<string>('', { nonNullable: true }),
  });

  constructor(private store: Store, private http: HttpClient) {
    this.store.select(selectLocale).pipe(untilDestroyed(this)).subscribe((locale: string) => {
      if (locale !== null) {
        this.locale = locale;
      }
    });

    this.store.select(selectShop).pipe(untilDestroyed(this)).subscribe((next) => {
      this.shop = next;
    });

    this.store.select(selectFullConnector).pipe(untilDestroyed(this)).subscribe((next) => {
      this.connector = next;
    });
  }

  public login(): void {
    this.error = false;
    this.store.dispatch(initShop());

    if (this.connector.loggedIn) {
      return;
    }

    const url = translate(this.shop.connectorUrl, this.locale);
    this.http
      .post(
        url,
        {
          data: {
            query: 'Login',
            arguments: [
              this.formGroup.get('username').getRawValue(),
              this.formGroup.get('password').getRawValue()
            ],
          },
          encode: 'json',
        },
        {
          withCredentials: true,
        }
      ).pipe(
        take(1)
    ).subscribe(() => {
      this.store.dispatch(initShop());
    }, () => {
      this.error = true;
    });
  }
}
