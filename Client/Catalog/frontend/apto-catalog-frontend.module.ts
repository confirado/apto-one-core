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
import { OnePageComponent } from '@apto-catalog-frontend-one-page';
import { ProductListComponent } from '@apto-catalog-frontend-product-list';
import { QuantityInputComponent } from './components/quantity-input/quantity-input.component';
import { QuantityInputEditableComponent} from '@apto-catalog-frontend-quantity-input-editable';
import { SaveDialogComponent } from '@apto-catalog-frontend-save-dialog';
import { ShareDialogComponent } from './components/common/dialogs/share-dialog/share-dialog.component';
import { SidebarSummaryButtonComponent } from './components/shared/sidebar-summary-button/sidebar-summary-button.component';
import { SidebarSummaryPriceComponent } from '@apto-catalog-frontend-sidebar-summary-price';
import { SidebarSummaryProgressComponent } from './components/shared/sidebar-summary-progress/sidebar-summary-progress.component';
import { SidebarSummaryRenderImageComponent } from '@apto-catalog-frontend-sidebar-summary-render-image';
import { SidebarSummaryComponent } from '@apto-catalog-frontend-sidebar-summary';
import { StepByStepComponent } from './components/step-by-step/step-by-step.component';
import { SummaryWrapperComponent } from './components/summary-wrapper/summary-wrapper.component';
import { SummaryComponent } from './components/summary/summary.component';
import { ElementPictureComponent } from '@apto-catalog-frontend/components/common/element-picture/element-picture.component';
import { CloseButtonComponent } from './components/common/close-button/close-button.component';
import { DialogService } from '@apto-catalog-frontend/components/common/dialogs/dialog-service';
import { TooltipDirective } from './components/common/tooltip.directive';
import { SummaryConfigurationComponent } from '@apto-catalog-frontend/components/summary/summary-configuration/summary-configuration.component';
import { SummarySectionPriceComponent } from '@apto-catalog-frontend-summary-section-price';
import { SummaryFinishMessageComponent } from '@apto-catalog-frontend/components/summary/summary-finish-message/summary-finish-message.component';
import { SectionPictureComponent } from '@apto-catalog-frontend/components/common/section-picture/section-picture.component';
import { SelectableValueRangeComponent } from '@apto-base-frontend/components/selectable-value-range/selectable-value-range.component';
import { SelectableValueTextComponent } from '@apto-base-frontend/components/selectable-value-text/selectable-value-text.component';
import { AptoSearchComponent } from '@apto-catalog-frontend/components/common/apto-search/apto-search.component';
import { DiscountTagComponent } from "@apto-catalog-frontend/components/common/discount-tag/discount-tag.component";
import { UpdatePasswordComponent } from './components/update-password/update-password.component';
import { SbsElementsComponent } from '@apto-catalog-frontend-sbs-elements';
import { SbsStepsComponent } from '@apto-catalog-frontend-sbs-steps';
import { SbsStepComponent } from '@apto-catalog-frontend-sbs-step';
import { AptoCatalogFrontendCustomModule } from '@apto-catalog-frontend-custom-module';
import { ElementAttachmentComponent } from '@apto-catalog-frontend/components/common/element-attachment/element-attachment.component';
import { OfferConfigurationButtonComponent } from '@apto-catalog-frontend/components/shared/offer-configuration-button/offer-configuration-button.component';
import { OfferConfigurationDialogComponent } from "@apto-catalog-frontend/components/common/dialogs/offer-configuration-dialog/offer-configuration-dialog.component";
import { ConfirmationDialogComponent } from '@apto-catalog-frontend-confirmation-dialog';

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
    OfferConfigurationButtonComponent,
    OfferConfigurationDialogComponent,
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
    ElementAttachmentComponent,
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
    AptoCatalogFrontendCustomModule,
    ElementAttachmentComponent,
    OPFullScreenComponent
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
