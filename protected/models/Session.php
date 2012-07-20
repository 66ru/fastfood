<?php
Yii::import('application.extensions.validators.guidValidator.*');
class Session extends CActiveRecord
{
    /**
     * @static
     * @param string $className
     * @return Session|CActiveRecord
     */
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    public function attributeLabels()
    {
        return array(
            'id' => 'Идентификатор сессии',
            'guid' => 'GUID устройства',
        );
    }

    public function behaviors()
    {
        return array(
            'FindOrCreate' => array(
                'class' => 'application.extensions.behaviors.FindOrCreateBehavior'
            )
        );
    }

    public function rules()
    {
        return array(
            array('id, guid', 'required'),
            array('guid', 'IsGuid'),
        );
    }
}
