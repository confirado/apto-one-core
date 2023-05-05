import AptoShopController from './pages/shop/shop.controller';
import ProductController from './pages/product/product.controller';
import CategoryController from './pages/category/category.controller';
import GroupController from './pages/group/group.controller';
import PriceMatrixController from './pages/price-matrix/price-matrix.controller';
import FilterPropertyController from './pages/filter/filter-property.controller';
import FilterCategoryController from './pages/filter/filter-category.controller';

const AptoCatalogPages = [
    AptoShopController,
    ProductController,
    CategoryController,
    GroupController,
    PriceMatrixController,
    FilterPropertyController,
    FilterCategoryController
];

export default AptoCatalogPages;