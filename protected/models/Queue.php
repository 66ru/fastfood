<?php

/**
 * Class Queue
 *
 * @property int $id
 * @property string $action
 * @property array $params
 * @property int $status
 * @property string $createdTime
 * @property string $updatedTime
 * @property mixed $error
 */
class Queue extends CActiveRecord
{
    const STATUS_HIGH_PRIORITY = 3;
    const STATUS_MEDIUM_PRIORITY = 2;
    const STATUS_LOW_PRIORITY = 1;
    const STATUS_DONE = 0;
    const STATUS_ERROR = -1;

    /**
     * @param string $className
     * @return DeviceToken
     */
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    public function init()
    {
        $this->status = self::STATUS_MEDIUM_PRIORITY;
    }

    public function rules()
    {
        return array(
            array('action', 'length', 'max' => 255, 'allowEmpty' => false),
            array('params, error', 'safe'),
            array('status', 'numerical', 'allowEmpty' => false),
            array('createdTime, updatedTime', 'date'),
        );
    }

    public function behaviors()
    {
        return array(
            'timestamps' => array(
                'class' => 'zii.behaviors.CTimestampBehavior',
                'createAttribute' => 'createdTime',
                'updateAttribute' => 'updatedTime',
                'setUpdateOnCreate' => true,
            ),
            'serialized' => array(
                'class' => 'ext.behaviors.SerializedFieldsBehavior',
                'serializedFields' => array('params', 'error'),
            )
        );
    }
}