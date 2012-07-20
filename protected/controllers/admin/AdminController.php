<?php

class AdminController extends Controller
{
	/** @var string Name of managed model */
	public $modelName = '';
	/**
	 * @var array Склонение должно соответствовать словам соответственно: (добавить .., редактирование .., список ..)
	 */
	public $modelHumanTitle = array('модель','модели','моделей');

	/** @var string Allowed actions. Separate by comma, without spaces. Possible values: add,view,edit,delete */

	public $visibilityField='visible';

	public $allowedActions = 'add,edit,delete,getImages,uploadImage,uploadFile,show,hide';

	public $defaultAction = 'list';

    public $rowCssClassExpression;

	public $additionalTemplates;
	public $additionalButtons;

	public function filters()
	{
		return array(
			'accessControl'
		);
	}

	public function actionShow()
	{
		$model = $this->modelName;
		$field = $this->visibilityField;
		$item = CActiveRecord::model($model)->findByPk($_GET['id']);
		$item->$field = 1;
		$item->save();
		$this->actionList();
	}

	public function actionHide()
	{
		$model = $this->modelName;
		$item = CActiveRecord::model($model)->findByPk($_GET['id']);
		$field = $this->visibilityField;
		$item->$field = 0;
		$item->save();
		$this->actionList();
	}

	public function accessRules()
	{
		$allowedActions = array_merge(explode(',', $this->allowedActions), array('index', 'list'));
		return array(
			array('allow',
				'actions' => $allowedActions,
				'roles'=>array('admin')
			),
            array('allow',
                'actions' => array('index'),
                'roles'=>array('moderator')
            ),
			array('deny',
				'users'=>array('*')
			),
		);
	}
	public function actionGetImages()
	{
		$files = array();
			$pics = Yii::app()->db->createCommand()
				->select('*')
				->from('static_images')
				->queryAll();
		foreach($pics as $pic)
		{
			$files[] = array(
				'image' => Yii::app()->fs->getFileUrl($pic['uid']),
				'thumb' => Yii::app()->fs->getResizedImageUrl($pic['uid'], array(100, 100)),
			);
		}

		echo stripslashes(json_encode($files));
		die;
	}

	public function actionUploadImage()
	{
		if(isset($_FILES['file']))
		{
			$file = $_FILES['file'];
			$picture = Yii::app()->fs->publishFile($file['tmp_name'],$file['name']);
			Yii::app()->fs->resizeImage($picture, array(100, 100)); //Thumbnail
			Yii::app()->db->createCommand()
				->insert('static_images',
					array(
						'uid'=>$picture
					)
				);
			echo CHtml::image(Yii::app()->fs->getFileUrl($picture));
		}
		die;
	}

	public function actionUploadFile()
	{
		if(isset($_FILES['file']))
		{
			$uploadedFile = $_FILES['file'];
			$file = Yii::app()->fs->publishFile($uploadedFile['tmp_name'],$uploadedFile['name']);

			echo CHtml::link($uploadedFile['name'],Yii::app()->fs->getFileUrl($file));
		}
		die;
	}
	public function actionAdd() {
		$this->actionEdit(true);
	}

	public function actionEdit($createNew = null) {
		if ($createNew) {
			$model = new $this->modelName();
		} else {
			$model = $this->loadModel();
		}

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

			$model->scenario = 'save';
			if($model->save()) {
				$this->afterSave($model);
				$this->redirect(array($this->getId()));
			}
		}

