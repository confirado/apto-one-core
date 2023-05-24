import { createSelector } from '@ngrx/store';
import { ImageUploadFeatureState, featureSelector } from '@apto-image-upload-frontend/store/feature';
import { CanvasState } from '@apto-image-upload-frontend/store/canvas/canvas.reducer';

export const selectCanvas = createSelector(featureSelector, (state: ImageUploadFeatureState): CanvasState => {
    return state.canvas;
  }
);
