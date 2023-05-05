import { createAction, props } from "@ngrx/store";

export enum LanguageActionTypes {
  SetLocale = '[Language] Set Locale'
}

export const setLocale = createAction(
  LanguageActionTypes.SetLocale,
  props<{ payload: string }>()
);
