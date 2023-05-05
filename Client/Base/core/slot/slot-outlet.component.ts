import { Component, Injector, Input, SimpleChanges, ViewContainerRef } from '@angular/core';
import { SlotRegistry } from './slot-registry';

@Component({
	selector: 'apto-slot-outlet',
	template: '',
})
export class SlotOutletComponent {
	@Input()
	public identifier: string | undefined;

	@Input()
	public section: any;

	@Input()
	public element: any;

	@Input()
	public product: any;

	public component: any | undefined;

	public constructor(public viewContainerRef: ViewContainerRef, public injector: Injector) {}

	public ngOnChanges(changes: SimpleChanges): void {
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
}
