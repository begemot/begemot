<?php

class m20170828_104508_000000_bug_fix_for_fast_migrations extends Migrations
{
    public function up()
    {

        if($this->isConfirmed(true) == true) return false;

        $sql = "UPDATE users SET lastvisit_at = '2000-01-01 00:00:00' WHERE lastvisit_at = '0000-00-00 00:00:00';
        UPDATE webParser SET date = '2000-01-01 00:00:00' WHERE date = '0000-00-00 00:00:00'";
        $this->execute($sql);

        return true;
    }

    public function down()
    {
        if($this->isConfirmed(true) == false) return false;

        echo "m20170828_104508_000000_bug_fix_for_fast_migrations does not support migration down.\n";
        return false;
    }

    public function getDescription()
    {
        return "Меняет 0000-00-00 00:00:00 на 2000-01-01 00:00:00 в таблицах webParser, users для того чтобы можно было импортировать таблицы в другие базы данных";
    }

    public function isConfirmed($returnBoolean = false){

        $result = 1;

        $sql = "SELECT id FROM users WHERE lastvisit_at = '0000-00-00 00:00:00';";
        if(Yii::app()->db->createCommand($sql)->execute() != 0){
            $result = 0;
        }

        if($result == 1){
            $sql = "SELECT id FROM webParser WHERE date = '0000-00-00 00:00:00';";
            
            if(Yii::app()->db->createCommand($sql)->execute() != 0){
                $result = 0;
            }
        }


        if($returnBoolean){
            return $result;
        }

        return parent::confirmByWords($result);
    }

}