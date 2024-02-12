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
import { AreaElementWrapperComponent } from '@area-element-wrapper';
import { AreaElementComponent } from '@area-element';

@NgModule({
  declarations: [
    AreaElementComponent,
    AreaElementWrapperComponent,
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
    MatButtonModule,
  ],
	providers: [
    {
      provide: MatDialogRef,
      useValue: {}
    },
  ],
})
export class AptoAreaElementFrontendModule {
	public constructor() {
		SlotRegistry.components.set('apto-element-area-element', AreaElementWrapperComponent);
	}
}
