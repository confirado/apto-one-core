import ShopActions from './actions/shop.actions';
import CategoryActions from './actions/category.actions';
import GroupActions from './actions/group.actions';
import ProductActions from './actions/product.actions';
import SectionActions from './actions/section.actions';
import ElementActions from './actions/element.actions';
import RuleActions from './actions/rule.actions';
import PriceMatrixActions from './actions/price-matrix.actions';
import BatchManipulationActions from './actions/batch-manipulation.actions';
import FilterPropertyActions from './actions/filter-property.actions';
import FilterCategoryActions from './actions/filter-category.actions';

// actions must be an angular factory
const AptoCatalogActions = [
    ShopActions,
    CategoryActions,
    GroupActions,
    ProductActions,
    SectionActions,
    ElementActions,
    RuleActions,
    PriceMatrixActions,
    BatchManipulationActions,
    FilterPropertyActions,
    FilterCategoryActions
];

export default AptoCatalogActions;