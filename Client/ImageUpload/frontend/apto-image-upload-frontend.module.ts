import { CommonModule, NgOptimizedImage } from '@angular/common';
import { HttpClientModule } from '@angular/common/http';
import { NgModule } from '@angular/core';
import { FormsModule, ReactiveFormsModule } from '@angular/forms';
import { MatDialogModule } from '@angular/material/dialog';
import { MatIconModule } from '@angular/material/icon';
import { RouterModule } from '@angular/router';
import { StoreModule } from '@ngrx/store';
import { AngularResizeEventModule } from 'angular-resize-event';

import { featureKey, reducers } from '@apto-image-upload-frontend/store/feature';
import { AptoBaseCoreModule } from '@apto-base-core/apto-base-core.module';
import { SlotRegistry } from '@apto-base-core/slot/slot-registry';
import { AptoBaseFrontendModule } from '@apto-base-frontend/apto-base-frontend.module';
import { AptoCatalogFrontendModule } from '@apto-catalog-frontend/apto-catalog-frontend.module';
import { ImageUploadComponent } from '@apto-image-upload-frontend/components/image-upload/image-upload.component';
import { DesignerComponent } from '@apto-image-upload-frontend/components/designer/designer.component';
import { MatButtonModule } from '@angular/material/button';

@NgModule({
  declarations: [ImageUploadComponent, DesignerComponent],
  exports: [DesignerComponent],
  entryComponents: [],
	imports: [
		RouterModule,
		CommonModule,
		HttpClientModule,
		StoreModule.forFeature(featureKey, reducers),
		AptoBaseCoreModule,
		AptoBaseFrontendModule,
		ReactiveFormsModule,
		FormsModule,
		MatDialogModule,
		MatIconModule,
		AptoCatalogFrontendModule,
		MatButtonModule,
		AngularResizeEventModule,
		NgOptimizedImage,
	],
  providers: [],
})
export class AptoImageUploadFrontendModule {
  public constructor() {
    SlotRegistry.components.set('apto-element-image-upload', ImageUploadComponent);
    SlotRegistry.components.set('one-page-designer', DesignerComponent);
  }
}
