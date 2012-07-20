<?php


class ApiController extends Controller
{
    /**
     * This is the default 'index' action that is invoked
     * when an action is not explicitly requested by users.
     */
    public function actionIndex()
    {

    }
    public function actionPing()
    {
        if(!empty($_GET['node']) && !empty($_GET['env']) && !empty($_GET['appname']))
        {
            $criteria = new CDbCriteria();
            $criteria->addCondition('envName=:envName');
            $criteria->params = array(':envName' => $_GET['env']);
            $environment = Environment::model()->with('nodes')->find($criteria);
            if(!$environment)
                throw new CHttpException(404);

            $criteria = new CDbCriteria();
            $criteria->addCondition('envId=:envId');
            $criteria->addCondition('hostname=:hostname');
            $criteria->addCondition('role=:role');
            $criteria->params = array(
                ':envId' => $environment->id,
                ':hostname' => $_GET['node'],
                ':role' => $_GET['appname'],
            );
            $node = Nodes::model()->find($criteria);
            $message = 'node pooled';
            if(!$node) {
                $message = 'node registered';
                $node = new Nodes();
                $node->envId = $environment->id;
                $node->hostname = $_GET['node'];
                $node->role = $_GET['appname'];
            }
            if ($node->save())
                echo 'OK. '.$message;
            else
                echo 'Failed updating node';
            Yii::app()->end();
        }
        else
            throw new CHttpException(404);
    }

    public function actionAsk()
    {
        if(!empty($_GET['node']) && !empty($_GET['env']) && !empty($_GET['appname']))
        {
            $criteria = new CDbCriteria();
            $criteria->addCondition('envName=:envName');
            $criteria->params = array(':envName' => $_GET['env']);
            $environment = Environment::model()->with('nodes')->find($criteria);
            if(!$environment)
                throw new CHttpException(404);
            $criteria = new CDbCriteria();
            $criteria->addCondition('envId=:envId');
            $criteria->addCondition('hostname=:hostname');
            $criteria->addCondition('role=:role');
            $criteria->params = array(
                ':envId' => $environment->id,
                ':hostname' => $_GET['node'],
                ':role' => $_GET['appname'],
            );
            $node = Nodes::model()->find($criteria);
            if(!$node) {

                throw new CHttpException(404);
            }
            else {
                $criteria = new CDbCriteria();
                $criteria->addCondition('nodeId=:nodeId');
                $criteria->params = array(':nodeId' => $node->id);
                $deployment = Deployment::model()->find($criteria);
                if($deployment && !$deployment->timeFinished)
                    if($node->branch)
                         echo $node->branch;
                     else
                         echo 'master';
                else {
                    if($node->needDeploy) {
                        $deployment = new Deployment();
                        $deployment->nodeId = $node->id;
                        $deployment->timeStarted = time();
                        $deployment->save();
                        if($node->branch)
                            echo $node->branch;
                        else
                            echo 'master';
                    }
                    else { //no need to deploy
                        throw new CHttpException(404);
                    }
                }
            }
            Yii::app()->end();
        }
        else
            throw new CHttpException(404);
    }

    public function actionDone()
    {
        if(!empty($_GET['node']) && !empty($_GET['env']) && !empty($_GET['appname']))
        {
            $criteria = new CDbCriteria();
            $criteria->addCondition('envName=:envName');
            $criteria->params = array(':envName' => $_GET['env']);
            $environment = Environment::model()->with('nodes')->find($criteria);
            if(!$environment)
                throw new CHttpException(404);
            $criteria = new CDbCriteria();
            $criteria->addCondition('envId=:envId');
            $criteria->addCondition('hostname=:hostname');
            $criteria->addCondition('role=:role');
            $criteria->params = array(
                ':envId' => $environment->id,
                ':hostname' => $_GET['node'],
                ':role' => $_GET['appname'],
            );
            $node = Nodes::model()->find($criteria);
            if(!$node) {
                throw new CHttpException(404);
            }
            else {
                $criteria = new CDbCriteria();
                $criteria->addCondition('nodeId=:nodeId');
                $criteria->params = array(':nodeId' => $node->id);
                $deployment = Deployment::model()->find($criteria);
                if($deployment && !$deployment->timeFinished){
                    $deployment->timeFinished = time();
                    $deployment->save();
                    $node->needDeploy = 0;
                    $node->save();
                    echo 'OK. Marked as deployed';
                }
                else
                    throw new CHttpException(404);
            }
            Yii::app()->end();
        }
        else
            throw new CHttpException(404);
    }

}
