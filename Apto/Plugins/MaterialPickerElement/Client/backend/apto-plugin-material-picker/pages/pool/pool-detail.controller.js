const PoolDetailControllerInject = ['$scope', '$mdDialog', '$ngRedux', 'targetEvent', 'showDetailsDialog', 'poolId', 'LanguageFactory', 'MaterialPickerPoolActions'];
const PoolDetailController = function($scope, $mdDialog, $ngRedux, targetEvent, showDetailsDialog, poolId, LanguageFactory, MaterialPickerPoolActions) {

    $scope.mapStateToThis = function(state) {
        return {
            pool: state.pluginMaterialPickerPool.pool,
            poolItems: state.pluginMaterialPickerPool.poolItems,
            materials: state.pluginMaterialPickerPool.materials,
            priceGroups: state.pluginMaterialPickerPool.priceGroups
        }
    };

    const subscribedActions = $ngRedux.connect($scope.mapStateToThis, {
        fetchPool: MaterialPickerPoolActions.fetchPool,
        fetchPoolItems: MaterialPickerPoolActions.fetchPoolItems,
        fetchMaterials: MaterialPickerPoolActions.fetchMaterials,
        fetchPriceGroups: MaterialPickerPoolActions.fetchPriceGroups,
        savePool: MaterialPickerPoolActions.savePool,
        addPoolItem: MaterialPickerPoolActions.addPoolItem,
        removePoolItem: MaterialPickerPoolActions.removePoolItem,
        resetPool: MaterialPickerPoolActions.resetPool
    })($scope);

    function init() {
        if (typeof poolId !== "undefined") {
            $scope.poolId = poolId;
            $scope.fetchPool(poolId);
            $scope.fetchPoolItems(poolId);
            $scope.fetchMaterials(poolId);
            $scope.fetchPriceGroups();
        }
        initNewPoolItem();
    }

    function initNewPoolItem() {
        $scope.newPoolItem = {
            material: null,
            priceGroup: null,
            materialSearch: '',
            priceGroupSearch: ''
        }
    }

    function addItem() {
        $scope.addPoolItem(
            poolId,
            $scope.newPoolItem.material.id,
            $scope.newPoolItem.priceGroup.id
        ).then(()=>{
            $scope.newPoolItem.material = null;
            $scope.newPoolItem.materialSearch = '';
            $scope.fetchMaterials(poolId);
            $scope.fetchPoolItems(poolId);
        });
    }

    function removeItem(poolItemId) {
        $scope.removePoolItem(poolId, poolItemId).then(()=>{
            $scope.fetchMaterials(poolId);
            $scope.fetchPoolItems(poolId);
        });
    }

    function save(poolForm, closeForm) {
        if(poolForm.$valid) {
            $scope.savePool($scope.pool).then(() => {
                if (typeof closeForm !== "undefined") {
                    close();
                } else if(typeof $scope.pool.id === "undefined") {
                    $scope.resetPool();
                    showDetailsDialog(targetEvent);
                }
            });
        }
    }

    function close() {
        $scope.resetPool();
        $mdDialog.cancel();
    }

    init();

    $scope.addItem = addItem;
    $scope.removeItem = removeItem;
    $scope.save = save;
    $scope.close = close;
    $scope.translate = LanguageFactory.translate;
    $scope.$on('$destroy', subscribedActions);
};

PoolDetailController.$inject = PoolDetailControllerInject;

export default PoolDetailController;