import { TranslatedValue } from '@apto-base-core/store/translated-value/translated-value.model';
import { Element, Section } from '@apto-catalog-frontend/store/product/product.model';

export enum ProgressStatuses {
  CURRENT = 'CURRENT',
  REMAINING = 'REMAINING',
  COMPLETED = 'COMPLETED',
}

// eslint-disable-next-line no-shadow
export enum SectionTypes {
  STATISCH = 'Statisch',
  WIEDERHOLBAR = 'Wiederholbar'
}

// this should be in sync with State.php's ITEM_TYPES
export enum ParameterStateTypes {
  QUANTITY = 'quantity',
  IGNORED_RULES = 'ignored_rules',
  REPETITIONS = 'repetitions',
}

export interface TempStateItem {
  sectionId: string;
  repetition: number;
  touched: boolean
}

export interface HumanReadableState {
  elementId: string;
  repetition: number;
  sectionId: string;
  values: Array<{ [key: string]: TranslatedValue }>;
}

export interface ConfigurationState {
  sectionId: string;
  elementId: string;
  sectionRepetition: number;
  property?: any;
  value: any;
}

export interface ParameterState {
  name: ParameterStateTypes;
  value: any
}

export type CompressedState = (ParameterState | HumanReadableState);

export interface CurrentSection {
  id: string;
  repetition?: number;
}

export interface SectionState {
	id: string;
	identifier: string;
	active: boolean;
	disabled: boolean;
	multiple: boolean;
	mandatory: boolean;
	hidden: boolean;
  repetition?: number;
  repeatableCalculatedValueName: null | string;
  repeatableType: SectionTypes;
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
  attachments: any;
  sectionRepetition?: number;
}

export interface Configuration {
	compressedState: CompressedState[];
	sections: SectionState[];
	elements: ElementState[];
  failedRules: any;
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
	status: ProgressStatuses;
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

export interface OwnSection extends Own {
  pseudoDiff: Price;
}

export interface Sum {
	pseudoPrice: Price;
	price: Price;
	netPrice?: Price;
	grossPrice?: Price;
}
export interface StatePriceSection {
	discount: Discount;
	own: OwnSection;
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

export interface UpdateBasketConfigurationArguments extends AddBasketConfigurationArguments {
	configurationId: string;
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

export interface FetchPartsListArguments {
  productId: string;
  compressedState: any;
  currency: string;
  customerGroupExternalId: string;
}

export interface PartsListPart {
  baseQuantity: number
  itemPrice: string
  itemPriceTotal: string
  partName: string
  partNumber: string
  quantity: string
  unit: string
}

export interface GetConfigurationStateArguments {
	productId: string;
	compressedState: any;
	updates: {
		set?: ConfigurationState[];
		remove?: ConfigurationState[];
		parameters?: ParameterState[];
	};
	locale: string;
	quantity: number;
	perspectives: string[];
	additionalData: any;
}

export interface GetConfigurationResult {
  state: Configuration,
  renderImages: [],
  updates: any
}

export interface StatePrice {
	discount: Discount;
	own: Own;
	sum: Sum;
	currency: string;
	sections: Record<string, StatePriceSection[]>;
	productSurcharges: unknown[];
}

export interface SectionPriceTableItem {
  elementId: string;
  name: TranslatedValue;
  value: string;
  position: number;
  isDiscount: boolean;
}
