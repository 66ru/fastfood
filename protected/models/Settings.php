<?php

/**
 * @property int id
 * @property string name
 * @property string description
 * @property int type
 * @property string value
 */
class Settings extends CActiveRecord
{
	CONST INPUT = 1;
	CONST HTML_INPUT = 2;
	CONST EDITOR = 3;
	/**
	 * @static
	 * @param string $className
	 * @return Settings|CActiveRecord
	 */
	public static function model($className = __CLASS__)
	{
		return parent::model($className);
	}

	public static function getTypes()
	{
		return array(
			self::INPUT=>'Текстовое поле',
			self::HTML_INPUT=>'HTML поле',
			self::EDITOR=>'Поле с редактором',
		);
	}

	public static function byName($name)
	{
		return self::model()->find('name=:name',array(':name'=>$name));
	}

	public function attributeLabels()
	{
		return array(
			'name' => 'Название параметра',
			'description' => 'Описание параметра',
			'type' => 'Тип параметра',
			'value' => 'Значение параметра',
		);
	}

	public function rules()
	{
		return array(
			array('name,type', 'required'),
			array('name', 'unique'),
            array('description, value', 'safe'),
		);
	}

    public function search()
   	{
   		$criteria=new CDbCriteria;

   		$criteria->compare('name', $this->name, true);
   		$criteria->compare('description', $this->description, true);

   		return new CActiveDataProvider($this, array(
   			'criteria' => $criteria,
   		));
   	}

}
