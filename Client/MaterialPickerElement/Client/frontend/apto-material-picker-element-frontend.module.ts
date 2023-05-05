import { CommonModule } from '@angular/common';
import { HttpClientModule } from '@angular/common/http';
import { NgModule } from '@angular/core';
import { FormsModule, ReactiveFormsModule } from '@angular/forms';
import { MatButtonModule } from '@angular/material/button';
import { MatCheckboxModule } from '@angular/material/checkbox';
import { MatDialogModule } from '@angular/material/dialog';
import { MatDividerModule } from '@angular/material/divider';
import { MatIconModule } from '@angular/material/icon';
import { MatRadioModule } from '@angular/material/radio';
import { RouterModule } from '@angular/router';

import { AptoBaseCoreModule } from '@apto-base-core/apto-base-core.module';
import { SlotRegistry } from '@apto-base-core/slot/slot-registry';
import { AptoBaseFrontendModule } from '@apto-base-frontend/apto-base-frontend.module';
import { MaterialPickerElementComponent } from './components/material-picker-element/material-picker-element.component';
import { MaterialPickerSecondMaterialComponent } from './components/material-picker-second-material/material-picker-second-material.component';
import { OverlayModule } from '@angular/cdk/overlay';
import { MaterialPickerDetailsPopupComponent } from './components/material-picker-details-popup/material-picker-details-popup.component';
import { MatTooltipModule } from '@angular/material/tooltip';
import { AptoCatalogFrontendModule } from '@apto-catalog-frontend/apto-catalog-frontend.module';
import { MaterialPickerHoverComponent } from './components/material-picker-hover/material-picker-hover.component';
import { ItemLightPropertiesComponent } from './components/common/item-light-properties/item-light-properties.component';
import { NgPipesModule } from 'ngx-pipes';

@NgModule({
	declarations: [
    MaterialPickerElementComponent,
    MaterialPickerSecondMaterialComponent,
    MaterialPickerDetailsPopupComponent,
    MaterialPickerHoverComponent,
    ItemLightPropertiesComponent,
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
    MatDividerModule,
    MatCheckboxModule,
    MatButtonModule,
    MatRadioModule,
    OverlayModule,
    MatTooltipModule,
    NgPipesModule
  ],
	providers: [],
})
export class AptoMaterialPickerElementFrontendModule {
	public constructor() {
		SlotRegistry.components.set('apto-element-material-picker', MaterialPickerElementComponent);
	}
}
