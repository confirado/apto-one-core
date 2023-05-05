import MaterialActions from './actions/material.actions';
import PriceGroupActions from './actions/price-group.actions';
import PoolActions from './actions/pool.actions';
import PropertyActions from './actions/property.actions';
import DefinitionActions from './actions/definition.actions';


// actions must be an angular factory
const MaterialPickerActions = [
    MaterialActions,
    PriceGroupActions,
    PoolActions,
    PropertyActions,
    DefinitionActions
];

export default MaterialPickerActions;