import { Component, forwardRef, Input, OnChanges, SimpleChanges } from '@angular/core';
import { ControlValueAccessor, FormControl, NG_VALUE_ACCESSOR } from '@angular/forms';
import { setQuantity } from '@apto-catalog-frontend/store/configuration/configuration.actions';
import { selectQuantity } from '@apto-catalog-frontend/store/configuration/configuration.selectors';
import { Store } from '@ngrx/store';
import { debounceTime, delay, map } from 'rxjs';

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
export class QuantityInputEditableComponent implements ControlValueAccessor, OnChanges {
	@Input()
	public name: string = '';

	@Input()
	public step: number = 1;

  @Input()
  public width: string = '';

  @Input()
  public size: 'small' | 'big' = 'small';

	public formElement: FormControl;

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
	public registerOnChange(fn: any): void {
		// TODO: Not good... can cause a loop

    this.formElement.valueChanges
			.pipe(
				map((value: string) => {
          let quantity = parseInt(value, 10);
          if (quantity <= 0 || isNaN(quantity)) {
            quantity = 1;
          }
          this.store.dispatch(setQuantity({ quantity }));
          return quantity;
				}),
				delay(0),
				debounceTime(100)
			)
			.subscribe(fn);
	}

	public registerOnTouched(fn: any): void {}

	public setDisabledState?(isDisabled: boolean): void {}

	public quantity$ = this.store.select(selectQuantity);

	public constructor(
    private store: Store
  ) {
    this.formElement = new FormControl<number>(1);

    this.quantity$.subscribe((value) => {
      this.formElement.setValue(value);
    });
  }

	public setQuantity(quantity: number): void {
		if (quantity > 0) {
      this.store.dispatch(setQuantity({ quantity }));
		}
	}
}








