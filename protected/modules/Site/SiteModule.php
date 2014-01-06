<?php

namespace Site;

class SiteModule extends \CWebModule {

	public $controllerNamespace = 'Site\controllers';
	public $defaultController = 'default';
	public $layout = 'main';

	public function init() {
		// this method is called when the module is being created
		// you may place code here to customize the module or the application
		// import the module-level models and components
		$this->setImport(array(
			'Site.models.*',
			'Site.components.*',
		));
		
		#languages
		\t2::InitializeTranslation(\Conf::$SiteModuleLangs);

		#main title
		\Yii::app()->name = \t2::Site_Common('Odinid');
	}

	public function beforeControllerAction($controller, $action) {
		if (parent::beforeControllerAction($controller, $action)) {
			// this method is called before any module controller action is performed
			// you may place customized code here
			\Tools\HTTP::TraverseModelPostName('Site\models\\');
			return true;
		}
		else
			return false;
	}

}
