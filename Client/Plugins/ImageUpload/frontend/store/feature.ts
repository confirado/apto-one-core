import { ActionReducerMap, createFeatureSelector } from '@ngrx/store';
import { PayloadAction } from '@apto-catalog-frontend/store/feature';
import { canvasReducer, CanvasState } from '@apto-image-upload-frontend/store/canvas/canvas.reducer';

export const featureKey = 'aptoImageUpload';

export interface ImageUploadFeatureState {
  canvas: CanvasState
}

export const featureSelector = createFeatureSelector<ImageUploadFeatureState>(featureKey);

export const reducers: ActionReducerMap<ImageUploadFeatureState, PayloadAction> = {
  canvas: canvasReducer
};
