<?php

/**

 */
class Deployment extends CActiveRecord
{
    /**
     * @static
     * @param string $className
     * @return User|CActiveRecord
     */
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    public function behaviors()
    {
        return array(
            'manyToMany' => array(
                'class' => 'lib.ar-relation-behavior.EActiveRecordRelationBehavior',
            ),
            'FindOrCreate' => array(
                'class' => 'application.extensions.behaviors.FindOrCreateBehavior'
            )
        );
    }

/*    public function beforeSave(){
        $this->lastUpdate = time();
        return parent::beforeSave();
    }*/

    /*public function relations()
    {
        return array(
            'authItems' => array(self::MANY_MANY, 'AuthItem', 'AuthAssignment(userid, itemname)'),
        );
    }*/

    public function attributeLabels()
    {
        return array(
            'nodeId' => 'Название',
            'timeStarted' => 'Название chef environment',
            'timeFinished' => 'Название chef environment',
        );
    }

    public function rules()
    {
        return array(
            array('nodeId, timeStarted', 'required'),
        );
    }

    public function defaultScope()
    {
        return array(
            'order' => 'timeStarted DESC',
        );
    }

    public function getNeedDeploy()
    {
        return true;
    }
}
