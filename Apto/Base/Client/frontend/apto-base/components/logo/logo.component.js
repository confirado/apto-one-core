import LogoTemplate from './logo.component.html';

const LogoControllerInject = ['$window', 'SnippetFactory'];
const LogoController = function($window, SnippetFactory) {
    const self = this;
    angular.element($window).bind('resize', getLogoSrc);

    function snippet(path, trustAsHtml) {
        return SnippetFactory.get('aptoLogo.' + path, trustAsHtml);
    }

    function openNewTab() {
        if (snippet('openInNewTab') === "active") {
            return "_blank";
        } else {
            return "_self";
        }
    }

    function getLogoSrc() {
        let mediaQuery = window.matchMedia('(max-width: 767px)');

        if (angular.element('.apto').hasClass('step-by-step')) {
            mediaQuery = window.matchMedia('(max-width: 1023px)');
        }

        if (self.snippet('logoImg')) {
            if (mediaQuery.matches) {
                return self.mediaUrl + self.snippet('logoImgMobile');
            } else {
                return self.mediaUrl + self.snippet('logoImg');
            }
        }
    }

    function getLogoMeasurements() {
        let style = {};
        let mediaQuery = window.matchMedia('(max-width: 767px)');

        if (angular.element('.apto').hasClass('step-by-step')) {
            mediaQuery = window.matchMedia('(max-width: 1023px)');
        }

        let contentSnippetWidth = 'logoWidthInPx';
        let contentSnippetHeight = 'logoHeightInPx';

        if (mediaQuery.matches) {
            contentSnippetWidth = 'logoWidthInPxMobile';
            contentSnippetHeight = 'logoHeightInPxMobile';
        }

        if(self.snippet(contentSnippetWidth)) {
            let width = self.snippet(contentSnippetWidth);
            width = ptr(width);
            style.width = width;
        }
        if(self.snippet(contentSnippetHeight)) {
            let height = self.snippet(contentSnippetHeight);
            height = ptr(height);
            style.height = height;
        }
        return style;
    }

    //convert px to rem
    function ptr(px){
        let rem = parseInt(px) / 16 + 'rem';
        return rem;
    }

    self.snippet = snippet;
    self.openNewTab = openNewTab;
    self.getLogoSrc = getLogoSrc;
    self.getLogoMeasurements = getLogoMeasurements;
    self.mediaUrl = APTO_API.media;
};

const Logo = {
    template: LogoTemplate,
    controller: LogoController
};

LogoController.$inject = LogoControllerInject;

export default ['aptoLogo', Logo];
