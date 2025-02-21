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
import { AsyncPipe, NgClass, NgForOf, NgIf } from '@angular/common';

@NgModule({
  declarations: [
    MisterpenHeaderComponent,
    MisterpenToolbarBottomComponent,
    MisterpenColorElementsComponent,
  ],
  exports: [
    MisterpenHeaderComponent,
    MisterpenToolbarBottomComponent,
    MisterpenColorElementsComponent,
  ],
  imports: [
    AptoBaseCoreModule,
    AsyncPipe,
    NgClass,
    NgIf,
    NgForOf,
  ],
})
export class AptoBaseFrontendCustomModule { }
