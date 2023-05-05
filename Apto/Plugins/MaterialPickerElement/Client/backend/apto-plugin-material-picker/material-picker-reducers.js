import MaterialReducer from './reducers/material.reducer';
import PriceGroupReducer from './reducers/price-group.reducer';
import PoolReducer from './reducers/pool.reducer';
import PropertyReducer from './reducers/property.reducer';
import DefinitionReducer from './reducers/definition.reducer';

// reducers must be an angular provider
const MaterialPickerReducers = [
    MaterialReducer,
    PriceGroupReducer,
    PoolReducer,
    PropertyReducer,
    DefinitionReducer
];

export default MaterialPickerReducers;