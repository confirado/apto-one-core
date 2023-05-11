import { createAction, props } from '@ngrx/store';

export enum CanvasActionTypes {
  SetCanvasElement = '[Canvas] Set Canvas Element'
}

export const setCanvasElement = createAction(
  CanvasActionTypes.SetCanvasElement,
  props<{
    payload: any;
  }>()
);
