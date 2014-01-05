<? /* @var $this Admin\Components\BaseController */ ?>
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
		  <link rel="stylesheet" type="text/css" href="<?= Yii::app()->request->baseUrl; ?>/_css/admin/screen.css" media="screen, projection" />
		  <link rel="stylesheet" type="text/css" href="<?= Yii::app()->request->baseUrl; ?>/_css/admin/print.css" media="print" />
		  <!--[if lt IE 8]>
		  <link rel="stylesheet" type="text/css" href="<?= Yii::app()->request->baseUrl; ?>/_css/admin/ie.css" media="screen, projection" />
		  <![endif]-->

		  <link rel="stylesheet" type="text/css" href="<?= Yii::app()->request->baseUrl; ?>/_css/admin/main.css" />
		  <link rel="stylesheet" type="text/css" href="<?= Yii::app()->request->baseUrl; ?>/_css/admin/form.css" /> */ ?>

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
		<?= \html::CSS_LinkTag('Admin') ?>
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
		<noscript><div style="background:#fff; color:#f00; text-align: center; padding: 10px">!!! To have a correct browsing JAVASCRIPT should be enabled !!!</div></noscript>

		<table cellpadding="0" cellspacing="0" class="BodyTable FullH TopAlign FullW <?= \Lng::General('LTR_RTL') ?>" width="100%">

			<tr>
				<td>

					<?= $content; ?>

				</td>
			</tr>
			<tr>
				<td class="Footer">
					<div class="AdminCopyright">
						Copyright &copy; <?= date('Y'); ?> by Odinid. All Rights Reserved.
					</div>
				</td>
			</tr>
		</table>

		<?= \html::JS_SrcTag('Basics/PostBack') ?><? /** Contains Postback and AJAX Tools    , true, true, false */ ?>
	</body>
</html>
