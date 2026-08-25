import { ImageUploadFeatureState } from '@apto-image-upload-frontend/store/feature';
import { selectCanvas } from './canvas.selectors';

describe('canvas selectors', () => {
  it('projects the complete canvas state from the image-upload feature', () => {
    const canvas = { element: { elementId: 'element-42' }, images: [{ id: 'image-1' }] };
    const state: ImageUploadFeatureState = { canvas };

    expect(selectCanvas.projector(state)).to.equal(canvas);
  });
});
