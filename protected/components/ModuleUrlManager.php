<?php
class ModuleUrlManager
{
	static function collectRules()
	{
		$rules = array(
			'/' => 'site/index',
			'/api/' => 'api/index',
			'/api/<action:\w+>/' => array('api/<action>'),
			'admin/' => 'admin/admin/index',
			'admin/<_a:(getImages|uploadImage|uploadFile)>/' => 'admin/admin/<_a>',
		);
		Yii::app()->getUrlManager()->addRules($rules,false);

		if(!empty(Yii::app()->modules)) {
			$cache = Yii::app()->getCache();
			foreach(Yii::app()->modules as $moduleName => $config) {

				$urlRules = false;
				if($cache)
					$urlRules = $cache->get('module.urls.'.$moduleName);
				if($urlRules===false){
					$urlRules = array();
					$module = Yii::app()->getModule($moduleName);
					if(isset($module->urlRules))
						$urlRules = $module->urlRules;
					if($cache)
						$cache->set('module.urls.'.$moduleName, $urlRules);
				}
				if(!empty($urlRules))
				{
					Yii::app()->getUrlManager()->addRules($urlRules);

				}
			}
		}
		$rules = array(
			'admin/<controller:\w+>/' => 'admin/admin<controller>',
			'admin/<controller:\w+>/<action:\w+>/' => 'admin/admin<controller>/<action>',
			'<_a:(login|logout|error|register|settings)>/' => 'site/<_a>',
			'<pagename:\w+>/' => 'site/textPage',
		);
		Yii::app()->getUrlManager()->addRules($rules);
		return true;
	}
}
