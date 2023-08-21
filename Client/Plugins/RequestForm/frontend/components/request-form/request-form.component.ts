import { Component, EventEmitter, Output } from '@angular/core';
import { FormBuilder, FormControl, UntypedFormControl, UntypedFormGroup, Validators } from '@angular/forms';
import {select, Store} from '@ngrx/store';
import {selectContentSnippet} from "@apto-base-frontend/store/content-snippets/content-snippets.selectors";
import { TranslatedValue } from '@apto-base-core/store/translated-value/translated-value.model';

interface Gender {
  surrogateId: string,
  id: string,
  name: TranslatedValue,
  isDefault: boolean,
  aptoPrices: [],
};

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
  }

	public onSendRequestForm(): void {
		this.sendRequestForm.emit();
	}
}
