import { Component, Input } from '@angular/core';
import { Store } from '@ngrx/store';
import { ProgressElement } from '@apto-catalog-frontend/store/configuration/configuration.model';
import { Product, Section } from '@apto-catalog-frontend/store/product/product.model';
import { environment } from '@apto-frontend/src/environments/environment';
import { BehaviorSubject, Observable, combineLatest } from 'rxjs';
import { filter, switchMap } from 'rxjs/operators';
import { ConfigurationRepository } from '@apto-catalog-frontend/store/configuration/configuration.repository';
import { selectCompressedState } from '@apto-catalog-frontend/store/configuration/configuration.selectors';

@Component({
	selector: 'apto-hint-element',
	templateUrl: './hint-element.component.html',
	styleUrls: ['./hint-element.component.scss'],
})
export class HintElementComponent {
	private readonly productSubject = new BehaviorSubject<Product | null | undefined>(null);
	public readonly descriptionSubject = new BehaviorSubject<string | null>(null);

	private _product: Product | null | undefined;
	private _element: ProgressElement | undefined | null;

	@Input()
	public set product(value: Product | null | undefined) {
		this._product = value;
		this.productSubject.next(value);
	}

	public get product(): Product | null | undefined {
		return this._product;
	}

	@Input()
	public section: Section | undefined;

	@Input()
	public set element(value: ProgressElement | undefined | null) {
		this._element = value;

		this.descriptionSubject.next(value?.element?.description ? Object.values(value.element.description)[0] ?? '' : '');
	}

	public get element(): ProgressElement | undefined | null {
		return this._element;
	}

	public mediaUrl = environment.api.media;

	public readonly resolvedDescription$: Observable<string>;

	public constructor(
		private store: Store,
		private configurationRepository: ConfigurationRepository,
	) {
		this.resolvedDescription$ = combineLatest([
			this.productSubject,
			this.descriptionSubject,
			this.store.select(selectCompressedState),
		]).pipe(
			filter(([product, description]) => !!product?.id && !!description),
			switchMap(([product, description, state]) =>
				this.configurationRepository.getSubstitutes(product.id, state, description),
			),
		);
	}

	protected get hasAttachments(): boolean {
		return (this.element?.element?.attachments?.length ?? 0) !== 0;
	}
}
