import { CustomProperty } from '@apto-base-core/store/custom-property/custom-property.model';
import { TranslatedValue } from '@apto-base-core/store/translated-value/translated-value.model';

export interface Product {
	id: string;
	identifier: string;
	seoUrl: string;
	name: TranslatedValue;
	description: TranslatedValue;
	previewImage: string | null;
	useStepByStep: boolean;
	position: number;
	customProperties: CustomProperty[];
}

export interface Group {
	id: string;
	identifier: string;
	name: TranslatedValue;
	position: number;
}

export interface Section {
	id: string;
	identifier: string;
	groupId: string | null;
	groupIdentifier: string | null;
	name: TranslatedValue;
	description: TranslatedValue;
	allowMultiple: boolean;
	isHidden: boolean;
	isMandatory: boolean;
	position: number;
	customProperties: CustomProperty[];
  previewImage: string | null;
}

export interface Conditions {
	type: string;
	step: number;
	max: number;
	min: number;
}

export interface InputField {
	conditions: Conditions;
}

export interface InfoField {
	default: number;
	prefix: TranslatedValue;
	suffix: TranslatedValue;
	rendering: string;
}

export interface PriceMatrix {
	column: unknown;
	id: unknown;
	row: unknown;
}

export interface PriceMultiplication {
	active: boolean;
	baseValueFormula: unknown;
	factor: number;
}

export interface defaultItem {
	id: string;
	elementId: string;
	name: TranslatedValue;
}

export interface StaticValues {
	aptoElementDefinitionId: string;
	fields?: InfoField[];
	prefixWidth?: TranslatedValue;
	prefixHeight?: TranslatedValue;
	suffixWidth?: TranslatedValue;
	suffixHeight?: TranslatedValue;
	suffix?: TranslatedValue;
	prefix?: TranslatedValue;
	livePricePrefix: TranslatedValue;
	livePriceSuffix: TranslatedValue;
	priceMatrix: PriceMatrix;
	priceMultiplication: PriceMultiplication;
	renderDialogInOnePageDesktop: boolean;
	renderingWidth?: string;
	renderingHeight?: string;
	defaultHeight?: string;
	defaultWidth?: string;
	defaultValue?: string;
	defaultItem?: defaultItem;
	rendering?: string;
	enableMultiplier?: boolean;
	enableMultiSelect?: boolean;
	multiplierPrefix?: TranslatedValue;
	multiplierSuffix?: TranslatedValue;
	sumOfFieldValueActive: boolean;
	poolId: string;
	placeholder: TranslatedValue;
	allowMultiple: boolean;
	searchboxActive: boolean;
	secondaryMaterialActive: boolean;
  monochromeImage: string;
  multicoloredImageAlternately: string;
  multicoloredImageInput: string;
  background: any;
  area: any;
}

export interface RangeField {
	type: string;
	minimum: number;
	maximum: number;
	step: number;
}

export interface HeightWidthProperties {
	height?: RangeField[];
	width?: RangeField[];
}
export interface Definition<Properties> {
	component: string;
	name: string;
	properties: Properties;
	staticValues: StaticValues;
}

export interface Element<DefinitionProperties = unknown> {
	id: string;
	identifier: string;
	sectionId: string;
	sectionIdentifier: string;
	name: TranslatedValue;
	description: TranslatedValue;
	definition: Definition<DefinitionProperties>;
	errorMessage: TranslatedValue;
	previewImage: string | null;
	isMandatory: boolean;
	isZoomable: boolean;
	position: number;
	customProperties: CustomProperty[];
}
