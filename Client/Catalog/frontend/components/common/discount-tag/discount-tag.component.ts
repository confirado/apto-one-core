import { Component, Input } from '@angular/core';
import { TranslatedValue } from "@apto-base-core/store/translated-value/translated-value.model";

@Component({
	selector: 'apto-discount-tag',
	templateUrl: './discount-tag.component.html',
	styleUrls: ['./discount-tag.component.scss'],
})
export class DiscountTagComponent {
  @Input()
  public label: TranslatedValue;
}
