<?php

/**
 * This is the model class for table "catItemsToItems".
 *
 * The followings are the available columns in table 'catItemsToItems':
 * @property integer $toItemId
 * @property integer $itemId
 */
class CatColor extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return CatItemsToCat the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}


	public function isLinkedWithColor($catItemId){
		$color = CatColorToCatItem::model()->findByAttributes(['catItemId'=>$catItemId,'colorId'=>$this->id]);

		if (!is_null($color)){
			return true;
		} else {
			return false;
		}
	}
        
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'catColors';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('name,', 'required'),
			array('id,colorCode', 'safe'),

		);
	}

	public static function createColor($colorName, $colorCode, $catItemId){
        $result = CatColor::model()->findAllByAttributes(['name'=>$colorName]);



        if (self::isCatItemHasColorByTitle($catItemId,$colorName)) return false;
        $color = new CatColor();
        $color->name = $colorName;
        $color->colorCode = $colorCode;
        $color->save();

        $colorToCatItem = new CatColorToCatItem();
        $colorToCatItem->catItemId = $catItemId;
        $colorToCatItem->colorId = $color->id;
        if ($colorToCatItem->save()){
            Yii::import('pictureBox.components.*');
            $pbox = new PBox('catColors',$catItemId,'color_'.$colorToCatItem->id);
            return $colorToCatItem->id;
        }


    }

    public static function isCatItemHasColorByTitle($catId,$title){

	   //CatColorToCatItem::model()->findAllByAttributes(['catItemId'=>$catId]);
	   $colorToItemArray = Yii::app()->db->createCommand()->select('*')->where('
	   catItemId='.$catId.'')->
           from('catColorsToCatItem')->queryAll();
        $colorIds = array_column($colorToItemArray,'colorId');


        $result = Yii::app()->db->createCommand()
            ->select('*')
        ->from('catColors')->where(
                ['and',
                    'name=:name',
                    ['in','id',$colorIds]
                    ],
                [':name'=>$title]
            )->queryAll();

        if (count($result)>0) {

            $colorId = $result[0]['id'];

            foreach ($colorToItemArray as $item){
                if ($colorId==$item['colorId']){
                    return $item['id'];
                }
            }


        } else return false;
    }

    public static function addImageToColor($colorToCatItemId, $image){

	    $model =  CatColorToCatItem::model()->findByPk($colorToCatItemId);
	    if (!$model){
            throw new Exception('не нашелся CatColorToCatItem c PK='.$colorToCatItemId);
        }
        Yii::import('pictureBox.components.*');

        $picturesConfig = array(
            'divId' => 'pictureBox',
            'nativeFilters' => array(
                'main' => true,
            ),
            'catalog' => true,
            'filtersTitles' => array(
                'main' => 'Основная',
                'catalog' => 'каталог',
            ),
            'imageFilters' => array(
                'main' => array(
                    0 => array(
                        'filter' => 'CropResize',
                        'param' => array(
                            'width' => 320,
                            'height' => 219,
                        ),
                    ),
                ),
                'catalog' => array(
                    0 => array(
                        'filter' => 'CropResize',
                        'param' => array(
                            'width' => 163,
                            'height' => 120,
                        ),
                    ),
                ),
            ),

        );
        $rConf = array_merge_recursive(PictureBoxFiles::getDefaultConfig(),$picturesConfig);


        $pbox = new PBox('catColors',$model->catItemId,'color_'.$model->colorId);
        $pbox->filters = $rConf;
        $pbox->addImagefile($image);
    }


}