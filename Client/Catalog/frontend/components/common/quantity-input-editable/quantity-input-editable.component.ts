import { Component, ElementRef, forwardRef, Input, OnChanges, OnInit, SimpleChanges, ViewChild } from '@angular/core';
import { ControlValueAccessor, NG_VALUE_ACCESSOR } from '@angular/forms';
import { setQuantity, updateConfigurationState } from '@apto-catalog-frontend/store/configuration/configuration.actions';
import { selectQuantity } from '@apto-catalog-frontend/store/configuration/configuration.selectors';
import { Store } from '@ngrx/store';
import { debounceTime, Subject } from 'rxjs';
import { MatSnackBar } from '@angular/material/snack-bar';
import { ParameterStateTypes } from '@apto-catalog-frontend/store/configuration/configuration.model';

@Component({
	selector: 'apto-quantity-input-editable',
	templateUrl: './quantity-input-editable.component.html',
	styleUrls: ['./quantity-input-editable.component.scss'],
	providers: [
		{
			provide: NG_VALUE_ACCESSOR,
			// eslint-disable-next-line
			useExisting: forwardRef(() => QuantityInputEditableComponent),
			multi: true,
		}
	],
})
export class QuantityInputEditableComponent implements ControlValueAccessor, OnChanges, OnInit {
	@Input()
	public name: string = '';

	@Input()
	public step: number = 1;

  @Input()
  public width: string = '';

  @Input()
  public size: 'small' | 'middle' | 'big' = 'small';

  @Input()
  public minValue = 1;

  @Input()
  public maxValue: number;

  @ViewChild('inputRef', { static: false }) inputRef: ElementRef | undefined;

	public ngOnChanges(changes: SimpleChanges): void { }

  /**
   * This will write the value to the view if the value changes occur on the model programmatically
   *
   * @param value
   */
	public writeValue(value: number): void {}

  /**
   * When the value in the UI is changed, this method will invoke a callback function
   *
   * @param fn
   */
	public registerOnChange(fn: any): void {}
	public registerOnTouched(fn: any): void {}
	public setDisabledState?(isDisabled: boolean): void {}

	public quantity$ = this.store.select(selectQuantity);
  private userInput$ = new Subject<any>();
  public inputValue: number = 1;

  public constructor(
    private store: Store,
    private matSnackBar: MatSnackBar
  ) { }

  public ngOnInit():void {
    this.quantity$.subscribe((value) => {
      this.inputValue = value;
    });

    this.userInput$
      .pipe(
        debounceTime(300),
        // distinctUntilChanged()
      )
      .subscribe((value) => {
        this.store.dispatch(setQuantity({ quantity: value }));
      });
  }

  /**
   * We want to prevent users from adding wrong values in the input field
   *
   * @param event
   */
  public onKeyDown(event: KeyboardEvent):void {

    // If the user has typed some not allowed character, just skipp it silently
    if (!this.isAllowedCharacter(event.key)) {
      event.preventDefault();
      return;
    }

    const inputValue = this.inputRef.nativeElement.value;
    const { selectionStart, selectionEnd } = this.inputRef.nativeElement;

    /*  At this point, we can have only either numeric characters or allowed characters like backspace, delete and so on.
        If the user has pressed backspace, delete or arrow buttons, we let them do their job.
        We are interested in handling only numeric characters   */
    if (this.isNumericCharacter(event.key)) {

      /*  We need to check also one spacial case, when user has selected number(s) in input with mouse, and types a new number
          in this case, we want to delete the selected number(s) and put typed number instead like expected.
          In order to do that, we need to know what will be the input after user has typed the number. We can not know
          it because we are in onKeyDown method not in keyUp. So we need to "take a look into the future" and
          calculate the resulting number beforehand  */
      const modifiedValue = inputValue.substring(0, selectionStart) + event.key + inputValue.substring(selectionEnd);
      const quantity = parseInt(modifiedValue, 10);

      // We prevent the user from typing a number out of the allowed range (they are set in backend).
      if (!this.isQuantityInRange(quantity)) {
        if (this.isQuantityValid(quantity)) {
          if (quantity < this.minValue && this.isQuantityValid(this.minValue)) {
            this.userInput$.next(this.minValue);
            this.inputRef.nativeElement.value = this.minValue;
          } else if (quantity > this.maxValue && this.isQuantityValid(this.maxValue)) {
            this.userInput$.next(this.maxValue);
            this.inputRef.nativeElement.value = this.maxValue;
          }
        }

        this.handleErrorCase(quantity);
        event.preventDefault();
      }
    }
  }

  /**
   * Called when user types value in input field
   *
   * @param event
   */
  public onKeyUp(event: KeyboardEvent):void {
    const inputValue = this.inputRef.nativeElement.value;
    const quantity = parseInt(inputValue, 10);

    if (this.isQuantityValid(quantity) && this.isQuantityInRange(quantity)) {
      this.userInput$.next(quantity);
    }
  }

  /**
   * Called when user clicks up or down arrows of the element
   *
   * @param quantity
   */
  public setQuantity(quantity: number): void {
    if (this.isQuantityInRange(quantity) && this.isQuantityValid(quantity)) {
      this.store.dispatch(setQuantity({ quantity: quantity }));

      this.store.dispatch(updateConfigurationState({
        updates: {
          parameters: [{
            name: ParameterStateTypes.QUANTITY,
            value: quantity
          }]
      }
      }));
    }

    if (!this.isQuantityInRange(quantity) && typeof quantity === 'number') {
      this.handleErrorCase(quantity);
    }
  }

  private isQuantityValid(quantity: any): boolean {
    return !isNaN(quantity) && typeof quantity === 'number' && quantity > 0;
  }

  private isQuantityInRange(quantity): boolean {
    // max value 0 means infinite in that case
    return (quantity <= this.maxValue || this.maxValue === 0) && quantity >= this.minValue;
  }

  private isAllowedCharacter(key: string): boolean {
    return /^[0-9]$/.test(key) ||
      key === 'Backspace' ||
      key === 'Delete' ||
      key === 'ArrowLeft' ||
      key === 'ArrowRight';
  }

  private isNumericCharacter(key: string): boolean {
    return /^[0-9]$/.test(key);
  }

  /**
   * We want to show an error message only when quantity not in range, and it is a valid number. For other cases when
   * the quantity has the wrong value, like a letter or space character, insted of showing error message that value is wrong
   * we just do not let that value be typed in input at all. We prevent it with 'onKeyDown' method
   *
   * @param quantity at this point we dont know yet what value has the quantity, it needs to be yet validated
   * @private
   */
  private handleErrorCase(quantity: any): void {
    let errorMessage = '';
    if (quantity > this.maxValue) {
      errorMessage = `Der maximal zulässige Wert beträgt ${this.maxValue}.`;
    }

    if (quantity < this.minValue) {
      errorMessage = `Der minimal zulässige Wert ist ${this.minValue}.`;
    }

    this.matSnackBar.open(
      errorMessage,
      undefined,
      { duration: 5000 },
    );
  }
}
