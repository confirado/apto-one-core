//@todo test language factory because of massive refactoring
const LanguageFactoryInject = [];
const LanguageFactory = function() {
    const self = this;

    // merge two translations, b's translations will overwrite a's translations, if specific translation not empty
    self.merge = function(a, b) {
        let c = angular.copy(a);
        for (let i in b) {
            if (b.hasOwnProperty(i) && (!a.hasOwnProperty(i) || a[i].trim() === '')) {
                c[i] = b[i];
            }
        }
        return c;
    };

    self.$getInject = ['$sce', '$ngRedux'];
    self.$get = function($sce, $ngRedux) {
        let languageFactory = {
            translate: translate,
            translateTrustAsHtml: translateTrustAsHtml,
            getIsoCode: getIsoCode,
            isEmpty: isEmpty,
            merge: self.merge,
            languages: [],
            activeLanguage: {}
        };

        this.mapStateToThis = function(state) {
            return {
                activeLanguage: state.index.activeLanguage,
                languages: state.index.languages
            }
        };
        $ngRedux.connect(this.mapStateToThis)(languageFactory);

        // check if value is empty
        function isEmpty(value) {
            if (!value) {
                return true;
            }

            for (let key in value) {
                if (value.hasOwnProperty(key) && key[0] !== '$' && value[key]) {
                    return false;
                }
            }
            return true;
        }

        // get translated value in active or first defined language
        function translate(value, isocode) {
            // no value, no translation, simple as that
            if (!value) {
                return '';
            }

            // no isocode given, use default or return value
            if (!isocode) {
                if (typeof languageFactory.activeLanguage !== "undefined") {
                    isocode = languageFactory.activeLanguage.isocode;
                }
            }

            // is translation with given isocode available, else return first available translation
            if (isocode && value[isocode]) {
                return value[isocode];
            } else {
                for (let key in value) {
                    if (value.hasOwnProperty(key) && key[0] !== '$') { // exclude Angular $$hashkey etc.
                        return value[key];
                    }
                }
                return '';
            }
        }

        // return html trusted translation
        function translateTrustAsHtml(value, isocode) {
            return $sce.trustAsHtml(translate(value, isocode));
        }

        // return current isocode
        function getIsoCode() {
            return languageFactory.activeLanguage.isocode;
        }

        return languageFactory;
    };
    self.$get.$inject = self.$getInject;
};

LanguageFactory.$inject = LanguageFactoryInject;

export default ['LanguageFactory', LanguageFactory];