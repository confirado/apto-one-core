import AptoStepByStepTemplate from './step-by-step.component.html';

const StepByStepControllerInject = ['$ngRedux'];
class StepByStepController {
    constructor ($ngRedux) {
        this.mapStateToThis = function(state) {
            return {
                productDetail: state.product.productDetail,
                selectedSection: state.product.selectedSection
            }
        };

        this.reduxUnsubscribe = $ngRedux.connect(this.mapStateToThis)(this);
    }

    $onChanges(changes) {
    };

    $onDestroy() {
        this.reduxUnsubscribe();
    };
}

StepByStepController.$inject = StepByStepControllerInject;

const StepByStepComponent = {
    bindings: {
    },
    template: AptoStepByStepTemplate,
    controller: StepByStepController
};

export default ['aptoStepByStep', StepByStepComponent];