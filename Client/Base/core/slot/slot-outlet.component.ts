import { Component, Input, OnInit, SimpleChanges, ViewContainerRef } from '@angular/core';
import { SlotRegistry } from './slot-registry';

@Component({
	selector: 'apto-slot-outlet',
	template: '',
})
export class SlotOutletComponent implements OnInit {
	@Input()
	public identifier: string | undefined;

	@Input()
	public section: any;

	@Input()
	public element: any;

	@Input()
	public product: any;

	public component: any | undefined;

	public constructor(public viewContainerRef: ViewContainerRef) {}
	public ngOnInit() {
		if (!this.identifier) {
			return;
		}

		this.viewContainerRef.clear();

		const component = SlotRegistry.components.get(this.identifier);

		if (component) {
			const componentRef = this.viewContainerRef.createComponent(component);
			this.component = componentRef.instance;
			this.component.section = this.section;
			this.component.element = this.element;
			this.component.product = this.product;
		}
	}

	public ngOnChanges(changes: SimpleChanges): void {
			if (!this.component) {
				return;
			}

			this.component.section = this.section;
			this.component.element = this.element;
			this.component.product = this.product;
	}
}
