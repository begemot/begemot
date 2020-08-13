<?php


class CatalogServiceController extends Controller
{


    /**
     * @return array action filters
     */
    public function filters()
    {
        return array(
            'accessControl', // perform access control for CRUD operations
            'ajaxOnly + delete', // we only allow deletion via POST request
        );
    }

    /**
     * Specifies the access control rules.
     * This method is used by the 'accessControl' filter.
     * @return array access control rules
     */
    public function accessRules()
    {
        return array(

            array('allow', // allow admin user to perform 'admin' and 'delete' actions

                'actions' => array(
                    'catalogItems', 'catalogItemOptions', 'catergories','catalogCategoryItems'),
                'expression' => 'Yii::app()->user->canDo("Catalog")'
            ),
            array('deny',  // deny all users
                'users' => array('*'),
            ),
        );
    }

    public function actionCatalogItems()
    {

        $nameFilter = '';
        if (isset($_REQUEST['nameFilter'])) {
            $nameFilter = $_REQUEST['nameFilter'];
        }

        $sql = "SELECT * FROM catItems WHERE name like '%$nameFilter%'";
        $models = CatItem::model()->findAllBySql($sql);


        $result = [];
        foreach ($models as $catItem) {
            $resultLine = [];
            $resultLine['id'] = $catItem->id;
            $resultLine['name'] = $catItem->name;
            $resultLine['img'] = $catItem->getItemMainPicture('admin');
            $result[] = $resultLine;
        }
        echo json_encode($result);
    }

    public function actionCatalogItemOptions($id)
    {


        $models = CatItem::model()->findByPk($id)->options;


        $result = [];
        foreach ($models as $catItem) {
            $resultLine = [];
            $resultLine['id'] = $catItem->id;
            $resultLine['name'] = $catItem->name;
            $resultLine['img'] = $catItem->getItemMainPicture('admin');
            $result[] = $resultLine;
        }
        echo json_encode($result);
    }

    public function actionCatalogCategoryItems($id)
    {


        $models = CatItemsToCat::model()->findAllByAttributes(['catId'=>$id],['order'=>'`order`']);


        $result = [];
        foreach ($models as $catItem) {
            $resultLine = [];
            $resultLine['id'] = $catItem->itemId;
            $resultLine['name'] = $catItem->item->name;
            $resultLine['order'] = $catItem->order;
            $resultLine['img'] = $catItem->item->getItemMainPicture('admin');
            $result[] = $resultLine;
        }

        echo json_encode($result);
    }


    public function actionCatergories($nameFilter)
    {


        if ($nameFilter == '') {
            $catModel = CatCategory::model();
            $catModel->loadCategories();
            $res = $catModel->getcategoriesTree();
        } else {
            $sql = "SELECT * FROM catCategory WHERE  `name` like '%" . $nameFilter . "%' order by `order`;";
            $baseLevelModels = CatCategory::model()->findAllBySql($sql);

            $res = [];

            foreach ($baseLevelModels as $category) {
                $categoryArray = array();
                $categoryArray['id'] = $category->id;
                $categoryArray['pid'] = $category->pid;
                $categoryArray['name'] = $category->name;
                $categoryArray['order'] = $category->order;
                $categoryArray['level'] = $category->level;
                $categoryArray['name_t'] = $category->name_t;
                $res[] = $categoryArray;
            }
        }

        echo json_encode($res);

        return;
        $resultTree = [];



        foreach ($baseLevelModels as $baseModel) {

        }
    }

    public function getSubCategories($catId)
    {
        $sql = "SELECT * FROM catCategory WHERE `pid`=" . $catId . ' order by `order`;';
        return $models = CatCategory::model()->findAllBySql($sql);
    }

}