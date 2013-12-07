<?php

namespace Admin;

class AdminModule extends \CWebModule {

	public $controllerNamespace = 'Admin\controllers';
	public $defaultController = 'Panel';

	public function init() {
		// this method is called when the module is being created
		// you may place code here to customize the module or the application
		// import the module-level models and components
		$this->setImport(array(
			'Admin.models.*',
			'Admin.components.*',
		));

		#languages
		\Lng::InitializeTranslation(\Conf::$AdminModuleLangs);

		#main title
		\Yii::app()->name = \Lng::Admin('tr_Common', 'Admin Main Title');
	}

	public function beforeControllerAction($controller, $action) {
		if (parent::beforeControllerAction($controller, $action)) {
			// this method is called before any module controller action is performed
			// you may place customized code here
			\Tools\HTTP::TraverseModelPostName('Admin\models\\');
			return true;
		}
		else
			return false;
	}

}
