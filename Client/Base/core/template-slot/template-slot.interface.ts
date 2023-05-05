import { SimpleChanges } from "@angular/core";

export interface TemplateSlotInterface {
  onPropsChanged(changes: SimpleChanges): void;
}
