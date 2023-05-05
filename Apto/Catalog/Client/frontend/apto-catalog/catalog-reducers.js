import ConfigurationReducer from './reducers/configuration.reducer';
import PersistedPropertiesReducer from './reducers/persisted-properties.reducer';
import FavoriteDesignsReducer from './reducers/favorite-designs.reducer';
import HumanReadableStateReducer from './reducers/human-readable-state.reducer';
import ProductReducer from './reducers/product.reducer';
import RenderImageReducer from './reducers/render-image.reducer';
import StatePriceReducer from './reducers/state-price.reducer';
import ProductListReducer from './reducers/product-list.reducer';

// reducers must be an angular provider
const AptoFrontendReducers = [
    ProductReducer,
    ConfigurationReducer,
    PersistedPropertiesReducer,
    RenderImageReducer,
    StatePriceReducer,
    HumanReadableStateReducer,
    FavoriteDesignsReducer,
    ProductListReducer
];

export default AptoFrontendReducers;