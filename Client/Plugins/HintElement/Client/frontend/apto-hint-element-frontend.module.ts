import { CommonModule } from '@angular/common';
import { HttpClientModule } from '@angular/common/http';
import { NgModule } from '@angular/core';
import { FormsModule, ReactiveFormsModule } from '@angular/forms';
import { MatDialogModule } from '@angular/material/dialog';
import { RouterModule } from '@angular/router';

import { AptoBaseCoreModule } from '@apto-base-core/apto-base-core.module';
import { AptoBaseFrontendModule } from '@apto-base-frontend/apto-base-frontend.module';
import { HintElementComponent } from "./components/hint-element/hint-element.component";
import {SlotRegistry} from "@apto-base-core/slot/slot-registry";
import {AptoCatalogFrontendModule} from "@apto-catalog-frontend/apto-catalog-frontend.module";
import {MatIconModule} from "@angular/material/icon";

@NgModule({
	declarations: [
    HintElementComponent
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
    AptoCatalogFrontendModule,
    MatIconModule,
  ],
	providers: [],
})
export class AptoHintElementFrontendModule {
	public constructor() {
		SlotRegistry.components.set('apto-element-hint-element', HintElementComponent);
	}
}
