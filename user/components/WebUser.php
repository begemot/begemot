<?php

class WebUser extends CWebUser
{
    public function getTableAlias(){
        return Yii::app()->getModule('user')->tableUsers;
    }
    public function getRole()
    {
        return $this->getState('__role');
    }

    public function getId()
    {
        return $this->getState('__id') ? $this->getState('__id') : 0;
    }

    protected function beforeLogin($id, $states, $fromCookie)
    {
        parent::beforeLogin($id, $states, $fromCookie);

        //$model = new UserLoginStats();
        //$model->attributes = array(
        //    'user_id' => $id,
        //    'ip' => ip2long(Yii::app()->request->getUserHostAddress())
        //);
        //$model->save();

        return true;
    }

    protected function afterLogin($fromCookie)
    {
        parent::afterLogin($fromCookie);
        $this->updateSession();


    }


    public function updateSession() {
        $user = $this->user($this->id);


        $userAttributes = CMap::mergeArray(array(
            'email' => $user->email,
            'username' => $user->username,
            'create_at' => $user->create_at,
            'lastvisit_at' => $user->lastvisit_at,
        // ), $user->profile->getAttributes());
        ), []);
        foreach ($userAttributes as $attrName => $attrValue) {
            $this->setState($attrName, $attrValue);
        }
    }



    public function model($id=0) {
        return User::model()->findByPk($id);


    }

    public function user($id = 0)
    {
        return $this->model($id);
    }

    public function getUserByName($username)
    {
        return Yii::app()->getModule('user')->getUserByName($username);
    }

    public function getAdmins()
    {
        return Yii::app()->getModule('user')->getAdmins();
    }

    public function isAdmin()
    {
        return Yii::app()->getModule('user')->isAdmin();
    }

    public function canDo($authItems = '',$params=null)
    {

        if (!is_array($authItems)) {
            $authItems = array($authItems);
        }


        if (!Yii::app()->user->isAdmin()) {

            foreach ($authItems as $authItem) {
                if (Yii::app()->authManager->checkAccess($authItem, Yii::app()->user->id,$params)) {
                    return true;
                }
            }

            return false;

        } else {

            return true;

        }
    }
}
