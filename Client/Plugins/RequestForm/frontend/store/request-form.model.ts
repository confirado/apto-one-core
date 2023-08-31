import { TranslatedValue } from '@apto-base-core/store/translated-value/translated-value.model';

export interface Gender {
  surrogateId: string,
  id: string,
  name: TranslatedValue,
  isDefault: boolean,
  aptoPrices: []
}

export enum FieldsWhenUserIsLoggedInEnum {
  ALL = 'all',
  ONLY_MESSAGE = 'onlyMessage'
}
