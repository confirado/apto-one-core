import PageHeaderTemplate from './page-header.component.html';

const PageHeaderControllerInject = ['$window', '$ngRedux', 'IndexActions', 'AclIsGrantedFactory', 'LanguageFactory'];
const PageHeaderController = function($window, $ngRedux, IndexActions, AclIsGrantedFactory, LanguageFactory) {
    const self = this;

    $ngRedux.connect(null, {
        setActiveLanguageAction: IndexActions.setActiveLanguage,
        clearAptoCache: IndexActions.clearAptoCache
    })(self);

    function setActiveLanguage(language) {
        self.setActiveLanguageAction(language);
    }

    function onSearchStringKeyUp($event) {
        if ($event.which == 13 || $event.keyCode == 13 || $event.key == "Enter") {
            self.actions.search.fnc(self.config.search.searchString)
        }
    }

    function clearCache(types) {
        self.clearAptoCache(types);
    }

    function openHelp() {
        $window.open('https://docs.confirado.net', '_blank');
    }

    self.setActiveLanguage = setActiveLanguage;
    self.onSearchStringKeyUp = onSearchStringKeyUp;
    self.clearCache = clearCache;
    self.openHelp = openHelp;

    self.$onInit = function () {
        self.config = angular.copy(self.configInput);
        self.actions = angular.copy(self.actionsInput);
        self.aclIsGranted = AclIsGrantedFactory;
        self.languageFactory = LanguageFactory;
    };

    self.$onChanges = function (changes) {
        if (changes.configInput) {
            self.config = angular.copy(self.configInput);
        }

        if (changes.actionsInput) {
            self.actions = angular.copy(self.actionsInput);
        }
    };
};

PageHeaderController.$inject = PageHeaderControllerInject;

const PageHeader = {
    bindings: {
        configInput: '<config',
        actionsInput: '<actions'
    },
    template: PageHeaderTemplate,
    controller: PageHeaderController
};

export default ['aptoPageHeader', PageHeader];