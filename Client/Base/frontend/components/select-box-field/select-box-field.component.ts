import { Component, EventEmitter, forwardRef, Input, OnInit, Output, ViewChild } from '@angular/core';
import { ControlValueAccessor, NG_VALUE_ACCESSOR } from '@angular/forms';
import { TranslatedValue } from '@apto-base-core/store/translated-value/translated-value.model';
import { SelectItem } from '@apto-catalog-frontend/models/select-items';
import { ProgressElement } from '@apto-catalog-frontend-configuration-model';

@Component({
	selector: 'apto-select-box-field',
	templateUrl: './select-box-field.component.html',
	styleUrls: ['./select-box-field.component.scss'],
	providers: [
		{
			provide: NG_VALUE_ACCESSOR,
			// eslint-disable-next-line
			useExisting: forwardRef(() => SelectBoxFieldComponent),
			multi: true,
		},
	],
})
export class SelectBoxFieldComponent<T extends { id: string; name: TranslatedValue } = SelectItem> implements ControlValueAccessor, OnInit {
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
	public element: ProgressElement | undefined;

	@Input()
	public placeholder: string = '';

  @Input()
  public width: string = '';

	public _items: T[] = [];

	public value: string[] = [];

	public itemsSelected: T[] = [];
	public onChange: Function | undefined;

	@Input()
	public set items(items: T[]) {
		this._items = items || [];
		this.updateValue();
	}

  @Output()
  public inputCleared = new EventEmitter();

	public isOverlayOpen = false;

	public valueGiven(): boolean {
		return this.value.length !== 0;
	}

	public writeValue(value: string[] | null): void {
		if (value) {
			this.value = [...value];
		}
		this.updateValue();
	}

	public registerOnChange(fn: any): void {
		this.onChange = fn;
	}

	public registerOnTouched(fn: any): void {}

	public setDisabledState(isDisabled: boolean): void {}

	public toggleOverlay(): void {
    this.isOverlayOpen = !this.isOverlayOpen;
	}

	public updateValue(): void {
		this.itemsSelected = this._items.filter((i) => this.value.find((v) => i.id === v));
	}

	public isSelected(item: string): boolean {
		return this.value.includes(item);
	}

	public selectItem(item: string): void {
    this.value = [...this.value];

    if (this.value.includes(item)) {
			this.value.splice(this.value.indexOf(item), 1);
		} else {
      this.value.push(item);
		}

    this.updateValue();
    this.onChange?.(this.value);
	}

	public clear(): void {
		this.value = [];
		this.updateValue();
		this.onChange?.(this.value);
		this.isOverlayOpen = false;
    this.inputCleared.emit();
	}

	public ngOnInit(): void {
		if (this.default) {
			this.value.push(this.default);
		}
		this.updateValue();
	}
}
