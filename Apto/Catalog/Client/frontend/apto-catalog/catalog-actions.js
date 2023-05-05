import ConfigurationActions from './actions/configuration.actions';
import HumanReadableStateActions from './actions/human-readable-state.actions';
import ProductActions from './actions/product.actions';
import RenderImageActions from './actions/render-image.actions';
import StatePriceActions from './actions/state-price.actions';
import ProductListActions from './actions/product-list.actions';

// actions must be an angular factory
const AptoFrontendActions = [
    ConfigurationActions,
    HumanReadableStateActions,
    ProductActions,
    RenderImageActions,
    StatePriceActions,
    ProductListActions
];

export default AptoFrontendActions;