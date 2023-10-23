import { Component, forwardRef, OnInit, Input } from '@angular/core';
import { ControlValueAccessor, FormControl, NG_VALUE_ACCESSOR } from '@angular/forms';
import { map } from 'rxjs';
import { debounceTime, distinctUntilChanged } from 'rxjs/operators';
import { selectContentSnippet } from '@apto-base-frontend/store/content-snippets/content-snippets.selectors';
import { Store } from '@ngrx/store';

@Component({
  selector: 'apto-search',
  templateUrl: './apto-search.component.html',
  styleUrls: ['./apto-search.component.scss'],
  providers: [
    {
      provide: NG_VALUE_ACCESSOR,
      // eslint-disable-next-line no-use-before-define
      useExisting: forwardRef(() => AptoSearchComponent),
      multi: true,
    },
  ],
})
export class AptoSearchComponent implements ControlValueAccessor, OnInit {

  @Input() public width: string = '';

  public readonly csProductList$ = this.store.select(selectContentSnippet('aptoProductList'));

  public formElement = new FormControl({ value: undefined, disabled: false });
  public disabled: boolean = false;

  public constructor(
    public store: Store
  ) { }

  public ngOnInit(): void { }

  public writeValue(obj: string | undefined): void {
    this.formElement.setValue(obj);
  }

  public registerOnTouched(fn: any): void { }

  public registerOnChange(fn: any): void {
    this.formElement.valueChanges
      .pipe(
        distinctUntilChanged(),
        debounceTime(500),
        map((value) => value)
      )
      .subscribe(fn);
  }

  /**
   * This is called ONLY when from parent input is disabled
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
