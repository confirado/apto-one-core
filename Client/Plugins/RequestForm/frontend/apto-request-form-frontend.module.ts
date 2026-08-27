import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';
import { HttpClientModule } from '@angular/common/http';
import { FormsModule, ReactiveFormsModule } from '@angular/forms';
import { RouterModule } from '@angular/router';

import { Store } from '@ngrx/store';

import { MatLegacyButtonModule as MatButtonModule } from '@angular/material/legacy-button';
import { MatLegacyCheckboxModule as MatCheckboxModule } from '@angular/material/legacy-checkbox';
import { MatIconModule } from '@angular/material/icon';
import { MatDividerModule } from '@angular/material/divider';

import { selectShop } from '@apto-base-frontend/store/shop/shop.selectors';
import { SlotRegistry } from '@apto-base-core/slot/slot-registry';
import { AptoBaseCoreModule } from '@apto-base-core/apto-base-core.module';
import { AptoBaseFrontendModule } from '@apto-base-frontend/apto-base-frontend.module';
import { AptoCatalogFrontendModule } from '@apto-catalog-frontend/apto-catalog-frontend.module';
import { RequestFormComponent } from '@apto-request-form-frontend-request-form';
import { RequestMessageStateComponent } from '@apto-request-form-frontend/components/request-message-state/request-message-state.component';
import { MatLegacyProgressSpinnerModule as MatProgressSpinnerModule } from '@angular/material/legacy-progress-spinner';
import { AptoRequestFormFrontendCustomModule } from '@apto-request-form-frontend-custom-module';
import { SummaryComponent as RequestFormSummaryComponent } from '@apto-request-form-frontend-summary';
import { SummaryComponent as CatalogSummaryComponent } from '@apto-catalog-frontend-summary';


@NgModule({
    declarations: [
        RequestFormComponent, RequestMessageStateComponent, RequestFormSummaryComponent
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
        this.store.select(selectShop).subscribe((shop) => {
            if (shop === null) {
                return;
            }
            const requestFormIsDisabled = shop.customProperties.some((property) => property.key === 'requestForm' && property.value === 'disabled');
            SlotRegistry.components.set('summary', requestFormIsDisabled ? CatalogSummaryComponent : RequestFormSummaryComponent);
        });
    }
}
