import { Component } from '@angular/core';
import { selectContentSnippet } from '@apto-base-frontend/store/content-snippets/content-snippets.selectors';
import { environment } from '@apto-frontend/src/environments/environment';
import { Store } from '@ngrx/store';
import { map } from 'rxjs';

@Component({
	selector: 'apto-footer',
	templateUrl: './footer.component.html',
	styleUrls: ['./footer.component.scss'],
})
export class FooterComponent {
	public readonly contentSnippet$ = this.store.select(selectContentSnippet('aptoFooterNav'));

	public readonly contentSnippetPayment$ = this.store.select(selectContentSnippet('aptoFooterPaymentProvider'));

  public readonly contentSnippetFooterInfo$ = this.store.select(selectContentSnippet('aptoFooterInfo'));

	public mediaUrl = environment.api.media + '/';

	public links$ = this.contentSnippet$.pipe(
		map((entry) => {
			const children = entry?.children?.[0]?.children || [];
			return children.filter((c) => !Array.isArray(c.children?.[0].content));
		})
	);

	public constructor(private store: Store) {}

  currentYear: number = new Date().getFullYear();
}
