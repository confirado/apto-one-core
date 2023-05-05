import { register } from 'app/apto-loader';
import Pages from './material-picker-pages';
import Actions from './material-picker-actions';
import Reducers from './material-picker-reducers';
import Components from './material-picker-components';

// load registered controllers
register('controllers', Pages);

// load registered actions
register('factories', Actions);

// load registered reducers
register('providers', Reducers);

// load registered components
register('components', Components);
