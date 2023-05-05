import { register } from 'app/apto-loader';
import Actions from './float-input-actions';
import Reducers from './float-input-reducers';
import Components from './float-input-components';

const AptoBackendModule = angular.module('AptoBackend');

// load registered actions
register('factories', Actions);

// load registered reducers
register('providers', Reducers);

// load registered components
register('components', Components);