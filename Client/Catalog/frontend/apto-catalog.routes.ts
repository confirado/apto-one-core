import { RegisteredRoute } from '@apto-base-core/router/router-registry';
import { ConfigurationComponent } from '@apto-catalog-frontend/components/configuration/configuration.component';
import { ProductListComponent } from '@apto-catalog-frontend/components/product-list/product-list.component';
import { SummaryWrapperComponent } from './components/summary-wrapper/summary-wrapper.component';

export const Routes: RegisteredRoute[] = [
	{ route: { path: '', component: ProductListComponent }, priority: 100 },
	{ route: { path: 'product/:productId', component: ConfigurationComponent }, priority: 1000 },
  { route: { path: 'configuration/:configurationType/:configurationId', component: ConfigurationComponent }, priority: 1000 },
	{ route: { path: 'product/:productId/summary', component: SummaryWrapperComponent }, priority: 2000 },
  { route: { path: 'configuration/:configurationType/:configurationId/summary', component: SummaryWrapperComponent }, priority: 2000 },
	{ route: { path: '*', redirectTo: '' }, priority: 2000 },
];
