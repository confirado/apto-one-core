import { environment } from '@apto-frontend/src/environments/environment';

export interface TranslatedValue {
  [locale: string]: string;
}

export function translate(value: TranslatedValue | undefined, locale: string): string {
  const defaultLocale: string = environment.defaultLocale;

  if (!value) {
    return '';
  }
  // return translation for current or default locale
  if (value[locale]) {
    return value[locale];
  }
  if (value[defaultLocale]) {
    return value[defaultLocale];
  }

  // return first translation
  for (let key in value) {
    if (value.hasOwnProperty(key)) {
      return value[key];
    }
  }

  // return empty string if no translation was found
  return '';
}
