import TranslatedValueTemplate from './translated-value.component.html';

const TranslatedValueControllerInject = ['$attrs', 'LanguageFactory'];
const TranslatedValueController = function($attrs, LanguageFactory) {
    const self = this;

    // change language on click
    function setLanguage(language) {
        self.selectedIsocode = language.isocode;
        self.selectedName = language.name;
        self.required = self.isRequired && noOtherTranslationSet();
    }

    // find language by isocode, return first language if non found
    function getLanguageByIsocode(isocode) {
        const languages = LanguageFactory.languages;
        for (let i in languages) {
            if (languages.hasOwnProperty(i) &&
                languages[i].isocode == isocode) {
                return languages[i];
            }
        }
        return languages[0];
    }

    // check, if no other translation value is set other than the current selected
    function noOtherTranslationSet() {
        if (!self.translatedValue || self.translatedValue.length == 0) {
            return true;
        }
        for (let i in self.translatedValue) {
            if (self.translatedValue.hasOwnProperty(i) &&
                self.selectedIsocode != i &&
                self.translatedValue[i] &&
                self.translatedValue[i].length > 0) {
                return false;
            }
        }
        return true;
    }

    function onUpdateSourceCode(sourceCode) {
        self.translatedValue[self.selectedIsocode] = sourceCode;
    }

    self.setLanguage = setLanguage;
    self.onUpdateSourceCode = onUpdateSourceCode;

    self.$onInit = function () {
        // @todo when entering in a language that has no translation set but another translation is set required field doesent work the way it should
        self.translatedValue = self.translatedValue ? self.translatedValue : {};
        self.label = angular.copy(self.labelInput);
        self.type = angular.copy(self.typeInput);
        self.isRequired = self.required = ('required' in $attrs) && (typeof $attrs.required) !== 'undefined';
        self.languageFactory = LanguageFactory;
        self.selectedIsocode = self.selectedIsocode ? self.selectedIsocode : LanguageFactory.activeLanguage.isocode;
        setLanguage(getLanguageByIsocode(self.selectedIsocode));
    };

    self.$onChanges = function (changes) {
        if (changes.labelInput) {
            self.label = angular.copy(self.labelInput);
        }

        if (changes.typeInput) {
            self.type = angular.copy(self.typeInput);
        }
    };

    self.$doCheck = function () {
        // @ todo i think we should find a better solution here to make sure self.translatedValue cant be an empty array
        if(typeof self.translatedValue === "undefined" || self.translatedValue === null || typeof self.translatedValue.length !== "undefined") {
            self.translatedValue = {}
        }
    };
};

TranslatedValueController.$inject = TranslatedValueControllerInject;

const TranslatedValue = {
    bindings: {
        translatedValue: '=',
        labelInput: '<label',
        typeInput: '<type'
    },
    template: TranslatedValueTemplate,
    controller: TranslatedValueController
};

export default ['aptoTranslatedValue', TranslatedValue];