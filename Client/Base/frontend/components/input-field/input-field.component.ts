import { Component, forwardRef, Input } from '@angular/core';
import { ControlValueAccessor, FormControl, NG_VALUE_ACCESSOR } from '@angular/forms';
import { UntilDestroy } from '@ngneat/until-destroy';
import { debounceTime, distinctUntilChanged, map } from 'rxjs';

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
export class InputFieldComponent implements ControlValueAccessor {

  @Input() public name: string = '';
  @Input() public prefix: string | undefined;
  @Input() public suffix: string | undefined;
  @Input() public placeholder: string | undefined = '';
  @Input() public type: 'text' | 'integer' | 'float' | 'numeric' | 'password' = 'text';
  @Input() public hint: string = '';
  @Input() public enableClear = false;

  @Input() public step: number = 1;
  @Input() public increaseStep: number | undefined;
  @Input() public decreaseStep: number | undefined;
  @Input() public min: number | undefined;
  @Input() public max: number | undefined;

  @Input() public fullWidth: boolean = false;
  @Input() public width: string = '';

  public formElement = new FormControl();
  public disabled: boolean = false;

  public registerOnChange(fn: any): void {
    this.formElement.valueChanges
      .pipe(
        distinctUntilChanged(),
        debounceTime(100),
        map((value) => value)
      )
      .subscribe(fn);
  }

  public writeValue(obj: number | string | undefined): void {
    const inputValue = this.isNumeric() ? obj : obj?.toString();

    this.formElement.setValue(inputValue);
  }

  public registerOnTouched(fn: any): void {}

  /**
   * This is called only when from parent input is disabled
   *
   * example:
   * this.formElement.get('width').disable();
   *
   * @param isDisabled
   */
  public setDisabledState(isDisabled: boolean): void {
    this.disabled = isDisabled;

    // we need to do this and enable or disable the input with this rather than with [disabled] attribute to skipp Angular's warning
    if (this.disabled) {
      this.formElement.disable();
    } else {
      this.formElement.enable();
    }
  }

  /**
   * If on focus value is 0 then remove the value
   *
   * @param event
   * @protected
   */
  protected onInputFocus(event: Event): void {
    const inputElement = event.target as HTMLInputElement;

    if (parseInt(inputElement.value, 10) === 0) {
      inputElement.value = '';
      this.formElement.setValue(null);
    }
  }

	public increase(): void {
    if (this.disabled || !this.isNumeric()) {
      return;
    }

    let newValue = 0;
    const increaseStep = this.increaseStep ? this.increaseStep : this.step;

    if (this.isInteger()) {
      newValue = this.parseInputNumber() + increaseStep;
    } else {
      newValue = parseFloat((this.parseInputNumber() + increaseStep).toFixed(this.countDigitsAfterZero(this.step)));
    }

    if (this.max !== undefined && newValue > this.max) {
      return;
    }

    this.formElement.setValue(newValue);
  }

	public decrease(): void {
    if (this.disabled || !this.isNumeric()) {
      return;
    }

    let newValue = 0;
    const decreaseStep = this.decreaseStep ? this.decreaseStep : this.step;

    if (this.isInteger()) {
      newValue = this.parseInputNumber() - decreaseStep;
    } else {
      newValue = parseFloat((this.parseInputNumber() - decreaseStep).toFixed(this.countDigitsAfterZero(this.step)));
    }

    if (this.min !== undefined && newValue < this.min) {
      return;
    }

    this.formElement.setValue(newValue);
	}

  protected isNumeric(): boolean {
    return this.type === 'integer' || this.type === 'float' || this.type === 'numeric';
  }

  private isInteger(): boolean {
    return Number.isInteger(this.step);
  }

  private parseInputNumber(): number {
    if (this.isInteger()) {
      return parseInt(this.formElement.value, 10) || 0;
    }
    return parseFloat(this.formElement.value) || 0;
  }

  private countDigitsAfterZero(number): number {
    const decimalPart = number.toString().split('.')[1];
    return !decimalPart ? 0 : decimalPart.replace(/0*$/, '').length;
  }

  public clear(): void {
    this.formElement.setValue(null);
  }
}
