import { register } from 'app/apto-loader';
import Actions from './material-picker-actions';
import Reducers from './material-picker-reducers';
import Components from './material-picker-components';

// load registered actions
register('factories', Actions);

// load registered reducers
register('providers', Reducers);

// load registered components
register('components', Components);
