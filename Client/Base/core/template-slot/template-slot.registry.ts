import { Type } from "@angular/core";
import { TemplateSlotInterface } from "./template-slot.interface";

export class TemplateSlotRegistry {
  private static slots = new Map<string, Type<TemplateSlotInterface>[]>();

  static addComponent(selector: string, component: Type<TemplateSlotInterface>) {
    let slots = TemplateSlotRegistry.getComponents(selector);
    slots.push(component);
    TemplateSlotRegistry.slots.set(selector, slots);
  }

  static getComponents(selector: string): Type<TemplateSlotInterface>[] {
    const slots = TemplateSlotRegistry.slots.get(selector);

    if (slots) {
      return slots;
    }
    return [];
  }

  static replaceComponent(selector: string, search: Type<TemplateSlotInterface>, replace: Type<TemplateSlotInterface>) {
    let slots = TemplateSlotRegistry.getComponents(selector);

    for (let i = 0; i < slots.length; i++) {
      if (search === slots[i]) {
        slots[i] = replace;
      }
    }
    TemplateSlotRegistry.slots.set(selector, slots);
  }

  static removeComponent(selector: string, component: Type<TemplateSlotInterface>) {
    let slots = TemplateSlotRegistry.getComponents(selector);

    for (let i = 0; i < slots.length; i++) {
      if (component === slots[i]) {
        slots.splice(i, 1);
      }
    }
    TemplateSlotRegistry.slots.set(selector, slots);
  }
}
