import { register } from 'app/apto-loader';
import Pages from './image-upload-pages';
import Actions from './image-upload-actions';
import Reducers from './image-upload-reducers';
import Components from './image-upload-components';

// load registered controllers
register('controllers', Pages);

// load registered actions
register('factories', Actions);

// load registered reducers
register('providers', Reducers);

// load registered components
register('components', Components);
