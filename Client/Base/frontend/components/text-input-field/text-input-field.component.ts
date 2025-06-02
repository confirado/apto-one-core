import { Component, forwardRef, Input, OnChanges, SimpleChanges } from '@angular/core';
import { ControlValueAccessor, FormControl, NG_VALUE_ACCESSOR } from '@angular/forms';
import { UntilDestroy } from '@ngneat/until-destroy';
import { debounceTime, delay } from 'rxjs';

@UntilDestroy()
@Component({
	selector: 'apto-text-input-field',
	templateUrl: './text-input-field.component.html',
	styleUrls: ['./text-input-field.component.scss'],
	providers: [
		{
			provide: NG_VALUE_ACCESSOR,
			// eslint-disable-next-line
			useExisting: forwardRef(() => TextInputFieldComponent),
			multi: true,
		},
	],
})
export class TextInputFieldComponent implements ControlValueAccessor, OnChanges {
	@Input()
	public name: string = '';

	@Input()
	public hint: string = '';

	@Input()
	public placeholder: string = '';

	@Input()
	public width: string = '';

  @Input()
  public rows: number = 4;

  @Input()
  public inputError: boolean = false;

	public formElement = new FormControl();

	public value: string | undefined;

	public onChange: Function | undefined;

	public ngOnChanges(changes: SimpleChanges): void {}

	public writeValue(obj: number | string | undefined): void {
		this.formElement.setValue(obj?.toString());
	}

	public registerOnChange(fn: any): void {
		// TODO: Not good... can cause a loop
		this.formElement.valueChanges.pipe(delay(0), debounceTime(100)).subscribe(fn);
	}

	public registerOnTouched(fn: any): void {}

	public setDisabledState?(isDisabled: boolean): void {}
}
