import { Component, EventEmitter, forwardRef, Input, OnInit, Output } from '@angular/core';
import { ControlValueAccessor, NG_VALUE_ACCESSOR } from '@angular/forms';
import { ThemePalette } from '@angular/material/core';
import { MatSliderChange } from '@angular/material/slider';

/**
 * @link https://material.angular.io/components/slider/api
 */
@Component({
  selector: 'apto-slider',
  templateUrl: './slider.component.html',
  styleUrls: ['./slider.component.scss'],
  providers: [
    {
      provide: NG_VALUE_ACCESSOR,
      useExisting: forwardRef(() => SliderComponent),
      multi: true,
    },
  ],
})
export class SliderComponent implements ControlValueAccessor {

  @Input()
  public disabled: boolean = false;

  @Input()
  public min: number = 1;

  @Input()
  public max: number = 100;

  @Input()
  public step: number = 1;

  @Input()
  public color: ThemePalette = 'primary';

  @Input()
  public hint: string = '';

  @Input()
  public prefix: string | undefined;

  @Input()
  public suffix: string | undefined;

  @Input()
  public showLabel = true;

  @Output()
  public sliderChanged = new EventEmitter();

  @Output()
  public sliderInputChanged = new EventEmitter();

  public value: number | undefined;
  public visibleValue: number | undefined;

  public writeValue(obj: string | undefined): void {
    this.value = parseInt(obj, 10);
  }

  public registerOnChange(fn: any): void {
    this.onInputChange = fn;
  }

  public onInputChange(value: number): void {}

  public onSliderMove(value: MatSliderChange): void {
    this.visibleValue = value.value;
    this.sliderInputChanged.emit(this.visibleValue);
  }

  public onSliderChange(value: MatSliderChange): void {
    this.sliderChanged.emit(value.value);
  }

  public registerOnTouched(fn: any): void {}

  public setDisabledState?(disabled: boolean): void {}
}
