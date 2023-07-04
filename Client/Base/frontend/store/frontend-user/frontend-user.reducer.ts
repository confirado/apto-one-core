import { Action, createReducer, on } from '@ngrx/store';
import { FrontendUser } from '@apto-base-frontend/store/frontend-user/frontend-user.model';
import { initShop } from '@apto-base-frontend/store/shop/shop.actions';
import {
  checkLoginStatusSuccess,
  login,
  loginSuccess,
  logoutSuccess,
} from '@apto-base-frontend/store/frontend-user/frontend-user.actions';

export interface FrontendUserState {
  currentUser: FrontendUser | null
}

export const frontendUserInitialState: FrontendUserState = {
  currentUser: null
};

const _frontendUserReducer = createReducer(
  frontendUserInitialState,
  on(loginSuccess, (state, action) => {
    return {
      ...state,
      currentUser: action.payload.currentUser
    };
  }),
  on(logoutSuccess, (state, action) => {
    return {
      ...state,
      currentUser: null
    };
  }),
  on(checkLoginStatusSuccess, (state, action) => {
    return {
      ...state,
      currentUser: action.payload.currentUser
    };
  })
);

export function frontendUserReducer(state: FrontendUserState | undefined, action: Action) {
  return _frontendUserReducer(state, action);
}
