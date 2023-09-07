import { createAction, props } from '@ngrx/store';

export enum CanvasActionTypes {
  SetCanvasElement = '[Canvas] Set Canvas Element',
  FindEditableRenderImage = '[RenderImage] Find Editable RenderImage',
  FindEditableRenderImageSuccess = '[RenderImage] Find Editable RenderImage Success'
}

export const setCanvasElement = createAction(
  CanvasActionTypes.SetCanvasElement,
  props<{
    payload: any;
  }>()
);

export const findEditableRenderImage = createAction(
  CanvasActionTypes.FindEditableRenderImage,
  props<{
    payload: any;
  }>()
);
