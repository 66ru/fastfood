<?php

Yii::import('application.controllers.admin.*');

class AdminEnvironmentController extends AdminController
{
    public $modelName = 'Environment';
    public $modelHumanTitle = array('партнера', 'партнеров', 'партнеров');
    public $allowedActions = 'add,edit,delete,getImages,uploadImage,uploadFile,show,hide,node,freeze,force,forceAll,editBranch';

    public $additionalTemplates = array('{detail}{fire}');
    public $additionalButtons = array(
        'detail'=> array(
            'label'=>'Показать ноды',
            'icon'=>'search',
            'url'=>'CHtml::NormalizeUrl(array("node","id"=>$data->id))',
            'options'=>array(
                'class'=>'showNodes',
                'data-target'=>'#myModal',
            ),

        ),
        'fire'=> array(
            'label'=>'Принудительное обновление',
            'url'=>'CHtml::NormalizeUrl(array("forceAll","id"=>$data->id))',
            'icon'=>'fire',
            'options'=>array(
                'class'=>'fireEnvironment',
            ),
        ),
    );

    public function actionEditBranch(){
        if(empty($_GET['id']))
            throw new CHttpException(404);
        $node = Nodes::model()->findByPk($_GET['id']);
        if(!$node)
            throw new CHttpException(404);

        if(!$node->branch)
            $node->branch = 'master';

        if(isset($_GET['val']))
        {
            if(empty($_GET['val']))
                $_GET['val']='master';
            $node->saveAttributes(array('branch'=>$_GET['val']));
        }
        else {
            $this->renderPartial('//admin/branchEdit',array('model'=>$node));
        }
    }

    public function actionNode()
    {
        if(empty($_GET['id']))
            throw new CHttpException(404);

        $criteria = new CDbCriteria();
        $criteria->addCondition('envId=:envId');
        $criteria->params = array(':envId'=>$_GET['id']);
        $provider = new CActiveDataProvider('Nodes', array(
            'criteria' => $criteria,
            'pagination'=>false,
        ));
        $columns = array(
            array('name'=>'id'),
            array('name'=>'hostname'),
            array('name'=>'role'),
            array('name'=>'lastUpdate','type'=>'dateTime'),
            array('name'=>'lastCompletedDeploy','type'=>'dateTime'),
            array(
                'name'=>'branch',
                'value'=>'$data->branch?$data->branch:"master"',
            ),
            array('name'=>'needDeploy','type'=>'boolean'),
            array(
                'class' => 'bootstrap.widgets.TbButtonColumn',
                'template' => '{edit}{freeze}{unfreeze}{fire}',
                'buttons' => array(
                    'edit'=> array(
                        'label'=>'Изменить ветку',
                        'url'=>'CHtml::NormalizeUrl(array("editBranch","id"=>$data->id))',
                        'icon'=>'edit',
                    ),
                    'freeze'=> array(
                        'label'=>'Не обновлять',
                        'url'=>'CHtml::NormalizeUrl(array("freeze","id"=>$data->id))',
                        'icon'=>'ban-circle',
                        'visible'=>'!$data->freezeDeploy'
                    ),
                    'unfreeze'=> array(
                        'label'=>'Обновлять',
                        'url'=>'CHtml::NormalizeUrl(array("freeze","id"=>$data->id,"start"=>1))',
                        'icon'=>'play',
                        'visible'=>'$data->freezeDeploy'
                    ),
                    'fire'=> array(
                        'label'=>'Обновить',
                        'url'=>'CHtml::NormalizeUrl(array("force","id"=>$data->id))',
                        'icon'=>'fire',
                        'options'=>array(
                            'class'=>'fireNode'
                        ),
                    ),
                ),
            ),
        );

        echo $this->renderPartial('//admin/subList',array(
            'provider'=>$provider,
            'columns'=>$columns,
            'timeout'=>Settings::byName('node_timeout')->value,
        ));
    }

    public function actionFreeze()
    {
        if(empty($_GET['id']))
            throw new CHttpException(404);
        $node = Nodes::model()->findByPk($_GET['id']);
        if(!$node)
            throw new CHttpException(404);

        if(!empty($_GET['start']))
        {
            $node->saveAttributes(array('freezeDeploy'=>0));
        }
        else{
            $node->saveAttributes(array('freezeDeploy'=>1));
        }
        $_GET['id'] = $node->envId;
        $this->actionNode();
    }

    public function actionForce()
    {
        if(empty($_GET['id']))
            throw new CHttpException(404);
        $node = Nodes::model()->findByPk($_GET['id']);
        if(!$node)
            throw new CHttpException(404);

        $node->saveAttributes(array('needDeploy'=>1));

        $_GET['id'] = $node->envId;
        $this->actionNode();

        Yii::app()->end();
    }

    public function actionForceAll()
    {
        if(empty($_GET['id']))
            throw new CHttpException(404);
        $environment = Environment::model()->findByPk($_GET['id']);
        if(!$environment)
            throw new CHttpException(404);
        foreach($environment->nodes as $node) {
            $node->saveAttributes(array('needDeploy'=>1));
        }
        echo CJSON::encode(array('status'=>'ok'));
        Yii::app()->end();
    }

    public function actionList() {
        /** @var $model CActiveRecord */
        $model=new $this->modelName('search');

        $this->beforeList($model, $_GET[$this->modelName]);
        if(isset($_GET[$this->modelName]))
            $model->attributes=$_GET[$this->modelName];

        $this->render('//admin/popupList', array(
            'model' => $model,
            'columns' => $this->getTableColumns(),
            'canAdd' => in_array('add', explode(',', $this->allowedActions)),
               'rowCssClassExpression'=>$this->rowCssClassExpression,
        ));
    }

    public function getEditFormElements($model)
    {
        $model->setScenario('admin');
        return array(
            'name' => array(
                'type' => 'textField',
            ),
            'envName' => array(
                'type' => 'textField'
            ),
            'schedule' => array(
                'type' => 'textField',
                'htmlOptions' => array('hint'=>'В формате "m h dom mon dow"'),
            ),
            'adminMail' => array(
                'type' => 'textField'
            ),
        );
    }

    public function getTableColumns()
    {
        $attributes = array(
            'name',
            'envName',
            'adminMail',
            'nextDeploy:dateTime',

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
