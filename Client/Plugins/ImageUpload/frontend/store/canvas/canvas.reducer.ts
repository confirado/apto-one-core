import { Action, createReducer, on } from '@ngrx/store';
import { setCanvasElement } from '@apto-image-upload-frontend/store/canvas/canvas.actions';

export interface CanvasState {
  element: any
  images: any
}

export const initialState: CanvasState = {
  element: null,
  images: []
};

const _reducer = createReducer(
  initialState,
  on(setCanvasElement, (state, action) => {
    return {
      ...state,
      element: action.payload.element
    }
  })
);

export function canvasReducer(state: CanvasState | undefined, action: Action): CanvasState {
  return _reducer(state, action);
}
