import { Component, forwardRef, Input, OnInit } from '@angular/core';
import { ControlValueAccessor, FormControl, NG_VALUE_ACCESSOR } from '@angular/forms';
import { debounceTime, distinctUntilChanged, map } from 'rxjs';
import { UntilDestroy } from '@ngneat/until-destroy';

// https://developer.mozilla.org/en-US/docs/Web/HTML/Element/input/number

@UntilDestroy()
@Component({
  selector: 'apto-selectable-value-range',
  templateUrl: './selectable-value-range.component.html',
  styleUrls: ['./selectable-value-range.component.scss'],
  providers: [
    {
      provide: NG_VALUE_ACCESSOR,
      // eslint-disable-next-line
      useExisting: forwardRef(() => SelectableValueRangeComponent),
      multi: true,
    },
  ],
})
export class SelectableValueRangeComponent implements ControlValueAccessor, OnInit {

  @Input() public name: string = '';
  @Input() public prefix: string | undefined;
  @Input() public suffix: string | undefined;
  @Input() public hint: string = '';
  @Input() public width: string = '';
  @Input() public fullWidth: boolean = false;
  @Input() public placeholder: string | undefined = '';

  @Input() public step: number = 1;
  @Input() public min: number | undefined = 1;
  @Input() public max: number | undefined;

  public formElement = new FormControl({ value: undefined, disabled: false });
  public disabled: boolean = false;

  // eslint-disable-next-line @typescript-eslint/no-empty-function
  public ngOnInit(): void {}

  public registerOnChange(fn: any): void {
    this.formElement.valueChanges
      .pipe(
        distinctUntilChanged(),
        debounceTime(500),
        map((value) => value)
      )
      .subscribe(fn);
  }

  public writeValue(obj: number | undefined): void {
    this.formElement.setValue(obj);
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

    // we need to do this and enable or disable the input with this rather than with [disabled] attribute in order to skipp angular's warning
    if (this.disabled) {
      this.formElement.disable();
    } else {
      this.formElement.enable();
    }
  }

  protected increase(): void {
    if (this.disabled) {
      return;
    }

    if (Number.isInteger(this.step)) {
      this.formElement.setValue(this.parseInputNumber() + this.step);
    } else {
      this.formElement.setValue(parseFloat((this.parseInputNumber() + this.step).toFixed(this.countDigitsAfterZero(this.step))));
    }
  }

  protected decrease(): void {
    if (this.disabled) {
      return;
    }

    if (Number.isInteger(this.step)) {
      this.formElement.setValue(this.parseInputNumber() - this.step);
    } else {
      this.formElement.setValue(parseFloat((this.parseInputNumber() - this.step).toFixed(this.countDigitsAfterZero(this.step))));
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

  private parseInputNumber(): number {
    if (Number.isInteger(this.step)) {
      return parseInt(this.formElement.value, 10) || 0;
    }
    return parseFloat(this.formElement.value) || 0;
  }

  private countDigitsAfterZero(number): number {
    const decimalPart = number.toString().split('.')[1];
    return !decimalPart ? 0 : decimalPart.replace(/0*$/, '').length;
  }
}
