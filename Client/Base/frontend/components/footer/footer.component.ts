import { Component } from '@angular/core';
import { Store } from '@ngrx/store';
import { environment } from '@apto-frontend/src/environments/environment';
import { selectContentSnippet } from '@apto-base-frontend/store/content-snippets/content-snippets.selectors';

@Component({
	selector: 'apto-footer',
	templateUrl: './footer.component.html',
	styleUrls: ['./footer.component.scss'],
})
export class FooterComponent {
	public readonly links$ = this.store.select(selectContentSnippet('aptoFooterNav.menu'));
	public readonly contentSnippetPayment$ = this.store.select(selectContentSnippet('aptoFooterPaymentProvider'));
  public readonly contentSnippetFooterInfo$ = this.store.select(selectContentSnippet('aptoFooterInfo'));
	public readonly mediaUrl = environment.api.media + '/';
  public readonly currentYear: number = new Date().getFullYear();

	public constructor(private store: Store) {}
}
