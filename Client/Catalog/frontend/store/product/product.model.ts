/* eslint-disable no-shadow */

import { CustomProperty } from '@apto-base-core/store/custom-property/custom-property.model';
import { TranslatedValue } from '@apto-base-core/store/translated-value/translated-value.model';
import { SectionTypes } from '@apto-catalog-frontend/store/configuration/configuration.model';
import {GalleryImages} from "@apto-catalog-frontend/models/material-picker";

export interface Product {
	id: string;
	identifier: string;
	seoUrl: string;
	name: TranslatedValue;
	description: TranslatedValue;
	previewImage: string | null;
	useStepByStep: boolean;
  keepSectionOrder: boolean;
	position: number;
	customProperties: CustomProperty[];
  hidden: boolean;
  active: boolean;
  minPurchase: number;
  maxPurchase: number;
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
  isZoomable: boolean;
  repetition?: number;
  repeatableCalculatedValueName?: null | string;
  repeatableType?: SectionTypes;
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

export enum SelectableValueTypes {
  SELECTABLE = 'Selectable',
  COMPUTABLE = 'Computable',
}

export enum CompareValueTypes {
  MINIMUM = 'Minimum',
  MAXIMUM = 'Maximum',
}

export interface ElementValueRefs {
  sectionId: string
  elementId: string,
  selectableValue: string,
  selectableValueType: SelectableValueTypes,
  compareValueType: CompareValueTypes,
  compareValueFormula: string
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
  renderingType: string;
  elementValueRefs: ElementValueRefs[];
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

export interface Attachments {
  id: string;
  name: TranslatedValue;
  mediaFile: {
    id: string;
    path: string;
    filename: TranslatedValue;
    extension: string;
  };
  fileUrl: string;
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
	position: number;
	customProperties: CustomProperty[];
  attachments: Attachments[];
  zoomFunction: ElementZoomFunctionEnum;
  sectionRepetition?: number;
  gallery?: any[];
}

export enum ElementZoomFunctionEnum {
  DEACTIVATED = 'deactivated',
  IMAGE_PREVIEW = 'image_preview',
  GALLERY = 'gallery'
}

export enum FloatInputTypes {
  INPUT = 'input',
  SLIDER = 'slider',
  INPUT_SLIDER = 'input_slider'
}

export enum ProductModes {
  STEP_BY_STEP = 'StepByStep',
  ONE_PAGE = 'OnePage',
}

export interface RuleRepairSettings {
  maxTries: number;
  operators: number[];
  selectEmptySections: boolean;
}
