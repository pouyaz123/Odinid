<?php

namespace Admin;

class AdminModule extends \CWebModule {

	public $controllerNamespace = 'Admin\controllers';
	public $defaultController = 'Panel';
	public $layout = 'main';
	//map route to controllers here to support case insensitive urls
	public $controllerMap=array(
		'user'=>'Admin\controllers\UserController',
		'panel'=>'Admin\controllers\PanelController',
	);

	public function init() {
		// this method is called when the module is being created
		// you may place code here to customize the module or the application
		// import the module-level models and components
		$this->setImport(array(
			'Admin.models.*',
			'Admin.components.*',
		));

		#languages
		\t2::InitializeTranslation(\Conf::$AdminModuleLangs);

		#main title
		\Yii::app()->name = \t2::admin_admin('Admin Main Title');
	}

	/**
	 * 
	 * @param Components\BaseController $controller
	 * @param \CAction $action
	 * @return boolean
	 */
	public function beforeControllerAction($controller, $action) {
		if (parent::beforeControllerAction($controller, $action)) {
			// this method is called before any module controller action is performed
			// you may place customized code here

			//models post name
			\Tools\HTTP::TraverseModelPostName('Admin\models\\');

			//environment
			if ($controller)
				$controller->SetEnv();
			
			return true;
		} else
			return false;
	}

}
