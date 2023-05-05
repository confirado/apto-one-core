import { register } from 'app/apto-loader';
import Actions from './width-height-actions';
import Reducers from './width-height-reducers';
import Components from './width-height-components';

// load registered actions
register('factories', Actions);

// load registered reducers
register('providers', Reducers);

// load registered components
register('components', Components);