import { Component, Inject, OnInit } from '@angular/core';
import { FormControl, FormGroup, Validators } from '@angular/forms';
import { Store } from '@ngrx/store';
import { selectLoginError } from '@apto-base-frontend/store/frontend-user/frontend-user.selectors';
import { selectContentSnippet } from '@apto-base-frontend/store/content-snippets/content-snippets.selectors';
import {
  updatePassword,
  updatePasswordSuccess,
} from '@apto-base-frontend/store/frontend-user/frontend-user.actions';
import { MAT_DIALOG_DATA, MatDialogRef } from '@angular/material/dialog';
import { UntilDestroy, untilDestroyed } from '@ngneat/until-destroy';
import { Actions, ofType } from '@ngrx/effects';

@UntilDestroy()
@Component({
  selector: 'apto-update-password-form',
  templateUrl: './update-password-form.component.html',
  styleUrls: ['./update-password-form.component.scss']
})
export class UpdatePasswordFormComponent implements OnInit {
  public readonly contentSnippets$ = this.store.select(selectContentSnippet('plugins.frontendUsers'));
  public readonly loginError$ = this.store.select(selectLoginError);
  public formGroup = new FormGroup({
    password: new FormControl<string>(null, [Validators.required]),
    repeatPassword: new FormControl<string>(null, [Validators.required]),
    token: new FormControl<string>(null, [Validators.required]),
  });

  constructor(
    private store: Store,
    @Inject(MAT_DIALOG_DATA) public data: {token: string},
    private dialogRef: MatDialogRef<UpdatePasswordFormComponent>,
    private readonly actions$: Actions
  ) { }

  public ngOnInit(): void {
    this.formGroup.patchValue({ token: this.data.token });
  }

  public save(): void {
    if (!this.formGroup.valid) {
      return;
    }

    this.closeModalOnSuccess();
    this.store.dispatch(updatePassword({
      payload: {
        password: this.formGroup.get('password').getRawValue(),
        repeatPassword: this.formGroup.get('repeatPassword').getRawValue(),
        token: this.formGroup.get('token').getRawValue(),
      }
    }));
  }

  private closeModalOnSuccess(): void {
    if (this.dialogRef?.id) {
      this.actions$.pipe(
        ofType(updatePasswordSuccess),
        untilDestroyed(this)
      ).subscribe((result) => {
        this.dialogRef.close();
      });
    }
  }
}
