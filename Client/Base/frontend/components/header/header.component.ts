import { Component } from '@angular/core';
import { selectContentSnippet } from '@apto-base-frontend/store/content-snippets/content-snippets.selectors';
import { setLocale } from '@apto-base-frontend/store/language/language.actions';
import { selectLanguages, selectLocale } from '@apto-base-frontend/store/language/language.selectors';
import { environment } from '@apto-frontend/src/environments/environment';
import { Store } from '@ngrx/store';

@Component({
	selector: 'apto-header',
	templateUrl: './header.component.html',
	styleUrls: ['./header.component.scss'],
})
export class HeaderComponent {
	locale$ = this.store.select(selectLocale);
	languages$ = this.store.select(selectLanguages);
	readonly contentSnippets$ = this.store.select(selectContentSnippet('aptoLogo'));
	mediaUrl = environment.api.media + '/';

	constructor(private store: Store) {}

	onChangeLanguage(locale: string) {
		this.store.dispatch(setLocale({ payload: locale }));
	}
}
