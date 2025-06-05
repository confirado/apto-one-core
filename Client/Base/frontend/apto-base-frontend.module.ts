import { CommonModule } from '@angular/common';
import { NgModule } from '@angular/core';
import { FormsModule, ReactiveFormsModule } from '@angular/forms';
import { RouterModule } from '@angular/router';
import { BrowserAnimationsModule } from '@angular/platform-browser/animations';
import { FullscreenOverlayContainer, OverlayContainer, OverlayModule } from '@angular/cdk/overlay';
import { PortalModule } from '@angular/cdk/portal';
import { MatCheckboxModule } from '@angular/material/checkbox';
import { MatRippleModule } from '@angular/material/core';
import { MatButtonModule } from '@angular/material/button';
import { MatIconModule } from '@angular/material/icon';
import { MatSelectModule } from '@angular/material/select';
import { MatSidenavModule } from '@angular/material/sidenav';
import { MatToolbarModule } from '@angular/material/toolbar';
import { MatDialogModule } from '@angular/material/dialog';
import { MatDividerModule } from '@angular/material/divider';
import { MatSliderModule } from '@angular/material/slider';

import { EffectsModule } from '@ngrx/effects';
import { StoreModule } from '@ngrx/store';

import { AptoBaseCoreModule } from '@apto-base-core/apto-base-core.module';
import { RouterRegistry } from '@apto-base-core/router/router-registry';
import { SlotRegistry } from '@apto-base-core/slot/slot-registry';

import { Routes } from '@apto-base-frontend/apto-base.routes';
import { featureKey, reducers } from '@apto-base-frontend/store/feature';

import { ShopEffects } from '@apto-base-frontend/store/shop/shop.effects';
import { FrontendUserEffects } from '@apto-base-frontend/store/frontend-user/frontend-user.effects';
import { ShopRepository } from '@apto-base-frontend/store/shop/shop.repository';
import { FrontendUserRepository } from '@apto-base-frontend/store/frontend-user/frontend-user.repository';
import { ContentSnippetRepository } from '@apto-base-frontend/store/content-snippets/content-snippet.repository';
import { ContentSnippetPipe } from '@apto-base-frontend/pipes/content-snippet.pipe';

import { BasketComponent } from '@apto-base-frontend-basket';
import { FooterComponent } from '@apto-base-frontend/components/footer/footer.component';
import { FrontendComponent } from '@apto-base-frontend/components/frontend/frontend.component';
import { HeaderComponent } from '@apto-base-frontend-header';
import { HomeComponent } from '@apto-base-frontend/components/home/home.component';
import { InputFieldComponent } from '@apto-base-frontend/components/input-field/input-field.component';
import { SelectBoxFieldComponent } from '@apto-base-frontend/components/select-box-field/select-box-field.component';
import { SelectFieldComponent } from '@apto-base-frontend/components/select-field/select-field.component';
import { TextInputFieldComponent } from '@apto-base-frontend/components/text-input-field/text-input-field.component';
import { FrontendUsersLoginComponent } from '@apto-base-frontend/components/frontend-users-login/frontend-users-login.component';
import { LoginCloseButtonComponent } from '@apto-base-frontend/components/frontend-users-login/login-close-button/login-close-button.component';
import { SliderComponent } from '@apto-base-frontend/components/slider/slider.component';
import { ForgotPasswordComponent } from './components/frontend-users-login/forgot-password/forgot-password.component';
import { AuthMessageBusService } from '@apto-base-frontend/services/auth-message-bus.service';
import { AptoBaseFrontendCustomModule } from '@apto-base-frontend-custom-module';
import { FrontendShopLoginComponent } from '@apto-base-frontend/components/frontend-shop-login/frontend-shop-login.component';

RouterRegistry.registerRoutes(Routes);

@NgModule({
	declarations: [
		FrontendComponent,
		HeaderComponent,
		FooterComponent,
		HomeComponent,
		ContentSnippetPipe,
		BasketComponent,
		InputFieldComponent,
		SelectFieldComponent,
		TextInputFieldComponent,
		SelectBoxFieldComponent,
    FrontendUsersLoginComponent,
    LoginCloseButtonComponent,
    SliderComponent,
    ForgotPasswordComponent,
    FrontendShopLoginComponent,
  ],
	exports: [
		FrontendComponent,
		HeaderComponent,
		FooterComponent,
		HomeComponent,
		ContentSnippetPipe,
		InputFieldComponent,
		SelectFieldComponent,
		TextInputFieldComponent,
		SelectBoxFieldComponent,
    FrontendUsersLoginComponent,
    LoginCloseButtonComponent,
    SliderComponent,
    ForgotPasswordComponent,
    AptoBaseFrontendCustomModule,
    FrontendShopLoginComponent,
  ],
  imports: [
    CommonModule,
    RouterModule,
    AptoBaseCoreModule,
    StoreModule.forFeature(featureKey, reducers),
    EffectsModule.forFeature([ShopEffects, FrontendUserEffects]),
    MatToolbarModule,
    MatSelectModule,
    BrowserAnimationsModule,
    MatSidenavModule,
    MatButtonModule,
    MatIconModule,
    OverlayModule,
    MatRippleModule,
    FormsModule,
    ReactiveFormsModule,
    MatCheckboxModule,
    PortalModule,
    MatDialogModule,
    MatDividerModule,
    MatSliderModule,
    AptoBaseFrontendCustomModule,
  ],
	providers: [
    ContentSnippetRepository,
    ShopRepository,
    FrontendUserRepository,
    { provide: OverlayContainer, useClass: FullscreenOverlayContainer },
    AuthMessageBusService
  ],
})
export class AptoBaseFrontendModule {
	public constructor() {
		SlotRegistry.components.set('header', HeaderComponent);
		SlotRegistry.components.set('footer', FooterComponent);
	}
}
