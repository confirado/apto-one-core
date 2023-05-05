const AptoShowGrantedInfoInject = ['$parse', 'AclIsGrantedFactory', '$mdMenu', '$rootScope', 'jsonFilter', 'prettifyFilter'];
const AptoShowGrantedInfo = function($parse, AclIsGrantedFactory, $mdMenu, $rootScope, jsonFilter, prettifyFilter) {
    return {
        restrict: 'A',
        link: function(scope, element, attrs) {
            if (AclIsGrantedFactory.showGrantedInfo()) {
                element.bind('contextmenu', function(event) {
                    scope.$apply(function() {
                        event.preventDefault();
                        const messages = JSON.parse(attrs.aptoShowGrantedInfo),
                            myCustomMenu = angular.element('<div class="md-open-menu-container md-whiteframe-z2"><md-menu-content><pre>' + prettifyFilter(jsonFilter(messages)) + '</pre></md-menu-content></div>'),
                            RightClickMenuCtrl = {
                                open: function(event) {
                                    $mdMenu.show({
                                        scope: $rootScope.$new(),
                                        mdMenuCtrl: RightClickMenuCtrl,
                                        element: myCustomMenu,
                                        target: event.target, // used for where the menu animates out of
                                        test: 'test'
                                    });
                                },
                                close: function() {
                                    $mdMenu.hide();
                                },
                                positionMode: function() {
                                    return {
                                        left: 'target',
                                        top: 'target'
                                    };
                                },
                                offsets: function() {
                                    return {
                                        top: 0,
                                        left: 0
                                    };
                                }
                            };

                        RightClickMenuCtrl.open(event);
                    });
                });
            }
        }
    }
};

AptoShowGrantedInfo.$inject = AptoShowGrantedInfoInject;
export default ['aptoShowGrantedInfo', AptoShowGrantedInfo];