import { register } from 'app/apto-loader';
import Constants from './catalog-constants';
import Pages from './catalog-pages';
import Actions from './catalog-actions';
import Reducers from './catalog-reducers';
import Components from './catalog-components';

// load registered constants
register('constants', Constants);

// load registered controllers
register('controllers', Pages);

// load registered actions
register('factories', Actions);

// load registered reducers
register('providers', Reducers);

// load registered components
register('components', Components);
