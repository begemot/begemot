<?php

/**
 * Class MassOperationController контроллер раздела админки с массовыми операциями
 */
class MassOperationController extends Controller
{
    public $layout = 'begemot.views.layouts.column2';
    public function accessRules()
    {
        return array(

            array('allow', // allow admin user to perform 'admin' and 'delete' actions

                'actions' => array(
                    'index','loadCatItems','loadCategories','moveToCategory','saveField'
                ),

                'expression' => 'Yii::app()->user->canDo("Catalog")'
            ),
            array('deny',  // deny all users
                'users' => array('*'),
            ),
        );
    }

    public function actionIndex()
    {
        $this->render('index');
    }

    public function actionLoadCategories(){
        $categories = CatCategory::model()->findAll();
        $catResultarray = [];
        $catResultarray[] = [
            'id'=>null,
            'name'=>'Без раздела'
        ];
        foreach ($categories as $categoryModel){
            $catLine = [];
            $catLine['id'] = $categoryModel->id;
            $catLine['name'] = $categoryModel->name;
            $catResultarray[]=$catLine;
        }
        echo json_encode($catResultarray);
    }
    public function actionMoveToCategory(){
        $marked = json_decode($_REQUEST['markedItems']);
        $sourceCat =json_decode($_REQUEST['sourceCat']);
        $targetCat = json_decode($_REQUEST['targetCat']);

        $attributes = [
            'catId'=>$sourceCat->id
        ];
        foreach ($marked as $catItemId => $tmp){
            $attributes['itemId'] = $catItemId;
            $model = CatItemsToCat::model()->findByAttributes($attributes);
            if ($model){
                $model->catId = $targetCat->id;
                if ($model->save()){
                    echo 'Перенесли!';
                }
            } else {
                echo 'Нет, облом!';
            }
        }


    }
    public function actionLoadCatItems(){

        $idSearch = '';

        $match='';
//        $q = new CDbCriteria( array(
//            'condition' => "`name` LIKE :match",
//            'params'    => array(':match' => "%$match%")
//        ) );
//
//
//        $items = CatItem::model()->findAll($q );

        $idAscDesc = 'DESC';
        if (isset($_REQUEST['idAsc'])){
            if ($_REQUEST['idAsc']==1){
                $idAscDesc = '';
            }
        }


        $page = 1;
        if (isset($_REQUEST['page'])){
            $page = $_REQUEST['page'];
        }
        $catId = '';
        if (isset($_REQUEST['catId'])){
            $catId = $_REQUEST['catId'];
        }

        $countPerPage = 20;
        $startPosition = ($page-1)*$countPerPage;

        $countSelectPart = 'SELECT count(*) FROM catItems';
        $middleSqlPart = "
            ORDER BY `id` $idAscDesc
        ";

        $whereSqlPart = " WHERE `id` like '%".$idSearch."%' ";
        if ($catId!=''){

            $whereSqlPart = $whereSqlPart." AND `id` in (select itemId from `catItemsToCat` where `catId`='$catId' AND `is_through_display_child`=0) ";
        }

        $countSql = $countSelectPart.' '.$whereSqlPart.' '.$middleSqlPart.';';

        $limitSqlPart = "LIMIT $startPosition,$countPerPage";
        $sql = "
              SELECT * FROM catItems
              $whereSqlPart
              $middleSqlPart
              $limitSqlPart
            ;";

        $connection=Yii::app()->db;

        $command = $connection->createCommand($countSql);
        $count = $command->queryScalar();

        $items = CatItem::model()->findAllBySql($sql );

        $result = [];
        foreach ($items as $item){
            $line = [];
            $line['id'] = $item->id;
            $line['name'] = $item->name;
            $line['price'] = $item->price;
            $line['img'] = $item->getItemMainPicture("admin");

            $result[] = $line;
        }
        echo json_encode(['count'=>$count,'data'=>$result]);
    }

    public function actionSaveField($fieldName,$value,$itemId){
       $catItem = CatItem::model()->findByPk($itemId);
       $catItem->$fieldName = $value;
       $catItem->save();
    }

}