import FooterTemplate from './footer.component.html';

const FooterControllerInject = ['$ngRedux', 'IndexActions'];
const FooterController = function($ngRedux, IndexActions) {
    const self = this;

};

const Footer = {
    template: FooterTemplate,
    controller: FooterController
};

FooterController.$inject = FooterControllerInject;

export default ['aptoFooter', Footer];