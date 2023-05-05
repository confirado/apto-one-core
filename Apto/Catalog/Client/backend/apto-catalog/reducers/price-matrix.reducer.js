import update from 'immutability-helper';

const PriceMatrixReducerInject = ['AptoReducersProvider'];
const PriceMatrixReducer = function(AptoReducersProvider) {
    const TYPE_NS = 'APTO_PRICE_MATRIX_';
    const initialState = {
        pageHeaderConfig: {
            title: 'Preismatrizen',
            pagination: {
                show: true,
                pageNumber: 1,
                recordsPerPage: 20,
                numberOfRecords: 0,
                numberOfPages: 1
            },
            search: {
                show: true,
                searchString: ''
            },
            add: {
                show: true,
                aclMessagesRequired: {
                    commands: ['AddPriceMatrix'],
                    queries: []
                }
            },
            listStyle: {
                show: true
            },
            listSettings: {
                show: false
            },
            selectAll: {
                show: false
            },
            toggleSideBarRight: {
                show: true
            }
        },
        dataListConfig: {
            listFields: {
                id: {
                    name: 'id',
                    label: 'Id'
                },
                name: {
                    name: 'name',
                    label: 'Name',
                    translatedValue: true
                },
                created: {
                    name: 'created',
                    label: 'Erstellt'
                }
            },
            listOptions: {
                card: {
                    headline: ['name'],
                    subHeadline: ['id', 'created'],
                    cardColumns: 3
                },
                table: ['id', 'name', 'created'],
                listTemplate: 'components/data-list/data-table-list.html'
            },
            itemActions: {
                select: {
                    show: false,
                    field: 'id',
                    selected: {}
                },
                edit: {
                    show: true,
                    field: 'id',
                    aclMessagesRequired: {commands: ['UpdatePriceMatrix'], queries: []},
                },
                remove: {
                    show: true,
                    field: 'id',
                    aclMessagesRequired: {commands: ['RemovePriceMatrix'], queries: []}
                }
            }
        },
        priceMatrices: [],
        priceMatrixDetail: {},
        elements: [],
        elementPrices: [],
        elementCustomProperties: {},
        uploads: {},
        uploadProgress: 0,
        runningUploads: 0,
        report: '',
        csvExportString: null
    };

    function getType(type) {
        return TYPE_NS + type;
    }

    function updateRunningUploads(state, action) {
        let uploads = angular.copy(state.uploads),
            total = 0,
            loaded = 0;

        uploads[action.payload.id] = {
            total: action.payload.total,
            loaded: action.payload.loaded
        };

        for (let id in uploads) {
            if (uploads.hasOwnProperty(id)) {
                total += uploads[id].total;
                loaded += uploads[id].loaded;
            }
        }

        return update(state, {
            uploads: {
                $set: uploads
            },
            uploadProgress: {
                $set: total === 0 ? 0 : parseInt(Math.round(loaded / total * 100))
            }
        });
    }

    function setSearchString(state, searchString) {
        return update(state, {
            pageHeaderConfig: {
                search: {
                    searchString: {
                        $set: searchString
                    }
                }
            }
        });
    }

    function setNumberOfPages(state, numberOfPages) {
        let newState = update(state, {
            pageHeaderConfig: {
                pagination: {
                    numberOfPages: {
                        $set: numberOfPages
                    }
                }
            }
        });

        if (numberOfPages < 1) {
            newState.pageHeaderConfig.pagination.numberOfPages = 1;
        }

        if (state.pageHeaderConfig.pagination.pageNumber >  numberOfPages) {
            newState = update(newState, {
                pageHeaderConfig: {
                    pagination: {
                        pageNumber: {
                            $set: newState.pageHeaderConfig.pagination.numberOfPages
                        }
                    }
                }
            });
        }
        return newState;
    }

    function setPageNumber(state, pageNumber) {
        let newState = update(state, {
            pageHeaderConfig: {
                pagination: {
                    pageNumber: {
                        $set: pageNumber
                    }
                }
            }
        });

        if (pageNumber < 1 ) {
            newState.pageHeaderConfig.pagination.pageNumber = 1;
        }

        if (pageNumber > state.pageHeaderConfig.pagination.numberOfPages ) {
            newState.pageHeaderConfig.pagination.pageNumber = newState.pageHeaderConfig.pagination.numberOfPages;
        }

        return newState;
    }

    function setNumberOfRecords(state, numberOfRecords) {
        return update(state, {
            pageHeaderConfig: {
                pagination: {
                    numberOfRecords: {
                        $set: numberOfRecords
                    }
                }
            }
        });
    }

    function setRecordsPerPage(state, recordsPerPage) {
        return update(state, {
            pageHeaderConfig: {
                pagination: {
                    recordsPerPage: {
                        $set: recordsPerPage
                    }
                }
            }
        });
    }

    function setListTemplate(state, listTemplate) {
        return update(state, {
            dataListConfig: {
                listOptions: {
                    listTemplate: {
                        $set: listTemplate
                    }
                }
            }
        });
    }

    this.priceMatrix = function (state, action) {
        let newState;
        if (typeof state === "undefined") {
            state = angular.copy(initialState);
        }

        switch (action.type) {
            // page header changes
            case getType('SET_SEARCH_STRING'):
                return setSearchString(state, action.payload);
            case getType('SET_PAGE_NUMBER'):
                return setPageNumber(state, action.payload);
            case getType('SET_RECORDS_PER_PAGE'):
                return setRecordsPerPage(state, action.payload);
            case getType('SET_NUMBER_OF_PAGES'):
                return setNumberOfPages(state, action.payload);
            case getType('SET_NUMBER_OF_RECORDS'):
                return setNumberOfRecords(state, action.payload);
            // data list changes
            case getType('SET_LIST_TEMPLATE'):
                return setListTemplate(state, action.payload);
            case getType('SET_SELECTED'):
                newState = update(state, {
                    dataListConfig: {
                        itemActions: {
                            select: {
                                selected: {
                                    $set: action.payload
                                }
                            }
                        }
                    }
                });
                return newState;
            // price matrix changes
            case getType('FETCH_PRICE_MATRICES_FULFILLED'):
                newState = update(state, {
                    priceMatrices: {
                        $set: action.payload.data.result.data
                    }
                });

                newState = setNumberOfPages(newState, action.payload.data.result.numberOfPages);
                newState = setNumberOfRecords(newState, action.payload.data.result.numberOfRecords);

                return newState;
            // priceMatrix detail
            case getType('FETCH_PRICE_MATRIX_DETAIL_FULFILLED'):
                newState = update(state, {
                    priceMatrixDetail: {
                        $set: action.payload.data.result
                    }
                });
                return newState;
            case getType('PRICE_MATRIX_DETAIL_RESET'):
                newState = update(state, {
                    priceMatrixDetail: {
                        $set: angular.copy(initialState.priceMatrixDetail)
                    },
                    elements: {
                        $set: angular.copy(initialState.elements)
                    }
                });
                return newState;
            case getType('FETCH_PRICE_MATRIX_ELEMENTS_FULFILLED'):
                newState = update(state, {
                    elements: {
                        $set: action.payload.data.result.elements
                    }
                });
                return newState;
            case getType('FETCH_PRICE_MATRIX_ELEMENT_PRICES_FULFILLED'):
                newState = update(state, {
                    elementPrices: {
                        $set: action.payload.data.result.elements[0].aptoPrices
                    }
                });
                return newState;
            case getType('FETCH_PRICE_MATRIX_ELEMENT_CUSTOM_PROPERTIES_FULFILLED'):
                newState = update(state, {
                    elementCustomProperties: {
                        $set: action.payload.data.result.elements[0].customProperties
                    }
                });
                return newState;
            // import
            case getType('RESET_IMPORT_REPORT'):
                return update(state, {
                    report: { $set: initialState.report }
                });

            case getType('UPLOAD_FILES_PROGRESS_START'):
                return update(state, {
                    runningUploads: { $set: state.runningUploads + 1 }
                });

            case getType('UPLOAD_FILES_PROGRESS_UPDATE'):
                return updateRunningUploads(state, action);

            case getType('UPLOAD_FILES_PROGRESS_END'):
                let oldReports = state.report;
                let newReport = action.payload.response.data.message.message.trim();
                let index = newReport.indexOf(': ');
                let newReports = oldReports + (state.report !== '' ? "<br>---<br>" : '') + newReport.substr(index > 0 ? index + 2 : 0).replace(/([^>\r\n]?)(\r\n|\n\r|\r|\n)/g, '$1<br>$2');
                newState = updateRunningUploads(state, action);
                newState = update(newState, {
                    runningUploads: {
                        $set: state.runningUploads - 1
                    },
                    report: {
                        $set: newReports
                    }
                });
                // no running uploads, tidy up a bit
                if (newState.runningUploads === 0) {
                    newState = update(newState, {
                        uploads: {
                            $set: {}
                        },
                        uploadProgress: {
                            $set: 0
                        }
                    });
                }
                return newState;
            case getType('FETCH_CSV_EXPORT_STRING_FULFILLED'):
                newState = update(state, {
                    csvExportString: {
                        $set: action.payload.data.result
                    }
                });
                return newState;
            case getType('RESET_CSV_EXPORT_STRING'):
                return update(state, {
                    csvExportString: { $set: initialState.csvExportString }
                });
        }

        return state;
    };

    AptoReducersProvider.addReducer('priceMatrix', this.priceMatrix);

    this.$get = function() {};
};

PriceMatrixReducer.$inject = PriceMatrixReducerInject;

export default ['PriceMatrixReducer', PriceMatrixReducer];