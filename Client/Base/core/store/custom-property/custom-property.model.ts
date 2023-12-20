import { TranslatedValue } from '@apto-base-core/store/translated-value/translated-value.model';

export interface CustomProperty {
  key: string;
  value: string | TranslatedValue;
  translatable: boolean;
}
