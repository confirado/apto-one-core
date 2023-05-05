import { Injectable } from '@angular/core';
import { MatDrawer } from '@angular/material/sidenav';

@Injectable({
	providedIn: 'root',
})
export class BasketService {
	public sideBar: MatDrawer | undefined;
}
