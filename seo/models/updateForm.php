<?php

/**
 * LoginForm class.
 * LoginForm is the data structure for keeping
 * user login form data. It is used by the 'login' action of 'SiteController'.
 */
class updateForm extends CFormModel
{
	public $text;
        public $seoTitle='';
        private $file;
        
        
        public function __construct($file) {
            parent::__construct();
           $this->file = str_replace('*', '/', $file);
           
           $dataPath = dirname($this->file).'/data/';

           if (!file_exists($dataPath)){
               mkdir($dataPath);
           }
           
           $dataFilePath = $dataPath.md5($this->file).'.data';
           
           if (file_exists($dataFilePath)){
               $data = require($dataFilePath);
               $this->seoTitle = $data['seoTitle'];
           } else{
               $this->seoTitle = '';
           }
           
           if (file_exists($this->file)){
            $this->text=  file_get_contents($this->file);
            }
        }



	public function rules()
	{
		return array(
			array('text,seoTitle', 'safe'),
		);
	}

	/**
	 * Declares attribute labels.
	 */
	public function attributeLabels()
	{
		return array(
			'text'=>'Содержание файла',
		);
	}
        
        public function saveFile(){

            file_put_contents($this->file, $this->text);
            
            $dataPath = dirname($this->file).'/data/';
            $dataFilePath = $dataPath.md5($this->file).'.data';
            
            $data = array();
            $data['seoTitle']=$this->seoTitle;
            
            file_put_contents($dataFilePath, '<?php return '.var_export($data,true).'; ?>'); 
            @chmod($this->file, 0777);
        }

}
?>