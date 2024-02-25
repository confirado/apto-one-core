import { OverlayModule } from '@angular/cdk/overlay';
import { CommonModule } from '@angular/common';
import { HttpClientModule } from '@angular/common/http';
import { NgModule } from '@angular/core';
import { FormsModule, ReactiveFormsModule } from '@angular/forms';
import { MatButtonModule } from '@angular/material/button';
import { MatCardModule } from '@angular/material/card';
import { MatCheckboxModule } from '@angular/material/checkbox';
import { MatRippleModule } from '@angular/material/core';
import { MatDialogModule } from '@angular/material/dialog';
import { MatDividerModule } from '@angular/material/divider';
import { MatExpansionModule } from '@angular/material/expansion';
import { MatFormFieldModule } from '@angular/material/form-field';
import { MatIconModule } from '@angular/material/icon';
import { MatInputModule } from '@angular/material/input';
import { MatProgressSpinnerModule } from '@angular/material/progress-spinner';
import { MatSelectModule } from '@angular/material/select';
import { MatSidenavModule } from '@angular/material/sidenav';
import { MatSnackBarModule } from '@angular/material/snack-bar';
import { MatStepperModule } from '@angular/material/stepper';
import { BrowserAnimationsModule } from '@angular/platform-browser/animations';
import { RouterModule } from '@angular/router';

import { RouterRegistry } from '@apto-base-core/router/router-registry';
import { SlotRegistry } from '@apto-base-core/slot/slot-registry';
import { Routes } from '@apto-catalog-frontend/apto-catalog.routes';
import { ConfigurationEffects } from '@apto-catalog-frontend/store/configuration/configuration.effects';
import { ConfigurationRepository } from '@apto-catalog-frontend/store/configuration/configuration.repository';
import { featureKey, reducers } from '@apto-catalog-frontend/store/feature';
import { ProductEffects } from '@apto-catalog-frontend/store/product/product.effects';
import { ProductRepository } from '@apto-catalog-frontend/store/product/product.repository';
import { EffectsModule } from '@ngrx/effects';
import { StoreModule } from '@ngrx/store';
import { AptoBaseCoreModule } from '@apto-base-core/apto-base-core.module';
import { AptoBaseFrontendModule } from '@apto-base-frontend/apto-base-frontend.module';
import { ConfigurationComponent } from './components/configuration/configuration.component';
import { OPButtonComponent } from './components/one-page/o-p-button/o-p-button.component';
import { OPElementDialogComponent } from './components/one-page/o-p-element-dialog/o-p-element-dialog.component';
import { OPFullScreenComponent } from './components/one-page/o-p-full-screen/o-p-full-screen.component';
import { OPStepComponent } from './components/one-page/o-p-step/o-p-step.component';
import { OPStepsComponent } from './components/one-page/o-p-steps/o-p-steps.component';
import { OnePageComponent } from './components/one-page/one-page.component';
import { ProductListComponent } from './components/product-list/product-list.component';
import { QuantityInputComponent } from './components/quantity-input/quantity-input.component';
import { QuantityInputEditableComponent} from './components/common/quantity-input-editable/quantity-input-editable.component';
import { SaveDialogComponent } from './components/common/dialogs/save-dialog/save-dialog.component';
import { ShareDialogComponent } from './components/common/dialogs/share-dialog/share-dialog.component';
import { SidebarSummaryButtonComponent } from './components/shared/sidebar-summary-button/sidebar-summary-button.component';
import { SidebarSummaryPriceComponent } from './components/shared/sidebar-summary-price/sidebar-summary-price.component';
import { SidebarSummaryProgressComponent } from './components/shared/sidebar-summary-progress/sidebar-summary-progress.component';
import { SidebarSummaryRenderImageComponent } from './components/shared/sidebar-summary-render-image/sidebar-summary-render-image.component';
import { SidebarSummaryComponent } from './components/shared/sidebar-summary/sidebar-summary.component';
import { SbsStepComponent } from './components/step-by-step/sbs-step/sbs-step.component';
import { SbsStepsComponent } from './components/step-by-step/sbs-steps/sbs-steps.component';
import { StepByStepComponent } from './components/step-by-step/step-by-step.component';
import { SummaryWrapperComponent } from './components/summary-wrapper/summary-wrapper.component';
import { SummaryComponent } from './components/summary/summary.component';
import { ElementPictureComponent } from '@apto-catalog-frontend/components/common/element-picture/element-picture.component';
import { CloseButtonComponent } from './components/common/close-button/close-button.component';
import { DialogService } from '@apto-catalog-frontend/components/common/dialogs/dialog-service';
import { ConfirmationDialogComponent } from '@apto-catalog-frontend/components/common/dialogs/confirmation-dialog/confirmation-dialog.component';
import { TooltipDirective } from './components/common/tooltip.directive';
import { SummaryConfigurationComponent } from '@apto-catalog-frontend/components/summary/summary-configuration/summary-configuration.component';
import { SummarySectionPriceComponent } from "@apto-catalog-frontend/components/summary/summary-section-price/summary-section-price.component";
import { SummaryFinishMessageComponent } from '@apto-catalog-frontend/components/summary/summary-finish-message/summary-finish-message.component';
import { SectionPictureComponent } from '@apto-catalog-frontend/components/common/section-picture/section-picture.component';
import { SelectableValueRangeComponent } from '@apto-base-frontend/components/selectable-value-range/selectable-value-range.component';
import { SelectableValueTextComponent } from '@apto-base-frontend/components/selectable-value-text/selectable-value-text.component';
import { AptoSearchComponent } from '@apto-catalog-frontend/components/common/apto-search/apto-search.component';
import { DiscountTagComponent } from "@apto-catalog-frontend/components/common/discount-tag/discount-tag.component";
import { UpdatePasswordComponent } from './components/update-password/update-password.component';
import { SbsElementsComponent } from '@apto-catalog-frontend-sbs-elements';
import { AptoCatalogFrontendCustomModule } from '@apto-catalog-frontend-custom-module';

