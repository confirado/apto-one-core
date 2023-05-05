import { TranslatedValue } from "@apto-base-core/store/translated-value/translated-value.model";

export interface Language {
  id: string;
  locale: string;
  name: TranslatedValue;
}
