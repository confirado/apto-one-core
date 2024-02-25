import { Component, Inject, OnInit } from '@angular/core';
import { selectContentSnippet } from '@apto-base-frontend/store/content-snippets/content-snippets.selectors';
import { FormControl, FormGroup, Validators } from '@angular/forms';
import { Store } from '@ngrx/store';
import { Actions, ofType } from '@ngrx/effects';
import { updatePassword, updatePasswordSuccess } from '@apto-base-frontend/store/frontend-user/frontend-user.actions';
import { UntilDestroy, untilDestroyed } from '@ngneat/until-destroy';
import { ActivatedRoute, Router } from '@angular/router';

@UntilDestroy()
@Component({
  selector: 'apto-update-password',
  templateUrl: './update-password.component.html',
  styleUrls: ['./update-password.component.scss']
})
export class UpdatePasswordComponent implements OnInit {

  public readonly contentSnippets$ = this.store.select(selectContentSnippet('plugins.frontendUsers'));
  public formGroup = new FormGroup({
    password: new FormControl<string>(null, [Validators.required]),
    repeatPassword: new FormControl<string>(null, [Validators.required]),
    token: new FormControl<string>(null, [Validators.required]),
  });

  constructor(
    private store: Store,
    private activatedRoute: ActivatedRoute,
    private router: Router,
    private readonly actions$: Actions
  ) { }

  public ngOnInit(): void {
    this.activatedRoute.params.subscribe((params: { token: string }) => {
      this.formGroup.patchValue({ token: params.token });
    });
  }

  public save(): void {
    if (!this.formGroup.valid) {
      return;
    }

    this.goToMainPageOnSuccess();
    this.store.dispatch(updatePassword({
      payload: {
        password: this.formGroup.get('password').getRawValue(),
        repeatPassword: this.formGroup.get('repeatPassword').getRawValue(),
        token: this.formGroup.get('token').getRawValue(),
      }
    }));
  }

  private goToMainPageOnSuccess(): void {
    this.actions$.pipe(
      ofType(updatePasswordSuccess),
      untilDestroyed(this)
    ).subscribe(() => {
      this.router.navigateByUrl('/');
    });
  }
}
