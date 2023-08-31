import { Component, EventEmitter, Output } from '@angular/core';
import { FormBuilder, Validators } from '@angular/forms';
import { Store } from '@ngrx/store';
import { combineLatest } from 'rxjs';
import { translate } from '@apto-base-core/store/translated-value/translated-value.model';
import { selectContentSnippet } from '@apto-base-frontend/store/content-snippets/content-snippets.selectors';
import { selectCurrentUser } from '@apto-base-frontend/store/frontend-user/frontend-user.selectors';
import { selectLocale } from '@apto-base-frontend/store/language/language.selectors';
import { FieldsWhenUserIsLoggedInEnum, Gender } from '@apto-request-form-frontend/store/request-form.model';

@Component({
	selector: 'apto-request-form',
	templateUrl: './request-form.component.html',
	styleUrls: ['./request-form.component.scss'],
})
export class RequestFormComponent {
  @Output()
  public requestFormChanged = new EventEmitter();

  @Output()
  public sendRequestForm = new EventEmitter();

  public readonly csRequestForm$ = this.store.select(selectContentSnippet('plugins.requestForm'));
  public readonly csGenders$ = this.store.select(selectContentSnippet('plugins.requestForm.aptoSummary.values.gender'));
  public readonly csFieldsWhenUserIsLoggedIn$ = this.store.select(selectContentSnippet('plugins.requestForm.aptoSummary.fieldsWhenUserIsLoggedIn'));
  public readonly currentUser$ = this.store.select(selectCurrentUser);
  public readonly locale$ = this.store.select(selectLocale);

  public diverse: Gender = {
    surrogateId: 'd',
    id: 'd',
    name: { de_DE: 'keine Angabe' },
    isDefault: false,
    aptoPrices: [],
  };

	public herr: Gender = {
		surrogateId: 'm',
		id: 'm',
		name: { de_DE: 'Herr' },
		isDefault: false,
		aptoPrices: [],
	};

	public frau: Gender = {
		surrogateId: 'f',
		id: 'f',
		name: { de_DE: 'Frau' },
		isDefault: false,
		aptoPrices: [],
	};

  public requestForm = this._formBuilder.group({
    customerNumber: [''],
    gender: ['', Validators.required],
    name: ['', Validators.required],
    email: ['', [Validators.required, Validators.email]],
    phone: [''],
    company: [''],
    street: [''],
    zipCode: [''],
    city: [''],
    message: [''],
    declarationOfConsent: [false, Validators.requiredTrue]
  });

  public fieldsWhenUserIsLoggedIn: FieldsWhenUserIsLoggedInEnum = FieldsWhenUserIsLoggedInEnum.ALL;

	public constructor(private store: Store, private _formBuilder: FormBuilder) {
    this.requestForm.valueChanges.subscribe(() => {
      this.requestFormChanged.emit(this.requestForm);
    });

    this.csGenders$.subscribe((next) => {
      next.children.forEach((gender) => {
        switch (gender.name) {
          case 'di': {
            this.diverse.name = gender.content;
            break;
          }
          case 'mr': {
            this.herr.name = gender.content;
            break;
          }
          case 'ms': {
            this.frau.name = gender.content;
            break;
          }
        }
      });
    });

    combineLatest([this.csFieldsWhenUserIsLoggedIn$, this.currentUser$, this.locale$]).subscribe(([csFieldsWhenUserIsLoggedIn, currentUser ,locale]) => {
      if (!currentUser) {
        this.requestForm.get('customerNumber').setValue('');
        this.fieldsWhenUserIsLoggedIn = FieldsWhenUserIsLoggedInEnum.ALL;
        return;
      }

      if (translate(csFieldsWhenUserIsLoggedIn.content, locale) === FieldsWhenUserIsLoggedInEnum.ONLY_MESSAGE) {
        this.requestForm.get('gender').setValue('d');
        this.fieldsWhenUserIsLoggedIn = FieldsWhenUserIsLoggedInEnum.ONLY_MESSAGE;
      }

      this.requestForm.get('email').setValue(currentUser.email);
      this.requestForm.get('name').setValue(currentUser.userName);
      this.requestForm.get('customerNumber').setValue(currentUser.customerNumber);
    });
  }

  public isFieldVisible(): boolean {
    return this.fieldsWhenUserIsLoggedIn !== FieldsWhenUserIsLoggedInEnum.ONLY_MESSAGE;
  }

	public onSendRequestForm(): void {
		this.sendRequestForm.emit();
	}
}
