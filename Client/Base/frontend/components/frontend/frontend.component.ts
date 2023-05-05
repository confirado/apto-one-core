import { AfterViewInit, Component, OnInit, ViewChild } from '@angular/core';
import { MatDrawer } from '@angular/material/sidenav';
import { BasketService } from '@apto-base-frontend/services/basket.service';
import { initShop } from '@apto-base-frontend/store/shop/shop.actions';
import { Shop } from '@apto-base-frontend/store/shop/shop.model';
import { selectShop } from '@apto-base-frontend/store/shop/shop.selectors';
import { Store } from '@ngrx/store';
import { Observable } from 'rxjs';

@Component({
	selector: 'apto-frontend',
	templateUrl: './frontend.component.html',
	styleUrls: ['./frontend.component.scss'],
})
export class FrontendComponent implements OnInit, AfterViewInit {
	shop$: Observable<Shop | null>;

	@ViewChild('drawer', { static: true }) public drawer!: MatDrawer;

	constructor(private store: Store, private basketService: BasketService) {
		this.shop$ = this.store.select(selectShop);
	}

	ngOnInit(): void {
		this.store.dispatch(initShop());
	}

	ngAfterViewInit(): void {
		this.basketService.sideBar = this.drawer;
	}
}
