<?php

/**
 * For Begemot CMS
 * 
 * Description of CLongTaskQueue
 * 
 * It`s class for process queue of tasks, that could not complete 
 * in one request. For example we need process 10000 photo images.
 * 
 * CLongTaskQueue create queue in special file as array() and it`s will get
 * 1 task per request. If task was completed successfull it wil be deleted 
 * from queue from systemFile.
 *
 *
 *
 * @author scott2to
 */
class CLongTaskQueue {

    public $queueDirPath = '/files/queueu';
    private $queueFileName = null;
    private $queueFilePath = null;
    private $queueData = array();
    public $queueStatus = array();
    
    private $activeTask = null;


    public function __construct($id) {

        if (!$this->systemCheck()) {
            return false;
        }
        $this->queueDirPath = Yii::getPathOfAlias('webroot').$this->queueDirPath;
        
        if (!file_exists($this->queueDirPath)){
            mkdir($this->queueDirPath,777);
        }
        
        $this->queueFileName = $id . '_queue.php';
        //$queueFilePath = $this->queueFilePath = $this->queueDirPath . $this->queueFileName;
        $this->queueFilePath = $this->queueDirPath .'/'. $this->queueFileName;

        if ($this->isQueueExist()) {
            $this->loadData();
        }
        return true;
    }
    
    public function getProgress(){
        $percent = 0;
        
        $taskCount = count($this->queueData);
        if ($this->queueStatus['startTaskCount']!=0){
            $percent = 100-($taskCount/$this->queueStatus['startTaskCount'])*100;
        }
        
        return $percent;
    }
    
    public function isQueueExist() {
        if (file_exists($this->queueFilePath)) {
            return true;
        } else {
            return false;
        }
    }

    public function startNewQueue($tasks) {
        return $this->addTasks($tasks);
        
    }

    public function getNewActiveTask() {
       // echo '<pre>';print_r($this->queueData);echo '</pre>';
        $this->activeTask = array_shift($this->queueData);
        return $this->activeTask;
    }

    public function activeTaskCompleted() {
        if (count($this->queueData) > 0) {
            
            $this->saveData();
            return true;
        } else {
            unlink($this->queueFilePath);
            return false;
        }
    }

    /*
     * Add one task orseveral by array of tasks to queue
     */
    
    private function addTasks($tasks) {

        if (is_array($tasks)) {
            $data = $this->queueData;
            $this->queueData = array_merge($data, $tasks);
        } else {
            $this->queueData[] = $task;
        }
        
        $this->queueStatus['startTaskCount'] = count($this->queueData);
        
        //some actions

        if ($this->saveData()) {
            return true;
        } else {
            return false;
        }
    }

    /*
     * Checking system path for existing
     * and writable.
     */

    private function systemCheck() {
        //TODO: check for existing and writable
        return true;
    }

    private function saveData() {

        $dataToFile = array(
            'status'=>$this->queueStatus,
            'data'=>$this->queueData
        );
        if (!file_put_contents($this->queueFilePath, '<?php return ' . var_export($dataToFile, true) . '; ?>')) {
            return false;
        } else {
            return true;
        }
    }

    private function loadData() {
       
        $dataToFile = require $this->queueFilePath;
         
        $this->queueData=$dataToFile['data'];
        $this->queueStatus=$dataToFile['status'];
        
       
        
    }

}

?>
