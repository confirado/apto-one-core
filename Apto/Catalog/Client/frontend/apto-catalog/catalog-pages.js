// pages
import AptoCatalogController from './pages/catalog.controller';
import AptoCatalogProductController from './pages/product/product.controller';
import AptoCatalogListController from './pages/list/list.controller';
import AptoCatalogHomeController from './pages/home/home.controller';

// dialogs
import AptoDialogConfirmSelectSectionController from './dialogs/confirm-select-section/confirm-select-section.controller';

const AptoFrontendPages = [
    AptoCatalogController,
    AptoCatalogProductController,
    AptoCatalogListController,
    AptoCatalogHomeController,
    AptoDialogConfirmSelectSectionController
];

export default AptoFrontendPages;