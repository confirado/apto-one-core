import { NgModule } from '@angular/core';
import { PrettifyPipe } from '@apto-base-core/pipes/prettify.pipe';
import { TranslatedValuePipe } from '@apto-base-core/pipes/translated-value.pipe';
import { MessageBusService } from '@apto-base-core/services/message-bus.service';
import { TemplateSlotDirective } from '@apto-base-core/template-slot/template-slot.directive';
import { TemplateSlotRegistry } from '@apto-base-core/template-slot/template-slot.registry';
import { SlotOutletComponent } from './slot/slot-outlet.component';
import { SafeHtmlPipe } from "@apto-base-core/pipes/save-html.pipe";

@NgModule({
	declarations: [TemplateSlotDirective, TranslatedValuePipe, PrettifyPipe, SlotOutletComponent, SafeHtmlPipe],
	exports: [TemplateSlotDirective, TranslatedValuePipe, PrettifyPipe, SlotOutletComponent, SafeHtmlPipe],
	imports: [],
	providers: [TemplateSlotRegistry, MessageBusService],
})
export class AptoBaseCoreModule {}
