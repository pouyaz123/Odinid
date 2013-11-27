<? /* @var $this Admin\Components\BaseController */ ?>
<? $this->beginContent('Admin.views.layouts.main'); ?>
<?= CHtml::encode(Yii::app()->name); ?>
<div id="divContent">
	<?= $content; ?>
</div>
<? $this->endContent(); ?>