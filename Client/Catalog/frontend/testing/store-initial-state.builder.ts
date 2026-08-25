import { productInitialState } from '@apto-catalog-frontend/store/product/product.reducer';
import { ConfigurationState, configurationInitialState } from '@apto-catalog-frontend-configuration-reducer';
import { shopInitialState } from '@apto-base-frontend/store/shop/shop.reducer';
import { frontendUserInitialState } from '@apto-base-frontend/store/frontend-user/frontend-user.reducer';
import { contentSnippetsReducer } from '@apto-base-frontend/store/content-snippets/content-snippets.reducer';
import { languageReducer } from '@apto-base-frontend/store/language/language.reducer';
import { BaseFeatureState } from '@apto-base-frontend/store/feature';
import { CatalogFeatureState } from '@apto-catalog-frontend/store/feature';
import { Action } from '@ngrx/store';
import { Product } from '@apto-catalog-frontend-product-model';
import {
  Configuration,
  ElementState,
  SectionTypes,
  SectionState,
} from '@apto-catalog-frontend-configuration-model';

const testInitialAction: Action = { type: '[Testing] Initial state' };
const contentSnippetsInitialState = contentSnippetsReducer(undefined, testInitialAction);
const languageInitialState = languageReducer(undefined, testInitialAction);

export type ConfigurationStateOverrides = Partial<Omit<ConfigurationState, 'state'>> & {
  state?: Partial<ConfigurationState['state']>;
};

export interface MockStoreStateOverrides {
  aptoCatalog?: {
    product?: Partial<CatalogFeatureState['product']>;
    configuration?: ConfigurationStateOverrides;
  };
  aptoBase?: {
    contentSnippets?: Partial<BaseFeatureState['contentSnippets']>;
    language?: Partial<BaseFeatureState['language']>;
    shop?: Partial<BaseFeatureState['shop']>;
    frontendUser?: Partial<BaseFeatureState['frontendUser']>;
  };
}

export interface MockStoreInitialState {
  aptoCatalog: CatalogFeatureState;
  aptoBase: BaseFeatureState;
}

export type ConfigurationOverrides = Partial<Omit<Configuration, 'sections' | 'elements'>> & {
  sections?: SectionState[];
  elements?: ElementState[];
};

export interface ConfigurationResponseFixture {
  compressedState?: Configuration['compressedState'];
  failedRules?: Configuration['failedRules'];
  renderImages?: unknown;
  intention?: unknown;
  configurationState: {
    sections: unknown[];
    elements: unknown[];
  };
}

export function buildConfiguration(overrides: ConfigurationOverrides = {}): Configuration {
  return {
    compressedState: [],
    sections: [],
    elements: [],
    failedRules: [],
    ...overrides,
  };
}

export function buildConfigurationSection(overrides: Partial<SectionState> = {}): SectionState {
  return {
    id: 'section-1',
    identifier: 'test-section',
    active: true,
    disabled: false,
    multiple: false,
    mandatory: false,
    hidden: false,
    repetition: 0,
    repeatableCalculatedValueName: null,
    repeatableType: SectionTypes.STATISCH,
    customProperties: [],
    ...overrides,
  };
}

export function buildConfigurationElement(overrides: Partial<ElementState> = {}): ElementState {
  return {
    id: 'element-1',
    identifier: 'test-element',
    sectionId: 'section-1',
    sectionIdentifier: 'test-section',
    active: true,
    disabled: false,
    mandatory: false,
    values: {},
    sectionRepetition: 0,
    customProperties: [],
    ...overrides,
  };
}

export function buildProduct(overrides: Partial<Product> = {}): Product {
  return {
    id: 'product-1',
    identifier: 'test-product',
    seoUrl: '',
    name: { de_DE: 'Test product' },
    description: { de_DE: '' },
    previewImage: null,
    useStepByStep: true,
    keepSectionOrder: false,
    position: 0,
    customProperties: [],
    hidden: false,
    active: true,
    minPurchase: 1,
    maxPurchase: 10,
    deliveryTime: 0,
    metaTitle: { de_DE: '' },
    metaDescription: { de_DE: '' },
    categories: [],
    ...overrides,
  };
}

/**
 * Builds the real feature-state shape used by selectors. Overrides are shallow
 * per feature state; only configuration.state receives one intentional shallow
 * merge, so nested fixtures must be supplied explicitly by each test.
 */
export function buildMockStoreInitialState(overrides: MockStoreStateOverrides = {}): MockStoreInitialState {
  const configurationOverrides = overrides.aptoCatalog?.configuration;
  const { state: stateOverrides, ...configurationTopLevelOverrides } = configurationOverrides ?? {};

  return {
    aptoCatalog: {
      product: { ...productInitialState, ...overrides.aptoCatalog?.product },
      configuration: {
        ...configurationInitialState,
        ...configurationTopLevelOverrides,
        state: { ...configurationInitialState.state, ...stateOverrides },
      },
    },
    aptoBase: {
      contentSnippets: { ...contentSnippetsInitialState, ...overrides.aptoBase?.contentSnippets },
      language: { ...languageInitialState, ...overrides.aptoBase?.language },
      shop: { ...shopInitialState, ...overrides.aptoBase?.shop },
      frontendUser: { ...frontendUserInitialState, ...overrides.aptoBase?.frontendUser },
    },
  };
}
