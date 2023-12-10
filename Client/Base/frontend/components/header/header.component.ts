import { Component } from '@angular/core';
import { selectContentSnippet } from '@apto-base-frontend/store/content-snippets/content-snippets.selectors';
import { setLocale } from '@apto-base-frontend/store/language/language.actions';
import { selectLanguages, selectLocale } from '@apto-base-frontend/store/language/language.selectors';
import { environment } from '@apto-frontend/src/environments/environment';
import { Store } from '@ngrx/store';
import { SelectConnector } from '@apto-base-frontend/store/shop/shop.model';
import { selectConnector } from '@apto-base-frontend/store/shop/shop.selectors';
import { selectIsLoggedIn } from '@apto-base-frontend/store/frontend-user/frontend-user.selectors';
import { login, logout } from '@apto-base-frontend/store/frontend-user/frontend-user.actions';
import { translate } from '@apto-base-core/store/translated-value/translated-value.model';
import { DialogSizesEnum } from '@apto-frontend/src/configs-static/dialog-sizes-enum';
import { DialogService } from '@apto-catalog-frontend/components/common/dialogs/dialog-service';
import {
  FrontendUsersLoginComponent
} from '@apto-base-frontend/components/frontend-users-login/frontend-users-login.component';
import { BasketService } from '@apto-base-frontend/services/basket.service';

@Component({
	selector: 'apto-header',
	templateUrl: './header.component.html',
	styleUrls: ['./header.component.scss'],
})
export class HeaderComponent {
  private locale: string = environment.defaultLocale;
	locale$ = this.store.select(selectLocale);
	languages$ = this.store.select(selectLanguages);
	readonly contentSnippets$ = this.store.select(selectContentSnippet('aptoLogo'));
	mediaUrl = environment.api.media + '/';
  public connector: SelectConnector | null;
  public isLoggedIn: boolean;
  public loginActive: boolean;

	constructor(private store: Store, private dialogService: DialogService, private basketService: BasketService) {
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

    this.store.select(selectContentSnippet('plugins.frontendUsers.loginActive')).subscribe((next) => {
      if (!next) {
        this.loginActive = false;
      } else {
        this.loginActive = translate(next.content, this.locale) === 'active' ? true : false;
      }
    });
  }

	onChangeLanguage(locale: string) {
		this.store.dispatch(setLocale({ payload: locale }));
	}

  showLoginButton() {
    if (this.connector && this.connector.configured === true || this.loginActive === false) {
      return false;
    }

    if (this.isLoggedIn) {
      return false;
    }

    return true;
  }

  showLogoutButton() {
    if (this.connector && this.connector.configured === true || this.loginActive === false) {
      return false;
    }

    if (!this.isLoggedIn) {
      return false;
    }

    return true;
  }

  login() {
    this.dialogService.openCustomDialog(FrontendUsersLoginComponent, DialogSizesEnum.md)
  }

  logout() {
    this.store.dispatch(logout());
  }

  public toggleSideBar(): void {
    this.basketService.sideBar?.toggle();
  }
}
