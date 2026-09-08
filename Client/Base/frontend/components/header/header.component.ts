import { Component } from '@angular/core';
import { Store } from '@ngrx/store';
import { take } from "rxjs";
import { environment } from '@apto-frontend/src/environments/environment';
import { DialogSizesEnum } from '@apto-frontend/src/configs-static/dialog-sizes-enum';
import { MessageBusService } from "@apto-base-core/services/message-bus.service";
import { translate } from '@apto-base-core/store/translated-value/translated-value.model';
import { selectContentSnippet } from '@apto-base-frontend/store/content-snippets/content-snippets.selectors';
import { setLocale } from '@apto-base-frontend/store/language/language.actions';
import { selectLanguages, selectLocale } from '@apto-base-frontend/store/language/language.selectors';
import { SelectConnector } from '@apto-base-frontend/store/shop/shop.model';
import { selectArticleQuantity, selectConnector } from '@apto-base-frontend/store/shop/shop.selectors';
import { selectIsLoggedIn } from '@apto-base-frontend/store/frontend-user/frontend-user.selectors';
import { logout } from '@apto-base-frontend/store/frontend-user/frontend-user.actions';
import { FrontendUsersLoginComponent } from '@apto-base-frontend/components/frontend-users-login/frontend-users-login.component';
import { BasketService } from '@apto-base-frontend/services/basket.service';
import { ForgotPasswordComponent } from '@apto-base-frontend/components/frontend-users-login/forgot-password/forgot-password.component';
import { DialogService } from '@apto-catalog-frontend/components/common/dialogs/dialog-service';

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
  readonly csLogin$ = this.store.select(selectContentSnippet('plugins.frontendUsers'));
  readonly csLinksToShop$ = this.store.select(selectContentSnippet('aptoLinksToShop'));
  mediaUrl = environment.api.media + '/';
  public connector: SelectConnector | null;
  public isLoggedIn: boolean;
  public loginActive: boolean;
  public totalQuantity: number;

	constructor(
    private store: Store,
    private dialogService: DialogService,
    private basketService: BasketService,
    private messageBusService: MessageBusService
  ) {
    this.store.select(selectLocale).subscribe((locale) => {
      if (locale !== null) {
        this.locale = locale;
      }
    });

    console.log('hello from core header component');

    this.store.select(selectConnector).subscribe((next) => {
      this.connector = next;
    });

    this.store.select(selectArticleQuantity).subscribe((result) => {
      this.totalQuantity = result;
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

  public onChangeLanguage(locale: string): void {
    this.messageBusService.setLocale(locale).pipe(take(1)).subscribe((next) => {
      if (next.currentLocale) {
        this.store.dispatch(setLocale({ payload: next.currentLocale }));
      }
    });
	}

  public showLoginButton(): boolean {
    if (this.connector && this.connector.configured === true || this.loginActive === false) {
      return false;
    }

    return !this.isLoggedIn;
  }

  public showLogoutButton(): boolean {
    if (this.connector && this.connector.configured === true || this.loginActive === false) {
      return false;
    }

    return this.isLoggedIn;
  }

  public login(): void {
    this.dialogService.openCustomDialog(FrontendUsersLoginComponent, DialogSizesEnum.md).afterClosed().subscribe((result) => {
      if (result?.openForgotModal) {
        this.dialogService.openCustomDialog(ForgotPasswordComponent, DialogSizesEnum.md);
      }
    });
  }

  public logout(): void {
    this.store.dispatch(logout());
  }

  public toggleSideBar(): void {
    this.basketService.sideBar?.toggle();
  }
}
