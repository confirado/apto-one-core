import { CommonModule } from '@angular/common';
import { NgModule } from '@angular/core';
import { MatButtonModule } from '@angular/material/button';
import { MatIconModule } from '@angular/material/icon';
import { MatSelectModule } from '@angular/material/select';
import { MatSidenavModule } from '@angular/material/sidenav';
import { MatToolbarModule } from '@angular/material/toolbar';

import { FullscreenOverlayContainer, OverlayContainer, OverlayModule } from '@angular/cdk/overlay';
import { FormsModule, ReactiveFormsModule } from '@angular/forms';
import { MatCheckboxModule } from '@angular/material/checkbox';
import { MatRippleModule } from '@angular/material/core';
import { BrowserAnimationsModule } from '@angular/platform-browser/animations';
import { RouterModule } from '@angular/router';

import { AptoBaseCoreModule } from '@apto-base-core/apto-base-core.module';
import { RouterRegistry } from '@apto-base-core/router/router-registry';
import { SlotRegistry } from '@apto-base-core/slot/slot-registry';
import { Routes } from '@apto-base-frontend/apto-base.routes';
import { ContentSnippetPipe } from '@apto-base-frontend/pipes/content-snippet.pipe';
import { ContentSnippetRepository } from '@apto-base-frontend/store/content-snippets/content-snippet.repository';
import { featureKey, reducers } from '@apto-base-frontend/store/feature';
import { ShopEffects } from '@apto-base-frontend/store/shop/shop.effects';
import { ShopRepository } from '@apto-base-frontend/store/shop/shop.repository';
import { EffectsModule } from '@ngrx/effects';
import { StoreModule } from '@ngrx/store';
import { BasketComponent } from './components/basket/basket.component';
import { FooterComponent } from './components/footer/footer.component';
import { FrontendComponent } from './components/frontend/frontend.component';
import { HeaderComponent } from './components/header/header.component';
import { HomeComponent } from './components/home/home.component';
import { InputFieldComponent } from './components/input-field/input-field.component';
import { SelectBoxFieldComponent } from './components/select-box-field/select-box-field.component';
import { SelectFieldComponent } from './components/select-field/select-field.component';
import { TextInputFieldComponent } from './components/text-input-field/text-input-field.component';
import { PortalModule } from '@angular/cdk/portal';
import { FrontendUserEffects } from '@apto-base-frontend/store/frontend-user/frontend-user.effects';
import { FrontendUserRepository } from '@apto-base-frontend/store/frontend-user/frontend-user.repository';

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
  ],
	providers: [
    ContentSnippetRepository,
    ShopRepository,
    FrontendUserRepository,
    { provide: OverlayContainer, useClass: FullscreenOverlayContainer }
  ],
})
export class AptoBaseFrontendModule {
	public constructor() {
		SlotRegistry.components.set('header', HeaderComponent);
		SlotRegistry.components.set('footer', FooterComponent);
	}
}
