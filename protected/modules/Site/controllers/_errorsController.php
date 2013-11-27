<?php

namespace Site\controllers;

use \Tools as T;
use \Consts as C;

class _errorsController extends \CController {

	public function actionError() {
		if ($error = \Yii::app()->errorHandler->error) {
			if (\Yii::app()->request->isAjaxRequest)
				echo $error['message'];
			else {
				switch ($error['code']) {
					case 400:
						$this->render('BadRequest400', $error);
						break;
					case 403:
						$this->render('Forbidden403', $error);
						break;
					case 404:
						$this->render('NotFound404', $error);
						break;
					case 500:
						$this->render('InternalError500', $error);
						break;
					case 503:
						$this->render('Maintenance503', $error);
						break;
					default:
						$this->render('Error', $error);
						break;
				}
			}
		}
	}

}