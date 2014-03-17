<? /* @var $this Admin\Components\BaseController */ ?>
<? $this->beginContent('Admin.views.layouts.main'); ?>
<? if (1): ?>
	<?= CHtml::encode(Yii::app()->name); ?>
	<div id="divContent">
		<?= $content; ?>
	</div>
<? endif; ?>
<? $this->endContent(); ?>