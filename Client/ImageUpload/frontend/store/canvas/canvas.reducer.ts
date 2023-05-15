import { Action, createReducer, on } from '@ngrx/store';
import { setStep } from '@apto-catalog-frontend/store/configuration/configuration.actions';
import {
  findEditableRenderImage,
  findEditableRenderImageSuccess,
  setCanvasElement,
} from '@apto-image-upload-frontend/store/canvas/canvas.actions';
import { RenderImage } from '@apto-catalog-frontend/store/configuration/configuration.model';

export interface CanvasState {
  element: any
  renderImage: any
  images: any
}

export const initialState: CanvasState = {
  element: null,
  renderImage: null,
  images: []
};

const _reducer = createReducer(
  initialState,
  on(setCanvasElement, (state, action) => ({
    ...state,
    element: action.payload.element,
    renderImage: action.payload.renderImage
  })),
  on(findEditableRenderImageSuccess, (state, action) => ({
    ...state,
    renderImage: action.payload
  })),
);

export function canvasReducer(state: CanvasState | undefined, action: Action): CanvasState {
  return _reducer(state, action);
}
