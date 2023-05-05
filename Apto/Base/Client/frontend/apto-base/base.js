import { register } from 'app/apto-loader';
import Configs from './base-config';
import Routes from './base-routes';
import Constants from './base-constants';
import Filters from './base-filter';
import Pages from './base-pages';
import Partials from './base-partials';
import Actions from './base-actions';
import Reducers from './base-reducers';
import Services from './base-services';
import Components from './base-components';
import Directives from './base-directives';
import Preload from "./base-preload";

// load registered preload
register('preload', Preload);

// load registered configs
register('configs', Configs);

// load registered routes
register('routes', Routes);

// load registered constants
register('constants', Constants);

// load registered filters
register('filters', Filters);

// load registered controllers
register('controllers', Pages);

// load registered partials
register('controllers', Partials);

// load registered provider
register('providers', Services.provider);

// load registered actions
register('factories', Actions);

// load registered reducers
register('providers', Reducers);

// load registered factories
register('factories', Services.factories);

// load registered services
register('services', Services.services);

// load registered components
register('components', Components);

// load registered directives
register('directives', Directives);