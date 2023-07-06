import { Action, createReducer, on } from '@ngrx/store';
import { FrontendUser } from '@apto-base-frontend/store/frontend-user/frontend-user.model';
import {
  checkLoginStatusSuccess,
  login, loginError,
  loginSuccess,
  logoutSuccess,
} from '@apto-base-frontend/store/frontend-user/frontend-user.actions';

export interface FrontendUserState {
  currentUser: FrontendUser | null,
  loginError: boolean
}

export const frontendUserInitialState: FrontendUserState = {
  currentUser: null,
  loginError: false
};

const _frontendUserReducer = createReducer(
  frontendUserInitialState,
  on(loginSuccess, (state, action) => {
    return {
      ...state,
      currentUser: action.payload.currentUser,
      loginError: false
    };
  }),
  on(loginError, (state, action) => {
    return {
      ...state,
      currentUser: null,
      loginError: true
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
