import { register } from 'app/apto-loader';
import Actions from './app-actions';
import Reducers from './app-reducers';
import Components from './app-components';

// load registered actions
register('factories', Actions);

// load registered reducers
register('providers', Reducers);

// load registered components
register('components', Components);