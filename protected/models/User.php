<?php

/**
 * @property int id
 * @property string email
 * @property string password
 *
 * @property DeviceToken|null device
 */
class User extends CActiveRecord
{
    public $apartment;
    /**
     * @static
     * @param string $className
     * @return User|CActiveRecord
     */
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    public $plainPassword;

    public function afterValidate(){
        if($this->plainPassword)
            $this->password = md5($this->plainPassword.Yii::app()->params['md5Salt']);
        return parent::afterValidate();
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
            'authItems' => array(self::MANY_MANY, 'AuthItem', 'AuthAssignment(userid, itemname)'),
        );
    }

    public function attributeLabels()
    {
        return array(
            'email' => 'E-mail',
            'plainPassword' => 'Пароль',
            'authItems' => 'Права',
        );
    }

    public function rules()
    {
        return array(
            array('email', 'required', 'on'=>'register'),
            array('email', 'email'),
            array('email,plainPassword', 'required', 'on'=>'admin'),
            array('email', 'unique'),
            array('plainPassword', 'required', 'on'=>'register'),
            array('plainPassword', 'length', 'max'=>31, 'on'=>'insert,update'),

            array('email', 'safe', 'on'=>'search'),
        );
    }

    public function search()
    {
        $criteria=new CDbCriteria;

        $criteria->compare('email', $this->email, true);
        $criteria->addNotInCondition('email',array(''));

        return new CActiveDataProvider($this, array(
            'criteria' => $criteria,
        ));
    }

}
