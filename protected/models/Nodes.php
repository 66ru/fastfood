<?php

/**

 */
class Nodes extends CActiveRecord
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

    public function beforeSave(){
        $this->lastUpdate = time();
        return parent::beforeSave();
    }

    public function getLastCompletedDeploy()
    {
        $criteria = new CDbCriteria();
        $criteria->addCondition('timeFinished is not null');
        $criteria->addCondition('nodeId=:nodeId');
        $criteria->params = array(':nodeId'=>$this->id);
        $deployment = Deployment::model()->find($criteria);
        if($deployment)
            return $deployment->timeFinished;
        else
            return false;
    }

    public function relations()
    {
        return array(
            'environment' => array(self::BELONGS_TO, 'Environment', 'envId'),
        );
    }

    public function attributeLabels()
    {
        return array(
            'hostname' => 'Хост',
            'role' => 'Роль',
            'lastUpdate' => 'Последний отклик',
            'lastCompletedDeploy' => 'Последняя успешная выкатка',
            'needDeploy' => 'Ожидаем выкатки',
            'branch' => 'Ветка'

        );
    }

    public function rules()
    {
        return array(
            array('hostname, role, envId', 'required'),
        );
    }

    public function beforeDelete()
    {
        $criteria = new CDbCriteria();
        $criteria->addCondition('nodeId=:nodeId');
        $criteria->params = array(':nodeId'=>$this->id);
        Deployment::model()->deleteAll($criteria);

        return parent::BeforeDelete();
    }
}
