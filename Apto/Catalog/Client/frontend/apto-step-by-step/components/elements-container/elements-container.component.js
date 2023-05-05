import ElementsContainerTemplate from './elements-container.component.html';

const ElementsContainerControllerInject = ['$location', '$ngRedux', '$rootScope', '$element', '$window', 'ConfigurationActions', 'LanguageFactory', 'ConfigurationService', 'SnippetFactory'];
const ElementsContainerController = function ($location, $ngRedux, $rootScope, $element, $window, ConfigurationActions, LanguageFactory, ConfigurationService, SnippetFactory) {
    const self = this;

    function mapStateToThis (state) {
        return {

            section: state.product.selectedSection
        }
    }

    function resetSelect() {
        self.selectedColorGroup = null;
    }

    function finishCurrentStep(sectionId) {
        if (nextStepIsDisabled()) {
            return;
        }
        ConfigurationService.setSectionComplete(sectionId, true);
    }

    function nextStepIsDisabled() {
        for (let i = 0; i < self.section.elements.length; i++) {
            const element = self.section.elements[i];

            if (
                !self.elementIsDisabled(self.section.id, element.id) &&
                !self.elementIsSelected(self.section.id, element.id) &&
                element.isMandatory
            ) {
                return true;
            }
        }
        return self.section.isMandatory && !self.sectionIsSelected(self.section.id);
    }

    function snippet(path, trustAsHtml) {
        return SnippetFactory.get('aptoStepByStep.elementsContainer.' + path, trustAsHtml);
    }

    const reduxSubscribe = $ngRedux.connect(mapStateToThis, {
        setSectionComplete: ConfigurationActions.setSectionComplete
    })(self);

    self.$onDestroy = function () {
        reduxSubscribe();
    };

    $rootScope.$on('CONTINUE_WITH_NEXT_SECTION', () => {
        let doc = $window.document,
            docElem = doc.documentElement,
            rect = $element[0].getBoundingClientRect(),
            offsetY = rect.top + $window.pageYOffset - docElem.clientTop;

        // scroll element to top for mobile views
        $element.find('section')[0].scrollTo(0, 0);

        // window should scroll on only, if top of element is not visible
        if (offsetY < $window.scrollY) {
            $window.scrollTo($window.scrollX, 0); // was offsetY
        }
    });

    self.translate = LanguageFactory.translate;
    self.translateTrustAsHtml = LanguageFactory.translateTrustAsHtml;
    self.elementIsSelected = ConfigurationService.elementIsSelected;
    self.elementIsDisabled = ConfigurationService.elementIsDisabled;
    self.sectionIsSelected = ConfigurationService.sectionIsSelected;
    self.previousState = ConfigurationService.previousState;
    self.nextState = ConfigurationService.nextState;
    self.nextStepIsDisabled = nextStepIsDisabled;
    self.resetSelect = resetSelect;
    self.finishCurrentStep = finishCurrentStep;
    self.snippet = snippet;
};

const ElementsContainer = {
    template: ElementsContainerTemplate,
    controller: ElementsContainerController,
};

ElementsContainerController.$inject = ElementsContainerControllerInject;

export default ['aptoElementsContainer', ElementsContainer];
