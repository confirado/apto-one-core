import { AfterViewInit, Component, OnInit, ViewChild } from '@angular/core';
import { MatDrawer } from '@angular/material/sidenav';
import { BasketService } from '@apto-base-frontend/services/basket.service';
import { initShop } from '@apto-base-frontend/store/shop/shop.actions';
import { SelectConnector, Shop } from '@apto-base-frontend/store/shop/shop.model';
import { selectConnector, selectShop } from '@apto-base-frontend/store/shop/shop.selectors';
import { Store } from '@ngrx/store';
import { Observable } from 'rxjs';
import { checkLoginStatus, login, logout } from '@apto-base-frontend/store/frontend-user/frontend-user.actions';
import { selectIsLoggedIn } from '@apto-base-frontend/store/frontend-user/frontend-user.selectors';
import { selectContentSnippet } from '@apto-base-frontend/store/content-snippets/content-snippets.selectors';
import { environment } from '@apto-frontend/src/environments/environment';
import { selectLocale } from '@apto-base-frontend/store/language/language.selectors';
import { untilDestroyed } from '@ngneat/until-destroy';
import { translate } from '@apto-base-core/store/translated-value/translated-value.model';

@Component({
	selector: 'apto-frontend',
	templateUrl: './frontend.component.html',
	styleUrls: ['./frontend.component.scss'],
})
export class FrontendComponent implements OnInit, AfterViewInit {
  private locale: string = environment.defaultLocale;
	public shop: Shop | null;
  public connector: SelectConnector | null;
  public isLoggedIn: boolean;
  public loginActive: boolean;
  public loginRequired: boolean;

	@ViewChild('drawer', { static: true }) public drawer!: MatDrawer;

	constructor(private store: Store, private basketService: BasketService) {
    this.store.select(selectLocale).subscribe((locale) => {
      if (locale !== null) {
        this.locale = locale;
      }
    });

		this.store.select(selectShop).subscribe((next) => {
      this.shop = next;
    });

    this.store.select(selectConnector).subscribe((next) => {
      this.connector = next;
    });

    this.store.select(selectIsLoggedIn).subscribe((next) => {
      this.isLoggedIn = next;
    });

    this.store.select(selectContentSnippet('plugins.frontendUsers.loginRequired')).subscribe((next) => {
      if (!next) {
        this.loginRequired = false;
      } else {

        this.loginRequired = translate(next.content, this.locale) === 'active' ? true : false;
      }
    });

    this.store.select(selectContentSnippet('plugins.frontendUsers.loginActive')).subscribe((next) => {
      if (!next) {
        this.loginActive = false;
      } else {
        this.loginActive = translate(next.content, this.locale) === 'active' ? true : false;
      }
    });
	}

	ngOnInit(): void {
		this.store.dispatch(initShop());
    this.store.dispatch(checkLoginStatus());
	}

	ngAfterViewInit(): void {
		this.basketService.sideBar = this.drawer;
	}

  accessGranted(): boolean {
    if (!this.shop || !this.connector) {
      return false;
    }

    if (this.connector.configured || this.loginActive === false) {
      return true;
    }

    if (this.loginRequired === true && this.isLoggedIn === false) {
      return false;
    }

    return true;
  }

  login() {
    console.error('login');
    this.store.dispatch(login({payload:{username: 'test', password: 'testtest'}}));
  }

  logout() {
    console.error('logout');
    this.store.dispatch(logout());
  }

  status() {
    console.error('status');
    this.store.dispatch(checkLoginStatus());
  }
}
