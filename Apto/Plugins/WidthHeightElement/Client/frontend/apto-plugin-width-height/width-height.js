import { register } from 'app/apto-loader';
import Extend from './width-height-extend';
import AptoFrontendComponents from './width-height-components';

// load registered extend
register('providers', Extend);

// load registered components
register('components', AptoFrontendComponents);