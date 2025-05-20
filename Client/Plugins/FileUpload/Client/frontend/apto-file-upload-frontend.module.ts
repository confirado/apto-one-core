import { CommonModule } from '@angular/common';
import { HttpClientModule } from '@angular/common/http';
import { NgModule } from '@angular/core';
import { FormsModule, ReactiveFormsModule } from '@angular/forms';
import { MatLegacyDialogModule as MatDialogModule } from '@angular/material/legacy-dialog';
import { RouterModule } from '@angular/router';

import { AptoBaseCoreModule } from '@apto-base-core/apto-base-core.module';
import { AptoBaseFrontendModule } from '@apto-base-frontend/apto-base-frontend.module';

@NgModule({
	declarations: [],
	exports: [],
	imports: [
		RouterModule,
		CommonModule,
		HttpClientModule,
		AptoBaseCoreModule,
		AptoBaseFrontendModule,
		ReactiveFormsModule,
		FormsModule,
		MatDialogModule,
	],
	providers: [],
})
export class AptoFileUploadFrontendModule {
	public constructor() {
		// SlotRegistry.components.set('apto-width-height-element', WidthHeightElementComponent);
	}
}
