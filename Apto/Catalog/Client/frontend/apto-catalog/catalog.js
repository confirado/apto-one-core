import { register } from 'app/apto-loader';
import Preload from './catalog-preload';
import Routes from './catalog-routes';
import Constants from './catalog-constants';
import Pages from './catalog-pages';
import Services from './catalog-services';
import Actions from './catalog-actions';
import Reducers from './catalog-reducers';
import Components from './catalog-components';

// load registered preload
register('preload', Preload);

// load registered routes
register('routes', Routes);

// load registered constants
register('constants', Constants);

// load registered controllers
register('controllers', Pages);

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
