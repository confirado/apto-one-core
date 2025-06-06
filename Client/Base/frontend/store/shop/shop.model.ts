import { TranslatedValue } from '@apto-base-core/store/translated-value/translated-value.model';
import { CustomProperty } from '@apto-base-core/store/custom-property/custom-property.model';
import { Language, LanguageResponse } from '@apto-base-frontend/store/language/language.model';

export interface Shop {
  id: string;
  name: string;
  description: TranslatedValue;
  connectorUrl: TranslatedValue;
  domain: string;
  currency: string;
  customProperties: CustomProperty[];
  templateId?: string;
}

export interface ShopResponse extends Shop {
  languages: LanguageResponse[];
  surrogateId?: number;
}

export interface Currency {
  symbol: string;
  name: string;
  currency: string;
  factor: number;
}

export interface ConnectorUser {
  email: string;
  externalId: string;
  firstName: string;
  gender: string;
  lastName: string;
  customerNumber: string;
}

export interface ExternalCustomerGroup {
  id: string;
  name: string;
  inputGross: boolean;
  showGross: boolean;
}

export interface ConnectorArticle {
  configId: string;
  deletable: boolean;
  id: string;
  name: string;
  orderNumber: string;
  price: string;
  quantity: number;
  thumbnail: string;
  type: string;
}

export interface ConnectorBasket {
  amount: string;
  quantity: number;
  articles: ConnectorArticle[];
}

export interface ConnectorUrl {
  cart: string;
  checkout: string;
  home: string;
  register?: string;
}

export interface Connector {
  sessionCookies: any;
  user: ConnectorUser | null;
  loggedIn: boolean;
  taxState: string;
  customerGroup: ExternalCustomerGroup;
  customerGroupExternalId: string | null;
  displayCurrency: Currency;
  shopCurrency: Currency;
  basket: ConnectorBasket;
  configured: boolean;
  url?: ConnectorUrl;
}

export interface SelectConnector {
  sessionCookies: any;
  taxState: string;
  customerGroup: ExternalCustomerGroup;
  displayCurrency: Currency;
  shopCurrency: Currency;
  locale: string;
  customerGroupExternalId: string | null;
  configured: boolean;
  user: ConnectorUser | null,
  loggedIn: boolean;
}

export interface ShopContext {
  shop: Shop;
  languages: Language[];
  locale: string;
}
