import { CommonModule } from '@angular/common';
import { HttpClientModule } from '@angular/common/http';
import { NgModule } from '@angular/core';
import { FormsModule, ReactiveFormsModule } from '@angular/forms';
import { MatButtonModule } from '@angular/material/button';
import { MatDialogModule, MatDialogRef } from '@angular/material/dialog';
import { MatIconModule } from '@angular/material/icon';
import { RouterModule } from '@angular/router';

import { AptoBaseCoreModule } from '@apto-base-core/apto-base-core.module';
import { SlotRegistry } from '@apto-base-core/slot/slot-registry';
import { AptoBaseFrontendModule } from '@apto-base-frontend/apto-base-frontend.module';
import { AreaElementWrapperComponent } from './components/area-element-wrapper/area-element-wrapper.component';
import { AreaElementComponent } from './components/area-element/area-element.component';
import { AptoCatalogFrontendModule } from '@apto-catalog-frontend/apto-catalog-frontend.module';

@NgModule({
  declarations: [
    AreaElementComponent,
    AreaElementWrapperComponent,
  ],
	exports: [],
	entryComponents: [],
  imports: [
    RouterModule,
    CommonModule,
    HttpClientModule,
    AptoBaseCoreModule,
    AptoBaseFrontendModule,
    AptoCatalogFrontendModule,
    ReactiveFormsModule,
    FormsModule,
    MatDialogModule,
    MatIconModule,
    MatButtonModule,
  ],
	providers: [
    {
      provide: MatDialogRef,
      useValue: {}
    },
  ],
})
export class AptoAreaElementFrontendModule {
	public constructor() {
		SlotRegistry.components.set('apto-element-area-element', AreaElementWrapperComponent);
	}
}
