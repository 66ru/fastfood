<?php

/**

 */
class Environment extends CActiveRecord
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

    public function relations()
    {
        return array(
            'nodes' => array(self::HAS_MANY, 'Nodes', 'envId'),
        );
    }

    public function attributeLabels()
    {
        return array(
            'name' => 'Название',
            'envName' => 'Название chef environment',
            'schedule' => 'Расписание выкатки',
            'adminMail' => 'Email для уведомлений',
            'nextDeploy' => 'Следующая выкатка',
            'lastCompletedDeploy' => 'Последняя успешная выкатка',
        );
    }

    public function rules()
    {
        return array(
            array('name, envName', 'required'),
            array('schedule', 'cronValidator'),
            array('schedule', 'required'),
            array('adminMail','email'),
        );
    }

    public function getNextDeploy()
    {
        Yii::import('ext.cron-extension.vendor.autoload',true);
        try{
            $cron = \Cron\CronExpression::factory($this->schedule);
            return $cron->getNextRunDate()->getTimestamp();
        }
        catch(InvalidArgumentException $e){
            return false;
        }
    }



    public function beforeDelete()
    {
        foreach($this->nodes as $node)
            $node->delete();

        return parent::BeforeDelete();
    }

    public function cronValidator($attribute, $params)
    {
        Yii::import('ext.cron-extension.vendor.autoload',true);
        try{
            \Cron\CronExpression::factory($this->$attribute);
        }
        catch(InvalidArgumentException $e){
            $this->addError($attribute, 'Выражение не по формату cron');
        }
    }

    public function search()
    {
        $criteria=new CDbCriteria;

        $criteria->compare('adminMail', $this->adminMail, true);
        $criteria->compare('name', $this->name, true);
        $criteria->compare('envName', $this->envName, true);

        return new CActiveDataProvider($this, array(
            'criteria' => $criteria,
        ));
    }


}
