import { Component } from '@angular/core';
import { selectContentSnippet } from '@apto-base-frontend/store/content-snippets/content-snippets.selectors';
import { selectLocale } from '@apto-base-frontend/store/language/language.selectors';
import { environment } from '@apto-frontend/src/environments/environment';
import { Store } from '@ngrx/store';
import { SelectConnector } from '@apto-base-frontend/store/shop/shop.model';
import { selectConnector } from '@apto-base-frontend/store/shop/shop.selectors';
import {
  selectCurrentUser,
  selectIsLoggedIn,
  selectLoginError,
} from '@apto-base-frontend/store/frontend-user/frontend-user.selectors';
import { login, logout } from '@apto-base-frontend/store/frontend-user/frontend-user.actions';
import { translate } from '@apto-base-core/store/translated-value/translated-value.model';
import { FormControl, FormGroup } from '@angular/forms';

@Component({
	selector: 'apto-frontend-users-login',
	templateUrl: './frontend-users-login.component.html',
	styleUrls: ['./frontend-users-login.component.scss'],
})
export class FrontendUsersLoginComponent {
  public readonly contentSnippets$ = this.store.select(selectContentSnippet('plugins.frontendUsers'));
  private locale: string = environment.defaultLocale;
  public connector: SelectConnector | null;
  public isLoggedIn: boolean;
  public loginActive: boolean;
  public currentUserName: string;
  public loginError: boolean;

  public formGroup = new FormGroup({
    username: new FormControl<string>('', { nonNullable: true }),
    password: new FormControl<string>('', { nonNullable: true }),
  });

	constructor(private store: Store) {
    this.store.select(selectLocale).subscribe((locale) => {
      if (locale !== null) {
        this.locale = locale;
      }
    });

    this.store.select(selectConnector).subscribe((next) => {
      this.connector = next;
    });

    this.store.select(selectIsLoggedIn).subscribe((next) => {
      this.isLoggedIn = next;
    });

    this.store.select(selectCurrentUser).subscribe((next) => {
      this.currentUserName = next ? next.userName : '';
    });

    this.store.select(selectContentSnippet('plugins.frontendUsers.loginActive')).subscribe((next) => {
      if (!next) {
        this.loginActive = false;
      } else {
        this.loginActive = translate(next.content, this.locale) === 'active' ? true : false;
      }
    });

    this.store.select(selectLoginError).subscribe((next) => {
      this.loginError = next;
    });
  }

  login(): void {
    this.store.dispatch(login({
      payload: {
        username: this.formGroup.get('username').getRawValue(),
        password: this.formGroup.get('password').getRawValue()
      }
    }));
  }

  logout() {
    this.store.dispatch(logout());
  }
}
