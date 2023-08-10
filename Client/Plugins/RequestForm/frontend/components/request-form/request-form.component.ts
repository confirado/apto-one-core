import { Component, EventEmitter, Output } from '@angular/core';
import { FormBuilder, FormControl, UntypedFormControl, UntypedFormGroup, Validators } from '@angular/forms';
import {select, Store} from '@ngrx/store';
import {selectContentSnippet} from "@apto-base-frontend/store/content-snippets/content-snippets.selectors";

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

  public diverse = {
    surrogateId: 'd',
    id: 'd',
    name: { de_DE: 'keine Angabe' },
    isDefault: false,
    aptoPrices: [],
  };

	public herr = {
		surrogateId: 'm',
		id: 'm',
		name: { de_DE: 'Herr' },
		isDefault: false,
		aptoPrices: [],
	};

	public frau = {
		surrogateId: 'f',
		id: 'f',
		name: { de_DE: 'Frau' },
		isDefault: false,
		aptoPrices: [],
	};

  public requestForm = this._formBuilder.group({
    gender: [null, Validators.required],
    name: [null, Validators.required],
    email: [null, [Validators.required, Validators.email]],
    phone: [null],
    company: [null],
    street: [null],
    zipCode: [null],
    city: [null],
    message: [null],
    declarationOfConsent: [null, Validators.requiredTrue]
  });

	public constructor(private store: Store, private _formBuilder: FormBuilder) {
    this.requestForm.valueChanges.subscribe(() => {
      this.requestFormChanged.emit(this.requestForm);
    });
  }

	public onSendRequestForm(): void {
		this.sendRequestForm.emit();
	}

  public readonly requestForm$ = this.store.select(selectContentSnippet('plugins.requestForm'));
}
