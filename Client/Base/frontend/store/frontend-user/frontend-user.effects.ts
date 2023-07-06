import { Injectable } from '@angular/core';
import { Actions, createEffect, ofType } from '@ngrx/effects';
import { map, switchMap } from 'rxjs/operators';
import { FrontendUserRepository } from '@apto-base-frontend/store/frontend-user/frontend-user.repository';
import {
  login, loginSuccess, loginError,
  logout, logoutSuccess,
  checkLoginStatus, checkLoginStatusSuccess
} from '@apto-base-frontend/store/frontend-user/frontend-user.actions';
import { catchError, of } from 'rxjs';

@Injectable()
export class FrontendUserEffects {
  public constructor(
    private actions$: Actions,
    private frontendUserRepository: FrontendUserRepository
  ) {}

  public login$ = createEffect(() =>
    this.actions$.pipe(
      ofType(login),
      switchMap((action) => {
        return this.frontendUserRepository.login(
          action.payload.username,
          action.payload.password
        ).pipe(
          map((result) => {
            return loginSuccess({
              payload: {
                currentUser: result
              }
            });
          }),
          catchError((response: any) => {
            return of(loginError());
          })
        );
      })
    )
  );

  public logout$ = createEffect(() =>
    this.actions$.pipe(
      ofType(logout),
      switchMap((action) => {
        return this.frontendUserRepository.logout();
      }),
      map((result) => {
        return logoutSuccess();
      })
    )
  );

  public checkLoginStatus$ = createEffect(() =>
    this.actions$.pipe(
      ofType(checkLoginStatus),
      switchMap((action) => {
        return this.frontendUserRepository.status();
      }),
      map((result) => {
        return checkLoginStatusSuccess({
          payload: {
            currentUser: result
          }
        });
      })
    )
  );
}
