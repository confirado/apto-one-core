import ShopReducer from './reducers/shop.reducer';
import ProductReducer from './reducers/product.reducer';
import SectionReducer from './reducers/section.reducer';
import ElementReducer from './reducers/element.reducer';
import RuleReducer from './reducers/rule.reducer';
import CategoryReducer from './reducers/category.reducer';
import GroupReducer from './reducers/group.reducer';
import PriceMatrixReducer from './reducers/price-matrix.reducer';
import BatchManipulationReducer from './reducers/batch-manipulation.reducer';
import FilterPropertyReducer from './reducers/filter-property.reducer';
import FilterCategoryReducer from './reducers/filter-category.reducer';

// reducers must be an angular provider
const AptoCatalogReducers = [
    ShopReducer,
    ProductReducer,
    SectionReducer,
    ElementReducer,
    RuleReducer,
    CategoryReducer,
    GroupReducer,
    PriceMatrixReducer,
    BatchManipulationReducer,
    FilterPropertyReducer,
    FilterCategoryReducer
];

export default AptoCatalogReducers;