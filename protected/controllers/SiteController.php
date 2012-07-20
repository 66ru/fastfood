<?php

Yii::app()->getComponent('bootstrap');

class SiteController extends Controller
{
    /**
     * This is the default 'index' action that is invoked
     * when an action is not explicitly requested by users.
     */
    public function actionIndex()
    {
        if(Yii::app()->user->isGuest){
            Yii::app()->user->setReturnUrl(array('site/index'));
            $this->redirect('/login');
        }
    }

    /**
     * This is the action to handle external exceptions.
     */
    public function actionError()
    {
        if($error=Yii::app()->errorHandler->error)
        {
            if(Yii::app()->request->isAjaxRequest)
                echo $error['message'];
            else
            {
                $this->pageTitle = Yii::app()->params['appName'].' - '.$error['code'];
                $this->render('error', $error);
            }
        }
    }

    /**
     * Displays the login page
     */
    public function actionLogin()
    {
        $this->pageTitle = Yii::app()->params['appName'].' - Авторизация';
        $model=new LoginForm;
        $model->scenario = 'login';
        // collect user input data
        if(isset($_POST['LoginForm']))
        {
            $model->attributes=$_POST['LoginForm'];
            // validate user input and redirect to the previous page if valid
            if($model->validate() && $model->login())
            {
                $this->redirect(Yii::app()->user->getReturnUrl(array('site/index')), true);
            }
        }
        // display the login form
        $this->render('login',array('loginForm'=>$model));
    }


    /**
     * Logs out the current user and redirect to homepage.
     */
    public function actionLogout()
    {
        Yii::app()->user->logout();
        $this->redirect(Yii::app()->homeUrl);
    }

}
