import { Store } from '@ngrx/store';
import { Pipe, PipeTransform } from '@angular/core';
import { UntilDestroy, untilDestroyed } from '@ngneat/until-destroy';
import { environment } from '@apto-frontend/src/environments/environment';
import { translate, TranslatedValue } from '@apto-base-core/store/translated-value/translated-value.model';
import { selectLocale } from '@apto-base-frontend/store/language/language.selectors';

@UntilDestroy()
@Pipe({
	name: 'translate',
	pure: false,
})
export class TranslatedValuePipe implements PipeTransform {
	private default: string = environment.defaultLocale;
	private locale: string;

	public constructor(private store: Store) {
		// set default locale
		this.locale = this.default;

		// subscribe for locale store value
		store
			.select(selectLocale)
			.pipe(untilDestroyed(this))
			.subscribe((locale) => {
				if (locale === null) {
					this.locale = this.default;
				} else {
					this.locale = locale;
				}
			});
	}

	public transform(value: TranslatedValue | undefined): string {
    return translate(value, this.locale);
	}
}
