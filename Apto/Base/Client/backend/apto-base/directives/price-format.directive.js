const DirectiveInject = ['$compile'];
const Directive = function ($compile) {
    return {
        restrict: 'A',
        link: function (scope, element, attrs) {
            let price = attrs.priceFormat;
            price = price.match(/^(.*)(.{2})/).slice(1).join(',');

            if(price.substring(0, 1) === ',') {
                price = '0'+  price;
            }

            element[0].innerHTML = price;
        }
    }
};

Directive.$inject = DirectiveInject;
export default ['priceFormat', Directive];