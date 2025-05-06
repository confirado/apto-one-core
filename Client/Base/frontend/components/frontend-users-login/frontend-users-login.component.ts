import { Component } from '@angular/core';
import { selectContentSnippet } from '@apto-base-frontend/store/content-snippets/content-snippets.selectors';
import { selectLocale } from '@apto-base-frontend/store/language/language.selectors';
import { environment } from '@apto-frontend/src/environments/environment';
import { Store } from '@ngrx/store';
import { UntilDestroy, untilDestroyed } from '@ngneat/until-destroy';
import {
  selectIsLoggedIn,
  selectLoginError,
} from '@apto-base-frontend/store/frontend-user/frontend-user.selectors';
import { login } from '@apto-base-frontend/store/frontend-user/frontend-user.actions';
import { translate } from '@apto-base-core/store/translated-value/translated-value.model';
import { FormControl, FormGroup } from '@angular/forms';
import { MatDialogRef } from '@angular/material/dialog';
import { ContentSnippet } from '@apto-base-frontend/store/content-snippets/content-snippet.model';

@UntilDestroy()
@Component({
	selector: 'apto-frontend-users-login',
	templateUrl: './frontend-users-login.component.html',
	styleUrls: ['./frontend-users-login.component.scss'],
})
export class FrontendUsersLoginComponent {
  public readonly contentSnippets$ = this.store.select(selectContentSnippet('plugins.frontendUsers'));
  public readonly loginError$ = this.store.select(selectLoginError);
  public loginActive: boolean;
  public formGroup = new FormGroup({
    username: new FormControl<string>('', { nonNullable: true }),
    password: new FormControl<string>('', { nonNullable: true }),
  });

  private locale: string = environment.defaultLocale;

  constructor(private store: Store, private dialogRef: MatDialogRef<FrontendUsersLoginComponent>) {
    this.store.select(selectLocale).pipe(untilDestroyed(this)).subscribe((locale: string) => {
      if (locale !== null) {
        this.locale = locale;
      }
    });

    this.store.select(selectIsLoggedIn).pipe(untilDestroyed(this)).subscribe((isLogged: boolean) => {
      if (isLogged) {
        this.dialogRef.close();
      }
    });

    this.store.select(selectContentSnippet('plugins.frontendUsers.loginActive')).pipe(untilDestroyed(this)).subscribe((snippet: ContentSnippet) => {
      if (!snippet) {
        this.loginActive = false;
      } else {
        this.loginActive = translate(snippet.content, this.locale) === 'active';
      }
    });
  }

  public login(): void {
    this.store.dispatch(login({
      payload: {
        username: this.formGroup.get('username').getRawValue(),
        password: this.formGroup.get('password').getRawValue()
      }
    }));
  }

  public openPasswordResetModal(): void {
    if (this.dialogRef.close !== undefined) {
      this.dialogRef.close({
        openForgotModal: true
      });
    } else {
      this.dialogService.openCustomDialog(ForgotPasswordComponent, DialogSizesEnum.md);
    }
  }
}
