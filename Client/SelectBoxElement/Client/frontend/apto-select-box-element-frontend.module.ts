import { CommonModule } from '@angular/common';
import { HttpClientModule } from '@angular/common/http';
import { NgModule } from '@angular/core';
import { FormsModule, ReactiveFormsModule } from '@angular/forms';
import { MatButtonModule } from '@angular/material/button';
import { MatDialogModule } from '@angular/material/dialog';
import { RouterModule } from '@angular/router';

import { AptoBaseCoreModule } from '@apto-base-core/apto-base-core.module';
import { SlotRegistry } from '@apto-base-core/slot/slot-registry';
import { AptoBaseFrontendModule } from '@apto-base-frontend/apto-base-frontend.module';
import { SelectboxElementComponent } from './components/selectbox-element/selectbox-element.component';
import { AptoCatalogFrontendModule } from '@apto-catalog-frontend/apto-catalog-frontend.module';

@NgModule({
	declarations: [SelectboxElementComponent],
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
	MatButtonModule,
	AptoCatalogFrontendModule,
  ],
	providers: [],
})
export class AptoSelectBoxElementFrontendModule {
	public constructor() {
		SlotRegistry.components.set('apto-element-select-box', SelectboxElementComponent);
	}
}
