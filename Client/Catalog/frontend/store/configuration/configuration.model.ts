import { TranslatedValue } from '@apto-base-core/store/translated-value/translated-value.model';
import { Element, Section } from '@apto-catalog-frontend/store/product/product.model';

export interface SectionState {
	id: string;
	identifier: string;
	active: boolean;
	disabled: boolean;
	multiple: boolean;
	mandatory: boolean;
	hidden: boolean;
}

export interface ElementState {
	id: string;
	identifier: string;
	sectionId: string;
	sectionIdentifier: string;
	active: boolean;
	disabled: boolean;
	mandatory: boolean;
	values: any;
}
export interface Configuration {
	compressedState: any;
	sections: SectionState[];
	elements: ElementState[];
}

export interface ComputedValues {
	[name: string]: string;
}

export interface RenderImageData {
  productId: string,
  renderImageId: string,
  layer: number,
  perspective: string,
  offsetX: number,
  offsetY: number,
  renderImageOptions: any,
  path: string,
  filename: string,
  extension: string,
  realWidth: number,
  realHeight: number,
  realOffsetX: number,
  realOffsetY: number
}

export interface RenderImage {
	perspective: string;
	url: string;
	images?: RenderImageData[];
}

export interface AreaElementDefinitionProperties {
	[key: string]: [{ type: 'range'; minimum: 0; maximum: 1; step: 1 }];
}

export interface ProgressElement<ElementDefinitionProperties = any> {
	state: ElementState;
	element: Element<ElementDefinitionProperties>;
}
export interface ProgressStep {
	status: string;
	fulfilled: boolean;
	description: string;
	section: Section;
	active: boolean;
	elements: ProgressElement[];
}
export interface ProgressState {
	productId: string | undefined;
	currentStep: ProgressStep | undefined;
	beforeSteps: ProgressStep[];
	afterSteps: ProgressStep[];
	steps: ProgressStep[];
	progress: number;
}

export interface Discount {
	discount: number;
	name: TranslatedValue;
	description: string | null;
}

export interface Price {
	amount: number;
	formatted: string;
}
export interface Own {
	pseudoPrice: Price;
	price: Price;
}

export interface Sum {
	pseudoPrice: Price;
	price: Price;
	netPrice?: Price;
	grossPrice?: Price;
}
export interface StatePriceSection {
	discount: Discount;
	own: Own;
	sum: Sum;
	elements: unknown;
}

export interface AddBasketConfigurationArguments {
	productId: string;
	compressedState: any;
	sessionCookies: any;
	locale?: string;
	quantity: number;
	perspectives: string[];
	additionalData: any;
}

export interface AddGuestConfigurationArguments {
	productId: string;
	compressedState: any;
	email: string;
	name: string;
	sendMail: boolean;
	id: string;
	payload: any;
}

export interface GetConfigurationStateArguments {
	productId: string;
	compressedState: any;
	updates: {
		set?: {
			sectionId: string;
			elementId: string;
			property?: any;
			value: any;
		}[];
		remove?: {
			sectionId: string;
			elementId: string;
			property?: any;
			value: any;
		}[];
	};
	locale: string;
	quantity: number;
	perspectives: string[];
	additionalData: any;
}
export interface StatePrice {
	discount: Discount;
	own: Own;
	sum: Sum;
	currency: string;
	sections: Record<string, StatePriceSection>;
	productSurcharges: unknown[];
}
