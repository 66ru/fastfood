<?php

Yii::import('application.controllers.admin.*');

class AdminUsersController extends AdminController
{
    public $modelName = 'User';
    public $modelHumanTitle = array('пользователя', 'пользователя', 'пользователей');

    public function getEditFormElements($model)
    {
        $model->setScenario('admin');
        return array(
            'email' => array(
                'type' => 'textField'
            ),
            'authItems' => array(
                'type' => 'dropDownList',
                'data' => EHtml::listData(AuthItem::model()),
                'htmlOptions' => array(
                    'multiple' => true,
                    'size' => 20,
                ),
            ),
            'plainPassword' => array(
                'type' => 'passwordField',
                'htmlOptions' => array(
                    'value' => '',
                    'hint' => 'Если ничего не вводить, то пароль не будет изменен.',
                ),
            ),
        );
    }

    public function getTableColumns()
    {
        $attributes = array(
            'email',
            $this->getButtonsColumn(),
        );

        return $attributes;
    }

    /**
     * @param User $model
     * @param array $attributes
     */
    public function beforeSetAttributes($model, &$attributes)
    {
        if (empty($attributes['plainPassword']))
            unset($attributes['plainPassword']);

        parent::beforeSetAttributes($model, $attributes);
    }

}
