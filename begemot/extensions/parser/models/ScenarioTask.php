<?php

/**
 * This is the model class for table "webParserScenarioTask".
 *
 * The followings are the available columns in table 'webParserScenarioTask':
 * @property integer $id
 * @property integer $processId
 * @property string $scenarioName
 * @property string $url
 */
class ScenarioTask extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return ScenarioTask the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}

	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'webParserScenarioTask';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('processId', 'numerical', 'integerOnly'=>true),
			array('scenarioName', 'length', 'max'=>45),
			//array('length', 'max'=>1000),


			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, processId, scenarioName', 'safe', 'on'=>'search'),
		);
	}

    public function completeTask ($error=null){
        if (!is_null($error)){

            $this->taskStatus = 'error';
        }
        else{
            $this->taskStatus = 'done';

        }

        $this->save();
    }

    public function getTargetData(){
        if($this->target_type == WebParserDataEnums::TASK_TARGET_DATA_TYPE_URL){
            $webPage = WebParserPage::model()->findByPk($this->target_id);
            return $webPage->content;
        }

        if($this->target_type == WebParserDataEnums::TASK_TARGET_DATA_TYPE_DATA){

        }
    }

    static public function isExistSomeTask ($processId){
        $sql = "SELECT COUNT(*) FROM webParserScenarioTask WHERE processId=".$processId.' and taskStatus="new"';
        $taskCount = Yii::app()->db->createCommand($sql)->queryScalar();

        if ($taskCount==0){
            return false;
        } else {
            return true;
        }
    }

    static public function isExistTask ($target_id,$target_type,$scenarioName,$processId){

        $sql = "SELECT COUNT(*) FROM webParserScenarioTask WHERE target_id='".$target_id."'and target_type='".$target_type."' and scenarioName='".$scenarioName."' and processId = ".$processId;
        $taskCount = Yii::app()->db->createCommand($sql)->queryScalar();

        if ($taskCount==0){
            return false;
        } else {
            return true;
        }
    }

    static public function getActiveTaskCount($processId){
        $sql ='SELECT count(*) FROM webParserScenarioTask where processId='.$processId.' and taskStatus="new";' ;
        $taskCount = Yii::app()->db->createCommand($sql)->queryScalar();
        return $taskCount;
    }



}