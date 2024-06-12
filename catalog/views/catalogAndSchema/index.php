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
        <div class="row">
            <div class="col-md-6">
                <h3>Выберите из Catalog</h3>
                <input type="text" ng-model="filterText" class="form-control mb-3" placeholder="Фильтр">
                <div class="scrollable-list">
                    <ul class="list-group">
                        <li class="list-group-item" ng-repeat="item in catItems | filter:filterText"
                            ng-click="selectItem(item)">
                            <img width=100 src="{{item.image}}">
                            {{item.name}} - id:{{item.id}}
                        </li>
                    </ul>
                </div>
            </div>
            <div class="col-md-6">
                <h3>Выбрано <button class="btn btn-primary btn-sm" ng-click="submitData()"
                        ng-if="attachButtonVisible">Прикрепить</button></h3>
                <div>Сообщения:{{msg}}</div>
                <div ng-if="selectedItems.length === 0" class="alert alert-danger">Ничего не выбрано</div>
                <div class="scrollable-list">
                    <ul class="list-group">
                        <li class="list-group-item" ng-repeat="item in selectedItems" ng-click="deselectItem(item)">
                            <img width=100 src="{{item.image}}">
                            {{item.name}} - id:{{item.id}}
                        </li>
                    </ul>
                </div>
            </div>
        </div>
        <div class="row mt-5">
            <div class="col-md-6">
                <h3>Выберите из SchemaLinks</h3>
                <div class="scrollable-list">
                    <ul class="list-group">
                        <li class="list-group-item" ng-class="{'selected-item': isSchemaLinkSelected(item)}"
                            ng-repeat="item in schemaLinks" ng-click="selectSchemaLink(item)">
                            <img width="100" src="{{item.image}}">
                            {{item.name}} - id:{{item.id}}
                        </li>
                    </ul>
                </div>
            </div>
            <div class="col-md-6">
                <h3>Данные SchemaLink</h3>
                <ul class="schema-data-list" ng-if="schemaLinkData">
                    <li ng-repeat="dataItem in schemaLinkData">
                        <strong>{{dataItem.name}}:</strong> {{dataItem.value}}
                    </li>
                </ul>
                <div ng-if="!schemaLinkData" class="alert alert-info">
                    Выберите элемент из списка SchemaLinks для отображения данных.
                </div>
            </div>
        </div>

    </div>



    <script src="/protected/modules/catalog/views/catalogAndSchema/js/app.js"></script>
</div>