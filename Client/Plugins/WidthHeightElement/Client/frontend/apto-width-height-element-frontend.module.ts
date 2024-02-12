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
import { AptoCatalogFrontendModule } from '@apto-catalog-frontend/apto-catalog-frontend.module';
import { MatRippleModule } from '@angular/material/core';
import { FullscreenOverlayContainer, OverlayContainer, OverlayModule } from '@angular/cdk/overlay';
import { PortalModule } from '@angular/cdk/portal';
import { WidthHeightElementComponent } from '@width-height-element';
import { WidthHeightElementWrapperComponent } from '@width-height-element-wrapper';

@NgModule({
	declarations: [
    WidthHeightElementComponent,
    WidthHeightElementWrapperComponent
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
    MatButtonModule,
    AptoCatalogFrontendModule,
    MatRippleModule,
    PortalModule,
    OverlayModule
],
	providers: [
    {
      provide: OverlayContainer,
      useClass: FullscreenOverlayContainer
    },
    {
      provide: MatDialogRef,
      useValue: {}
    },
  ],
})
export class AptoWidthHeightElementFrontendModule {
	public constructor() {
		SlotRegistry.components.set('apto-element-width-height', WidthHeightElementWrapperComponent);
	}
}
