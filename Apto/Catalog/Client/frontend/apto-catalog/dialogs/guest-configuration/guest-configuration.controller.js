const AptoGuestConfigurationDialogControllerInject = ['$scope', 'ngDialog', 'ConfigurationService', 'SnippetFactory', 'LanguageFactory'];
const AptoGuestConfigurationDialogController = function($scope, ngDialog, ConfigurationService, SnippetFactory, LanguageFactory) {

    function addGuestConfiguration(addGuestConfigurationForm, $event) {
        $event.preventDefault();
        if(addGuestConfigurationForm.$valid) {
            ConfigurationService.addGuestConfiguration(
                $scope.guestConfiguration.email,
                $scope.guestConfiguration.name,
                true, '',
                {
                    humanReadableState: getHumanReadableState()
                }
            );
            $scope.guestConfiguration.finish = true;
        }
    }

    function getHumanReadableState() {
        let humanReadableSate = {},
            currentState = ConfigurationService.getStateSummary();

        currentState.sections.forEach(function(section) {
            const sectionName = LanguageFactory.translate(section.name);

            section.elements.forEach(function(element) {
                let elementName = LanguageFactory.translate(element.name),
                    elementValues = {},
                    elementObject = ConfigurationService.getElementById(section.id, element.id);

                // check for human readable values
                if (typeof elementObject.humanReadableState !== 'undefined') {

                    // translate human readable value
                    for (let elementValue in elementObject.humanReadableState) {
                        if (!elementObject.humanReadableState.hasOwnProperty(elementValue)) {
                            continue;
                        }

                        elementValues[elementValue] = LanguageFactory.translate(elementObject.humanReadableState[elementValue]);
                    }
                }

                if (!humanReadableSate[sectionName]) {
                    humanReadableSate[sectionName] = [];
                }

                humanReadableSate[sectionName].push({
                    id: element.id,
                    name: elementName,
                    previewImage: element.previewImage ? APTO_API.media + element.previewImage.mediaFile.path + '/' + element.previewImage.mediaFile.filename + '.' + element.previewImage.mediaFile.extension : null,
                    values: elementValues
                });
            });
        });

        return humanReadableSate;
    }

    function snippet(path) {
        return SnippetFactory.get('AptoGuestConfigurationDialog.' + path);
    }

    $scope.guestConfiguration = {
        email: null,
        name: null,
        finish: false
    };

    $scope.snippet = snippet;
    $scope.addGuestConfiguration = addGuestConfiguration;
    $scope.close = ngDialog.close;
};

AptoGuestConfigurationDialogController.$inject = AptoGuestConfigurationDialogControllerInject;

export default AptoGuestConfigurationDialogController;
