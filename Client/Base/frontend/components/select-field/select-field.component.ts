import { Component, ElementRef, forwardRef, Input, ViewChild } from '@angular/core';
import { ControlValueAccessor, NG_VALUE_ACCESSOR } from '@angular/forms';
import { TranslatedValue } from '@apto-base-core/store/translated-value/translated-value.model';
import { SelectItem } from '@apto-catalog-frontend/models/select-items';

@Component({
	selector: 'apto-select-field',
	templateUrl: './select-field.component.html',
	styleUrls: ['./select-field.component.scss'],
	providers: [
		{
			provide: NG_VALUE_ACCESSOR,
			// eslint-disable-next-line
			useExisting: forwardRef(() => SelectFieldComponent),
			multi: true,
		},
	],
})
export class SelectFieldComponent<T extends { id: string; name: TranslatedValue } = SelectItem> implements ControlValueAccessor {
	@Input()
	public name: string = '';

	@Input()
	public prefix: string | undefined;

	@Input()
	public suffix: string | undefined;

	@Input()
	public hint: string = '';

	@Input()
	public enableClear = true;

	@Input()
	public disabled: boolean = false;

	@Input()
	public default: string | undefined = '';

	@Input()
	public asArray: boolean = false;

	@Input()
	public width: string = '';

	public _items: T[] = [];

	public value: string | null = null;

	public item: T | undefined;

	public onChange: Function | undefined;

	@Input()
	public set items(items: T[]) {
		this._items = items || [];
		this.updateValue();
	}

	public isOverlayOpen = false;

  public overlayWidth: number;

  @ViewChild('overlayContent') overlayContent: ElementRef<HTMLElement>;

  calculateOverlayWidth(): number {
    return this.overlayContent.nativeElement.getBoundingClientRect().width + 2; // +2 for borders
  }

  public writeValue(value: string | null): void {
		this.value = value;
		this.updateValue();
	}

	public registerOnChange(fn: any): void {
		this.onChange = fn;
	}

	public registerOnTouched(fn: any): void {}

	public setDisabledState(isDisabled: boolean): void {}

	public toggleOverlay(): void {
    this.overlayWidth = this.calculateOverlayWidth();
    this.isOverlayOpen = !this.isOverlayOpen;
  }

	public updateValue(): void {
		this.item = this.value ? this._items.find((i) => i.id === this.value) : undefined;
	}

  public selectItem(item: T): void {
    this.overlayWidth = this.calculateOverlayWidth();

		this.value = item.id;
		this.updateValue();
		if (this.asArray) {
			this.onChange?.([item.id]);
		} else {
			this.onChange?.(item.id);
		}
		this.isOverlayOpen = false;
	}

	public clear(): void {
		this.value = null;
		this.updateValue();
		if (this.asArray) {
			this.onChange?.([]);
		} else {
			this.onChange?.(null);
		}
		this.isOverlayOpen = false;
	}
}
