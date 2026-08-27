import { findPoolItemsSuccess, initMaterialPickerSuccess } from './material-picker.actions';
import { initialState, materialPickerReducer } from './material-picker.reducer';

describe('materialPickerReducer', () => {
  it('stores the complete pool response during initialization', () => {
    const payload = { items: [], colors: [], priceGroups: [], propertyGroups: [] };

    expect(materialPickerReducer(initialState, initMaterialPickerSuccess({ payload }))).to.deep.equal(payload);
  });

  it('refreshes only filterable pool data while preserving available groups', () => {
    const state = {
      ...initialState,
      priceGroups: [{ id: 'group-1', name: { de_DE: 'Group' } }],
      propertyGroups: [{ id: 'property-group', name: { de_DE: 'Properties' }, allowMultiple: false, properties: [] }],
    };

    const result = materialPickerReducer(state, findPoolItemsSuccess({ payload: { items: [], colors: [] } }));

    expect(result).to.deep.equal({ ...state, items: [], colors: [] });
  });
});
