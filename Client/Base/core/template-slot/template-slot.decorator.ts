import { Type } from "@angular/core";
import { TemplateSlotRegistry } from "@apto-base-core/template-slot/template-slot.registry";
import { TemplateSlotInterface } from "@apto-base-core/template-slot/template-slot.interface";

export const TemplateSlot = (options: TemplateSlotOptions): Function => {
  return (target: Type<TemplateSlotInterface>) => {
    if (options.remove) {
      options.remove.forEach((value: Type<TemplateSlotInterface>) => {
        TemplateSlotRegistry.removeComponent(options.slot, value);
      });
    }

    if (options.replace) {
      options.replace.forEach((value: Type<TemplateSlotInterface>) => {
        TemplateSlotRegistry.replaceComponent(options.slot, value, target);
      });
    } else {
      TemplateSlotRegistry.addComponent(options.slot, target);
    }
  }
}

export type TemplateSlotOptions = {
  slot: string;
  replace?: Type<TemplateSlotInterface>[];
  remove?: Type<TemplateSlotInterface>[];
}
