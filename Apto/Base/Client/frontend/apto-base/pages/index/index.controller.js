const AptoIndexControllerInject = ['$scope', '$window', '$templateCache', '$ngRedux', 'IndexActions', 'LanguageFactory', 'SnippetFactory'];
const AptoIndexController = function($scope, $window, $templateCache, $ngRedux, IndexActions, LanguageFactory, SnippetFactory) {
    const self = this;

    function mapStateToThis (state) {
        return {
            shopSessionUseFallback: state.index.shopSessionUseFallback,
            shopSession: state.index.shopSession,
            sidebarRightOpen: state.index.sidebarRightOpen,
            sidebarRightOpenHTML: state.index.sidebarRightOpenHTML,
            scrollbarWidth: state.index.scrollbarWidth,
            spinnerPage: state.index.spinnerPage,
            metaData: state.index.metaData,
            contentSnippets: state.index.contentSnippets
        }
    }

    const reduxSubscribe = $ngRedux.connect(mapStateToThis, {
        closeSidebarRightAction: IndexActions.closeSidebarRight,
        shopSessionFetch: IndexActions.shopSessionFetch,
        setScrollbarWidth: IndexActions.setScrollbarWidth,
        fetchContentSnippets: IndexActions.fetchContentSnippets
    })(self);

    function closeSidebarRight($event) {
        $event.preventDefault();
        self.closeSidebarRightAction();
    }

    function getScrollbarWith() {
        let inner = $window.document.createElement('p');
        inner.style.width = "100%";
        inner.style.height = "200px";

        let outer = $window.document.createElement('div');
        outer.style.position = "absolute";
        outer.style.top = "0px";
        outer.style.left = "0px";
        outer.style.visibility = "hidden";
        outer.style.width = "200px";
        outer.style.height = "150px";
        outer.style.overflow = "hidden";
        outer.appendChild (inner);

        $window.document.body.appendChild (outer);
        let w1 = inner.offsetWidth;
        outer.style.overflow = 'scroll';
        let w2 = inner.offsetWidth;
        if (w1 == w2) w2 = outer.clientWidth;

        $window.document.body.removeChild (outer);

        return (w1 - w2);
    }

    function snippet(path, trustAsHtml) {
        return SnippetFactory.get('' + path, trustAsHtml);
    }

    function getFaviconSrc() {
        if (snippet('aptoFavicon')) {
                return APTO_API.media + snippet('aptoFavicon.iconMediaLink');
        }
    }

    function init() {
        for (let component in self.contentSnippets) {
            if (!self.contentSnippets.hasOwnProperty(component)) {
                continue;
            }

            SnippetFactory.add(component, self.contentSnippets[component]);
        }
        self.setScrollbarWidth(getScrollbarWith());
    }

    init();

    self.closeSidebarRight = closeSidebarRight;
    self.snippet = snippet;
    self.getFaviconSrc = getFaviconSrc;
    self.translate = LanguageFactory.translate;
    self.translateTrustAsHtml = LanguageFactory.translateTrustAsHtml;

    self.getMetaTitle = function () {
        return self.metaData.title ? self.translate(self.metaData.title) : '';
    };

    self.getMetaDescription = function () {
        return self.metaData.description ? self.translate(self.metaData.description) : '';
    };

    $scope.$on('$destroy', reduxSubscribe);
};

AptoIndexController.$inject = AptoIndexControllerInject;

export default ['AptoIndexController', AptoIndexController];
