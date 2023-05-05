import { register } from 'app/apto-loader';

import AptoFrontendActions from './one-page-actions';
import AptoFrontendReducers from './one-page-reducers';
import AptoFrontendComponents from './one-page-components';

// load registered actions
register('factories', AptoFrontendActions);

// load registered reducers
register('providers', AptoFrontendReducers);

// load registered components
register('components', AptoFrontendComponents);
