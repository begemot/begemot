<script src="/bower_components/jquery/dist/jquery.min.js"></script>
<div ng-app="myApp" ng-controller="myCtrl">
    <style>
    .scrollable-list {
        max-height: 300px;
        overflow-y: auto;
    }

    .list-group-item {
        cursor: pointer;
        transition: background-color 0.3s;
    }

    .list-group-item:hover {
        background-color: #e0e0e0;
    }

    .selected-item {
        background-color: #a8d5e2;
        color: #ffffff;
    }

    .schema-data-list {
        list-style-type: none;
        padding: 0;
    }

    .schema-data-list li {
        padding: 5px 0;
        border-bottom: 1px solid #ddd;
    }

    .schema-data-list li:last-child {
        border-bottom: none;
    }
    </style>

    <div class="container mt-5">
        <div class="row mt-5">
            <div>
                <button class="btn btn-primary btn-sm" ng-click="submitData()"
                    ng-if="attachButtonVisible">Прикрепить</button>
                <cat-item-select selected-items="selectedItems" on-select-change="onSelectAndUnselect(items)"
                    selected-items-view='1' show-cats='true'>
                    <div>{{item.name}}</div>


                </cat-item-select>
            </div>
            <div class="col-md-6"> </div>

        </div>
    </div>



    <div class="container mt-5">
        <h1>Status Switcher</h1>
        <div class="btn-group" role="group" aria-label="Status Switcher">
            <input type="radio" class="btn-check" name="status" id="status_stock" autocomplete="off"
                ng-click='moveToStandartCat("stock")'>
            <label class="btn btn-outline-success" for="status_stock">Stock</label>

            <input type="radio" class="btn-check" name="status" id="status_catalog" autocomplete="off"
                ng-click='moveToStandartCat("catalog")'>
            <label class="btn btn-outline-primary" for="status_catalog">Catalog</label>

            <input type="radio" class="btn-check" name="status" id="status_sold" autocomplete="off"
                ng-click='moveToStandartCat("sold")'>
            <label class="btn btn-outline-danger" for="status_sold">Sold</label>

            <input type="radio" class="btn-check" name="status" id="status_archive" autocomplete="off"
                ng-click='moveToStandartCat("archive")'>
            <label class="btn btn-outline-secondary" for="status_archive">Archive</label>

            <input type="radio" class="btn-check" name="status" id="status_clear" autocomplete="off"
                ng-click='moveToStandartCat("clear")'>
            <label class="btn btn-outline-danger" for="status_clear">убрать отовсюду</label>
        </div>

        <div class="mt-3">
            <p>Selected status: <span id="selected_status">None</span></p>
        </div>
    </div>

    <div class="container mt-5">
        <category-select selected-categories='selectedCategories' business-logic-enabled='true'></category-select>
    </div>


    <script src="/bower_components/lodash/dist/lodash.min.js"></script>
    <script src="/protected/modules/catalog/assets/js/ui/uiModule.js"></script>
    <script src="/protected/modules/catalog/assets/js/ui/catItemSelect.directive.js"></script>
    <script src="/protected/modules/catalog/assets/js/ui/catItemCatList.directive.js"></script>
    <script src="/protected/modules/catalog/assets/js/ui/catItemList.directive.js"></script>
    <script src="/protected/modules/catalog/assets/js/ui/categorySelect.directive.js"></script>

    <script src="/protected/modules/begemot/ui/commonUiBs5/commonUi.js"></script>
    <script src="/protected/modules/begemot/ui/commonUiBs5/modal.commonUi.js"></script>
    <script src="/protected/modules/catalog/views/catCategory/js/massItemsMoveBetweenCategories.js"></script>


    <script>
    $(document).ready(function() {
        $('input[name="status"]').change(function() {
            var selectedStatus = $(this).next('label').text();
            $('#selected_status').text(selectedStatus);
        });
    });
    </script>

</div>