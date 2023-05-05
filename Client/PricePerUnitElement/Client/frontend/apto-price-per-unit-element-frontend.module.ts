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
import { DefaultElementOnePageComponent } from './components/default-element-one-page/default-element-one-page.component';
import { DefaultElementStepByStepComponent } from './components/default-element-step-by-step/default-element-step-by-step.component';
import { DefaultElementComponent } from './components/default-element/default-element.component';
import { AptoCatalogFrontendModule } from '@apto-catalog-frontend/apto-catalog-frontend.module';

@NgModule({
	declarations: [DefaultElementComponent, DefaultElementOnePageComponent, DefaultElementStepByStepComponent],
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
