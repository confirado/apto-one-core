import { register } from 'app/apto-loader';
import Actions from './price-per-unit-actions';
import Reducers from './price-per-unit-reducers';
import Components from './price-per-unit-components';

// load registered actions
register('factories', Actions);

// load registered reducers
register('providers', Reducers);

// load registered components
register('components', Components);