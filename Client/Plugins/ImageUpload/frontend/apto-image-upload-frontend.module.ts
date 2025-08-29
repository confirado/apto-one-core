import { CommonModule, NgOptimizedImage } from '@angular/common';
import { HttpClientModule } from '@angular/common/http';
import { NgModule } from '@angular/core';
import { FormsModule, ReactiveFormsModule } from '@angular/forms';
import { MatDialogModule } from '@angular/material/dialog';
import { MatIconModule } from '@angular/material/icon';
import { RouterModule } from '@angular/router';
import { StoreModule } from '@ngrx/store';
import { EffectsModule } from '@ngrx/effects';
import { AngularResizeEventModule } from 'angular-resize-event';
import { MAT_COLOR_FORMATS, NgxMatColorPickerModule, NGX_MAT_COLOR_FORMATS } from '@angular-material-components/color-picker';
import { NgxMatFileInputModule } from '@angular-material-components/file-input';
import { MatSelectModule } from '@angular/material/select';

import { featureKey, reducers } from '@apto-image-upload-frontend/store/feature';
import { AptoBaseCoreModule } from '@apto-base-core/apto-base-core.module';
import { SlotRegistry } from '@apto-base-core/slot/slot-registry';
import { AptoBaseFrontendModule } from '@apto-base-frontend/apto-base-frontend.module';
import { AptoCatalogFrontendModule } from '@apto-catalog-frontend/apto-catalog-frontend.module';
import { CanvasEffects } from '@apto-image-upload-frontend/store/canvas/canvas.effects';
import { ImageUploadComponent } from '@apto-image-upload-frontend/components/image-upload/image-upload.component';
import { DesignerComponent } from '@apto-image-upload-frontend/components/designer/designer.component';
import { MatButtonModule } from '@angular/material/button';
import { MatButtonToggleModule } from '@angular/material/button-toggle';
import { NgxFileDropModule } from 'ngx-file-drop';
import { DesignerPerspectiveComponent } from './components/designer-perspective/designer-perspective.component';

@NgModule({
  declarations: [ImageUploadComponent, DesignerComponent, DesignerPerspectiveComponent],
  exports: [DesignerComponent, DesignerPerspectiveComponent],
  entryComponents: [],
	imports: [
		RouterModule,
		CommonModule,
		HttpClientModule,
		StoreModule.forFeature(featureKey, reducers),
    EffectsModule.forFeature([CanvasEffects]),
		AptoBaseCoreModule,
		AptoBaseFrontendModule,
		ReactiveFormsModule,
		FormsModule,
		MatDialogModule,
		MatIconModule,
    MatButtonModule,
    MatButtonToggleModule,
		AptoCatalogFrontendModule,
		AngularResizeEventModule,
		NgOptimizedImage,
    NgxMatColorPickerModule,
    NgxMatFileInputModule,
    MatSelectModule,
        NgxFileDropModule
	],
  providers: [
    { provide: MAT_COLOR_FORMATS, useValue: NGX_MAT_COLOR_FORMATS }
  ],
})
export class AptoImageUploadFrontendModule {
  public constructor() {
    SlotRegistry.components.set('apto-element-image-upload', ImageUploadComponent);
    SlotRegistry.components.set('one-page-designer', DesignerPerspectiveComponent);
  }
}
