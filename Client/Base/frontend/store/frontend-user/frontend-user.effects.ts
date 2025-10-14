import { Injectable } from '@angular/core';
import { Actions, createEffect, ofType } from '@ngrx/effects';
import { map, switchMap, withLatestFrom } from 'rxjs/operators';
import { FrontendUserRepository } from '@apto-base-frontend/store/frontend-user/frontend-user.repository';
import {
  login, loginSuccess, loginError,
  logout, logoutSuccess,
  checkLoginStatus, checkLoginStatusSuccess, resetPassword, resetPasswordSuccess, updatePassword, updatePasswordSuccess,
} from '@apto-base-frontend/store/frontend-user/frontend-user.actions';
import { catchError, of } from 'rxjs';
import { DialogService } from '@apto-catalog-frontend/components/common/dialogs/dialog-service';
import { selectLocale } from '@apto-base-frontend/store/language/language.selectors';
import { environment } from '@apto-frontend/src/environments/environment';
import { selectContentSnippet } from '@apto-base-frontend/store/content-snippets/content-snippets.selectors';
import { DialogTypesEnum } from '@apto-frontend/src/configs-static/dialog-types-enum';
import { Store } from '@ngrx/store';
import {
  ConfirmationDialogComponent
} from '@apto-catalog-frontend-confirmation-dialog';
import { DialogSizesEnum } from '@apto-frontend/src/configs-static/dialog-sizes-enum';
import { ContentSnippet } from '@apto-base-frontend/store/content-snippets/content-snippet.model';
import { translate } from '@apto-base-core/store/translated-value/translated-value.model';

@Injectable()
export class FrontendUserEffects {
  public constructor(
    private actions$: Actions,
    private frontendUserRepository: FrontendUserRepository,
    private dialogService: DialogService,
    private store$: Store,
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

  public resetPassword$ = createEffect(() =>
    this.actions$.pipe(
      ofType(resetPassword),
      switchMap((action) =>
        this.frontendUserRepository.resetPassword(action.payload.email)
          .pipe(map(() => resetPasswordSuccess()))
      )
    )
  );

  public resetPasswordSuccess$ = createEffect(
    () =>
      this.actions$.pipe(
        ofType(resetPasswordSuccess),
        withLatestFrom(
          this.store$.select(selectLocale).pipe(map((l) => l || environment.defaultLocale)),
          this.store$.select(selectContentSnippet('plugins.frontendUsers.resetSuccess'))
        ),
        map(([action, locale, snippet]: [null, string, ContentSnippet]) => {
          this.dialogService.openCustomDialog(ConfirmationDialogComponent, DialogSizesEnum.md, {
            type: DialogTypesEnum.CONFIRM,
            hideIcon: true,
            descriptionText: translate(snippet.content, locale)
          });
        })
      ),
    { dispatch: false }
  );

  public updatePassword$ = createEffect(() =>
    this.actions$.pipe(
      ofType(updatePassword),
      switchMap((action) => {
        return this.frontendUserRepository.updatePassword(action.payload.password, action.payload.repeatPassword, action.payload.token);
      }),
      map(() => {
        return updatePasswordSuccess();
      })
    )
  );

  public updatePasswordSuccess$ = createEffect(
    () =>
      this.actions$.pipe(
        ofType(updatePasswordSuccess),
        withLatestFrom(
          this.store$.select(selectLocale).pipe(map((l) => l || environment.defaultLocale)),
          this.store$.select(selectContentSnippet('plugins.frontendUsers.updatePasswordSuccess'))
        ),
        map(([action, locale, snippet]: [null, string, ContentSnippet]) => {
          this.dialogService.openCustomDialog(ConfirmationDialogComponent, DialogSizesEnum.md, {
            type: DialogTypesEnum.CONFIRM,
            hideIcon: true,
            descriptionText: translate(snippet.content, locale)
          });
        })
      ),
    { dispatch: false }
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