RouterRegistry.registerRoutes(Routes);

@NgModule({
	declarations: [
		ProductListComponent,
		ConfigurationComponent,
		OnePageComponent,
		StepByStepComponent,
		SbsStepsComponent,
		SidebarSummaryComponent,
		SbsElementsComponent,
		SummaryComponent,
		SbsStepComponent,
		SidebarSummaryProgressComponent,
		SidebarSummaryPriceComponent,
		SidebarSummaryRenderImageComponent,
		SidebarSummaryButtonComponent,
		QuantityInputComponent,
    QuantityInputEditableComponent,
    DiscountTagComponent,
		OPStepsComponent,
		OPStepComponent,
		OPButtonComponent,
		SaveDialogComponent,
		ShareDialogComponent,
		OPFullScreenComponent,
		OPElementDialogComponent,
		SummaryWrapperComponent,
    SummaryConfigurationComponent,
    SummarySectionPriceComponent,
    SummaryFinishMessageComponent,
    ElementPictureComponent,
    SectionPictureComponent,
    CloseButtonComponent,
    ConfirmationDialogComponent,
    TooltipDirective,
    SelectableValueRangeComponent,
    SelectableValueTextComponent,
    AptoSearchComponent,
    UpdatePasswordComponent,
  ],
  exports: [
    QuantityInputEditableComponent,
    DiscountTagComponent,
    SummaryConfigurationComponent,
    SummarySectionPriceComponent,
    SummaryFinishMessageComponent,
    OPElementDialogComponent,
	  ElementPictureComponent,
    SectionPictureComponent,
	  CloseButtonComponent,
	  TooltipDirective,
    SelectableValueRangeComponent,
    SelectableValueTextComponent,
    AptoSearchComponent,
    AptoCatalogFrontendCustomModule
  ],
	imports: [
		RouterModule,
		CommonModule,
		HttpClientModule,
		StoreModule.forFeature(featureKey, reducers),
		AptoBaseCoreModule,
		AptoBaseFrontendModule,
		EffectsModule.forFeature([ProductEffects, ConfigurationEffects]),
		MatStepperModule,
		MatFormFieldModule,
		FormsModule,
		ReactiveFormsModule,
		MatIconModule,
		MatCardModule,
		MatButtonModule,
		MatProgressSpinnerModule,
		MatDialogModule,
		MatInputModule,
		MatExpansionModule,
		BrowserAnimationsModule,
		MatSidenavModule,
		MatSelectModule,
		MatSnackBarModule,
		MatDividerModule,
		OverlayModule,
		MatRippleModule,
		MatCheckboxModule,
    AptoCatalogFrontendCustomModule
	],
	providers: [
    ProductRepository,
    ConfigurationRepository,
    DialogService,
  ],
})
export class AptoCatalogFrontendModule {
	public constructor() {
		SlotRegistry.components.set('summary', SummaryComponent);
	}
}
