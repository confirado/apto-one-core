import { register } from 'app/apto-loader';
import Pages  from './app-pages';
import Actions from './app-actions';
import Reducers from './app-reducers';

// load registered controllers
register('controllers', Pages);

// load registered actions
register('factories', Actions);

// load registered reducers
register('providers', Reducers);
