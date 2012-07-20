<?php

Yii::import('application.controllers.admin.*');

class AdminSettingsController extends AdminController
{
	public $modelName = 'Settings';
	public $modelHumanTitle = array('настройку', 'настройки', 'настроек');

	public $additionalTemplates = array('{view}','{fill}');
	public $additionalButtons = array(
		'fill'=> array(
			'label'=>'Заполнить',
			'url'=>'CHtml::NormalizeUrl(array("fill","id"=>$data->id))',
			'icon'=>'list-alt',
		),
		'update'=> array(
			'visible'=>'$data->deletable'
		),
		'view'=> array(
			'visible'=>'!$data->deletable'
		),
		'delete'=> array(
			'visible'=>'$data->deletable'
		)
	);

    public $allowedActions = 'add,edit,delete,fill,view';

	public function getEditFormElements($model)
	{
		return array(
			'name' => array(
				'type' => 'textField'
			),
            'description' => array(
                'type' => 'textField'
            ),
            'type' => array(
				'type' => 'dropDownList',
				'data' => Settings::getTypes(),
            ),
		);
	}

	public function getFillFormElements($model)
	{
		switch($model->type)
		{
			case Settings::INPUT:
				return array(
					'value' => array(
						'type' => 'textField'
					),
				);
				break;
			case Settings::HTML_INPUT:
				return array(
					'value' => array(
						'type' => 'textarea'
					),
				);
				break;
			case Settings::EDITOR:
				return array(
					'value' => array(
						'class' => 'application.widgets.RedactorJSBoot',
						'options'=>array(
							'editorOptions'=>array(
								'imageGetJson'=>'/admin/getImages',
								'imageUpload'=>'/admin/uploadImage',
								'fileUpload'=>'/admin/uploadFile',
							)
						),
					)
				);
				break;
		}
	}

	public function actionFill() {
		$model = $this->loadModel();

		if(isset($_POST[$this->modelName])) {
			foreach ($_POST[$this->modelName] as &$postValue) {
				if (is_string($postValue)) {
					$postValue = trim($postValue);
					if ($postValue === '')
						$postValue = null;
				}
			}

			$this->beforeSetAttributes($model, $_POST[$this->modelName]);
			$model->setAttributes($_POST[$this->modelName]);
			foreach($model->relations() as $relationName => $relationAttributes) {
				if (isset($_POST[$this->modelName][$relationName]))
					$model->$relationName = $_POST[$this->modelName][$relationName];
			}
			$this->beforeSave($model);

			$model->scenario = 'fill';
			if($model->save()) {
				$this->afterSave($model);
				$this->redirect(array($this->getId()));
			}
		}

		$this->beforeEdit($model);
		$this->render('//admin/crud/edit', array(
			'model' => $model,
			'editFormElements' => $this->getFillFormElements($model),
		));
	}

	public function getTableColumns()
	{
		$attributes = array(
			'name',
			'description',
			$this->getButtonsColumn(),
		);

		return $attributes;
	}
}
