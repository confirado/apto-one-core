import 'trumbowyg/dist/trumbowyg';
import 'trumbowyg/dist/langs/de.min';
import 'trumbowyg/dist/plugins/fontsize/trumbowyg.fontsize';
import 'trumbowyg/dist/plugins/table/trumbowyg.table';
import 'trumbowyg/dist/plugins/template/trumbowyg.template';
import '../libs/trumbowyg/plugins/colors/trumbowyg.colors';
import TrumbowygSvgPath from 'trumbowyg/dist/ui/icons.svg';

import sanitizeHtml from 'sanitize-html';

const DirectiveInject = ['APTO_TRUMBOWYG_TEMPLATES', 'APTO_DIST_PATH_URL'];
const Directive = function(APTO_TRUMBOWYG_TEMPLATES, APTO_DIST_PATH_URL) {
    return {
        restrict: 'A',
        scope: {
            sourceCode: '=',
            onSourceCodeChanged: '&'
        },
        link: function (scope, element, attrs) {
            element.addClass('apto-trumbowyg-reset-css');

            const trumbowygConfig = {
                lang: 'de',
                svgPath: APTO_DIST_PATH_URL + TrumbowygSvgPath,
                btns: [
                    ['viewHTML'],
                    ['formatting'],
                    ['fontsize'],
                    ['strong', 'em'],
                    ['foreColor', 'backColor'],
                    ['link', 'table'],
                    ['insertImage'],
                    ['justifyLeft', 'justifyCenter', 'justifyRight', 'justifyFull'],
                    ['unorderedList', 'orderedList'],
                    ['removeformat'],
                    ['fullscreen']
                ],
                btnsDef: {
                    formatting: {
                        dropdown: ['p', 'h2', 'h3', 'h4'],
                        ico: 'p'
                    }
                },
                plugins: {
                },
                resetCss: false,
                removeformatPasted: true
            };

            if (APTO_TRUMBOWYG_TEMPLATES.length > 0) {
                trumbowygConfig.btns.push(['template']);
                trumbowygConfig.plugins.templates = APTO_TRUMBOWYG_TEMPLATES;
            }

            const sanitizeHtmlConfig = {
                allowedTags: [
                    'div', 'p', 'ol', 'ul', 'li', 'h2', 'h3', 'h4', 'img', 'span', 'strong', 'b', 'em', 'i', 'a', 'blockquote', 'font', 'br', 'table', 'tbody', 'tr', 'th', 'td'
                ],
                allowedAttributes: {
                    '*': ['style', 'class'],
                    'a': ['href', 'title', 'target'],
                    'img': ['src', 'title', 'alt'],
                    'font': ['color']
                }
            };

            sanitizeHtml.defaults.allowedTags = sanitizeHtmlConfig.allowedTags;
            sanitizeHtml.defaults.allowedAttributes = sanitizeHtmlConfig.allowedAttributes;

            function sanitize(sourceCode) {
                return sanitizeHtml(sourceCode, sanitizeHtmlConfig);
            }

            function onTbwHtmlChanged(event) {
                scope.onSourceCodeChanged({
                    sourceCode: sanitize(
                        element.trumbowyg('html')
                    )
                });
            }

            // init trumbowyg
            element.trumbowyg(trumbowygConfig);

            // bin on change event
            element.trumbowyg().on('tbwchange', onTbwHtmlChanged);

            scope.$on('$destroy', () => {
                element.trumbowyg('destroy');
            });

            scope.$watch('sourceCode', () =>{
                element.trumbowyg('html', scope.sourceCode ? sanitize(scope.sourceCode) : '');
            });
        }
    }
};

Directive.$inject = DirectiveInject;
export default ['aptoTrumbowyg', Directive];
