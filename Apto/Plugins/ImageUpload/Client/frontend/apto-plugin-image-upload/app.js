import { register, setComponentTemplate, setComponentController } from 'app/apto-loader';
import Constants from './app-constants';
import Actions from './app-actions';
import Directives from './app-directives';
import Reducers from './app-reducers';
import Components from './app-components';
import Services from "./app-services";

// register constants
register('constants', Constants);

// load registered provider
register('providers', Services.provider);

// load registered actions
register('factories', Actions);

// load registered reducers
register('providers', Reducers);

// load registered factories
register('factories', Services.factories);

// load registered services
register('services', Services.services);

// load registered directives
register('directives', Directives);

// load registered components
register('components', Components.components);

// set registered component controllers
for (let i = 0; i < Components.controllers.length; i++) {
    setComponentController(Components.controllers[i][0], Components.controllers[i][1]);
}

// set registered component templates
for (let i = 0; i < Components.templates.length; i++) {
    setComponentTemplate(Components.templates[i][0], Components.templates[i][1]);
}

