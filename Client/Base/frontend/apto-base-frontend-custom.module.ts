import { NgModule } from '@angular/core';
import {
    MisterpenHeaderComponent
} from '@apto-one-template-frontend/components/misterpen-header/misterpen-header.component';
import {
  MisterpenToolbarBottomComponent
} from '@apto-one-template-frontend/components/misterpen-toolbar-bottom/misterpen-toolbar-bottom.component';
import {
  MisterpenColorElementsComponent
} from '@apto-one-template-frontend/components/misterpen-color-elements/misterpen-color-elements.component';
import { AptoBaseCoreModule } from '@apto-base-core/apto-base-core.module';
import { AsyncPipe, NgClass, NgForOf, NgIf, NgStyle } from '@angular/common';
import {
  MisterpenPrintingTechnologyComponent
} from '@apto-one-template-frontend/components/misterpen-printing-technology/misterpen-printing-technology.component';
import {
  MisterpenLogoTextComponent
} from '@apto-one-template-frontend/components/misterpen-logo-text/misterpen-logo-text.component';
import { MisterpenLogoComponent } from '@apto-one-template-frontend/components/misterpen-logo/misterpen-logo.component';
import { MisterpenTextComponent } from '@apto-one-template-frontend/components/misterpen-text/misterpen-text.component';
import { FormsModule } from '@angular/forms';
import {
  MisterpenPrintSettingsComponent
} from '@apto-one-template-frontend/components/misterpen-print-settings/misterpen-print-settings.component';

@NgModule({
  declarations: [
    MisterpenHeaderComponent,
    MisterpenColorElementsComponent,
    MisterpenLogoComponent,
    MisterpenLogoTextComponent,
    MisterpenPrintingTechnologyComponent,
    MisterpenPrintSettingsComponent,
    MisterpenTextComponent,
    MisterpenToolbarBottomComponent
  ],
  exports: [
    MisterpenHeaderComponent,
    MisterpenColorElementsComponent,
    MisterpenLogoComponent,
    MisterpenLogoTextComponent,
    MisterpenPrintingTechnologyComponent,
    MisterpenPrintSettingsComponent,
    MisterpenTextComponent,
    MisterpenToolbarBottomComponent
  ],
  imports: [
    AptoBaseCoreModule,
    AsyncPipe,
    NgClass,
    NgIf,
    NgForOf,
    NgStyle,
    FormsModule,
  ],
})
export class AptoBaseFrontendCustomModule { }
