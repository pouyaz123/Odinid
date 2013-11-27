<?php /* @var $this \Site\Components\BaseController */ ?>
<?php $this->beginContent('//layouts/main'); ?>
<div class="span-19">
	<div id="divContent">
		<?php echo $content; ?>
	</div>
</div>
<div class="span-5 last">
	<div id="divSidebar">
	<?php
		$this->beginWidget('zii.widgets.CPortlet', array(
			'title'=>'Operations',
		));
		$this->widget('zii.widgets.CMenu', array(
			'items'=>$this->menu,
			'htmlOptions'=>array('class'=>'operations'),
		));
		$this->endWidget();
	?>
	</div>
</div>
<?php $this->endContent(); ?>