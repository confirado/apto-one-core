import { CommonModule } from '@angular/common';
import { HttpClientModule } from '@angular/common/http';
import { NgModule } from '@angular/core';
import { FormsModule, ReactiveFormsModule } from '@angular/forms';
import { MatDialogModule } from '@angular/material/dialog';
import { RouterModule } from '@angular/router';

import { MatButtonModule } from '@angular/material/button';
import { AptoBaseCoreModule } from '@apto-base-core/apto-base-core.module';
import { SlotRegistry } from '@apto-base-core/slot/slot-registry';
import { AptoBaseFrontendModule } from '@apto-base-frontend/apto-base-frontend.module';
import { AptoCatalogFrontendModule } from '@apto-catalog-frontend/apto-catalog-frontend.module';
import { MatIconModule } from '@angular/material/icon';
import { CustomTextElementComponent } from './components/custom-text-element/custom-text-element.component';

@NgModule({
	declarations: [CustomTextElementComponent],
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
  MatIconModule,
  ],
	providers: [],
})
export class AptoCustomTextFrontendModule {
	public constructor() {
		SlotRegistry.components.set('apto-element-custom-text', CustomTextElementComponent);
	}
}
