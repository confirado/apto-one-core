import { ElementState, ProgressElement } from '@apto-catalog-frontend-configuration-model';
import { Definition, Element, ElementZoomFunctionEnum, StaticValues } from '@apto-catalog-frontend-product-model';

type ProgressElementIdentity = Pick<ElementState, 'id' | 'identifier' | 'sectionId' | 'sectionIdentifier'>;

type DefinitionOverrides<Properties> = Partial<Omit<Definition<Properties>, 'staticValues'>> & {
  staticValues?: Partial<StaticValues>;
};

type ElementOverrides<Properties> = Partial<Omit<Element<Properties>, 'definition'>> & {
  definition?: DefinitionOverrides<Properties>;
};

export interface ProgressElementOverrides<Properties = Record<string, unknown>> extends Partial<ProgressElementIdentity> {
  state?: Partial<ElementState>;
  element?: ElementOverrides<Properties>;
}

const defaultIdentity: ProgressElementIdentity = {
  id: 'element-1',
  identifier: 'test-element',
  sectionId: 'section-1',
  sectionIdentifier: 'test-section',
};

function buildElementState(overrides: Partial<ElementState> = {}): ElementState {
  return {
    ...defaultIdentity,
    active: true,
    disabled: false,
    mandatory: false,
    values: {},
    customProperties: [],
    ...overrides,
  };
}

function buildStaticValues(overrides: Partial<StaticValues> = {}): StaticValues {
  /*
   * StaticValues is shared by unrelated element definitions and therefore has no
   * meaningful complete default. Tests intentionally provide only the fields for
   * the rendered element variant; keep that fixture-only assertion in one place.
   */
  return overrides as StaticValues;
}

function buildDefinition<Properties = Record<string, unknown>>(
  overrides: DefinitionOverrides<Properties> = {}
): Definition<Properties> {
  const { staticValues: staticValuesOverrides, ...topLevelOverrides } = overrides;

  return {
    component: 'test-component',
    name: 'test-definition',
    properties: {} as Properties,
    staticValues: buildStaticValues(staticValuesOverrides),
    ...topLevelOverrides,
  };
}

function buildElement<Properties = Record<string, unknown>>(
  overrides: ElementOverrides<Properties> = {}
): Element<Properties> {
  const { definition: definitionOverrides, ...topLevelOverrides } = overrides;

  return {
    ...defaultIdentity,
    name: {},
    description: {},
    definition: buildDefinition<Properties>(definitionOverrides),
    errorMessage: {},
    previewImage: null,
    isMandatory: false,
    position: 0,
    attachments: [],
    zoomFunction: ElementZoomFunctionEnum.DEACTIVATED,
    customProperties: [],
    ...topLevelOverrides,
  };
}

export function buildProgressElement<Properties = Record<string, unknown>>(
  overrides: ProgressElementOverrides<Properties> = {}
): ProgressElement<Properties> {
  const { state: stateOverrides, element: elementOverrides, ...identityOverrides } = overrides;
  const identity = { ...defaultIdentity, ...identityOverrides };

  return {
    // A nested override deliberately wins, allowing tests to model malformed API
    // payloads when that inconsistency is itself the subject under test.
    state: buildElementState({ ...identity, ...stateOverrides }),
    element: buildElement<Properties>({ ...identity, ...elementOverrides }),
  };
}
