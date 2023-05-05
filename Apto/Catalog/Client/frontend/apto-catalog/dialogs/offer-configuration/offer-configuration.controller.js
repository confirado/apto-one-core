const AptoOfferConfigurationDialogControllerInject = ['$scope', 'ngDialog', 'ConfigurationService', 'SnippetFactory', 'LanguageFactory'];
const AptoOfferConfigurationDialogController = function($scope, ngDialog, ConfigurationService, SnippetFactory, LanguageFactory) {

    function addOfferConfiguration(addOfferConfigurationForm, $event) {
        $event.preventDefault();
        if(addOfferConfigurationForm.$valid) {
            ConfigurationService.addOfferConfiguration(
                $scope.offerConfiguration.email,
                $scope.offerConfiguration.name,
                {
                    formData: $scope.input,
                    humanReadableState: getHumanReadableState()
                }
            );
            $scope.offerConfiguration.finish = true;
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
        return SnippetFactory.get('AptoOfferConfigurationDialog.' + path);
    }

    function getFields() {
        let fieldsSelector = 'AptoOfferConfigurationDialog.form.fields';
        const snippetFields = SnippetFactory.getNode(fieldsSelector);
        let fields = [];

        // translate fields and make an array from object
        for (let fieldKey in snippetFields) {
            if (!snippetFields.hasOwnProperty(fieldKey)) {
                continue;
            }

            // set field
            let field = {
                key: fieldKey,
                label: SnippetFactory.get(fieldsSelector + '.' + fieldKey + '.label'),
                position: SnippetFactory.get(fieldsSelector + '.' + fieldKey + '.position'),
                required: false,
                type: 'text'
            };

            // set select
            let select = SnippetFactory.get(fieldsSelector + '.' + fieldKey + '.select');
            if (select) {
                field.select = select.split('|');
            }

            // set type
            let type = SnippetFactory.get(fieldsSelector + '.' + fieldKey + '.type');
            if (type) {
                field.type = type;
            }

            // set required
            let required = SnippetFactory.get(fieldsSelector + '.' + fieldKey + '.required');
            if (required) {
                field.required = true;
            }

            fields.push(field);
        }

        // sort fields
        fields.sort((a, b) => {
            if (!a.position && !b.position) {
                return 0;
            }

            if (!a.position) {
                return 1;
            }

            if (!b.position) {
                return -1;
            }

            if (parseInt(a.position) < parseInt(b.position)) {
                return -1;
            }

            if (parseInt(a.position) > parseInt(b.position)) {
                return 1;
            }

            return 0;
        });

        return fields;
    }

    $scope.offerConfiguration = {
        email: null,
        name: null,
        finish: false,
        fields: getFields()
    };

    $scope.input = {};

    $scope.snippet = snippet;
    $scope.addOfferConfiguration = addOfferConfiguration;
    $scope.close = ngDialog.close;
};

AptoOfferConfigurationDialogController.$inject = AptoOfferConfigurationDialogControllerInject;

export default AptoOfferConfigurationDialogController;
