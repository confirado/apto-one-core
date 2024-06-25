import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';
import { HttpClientModule } from '@angular/common/http';
import { FormsModule, ReactiveFormsModule } from '@angular/forms';
import { RouterModule } from '@angular/router';

import { Store } from '@ngrx/store';

import { MatButtonModule } from '@angular/material/button';
import { MatCheckboxModule } from '@angular/material/checkbox';
import { MatIconModule } from '@angular/material/icon';
import { MatDividerModule } from '@angular/material/divider';

import { selectShop } from '@apto-base-frontend/store/shop/shop.selectors';
import { SlotRegistry } from '@apto-base-core/slot/slot-registry';
import { AptoBaseCoreModule } from '@apto-base-core/apto-base-core.module';
import { AptoBaseFrontendModule } from '@apto-base-frontend/apto-base-frontend.module';
import { AptoCatalogFrontendModule } from '@apto-catalog-frontend/apto-catalog-frontend.module';
import { SummaryComponent } from './components/summary/summary.component';
import { RequestFormComponent } from '@apto-request-form-frontend-request-form';
import { RequestMessageStateComponent } from '@apto-request-form-frontend/components/request-message-state/request-message-state.component';
import { MatProgressSpinnerModule } from '@angular/material/progress-spinner';
import { AptoRequestFormFrontendCustomModule } from "@apto-request-form-frontend-custom-module";

@NgModule({
	declarations: [
    RequestFormComponent, SummaryComponent, RequestMessageStateComponent
  ],
	exports: [
    AptoRequestFormFrontendCustomModule,
  ],
	imports: [
		RouterModule,
		CommonModule,
		HttpClientModule,
		AptoBaseCoreModule,
		AptoBaseFrontendModule,
    AptoCatalogFrontendModule,
		ReactiveFormsModule,
		FormsModule,
		MatCheckboxModule,
    MatIconModule,
		MatButtonModule,
    MatDividerModule,
    MatProgressSpinnerModule,
    AptoRequestFormFrontendCustomModule,
	],
	providers: [],
})
export class AptoRequestFormFrontendModule {
	public constructor(private store: Store) {
    this.store.select(selectShop).subscribe((result) => {
      if (result === null) {
        return;
      }

      // requestForm is set from -> web/backend#!/shop/ -> Katalog -> Domains -> BENUTZERDEFINIERTE EIGENSCHAFTEN
      const rfCustomProperty = result.customProperties
        .filter((customProperty) => customProperty.key === 'requestForm' && customProperty.value === 'disabled');

      if (rfCustomProperty.length < 1) {
        SlotRegistry.components.set('summary', SummaryComponent);
      }
    });
	}
}
