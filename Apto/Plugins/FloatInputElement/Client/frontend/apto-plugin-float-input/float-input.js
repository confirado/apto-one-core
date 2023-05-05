import { register } from 'app/apto-loader';
import Components from './float-input-components';
import Extend from "./float-input-extend";

// load registered extend
register('providers', Extend);
// load registered components
register('components', Components);