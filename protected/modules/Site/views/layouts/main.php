<? /* @var $this \Site\Components\BaseController */ ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<meta name="language" content="en" />

		<?= \html::JS_SrcTag('Basics/jquery-1.8.0.min', true, true, false) ?>
		<?= \html::JS_SrcTag('jqUI/jquery.ui.core.min', true, true, false) ?>
		<?= \html::JS_SrcTag('Basics/Tools', true, true, false) ?>
		<? /* <script src="/_js/print_r.js"></script> */ ?>
		<? /* 		<!-- blueprint CSS framework -->
		  <link rel="stylesheet" type="text/css" href="<?= Yii::app()->request->baseUrl; ?>/_css/screen.css" media="screen, projection" />
		  <link rel="stylesheet" type="text/css" href="<?= Yii::app()->request->baseUrl; ?>/_css/print.css" media="print" />
		  <!--[if lt IE 8]>
		  <link rel="stylesheet" type="text/css" href="<?= Yii::app()->request->baseUrl; ?>/_css/ie.css" media="screen, projection" />
		  <![endif]-->

		  <link rel="stylesheet" type="text/css" href="<?= Yii::app()->request->baseUrl; ?>/_css/main.css" />
		  <link rel="stylesheet" type="text/css" href="<?= Yii::app()->request->baseUrl; ?>/_css/form.css" /> */ ?>

		<title><?= CHtml::encode($this->pageTitle); ?></title>
		<script type="text/javascript">
			PageURL = window.location.href;
			Resources = {
				PostBack_AJAX_Err: "<?= \Lng::General('Ajax communication error') ?>"
				, PostBack_AJAX_ErrRetry: "<?= \Lng::General('Ajax communication error. retry?') ?>"
				, Confirmation: "<?= \Lng::General('Are you sure?') ?>"
			}
		</script>
		<?= \html::CSS_LinkTag('Generally') ?>
		<?= \html::CSS_LinkTag('Site') ?>
		<? /* = \html::CSS_LinkTag('form') */ ?>
		<?= \html::CSS_LinkTag('*/_js/Basics/PostBack.css') ?>
		<?= \html::$cntIncludedCSS ?>
		<?= \html::InlineCSS_GetRenderedMarkup() ?>
		<?= \html::$cntIncludedJS ?>
		<?= \html::InlineJS_GetRenderedMarkup() ?>
		<?= \html::CSS_LinkTag('*/_js/Titler/Titler.css'); ?>
		<?= \html::JS_SrcTag('Titler/Titler'); ?>
	</head>

	<body>

		<div class="container" id="page">

			<div id="header">
				<div id="logo"><?= CHtml::encode(Yii::app()->name); ?></div>
			</div>

			<div id="mainmenu">
				<?
				$this->widget('zii.widgets.CMenu', array(
					'items' => array(
						array('label' => 'Home', 'url' => array('/site/index')),
						array('label' => 'About', 'url' => array('/site/page', 'view' => 'about')),
						array('label' => 'Contact', 'url' => array('/site/contact')),
						array('label' => 'Login', 'url' => array('/site/login'), 'visible' => Yii::app()->user->isGuest),
						array('label' => 'Logout (' . Yii::app()->user->name . ')', 'url' => array('/site/logout'), 'visible' => !Yii::app()->user->isGuest)
					),
				));
				?>
			</div>
			<? if (isset($this->breadcrumbs)): ?>
				<?
				$this->widget('zii.widgets.CBreadcrumbs', array(
					'links' => $this->breadcrumbs,
				));
				?>
			<? endif ?>
			<div id="divContent">
				<?= $content; ?>
			</div>
			<div class="clear"></div>

			<div id="footer">
				Copyright &copy; <?= date('Y'); ?> by Odinid.<br/>
				All Rights Reserved.<br/>
			</div>

		</div>
		<?= \html::JS_SrcTag('Basics/PostBack') ?><? /** Contains Postback and AJAX Tools    , true, true, false */ ?>
	</body>
</html>
