<?php
Yii::import('ext.cron-extension.vendor.autoload',true);
class DeployEnvironmentsCommand extends CConsoleCommand
{
	public function actionIndex() {
        $environments = Environment::model()->findAll();
        foreach($environments as $environment) {

            try{
                $cron = \Cron\CronExpression::factory($environment->schedule);
                if($cron->isDue()){
                    foreach($environment->nodes as $node){
                        if(!$node->freezeDeploy){
                            $node->needDeploy = 1;
                            $node->save();
                        }
                    }
                }
            }
            catch(InvalidArgumentException $e){
                continue;
            }
        }
	}
}
