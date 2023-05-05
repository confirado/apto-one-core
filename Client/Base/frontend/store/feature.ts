import { Action, ActionReducerMap, createFeatureSelector } from "@ngrx/store";
import { contentSnippetsReducer, ContentSnippetsState } from "@apto-base-frontend/store/content-snippets/content-snippets.reducer";
import { languageReducer, LanguageState } from "@apto-base-frontend/store/language/language.reducer";
import { shopReducer, ShopState } from "@apto-base-frontend/store/shop/shop.reducer";

export const featureKey = 'aptoBase';
export const featureSelector = createFeatureSelector<BaseFeatureState>(featureKey);

export interface BaseFeatureState {
  contentSnippets: ContentSnippetsState;
  language: LanguageState;
  shop: ShopState;
}

export interface PayloadAction extends Action {
  payload: any;
}

export const reducers: ActionReducerMap<BaseFeatureState, PayloadAction> = {
  contentSnippets: contentSnippetsReducer,
  language: languageReducer,
  shop: shopReducer
}

// @todo: we have a loading flag for product and content snippets, is it possible with rxjs, to react to all actions that set a loading flag?
// @todo: e.g. for a global loading spinner, a stream that gives us always the last loading flag of all actions
