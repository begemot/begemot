<?php



Yii::import('schema.components.SchemaGroupManager');
$arr = SchemaGroupManager::getAllGroups();
$arr = array_map(function($item){
    $resItem = [];
    $resItem['schemaGroup']=$item['schemaGroup'];
    $resItem['title']=$item['title'];
    $resItem['_id']=$item['_id']->__toString();
    // $resItem['_id']=$item['_id'];
    return $resItem;
    // return $item['_id']='sdfg';
},$arr);

$res = json_encode($arr,0);


?>
  <?php Yii::app()->clientScript->registerScriptFile(Yii::app()->baseUrl . '/protected/modules/schema/assets/js/schemaGroupIndex.angularjs.js', 2);?>
<div ng-app="myApp" ng-controller="MainController as ctrl">


  <div class="container mt-5">
    <h2>Vehicle List</h2>
    <table class="table table-bordered table-striped">
      <thead>
        <tr>
          <th>
              <td></td>
            <input type="text" class="form-control" ng-model="ctrl.filters._id" placeholder="Filter ID">
          </th>
          <th>
            <input type="text" class="form-control" ng-model="ctrl.filters.title" placeholder="Filter Title">
          </th>
      
          </th>
          <th>
            <input type="text" class="form-control" ng-model="ctrl.filters.schemaGroup" placeholder="Filter Schema Group">
          </th>
        </tr>
        <tr>
              <td></td>
          <th ng-click="ctrl.sortBy('_id')">
            ID
            <span ng-if="ctrl.sortField === '_id'">
              {{ ctrl.reverseSort ? '▼' : '▲' }}
            </span>
          </th>
          <th ng-click="ctrl.sortBy('title')">
            Title
            <span ng-if="ctrl.sortField === 'title'">
              {{ ctrl.reverseSort ? '▼' : '▲' }}
            </span>
          </th>

          <th ng-click="ctrl.sortBy('schemaGroup')">
            Schema Group
            <span ng-if="ctrl.sortField === 'schemaGroup'">
              {{ ctrl.reverseSort ? '▼' : '▲' }}
            </span>
          </th>
        </tr>
      </thead>
      <tbody>
        <tr ng-repeat="item in ctrl.getFilteredItems() | orderBy:ctrl.sortField:ctrl.reverseSort track by item._id">
              <td>{{ $index + 1 }}</td>
          <td>{{ item._id }}</td>
          <td><a target="_blank" href="/schema/schemaGroup/update/id/{{ item.schemaGroup }}">{{ item.title }}</a></td>

          <td>{{ item.schemaGroup }}</td>
        </tr>
      </tbody>
    </table>
  </div>
    <script type="text/javascript">
    var initialData = <?php echo $res; ?>;
  </script>
</div>
