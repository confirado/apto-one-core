import { Component, forwardRef, Input, OnInit } from '@angular/core';
import { ControlValueAccessor, FormControl, NG_VALUE_ACCESSOR } from '@angular/forms';
import { debounceTime, distinctUntilChanged, map } from 'rxjs';
import { UntilDestroy } from '@ngneat/until-destroy';

// https://developer.mozilla.org/en-US/docs/Web/HTML/Element/input/text

@UntilDestroy()
@Component({
  selector: 'apto-selectable-value-text',
  templateUrl: './selectable-value-text.component.html',
  styleUrls: ['./selectable-value-text.component.scss'],
  providers: [
    {
      provide: NG_VALUE_ACCESSOR,
      // eslint-disable-next-line
      useExisting: forwardRef(() => SelectableValueTextComponent),
      multi: true,
    },
  ],
})
export class SelectableValueTextComponent implements ControlValueAccessor, OnInit {

  @Input() public name: string = '';
  @Input() public prefix: string | undefined;
  @Input() public suffix: string | undefined;
  @Input() public hint: string = '';
  @Input() public width: string = '';
  @Input() public fullWidth: boolean = false;
  @Input() public placeholder: string | undefined = '';

  @Input() public minlength: number | undefined = undefined;
  @Input() public maxlength: number | undefined = undefined;
  @Input() public required: boolean = false;

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

  public writeValue(obj: string | undefined): void {
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
}
