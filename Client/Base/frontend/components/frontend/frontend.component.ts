import { AfterViewInit, Component, OnInit, ViewChild } from '@angular/core';
import { MatDrawer } from '@angular/material/sidenav';
import { BasketService } from '@apto-base-frontend/services/basket.service';
import { initShop } from '@apto-base-frontend/store/shop/shop.actions';
import { Shop } from '@apto-base-frontend/store/shop/shop.model';
import { selectShop } from '@apto-base-frontend/store/shop/shop.selectors';
import { Store } from '@ngrx/store';
import { Observable } from 'rxjs';
import { checkLoginStatus, login, logout } from '@apto-base-frontend/store/frontend-user/frontend-user.actions';
import { selectIsLoggedIn } from '@apto-base-frontend/store/frontend-user/frontend-user.selectors';
import { selectContentSnippet } from '@apto-base-frontend/store/content-snippets/content-snippets.selectors';

@Component({
	selector: 'apto-frontend',
	templateUrl: './frontend.component.html',
	styleUrls: ['./frontend.component.scss'],
})
export class FrontendComponent implements OnInit, AfterViewInit {
	public shop$: Observable<Shop | null>;
  public isLoggedIn$: Observable<boolean>;
  readonly csFrontendUsers$ = this.store.select(selectContentSnippet('plugins.frontendUsers'));

	@ViewChild('drawer', { static: true }) public drawer!: MatDrawer;

	constructor(private store: Store, private basketService: BasketService) {
		this.shop$ = this.store.select(selectShop);
    this.isLoggedIn$ = this.store.select(selectIsLoggedIn);
	}

	ngOnInit(): void {
		this.store.dispatch(initShop());
    this.store.dispatch(checkLoginStatus());
	}

	ngAfterViewInit(): void {
		this.basketService.sideBar = this.drawer;
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
