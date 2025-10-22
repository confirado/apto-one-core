import { Component, forwardRef, Input, OnChanges, SimpleChanges } from '@angular/core';
import { ControlValueAccessor, FormControl, NG_VALUE_ACCESSOR } from '@angular/forms';
import { setQuantity } from '@apto-catalog-frontend-configuration-actions';
import { selectQuantity } from '@apto-catalog-frontend-configuration-selectors';
import { Store } from '@ngrx/store';
import { debounceTime, delay, map } from 'rxjs';

@Component({
	selector: 'apto-quantity-input',
	templateUrl: './quantity-input.component.html',
	styleUrls: ['./quantity-input.component.scss'],
	providers: [
		{
			provide: NG_VALUE_ACCESSOR,
			// eslint-disable-next-line
			useExisting: forwardRef(() => QuantityInputComponent),
			multi: true,
		},
	],
})
export class QuantityInputComponent implements ControlValueAccessor, OnChanges {
	@Input()
	public name: string = '';

	@Input()
	public justText: boolean = false;

	@Input()
	public prefix: string | undefined;

	@Input()
	public suffix: string | undefined;

	@Input()
	public placeholder: string = '';

	@Input()
	public type: 'text' | 'integer' | 'float' = 'text';

	@Input()
	public step: number = 1;

	@Input()
	public hint: string = '';

	public formElement = new FormControl();

	public value: string | undefined;

	public onChange: Function | undefined;

	public ngOnChanges(changes: SimpleChanges): void {}

	public writeValue(obj: number | string | undefined): void {
		this.formElement.setValue(obj?.toString());
	}

	public registerOnChange(fn: any): void {
		// TODO: Not good... can cause a loop
		this.formElement.valueChanges
			.pipe(
				map((value) => {
					if (this.type === 'integer') {
						return parseInt(value, 10);
					}
					if (this.type === 'float') {
						return parseFloat(value);
					}
					return value;
				}),
				delay(0),
				debounceTime(100)
			)
			.subscribe(fn);
	}

	public registerOnTouched(fn: any): void {}

	public setDisabledState?(isDisabled: boolean): void {}

	public quantity$ = this.store.select(selectQuantity);

	public constructor(private store: Store) {}

	public setQuantity(quantity: number): void {
		if (quantity > 0) {
			this.store.dispatch(setQuantity({ quantity }));
		}
	}
}
