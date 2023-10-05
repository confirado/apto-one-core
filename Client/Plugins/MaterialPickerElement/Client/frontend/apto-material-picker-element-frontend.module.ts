import { CommonModule } from '@angular/common';
import { HttpClientModule } from '@angular/common/http';
import { NgModule } from '@angular/core';
import { RouterModule } from '@angular/router';
import { FormsModule, ReactiveFormsModule } from '@angular/forms';
import { MatButtonModule } from '@angular/material/button';
import { MatCheckboxModule } from '@angular/material/checkbox';
import { MatDialogModule } from '@angular/material/dialog';
import { MatDividerModule } from '@angular/material/divider';
import { MatIconModule } from '@angular/material/icon';
import { MatRadioModule } from '@angular/material/radio';
import { MatTooltipModule } from '@angular/material/tooltip';
import { OverlayModule } from '@angular/cdk/overlay';
import { StoreModule } from "@ngrx/store";
import { EffectsModule } from "@ngrx/effects";
import { NgPipesModule } from 'ngx-pipes';

import { AptoBaseCoreModule } from '@apto-base-core/apto-base-core.module';
import { SlotRegistry } from '@apto-base-core/slot/slot-registry';
import { AptoBaseFrontendModule } from '@apto-base-frontend/apto-base-frontend.module';
import { AptoCatalogFrontendModule } from '@apto-catalog-frontend/apto-catalog-frontend.module';
import { featureKey, reducers } from "@apto-material-picker-element-frontend/store/feature";
import { MaterialPickerElementComponent } from '@apto-material-picker-element-frontend/components/material-picker-element/material-picker-element.component';
import { MaterialPickerDetailsPopupComponent } from '@apto-material-picker-element-frontend/components/material-picker-details-popup/material-picker-details-popup.component';
import { MaterialPickerHoverComponent } from '@apto-material-picker-element-frontend/components/material-picker-hover/material-picker-hover.component';
import { ItemLightPropertiesComponent } from "@apto-material-picker-element-frontend/components/common/item-light-properties/item-light-properties.component";
import { MaterialPickerEffects } from "@apto-material-picker-element-frontend/store/material-picker/material-picker.effects";

@NgModule({
  declarations: [
    MaterialPickerElementComponent,
    MaterialPickerDetailsPopupComponent,
    MaterialPickerHoverComponent,
    ItemLightPropertiesComponent,
  ],
  exports: [],
  imports: [
    StoreModule.forFeature(featureKey, reducers),
    EffectsModule.forFeature([MaterialPickerEffects]),
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
