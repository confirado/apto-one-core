import { register, setComponentTemplate, setComponentController } from 'app/apto-loader';
import Components from './app-components';
import Partials from './app-partials';
import Actions from  './app-actions';
import Reducers from './app-reducers';

// load registered components
register('components', Components.components);

// load registered actions
register('factories', Actions);

// load registered reducers
register('providers', Reducers);

// set registered component controllers
for (let i = 0; i < Components.controllers.length; i++) {
    setComponentController(Components.controllers[i][0], Components.controllers[i][1]);
}

// set registered component templates
for (let i = 0; i < Components.templates.length; i++) {
    setComponentTemplate(Components.templates[i][0], Components.templates[i][1]);
}

// load registered partials
register('controllers', Partials);