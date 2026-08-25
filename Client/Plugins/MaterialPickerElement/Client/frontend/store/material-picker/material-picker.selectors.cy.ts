import { MaterialPickerFeatureState } from '@apto-material-picker-element-frontend/store/feature';
import { selectMultiPropertyGroups, selectSinglePropertyGroups } from './material-picker.selectors';

describe('material-picker selectors', () => {
  it('separates single and multi-select property groups', () => {
    const state: MaterialPickerFeatureState = {
      state: {
        items: [],
        colors: [],
        priceGroups: [],
        propertyGroups: [
          { id: 'single', name: { de_DE: 'Single' }, allowMultiple: false, properties: [] },
          { id: 'multi', name: { de_DE: 'Multi' }, allowMultiple: true, properties: [] },
        ],
      },
    };

    expect(selectSinglePropertyGroups.projector(state).map((group) => group.id)).to.deep.equal(['single']);
    expect(selectMultiPropertyGroups.projector(state).map((group) => group.id)).to.deep.equal(['multi']);
  });
});
