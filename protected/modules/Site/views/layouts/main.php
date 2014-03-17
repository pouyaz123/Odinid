<? /* @var $this \Site\Components\BaseController */ ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<meta name="language" content="en" />
		<? /* <meta name="viewport" content="width=device-width, initial-scale=1.0" /> */ ?>

		<?= CHtml::linkTag('icon', 'image/ico', '/favicon.ico') ?>
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
				PostBack_AJAX_Err: "<?= \t2::General('Ajax communication error') ?>"
				, PostBack_AJAX_ErrRetry: "<?= \t2::General('Ajax communication error. retry?') ?>"
				, Confirmation: "<?= \t2::General('Are you sure?') ?>"
			}
		</script>
		<?= \html::CSS_LinkTag('Generally') ?>
		<?= \html::CSS_LinkTag('Site') ?>
		<?= \html::CSS_LinkTag('form') ?>
		<?= \html::CSS_LinkTag('*/_js/Basics/PostBack.css') ?>
		<?= \html::$cntIncludedCSS ?>
		<?= \html::InlineCSS_GetRenderedMarkup() ?>
		<?= \html::$cntIncludedJS ?>
		<?= \html::InlineJS_GetRenderedMarkup() ?>
		<?= \html::CSS_LinkTag('*/_js/Titler/Titler.css'); ?>
		<?= \html::JS_SrcTag('Titler/Titler'); ?>
	</head>

	<body class="Titler">

		<div class="<?= \t2::General('LTR_RTL') ?>">

			<div id="Hdr">
				<div class="CntrCol">
					<div class="LeftCol">
						<a id="Logo" href="<?= $this->createAbsoluteUrl(Site\Consts\Routes::Home) ?>">
							<img src="/_img/logo.png" alt="<?= Yii::app()->name ?>" title="<?= Yii::app()->name ?>" />
						</a>
						<div id="HNav">
							<?= Site\Components\HeaderNav::GetInstance() ?>
						</div>
					</div>
					<div class="RightCol">
						<?= CHtml::textField('txtSearch', 'Search ...') ?>
					</div>
				</div>
			</div>
		</div>
		<div id="divContent" class="CntrCol" rel="<?= \html::AjaxLinks("#divContent:insert") ?>">
			<? /* if (isset($this->breadcrumbs)): ?>
			  <?
			  $this->widget('zii.widgets.CBreadcrumbs', array(
			  'links' => $this->breadcrumbs,
			  ));
			  ?>
			  <? endif */ ?>
			<?= $content; ?>
		</div>
		<div id="Footer">
			Copyright &copy; <?= date('Y'); ?> by
			<?= \CHtml::link(\Yii::app()->name, $this->createAbsoluteUrl(Site\Consts\Routes::Home)) ?>. All Rights Reserved.
		</div>

		</div>
		<?= \html::JS_SrcTag('Basics/PostBack') ?><? /** Contains Postback and AJAX Tools    , true, true, false */ ?>
	</body>
</html>
