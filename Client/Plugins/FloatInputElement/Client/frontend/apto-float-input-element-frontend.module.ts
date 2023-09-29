import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';
import { HttpClientModule } from '@angular/common/http';
import { RouterModule } from '@angular/router';
import { FormsModule, ReactiveFormsModule } from '@angular/forms';
import { MatButtonModule } from '@angular/material/button';
import { MatDialogModule } from '@angular/material/dialog';
import { MatSliderModule } from '@angular/material/slider';
import { MatIconModule } from "@angular/material/icon";

import { AptoBaseCoreModule } from '@apto-base-core/apto-base-core.module';
import { SlotRegistry } from '@apto-base-core/slot/slot-registry';
import { AptoBaseFrontendModule } from '@apto-base-frontend/apto-base-frontend.module';
import { FloatInputElementComponent } from './components/float-input-element/float-input-element.component';
import { AptoCatalogFrontendModule } from '@apto-catalog-frontend/apto-catalog-frontend.module';

@NgModule({
	declarations: [FloatInputElementComponent],
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
        MatSliderModule,
        AptoCatalogFrontendModule,
        MatIconModule,
    ],
	providers: [],
})
export class AptoFloatInputElementFrontendModule {
	public constructor() {
		SlotRegistry.components.set('apto-element-float-input-element', FloatInputElementComponent);
	}
}
