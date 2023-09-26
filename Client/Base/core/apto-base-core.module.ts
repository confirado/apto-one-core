import { NgModule } from '@angular/core';
import { PrettifyPipe } from '@apto-base-core/pipes/prettify.pipe';
import { TranslatedValuePipe } from '@apto-base-core/pipes/translated-value.pipe';
import { MessageBusService } from '@apto-base-core/services/message-bus.service';
import { TemplateSlotDirective } from '@apto-base-core/template-slot/template-slot.directive';
import { TemplateSlotRegistry } from '@apto-base-core/template-slot/template-slot.registry';
import { SlotOutletComponent } from './slot/slot-outlet.component';
import { SafeHtmlPipe } from '@apto-base-core/pipes/save-html.pipe';
import { LoadingIndicatorComponent } from '@apto-base-core/components/common/loading-indicator/loading-indicator.component';
import { MatProgressSpinnerModule } from '@angular/material/progress-spinner';

@NgModule({
  declarations: [
    TemplateSlotDirective,
    TranslatedValuePipe,
    PrettifyPipe,
    SlotOutletComponent,
    SafeHtmlPipe,
    LoadingIndicatorComponent,
  ],
  exports: [
    TemplateSlotDirective,
    TranslatedValuePipe,
    PrettifyPipe,
    SlotOutletComponent,
    SafeHtmlPipe,
    LoadingIndicatorComponent,
  ],
  imports: [
    MatProgressSpinnerModule,
  ],
  providers: [
    TemplateSlotRegistry,
    MessageBusService,
  ],
})
export class AptoBaseCoreModule {
}
