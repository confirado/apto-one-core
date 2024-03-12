import { Component, Input } from '@angular/core';
import { FormControl, FormGroup, Validators } from '@angular/forms';
import { selectContentSnippet } from '@apto-base-frontend/store/content-snippets/content-snippets.selectors';
import { Store } from '@ngrx/store';
import { MatDialogRef } from '@angular/material/dialog';
import { resetPassword, resetPasswordSuccess } from '@apto-base-frontend/store/frontend-user/frontend-user.actions';
import { Actions, ofType } from '@ngrx/effects';
import { UntilDestroy, untilDestroyed } from '@ngneat/until-destroy';

@UntilDestroy()
@Component({
  selector: 'apto-forgot-password',
  templateUrl: './forgot-password.component.html',
  styleUrls: ['./forgot-password.component.scss']
})
export class ForgotPasswordComponent {
  public readonly contentSnippets$ = this.store.select(selectContentSnippet('plugins.frontendUsers'));
  public formGroup = new FormGroup({
    email: new FormControl<string>(null, [Validators.required, Validators.email]),
  });
  constructor(
    private store: Store,
    private dialogRef: MatDialogRef<ForgotPasswordComponent>,
    private readonly actions$: Actions
  ) {}

  public save(): void {
    if (!this.formGroup.valid) {
      return;
    }

    this.closeModalOnSuccess();
    this.store.dispatch(resetPassword({
      payload: {
        email: this.formGroup.get('email').getRawValue(),
      }
    }));
  }

  private closeModalOnSuccess(): void {
    if (this.dialogRef?.id) {
      this.actions$.pipe(
        ofType(resetPasswordSuccess),
        untilDestroyed(this)
      ).subscribe((result) => {
        this.dialogRef.close();
      });
    }
  }
}
