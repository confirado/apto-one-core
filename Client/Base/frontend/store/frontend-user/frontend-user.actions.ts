import { createAction, props } from "@ngrx/store";
import { FrontendUser } from '@apto-base-frontend/store/frontend-user/frontend-user.model';

export enum FrontendUserActionTypes {
  Login                 = '[FrontendUser] Login',
  LoginSuccess          = '[FrontendUser] Login Success',
  LoginError            = '[FrontendUser] Login Error',
  Logout                = '[FrontendUser] Logout',
  LogoutSuccess         = '[FrontendUser] Logout Success',
  LoginStatus           = '[FrontendUser] Login Status',
  LoginStatusSuccess    = '[FrontendUser] Login Status Success',
  ResetPassword         = '[FrontendUser] ResetPassword',
  ResetPasswordSuccess  = '[FrontendUser] ResetPasswordSuccess',
  UpdatePassword        = '[FrontendUser] UpdatePassword',
  UpdatePasswordSuccess = '[FrontendUser] UpdatePasswordSuccess',
}

export const login = createAction(
  FrontendUserActionTypes.Login,
  props<{ payload: { username: string, password: string }; }>()
);

export const loginSuccess = createAction(
  FrontendUserActionTypes.LoginSuccess,
  props<{ payload: { currentUser: FrontendUser | null }; }>()
);

export const loginError = createAction(
  FrontendUserActionTypes.LoginError
);

export const logout = createAction(
  FrontendUserActionTypes.Logout
);

export const logoutSuccess = createAction(
  FrontendUserActionTypes.LogoutSuccess
);

export const checkLoginStatus = createAction(
  FrontendUserActionTypes.LoginStatus
);

export const checkLoginStatusSuccess = createAction(
  FrontendUserActionTypes.LoginStatusSuccess,
  props<{ payload: { currentUser: FrontendUser | null }; }>()
);

export const resetPassword = createAction(
  FrontendUserActionTypes.ResetPassword,
  props<{ payload: { email: string }; }>()
);

export const resetPasswordSuccess = createAction(
  FrontendUserActionTypes.ResetPasswordSuccess
);

export const updatePassword = createAction(
  FrontendUserActionTypes.UpdatePassword,
  props<{
    payload: {
      password: string;
      repeatPassword: string;
      token: string;
    };
  }>()
);

export const updatePasswordSuccess = createAction(
  FrontendUserActionTypes.UpdatePasswordSuccess
);
