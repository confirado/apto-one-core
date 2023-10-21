import { TranslatedValue } from '@apto-base-core/store/translated-value/translated-value.model';
import { ExistingLanguages } from './globals';

export class Language {
  public static isTranslatedValueSet(value: TranslatedValue): boolean {
    for (const key in value) {
      // @ts-ignore
      if (Object.values(ExistingLanguages).includes(key)) {
        return true;
      }
    }
    return false;
  }
}
