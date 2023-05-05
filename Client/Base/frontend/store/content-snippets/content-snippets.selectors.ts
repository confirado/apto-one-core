import { createSelector } from "@ngrx/store";
import { BaseFeatureState, featureSelector } from "@apto-base-frontend/store/feature";
import { ContentSnippet } from "@apto-base-frontend/store/content-snippets/content-snippet.model";

export const selectContentSnippets = createSelector(featureSelector,
  (state: BaseFeatureState) => {
    return state.contentSnippets;
  }
);

export const selectContentSnippet = (path: string) => createSelector(featureSelector,
  (state: BaseFeatureState) => {
    const pathSegments = path.split('.');
    const layer = 1;

    const selectContentSnippet = (contentSnippets: ContentSnippet[], name: string, layer: number): ContentSnippet | null => {
      for (let i = 0; i < contentSnippets.length; i++) {
        const contentSnippet = contentSnippets[i];
        if (contentSnippet.name === name) {
          if (layer === pathSegments.length) {
            return contentSnippet;
          }

          if (contentSnippet.children) {
            layer++;
            return selectContentSnippet(contentSnippet.children, pathSegments[layer - 1], layer);
          }
        }
      }
      return null;
    }

    return  selectContentSnippet(state.contentSnippets.snippets, pathSegments[layer - 1], layer);
  }
);
