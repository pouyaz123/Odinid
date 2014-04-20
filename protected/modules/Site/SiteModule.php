<?php

namespace Site;

class SiteModule extends \CWebModule {

	public $controllerNamespace = 'Site\controllers';
	public $defaultController = 'default';
	public $layout = 'main';
	/**
	 * Essentially put the controller map name to <b>\Conf::SiteModuleControllers</b><br/>
	 * <ul>
	 * <li>\Conf::SiteModuleControllers prevents users from registring by a username same as a Site module controller</li>
	 * <li>Also \Conf::SiteModuleControllers sets urlManager to grab routes before they go to profile controller</li>
	 * </ul>
	 * @see \Conf::SiteModuleControllers
	 * @var array
	 */
	//map route to controllers here to support case insensitive urls
	public $controllerMap=array(
		'user' => 'Site\controllers\UserController',
//		'profile'=>'Site\controllers\ProfileController',//not needed because it is by the username not controller name
	);

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
		\Yii::app()->name = \t2::site_site('Odinid');
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
			\Tools\HTTP::TraverseModelPostName('Site\models\\');

			//environment
			if ($controller)
				$controller->SetEnv();

			return true;
		} else
			return false;
	}

}
