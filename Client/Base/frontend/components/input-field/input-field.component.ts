import { Component, forwardRef, Input, OnChanges, SimpleChanges } from '@angular/core';
import { ControlValueAccessor, FormControl, NG_VALUE_ACCESSOR } from '@angular/forms';
import { UntilDestroy } from '@ngneat/until-destroy';
import { debounceTime, delay, map } from 'rxjs';

@UntilDestroy()
@Component({
	selector: 'apto-input-field',
	templateUrl: './input-field.component.html',
	styleUrls: ['./input-field.component.scss'],
	providers: [
		{
			provide: NG_VALUE_ACCESSOR,
			// eslint-disable-next-line
			useExisting: forwardRef(() => InputFieldComponent),
			multi: true,
		},
	],
})
export class InputFieldComponent implements ControlValueAccessor, OnChanges {
	@Input()
	public name: string = '';

	@Input()
	public prefix: string | undefined;

	@Input()
	public suffix: string | undefined;

	@Input()
	public placeholder: string | undefined = '';

	@Input()
	public type: 'text' | 'integer' | 'float' | 'password' = 'text';

	@Input()
	public step: number = 1;

	@Input()
	public hint: string = '';

	@Input()
	public fullWidth: boolean = false;

	@Input()
	public width: string = '';

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

	public increase(): void {
		if (this.type === 'integer') {
      let previousValue = parseInt(this.formElement.value, 10) || 0;
			this.formElement.setValue(previousValue + this.step);
		}
		if (this.type === 'float') {
      let previousValue = parseInt(this.formElement.value, 10) || 0;
      this.formElement.setValue(previousValue + this.step);
		}
	}

	public decrease(): void {
    let previousValue = parseInt(this.formElement.value, 10) || 0;
    this.formElement.setValue(previousValue - this.step);
	}
}
