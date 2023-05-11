import update from 'immutability-helper';

const DefinitionReducerInject = ['AptoReducersProvider'];
const DefinitionReducer = function(AptoReducersProvider) {
    const TYPE_NS = 'PLUGIN_IMAGE_UPLOAD_DEFINITION_';
    const elementTemplate = {
        element: null,
        editable: false,
        origPerspective: null,
        background: null,
        timestamp: null,
        userUpload: {
            loading: false,
            error: false,
            progress: 0
        },
        userImageUploadErrors: [],
        renderImage: null,
        //multi Fabric Items update
        fabricItemsOnCanvas: {
            text: {},
            image: {},
            clipArt: {}
        }
    };

    const initialState = {
        elements: {},
        activeElement: null,
        activeItem: null,
        poolItems: null,
        numberOfMaterials: 0,
        userUploads: []
    };

    function getType(type) {
        return TYPE_NS + type;
    }

    this.reducer = function (state, action) {
        if (typeof state === 'undefined') {
            state = angular.copy(initialState);
        }

        switch (action.type) {
            case getType('SET_USER_IMAGE_ELEMENT'):
                // init element if not already initialized
                if (!state.elements[action.payload.id]) {
                    let stateUpdate = {};
                    stateUpdate[action.payload.id] = {
                        $set: angular.copy(elementTemplate)
                    };

                    state = update(state, {
                        elements: stateUpdate
                    });
                }


                // update active element
                state = update(state, {
                    activeElement: {
                        $set: action.payload.id
                    }
                });

                // update element
                let element = {};
                element[action.payload.id] = {
                    element: {
                        $set: action.payload
                    }
                };

                state = update(state, {
                    elements: element
                });
                break;

            case getType('ADD_USER_UPLOAD'):
                let newUploads = angular.copy(state.userUploads);
                newUploads.push(action.payload);

                // update active element
                state = update(state, {
                    userUploads: {
                        $set: newUploads
                    }
                });
                break;

            case getType('SET_USER_IMAGE_EDITABLE'):
                let editable = {};
                editable[action.payload.elementId] = {
                    editable: {
                        $set: action.payload.editable
                    }
                };

                state = update(state, {
                    elements: editable
                });
                break;

            case getType('SET_ORIG_PERSPECTIVE'):
                let origPerspective = {};
                origPerspective[action.payload.elementId] = {
                    origPerspective: {
                        $set: action.payload.perspective
                    }
                };

                state = update(state, {
                    elements: origPerspective
                });
                break;

            case getType('SET_BACKGROUND'):
                let background = {};
                background[action.payload.elementId] = {
                    background: {
                        $set: action.payload.background
                    }
                };

                state = update(state, {
                    elements: background
                });
                break;

            case getType('SET_TIMESTAMP'):
                let timestamp = {};
                timestamp[action.payload.elementId] = {
                    timestamp: {
                        $set: action.payload.timestamp
                    }
                };
                state = update(state, {
                    elements: timestamp
                });
                break;

            case getType('UPLOAD_USER_IMAGE_LOADING'):
                let loading = {};
                loading[action.payload.elementId] = {
                    userUpload: {
                        loading: {
                            $set: action.payload.loading
                        }
                    }
                };

                state = update(state, {
                    elements: loading
                });
                break;

            case getType('UPLOAD_USER_IMAGE_PROGRESS'):
                let progress = {};
                progress[action.payload.elementId] = {
                    userUpload: {
                        progress: {
                            $set: action.payload.progress
                        }
                    }
                };

                state = update(state, {
                    elements: progress
                });
                break;

            case getType('UPLOAD_USER_IMAGE_ERROR'):
                let error = {};
                error[action.payload.elementId] = {
                    userUpload: {
                        error: {
                            $set: action.payload.error
                        }
                    }
                };

                state = update(state, {
                    elements: error
                });
                break;

            case getType('SET_USER_IMAGE_UPLOAD_ERRORS'):
                let errors = {};
                errors[action.payload.elementId] = {
                    userImageUploadErrors: {
                        $set: action.payload.userImageUploadErrors
                    }
                };

                state = update(state, {
                    elements: errors
                });
                break;

            case getType('SET_ITEM_ON_CANVAS'):
                let items = {};
                let item = {
                    itemType: action.payload.itemType,
                    fabricItemInput: action.payload.fabricItemInput,
                    fabricItemOptions: action.payload.fabricItemOptions
                }
                let fabricItemsOnCanvas = state.elements[action.payload.elementId].fabricItemsOnCanvas;
                if (!fabricItemsOnCanvas) {
                    fabricItemsOnCanvas = angular.copy(elementTemplate.fabricItemsOnCanvas);
                }

                if (Array.isArray(fabricItemsOnCanvas.text)) {
                    fabricItemsOnCanvas = update(fabricItemsOnCanvas, {
                        text: {
                            $set: {}
                        }
                    });
                }

                if (Array.isArray(fabricItemsOnCanvas.image)) {
                    fabricItemsOnCanvas = update(fabricItemsOnCanvas, {
                        image: {
                            $set: {}
                        }
                    });
                }

                if (Array.isArray(fabricItemsOnCanvas.clipArt)) {
                    fabricItemsOnCanvas = update(fabricItemsOnCanvas, {
                        clipArt: {
                            $set: {}
                        }
                    });
                }

                fabricItemsOnCanvas[action.payload.itemType][action.payload.fabricItemInput.fabricItemId] = angular.copy(item);
                items[action.payload.elementId] = {
                    fabricItemsOnCanvas: {
                        $set: fabricItemsOnCanvas
                    }
                };
                state = update(state, {
                    elements: items
                });
                break;

            case getType('REMOVE_ITEM_ON_CANVAS'):
                let elementsOnCanvas = {};
                let fabricItems = angular.copy(state.elements[action.payload.elementId].fabricItemsOnCanvas);
                delete fabricItems[action.payload.itemType][action.payload.fabricItemId];
                elementsOnCanvas[action.payload.elementId] = {
                    fabricItemsOnCanvas: {
                        $set: fabricItems
                    }
                };
                state = update(state, {
                    elements: elementsOnCanvas
                })
                break;

            case getType('SET_ITEMS_ON_CANVAS'):
                let itemsOnCanvas = {};
                itemsOnCanvas[action.payload.elementId] = {
                    fabricItemsOnCanvas: {
                        $set: action.payload.fabricItems
                    }
                };
                state = update(state, {
                    elements: itemsOnCanvas
                })
                break;

            case getType('SET_RENDER_IMAGE'):
                let renderImage = {};
                renderImage[action.payload.elementId] = {
                    renderImage: {
                        $set: action.payload.renderImage
                    }
                };

                state = update(state, {
                    elements: renderImage
                });
                break;

            case getType('SET_CURRENT_FABRIC_ITEM'):
                state = update(state, {
                    activeItem: {
                        $set: action.payload.currentFabricItemId
                    }
                });
                break;

            case getType('RESET'):
                if (state.elements[action.payload]) {
                    let itemsOnCanvas = {};
                    itemsOnCanvas[action.payload] = {
                        fabricItemsOnCanvas: {
                            $set: {
                                text: {},
                                image: {},
                                clipArt: {}
                            }
                        }
                    };
                    state = update(state, {
                        elements: itemsOnCanvas
                    });

                    let renderImage = {};
                    renderImage[action.payload] = {
                        renderImage: {
                            $set: null
                        }
                    };

                    state = update(state, {
                        elements: renderImage
                    });
                }
                break;

            case getType('FETCH_POOL_ITEMS_FULFILLED'):

                state = update(state, {
                    poolItems: {
                        $set: action.payload.data.result.data
                    }
                });
                break;
        }

        return state;
    };

    AptoReducersProvider.addReducer('pluginImageUploadDefinition', this.reducer);

    this.$get = function() {};
};

DefinitionReducer.$inject = DefinitionReducerInject;

export default ['ImageUploadDefinitionReducer', DefinitionReducer];
