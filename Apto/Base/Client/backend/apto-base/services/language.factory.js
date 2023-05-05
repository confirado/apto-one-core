const LanguageFactoryInject = ['$sce', '$ngRedux'];
const LanguageFactory = function($sce, $ngRedux) {
    var languageFactory = {
        translate: translate,
        translateTrustAsHtml: translateTrustAsHtml,
        getIsoCode: getIsoCode,
        isEmpty: isEmpty,
        merge: merge,
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
                    return value[key] + ' [' + key + ']';
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

    // merge two translations, b's translations will overwrite a's translations, if specific translation not empty
    function merge(a, b) {
        let c = angular.copy(a);
        for (let i in b) {
            if (b.hasOwnProperty(i) && (!a.hasOwnProperty(i) || a[i].trim() === '')) {
                c[i] = b[i];
            }
        }
        return c;
    }

    // methods available in factory
    this.merge = merge;

    return languageFactory;
};

LanguageFactory.$inject = LanguageFactoryInject;

export default ['LanguageFactory', LanguageFactory];