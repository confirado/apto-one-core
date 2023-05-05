import { Action, createReducer, on } from '@ngrx/store';
import { ContentSnippet } from "@apto-base-frontend/store/content-snippets/content-snippet.model";
import { initShopSuccess } from "@apto-base-frontend/store/shop/shop.actions";

export interface ContentSnippetsState {
  snippets: ContentSnippet[]
}

const contentSnippetsInitialState: ContentSnippetsState = {
  snippets: []
};

const _contentSnippetsReducer = createReducer(
  contentSnippetsInitialState,
  on(initShopSuccess, (state, action) => {
    return {
      ...state,
      snippets: action.payload.contentSnippets,
      loading: false
    };
  }),
);

export function contentSnippetsReducer(state: ContentSnippetsState | undefined, action: Action) {
  return _contentSnippetsReducer(state, action);
}
