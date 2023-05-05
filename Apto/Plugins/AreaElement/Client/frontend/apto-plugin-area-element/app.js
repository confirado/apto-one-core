import { register } from 'app/apto-loader';
import Extend from './app-extend';
import Components from './app-components';

// load registered extend
register('providers', Extend);

// load registered components
register('components', Components);