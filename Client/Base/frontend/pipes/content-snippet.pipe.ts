import { Pipe, PipeTransform } from '@angular/core';
import { Subscription } from "rxjs";
import { Store } from "@ngrx/store";
import { UntilDestroy, untilDestroyed } from '@ngneat/until-destroy';
import { environment } from "@apto-frontend/src/environments/environment";
import { translate, TranslatedValue } from "@apto-base-core/store/translated-value/translated-value.model";
import { ContentSnippet } from "@apto-base-frontend/store/content-snippets/content-snippet.model";
import { selectLocale } from "@apto-base-frontend/store/language/language.selectors";

@UntilDestroy()
@Pipe({
  name: 'contentSnippet',
  pure: false
})
export class ContentSnippetPipe implements PipeTransform {
  private default: string = environment.defaultLocale;
  private locale: string;
  private subscription: Subscription;

  constructor(private store: Store) {
    // set default locale
    this.locale = this.default;

    // subscribe for locale store value
    store
      .select(selectLocale)
      .pipe(untilDestroyed(this))
      .subscribe((locale) => {
        if (locale !== null) {
          this.locale = locale;
        }
      });
  }

  transform(value: ContentSnippet | null, path: string): string {
    if (value === null) {
      return '[ContentSnippet: No value given!]';
    }

    const pathSegments = (value.name + '.' + path).split('.');
    const layer = 1;

    const selectContentSnippet = (contentSnippets: ContentSnippet[], name: string, layer: number): ContentSnippet | null => {
      for (let i = 0; i < contentSnippets.length; i++) {
        const contentSnippet = contentSnippets[i];
        if (contentSnippet.name === name) {
          if (layer === pathSegments.length) {
            return contentSnippet;
          }

          if (contentSnippet.children) {
            layer++;
            return selectContentSnippet(contentSnippet.children, pathSegments[layer - 1], layer);
          }
        }
      }
      return null;
    }

    const contentSnippet = selectContentSnippet([value], pathSegments[layer - 1], layer);

    // translate content snippet
    if (contentSnippet !== null) {
      const translation = translate(contentSnippet.content, this.locale);
      if (translation) {
        return translation;
      }
    }

    return '[ContentSnippet: ' + value?.name + '.' + path + ']';
  }
}
