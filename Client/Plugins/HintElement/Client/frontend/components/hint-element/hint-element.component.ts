import { Component, Input } from '@angular/core';
import { Store } from '@ngrx/store';
import { ProgressElement } from '@apto-catalog-frontend-configuration-model';
import { Product, Section } from '@apto-catalog-frontend/store/product/product.model';
import { environment } from '@apto-frontend/src/environments/environment';

@Component({
	selector: 'apto-hint-element',
	templateUrl: './hint-element.component.html',
	styleUrls: ['./hint-element.component.scss'],
})
export class HintElementComponent {
	@Input()
	public product: Product | null | undefined;

	@Input()
	public section: Section | undefined;

	@Input()
	public element: ProgressElement | undefined | null;

  public mediaUrl = environment.api.media;

  public constructor(private store: Store) {}

  protected get hasAttachments(): boolean {
    return this.element.element.attachments?.length !== 0;
  }
}
