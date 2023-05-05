import {
  ComponentRef,
  Directive,
  Input,
  OnChanges,
  OnInit,
  SimpleChanges,
  ViewContainerRef
} from "@angular/core";
import { TemplateSlotRegistry } from "./template-slot.registry";
import { TemplateSlotInterface } from "./template-slot.interface";

@Directive({
  selector: '[template-slot]'
})
export class TemplateSlotDirective implements OnChanges, OnInit {
  private componentRefs: Array<ComponentRef<TemplateSlotInterface>>;
  private changes: SimpleChanges | null;
  @Input() slot: string = '';

  constructor(private view: ViewContainerRef) {
    this.componentRefs = [];
    this.changes = null;
  }

  ngOnInit() {
    // clear view
    this.view.clear();

    // get components for this slot
    const components = TemplateSlotRegistry.getComponents(this.slot);

    // create components
    for (let i= 0; i < components.length; i++) {
      let component: ComponentRef<TemplateSlotInterface> = this.view.createComponent(components[i]);
      this.componentRefs.push(component);

      // apply last changes
      if (this.changes !== null) {
        component.instance.onPropsChanged(this.changes);
      }
    }
  }

  ngOnChanges(changes: SimpleChanges) {
    this.changes = changes;
    for (let i = 0; i < this.componentRefs.length; i++) {
      this.componentRefs[i].instance.onPropsChanged(this.changes);
    }
  }
}
