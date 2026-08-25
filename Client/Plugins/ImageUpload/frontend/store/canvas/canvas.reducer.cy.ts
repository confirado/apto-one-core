import { canvasReducer, initialState } from './canvas.reducer';
import { setCanvasElement } from './canvas.actions';

describe('canvasReducer', () => {
  it('stores the selected canvas element without discarding existing images', () => {
    const element = {
      elementId: 'element-42',
      sectionId: 'section-7',
      sectionRepetition: 1,
      staticValues: {},
      state: {},
    };

    const result = canvasReducer({ ...initialState, images: [{ id: 'image-1' }] }, setCanvasElement({ payload: { element } }));

    expect(result).to.deep.equal({ element, images: [{ id: 'image-1' }] });
  });
});
