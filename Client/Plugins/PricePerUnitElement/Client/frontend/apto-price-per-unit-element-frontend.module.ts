import { CommonModule } from '@angular/common';
import { HttpClientModule } from '@angular/common/http';
import { NgModule } from '@angular/core';
import { FormsModule, ReactiveFormsModule } from '@angular/forms';
import { MatDialogModule } from '@angular/material/dialog';
import { MatIconModule } from '@angular/material/icon';
import { RouterModule } from '@angular/router';

import { AptoBaseCoreModule } from '@apto-base-core/apto-base-core.module';
import { SlotRegistry } from '@apto-base-core/slot/slot-registry';
import { AptoBaseFrontendModule } from '@apto-base-frontend/apto-base-frontend.module';
import { AptoCatalogFrontendModule } from '@apto-catalog-frontend/apto-catalog-frontend.module';
import { DefaultElementComponent } from '@element-definition-price-per-unit-default-element';
import { DefaultElementStepByStepComponent } from '@element-definition-price-per-unit-default-element-step-by-step';
import { DefaultElementOnePageComponent } from '@element-definition-price-per-unit-default-element-one-page';

@NgModule({
	declarations: [
    DefaultElementComponent,
    DefaultElementOnePageComponent,
    DefaultElementStepByStepComponent
  ],
	exports: [],
	entryComponents: [],
  imports: [
    RouterModule,
    CommonModule,
    HttpClientModule,
    AptoBaseCoreModule,
    AptoBaseFrontendModule,
    ReactiveFormsModule,
    FormsModule,
    MatDialogModule,
    MatIconModule,
    AptoCatalogFrontendModule,
  ],
	providers: [],
})
export class AptoPricePerUnitElementFrontendModule {
	public constructor() {
		SlotRegistry.components.set('apto-element-price-per-unit', DefaultElementComponent);
		SlotRegistry.components.set('apto-element-default-element', DefaultElementComponent);
	}
}