		$this->beforeEdit($model);
		$this->render('//admin/crud/'.($createNew ? 'add' : 'edit'), array(
			'model' => $model,
			'editFormElements' => $this->getEditFormElements($model),
		));
	}

	public function actionView() {
		$model = $this->loadModel();

		$this->render('//admin/crud/view', array(
			'model' => $model,
			'editFormElements' => $this->getEditFormElements($model),
		));
	}

	public function loadModel() {
		$model = null;
		if (isset($_GET['id']))
			$model = CActiveRecord::model($this->modelName)->findbyPk($_GET['id']);
		if ($model === null)
			throw new CHttpException(404);
		return $model;
	}

	public function actionIndex() {
		$this->render('//admin/index');
	}

	public function actionList() {
		/** @var $model CActiveRecord */
		$model=new $this->modelName('search');

		$this->beforeList($model, $_GET[$this->modelName]);
		if(isset($_GET[$this->modelName]))
			$model->attributes=$_GET[$this->modelName];

		$this->render('//admin/crud/list', array(
			'model' => $model,
			'columns' => $this->getTableColumns(),
			'canAdd' => in_array('add', explode(',', $this->allowedActions)),
            'rowCssClassExpression'=>$this->rowCssClassExpression,
		));
	}

	public function actionDelete()
	{
		if(Yii::app()->request->isPostRequest)
		{
			// we only allow deletion via POST request
			$this->loadModel()->delete();

			// if AJAX request (triggered by deletion via admin grid view), we should not redirect the browser
			if(!isset($_GET['ajax']))
				$this->redirect(array($this->getId()));
		}
		else
			throw new CHttpException(400);
	}

	public function getTableColumns() {
		$model = CActiveRecord::model($this->modelName);
		$attributes = $model->getAttributes();
		unset($attributes[ $model->metaData->tableSchema->primaryKey ]);
		$columns = array_keys($attributes);

		$columns[] = $this->getButtonsColumn();

		return $columns;
	}

	public function getButtonsColumn() {
		$allowedActions = explode(',', $this->allowedActions);
		$allowDelete = in_array('delete', $allowedActions);
		$allowView = in_array('view', $allowedActions);
		$allowEdit = in_array('edit', $allowedActions);

		$templates = array();
		if (!$allowEdit && $allowView)
			$templates[] = '{view}';
		elseif ($allowEdit)
			$templates[] = '{update}';
		if ($allowDelete) {
			$templates[] = '{delete}';
		}
		if($this->additionalTemplates)
			$templates = array_merge($templates, $this->additionalTemplates);

		$template = implode('',$templates);

		$return = array(
			'class' => 'bootstrap.widgets.TbButtonColumn',
			'template' => $template,
			'updateButtonUrl' => 'Yii::app()->controller->createUrl("edit",array("id"=>$data->primaryKey))'
		);

		if ($this->additionalButtons)
			$return['buttons'] = $this->additionalButtons;
		return $return;
	}

	/**
	 * Example:
	 * <code>
	 *  return array(
	 *      'name' => array(
	 *          'type' => 'textField',
	 *      ),
	 *      'clientId' => array(
	 *          'type' => 'dropDownList',
	 *          'data' => CHtml::listData(Client::model()->findAll(), 'id', 'name'),
	 *          'htmlOptions' => array(
	 *              'empty' => 'Empty',
	 *          ),
	 *      ),
	 *      'logoUrl' => array(
	 *          'class' => 'ext.ImageFileRowWidget',
	 *          'options' => array(
	 *              'uploadedFileFieldName' => '_logo',
	 *              'removeImageFieldName' => '_removeLogoFlag',
	 *              'thumbnailImageUrl' => $model->getResizedLogoUrl(120, 120),
	 *          ),
	 *      ),
	 *  );
	 * </code>
	 *
	 * @param CActiveRecord $model
	 * @return array
	 */
	public function getEditFormElements($model) {
		return array();
	}

	/**
	 * @param CActiveRecord $model
	 * @param array $attributes
	 */
	public function beforeSetAttributes($model, &$attributes) {}

	/**
	 * @param CActiveRecord $model
	 * @param array $attributes
	 */
	public function beforeList($model, &$attributes) {}

	/**
	 * @param CActiveRecord $model
	 */
	public function beforeSave($model) {}
	/**
	 * @param CActiveRecord $model
	 */
	public function afterSave($model) {}
	/**
	 * @param CActiveRecord $model
	 */
	public function beforeEdit($model) {}
}
