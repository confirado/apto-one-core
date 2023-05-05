import { Action, createReducer, on } from '@ngrx/store';
import { Language } from "@apto-base-frontend/store/language/language.model";
import { setLocale } from "@apto-base-frontend/store/language/language.actions";
import { initShopSuccess } from "@apto-base-frontend/store/shop/shop.actions";

export interface LanguageState {
  languages: Language[];
  locale: string | null;
}

const languageInitialState: LanguageState = {
  languages: [],
  locale: null,
};

const _languageReducer = createReducer(
  languageInitialState,
  on(initShopSuccess, (state, action) => {
    return {
      ...state,
      languages: action.payload.languages,
      locale: action.payload.locale
    };
  }),
  on(setLocale, (state, action) => {
    return {
      ...state,
      locale: action.payload,
    };
  }),
);

export function languageReducer(state: LanguageState | undefined, action: Action) {
  return _languageReducer(state, action);
}
