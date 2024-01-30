import angular from "angular";
import Components from './hint-components';

const AptoBackendModule = angular.module('AptoBackend');

// load registered components
for (let i = 0; i < Components.length; i++) {
    AptoBackendModule.component(Components[i][0], Components[i][1]);
}