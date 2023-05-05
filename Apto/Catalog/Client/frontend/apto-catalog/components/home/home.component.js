import HomeTemplate from './home.component.html';

const HomeControllerInject = [];
class HomeController {
}

HomeController.$inject = HomeControllerInject;

const HomeComponent = {
    template: HomeTemplate,
    controller: HomeController
};

export default ['aptoHome', HomeComponent];