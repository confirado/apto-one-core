import { TranslatedValue } from '@apto-base-core/store/translated-value/translated-value.model';
import { CustomProperty } from "@apto-base-core/store/custom-property/custom-property.model";

export interface Shop {
  id: string;
  name: string;
  description: TranslatedValue;
  connectorUrl: TranslatedValue;
  domain: string;
  currency: string;
  customProperties: CustomProperty[]
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
}

export interface CustomerGroup {
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

export interface Connector {
	sessionCookies: any;
	user: ConnectorUser | null;
	loggedIn: boolean;
	taxState: string;
	customerGroup: CustomerGroup;
	customerGroupExternalId: string | null;
	displayCurrency: Currency;
	shopCurrency: Currency;
	basket: ConnectorBasket;
  configured: boolean;
}

export interface SelectConnector {
	sessionCookies: any;
	taxState: string;
	customerGroup: CustomerGroup;
	displayCurrency: Currency;
	shopCurrency: Currency;
	locale: string;
	customerGroupExternalId: string | null;
  configured: boolean;
}
