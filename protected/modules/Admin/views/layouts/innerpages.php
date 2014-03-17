<? /* @var $this Admin\Components\BaseController */ ?>
<?php $this->beginContent('Admin.views.layouts.main'); ?>
<? if (1): ?>
	<? /* <div id="divMainMenu">
	  <?
	  $this->widget('zii.widgets.CMenu', array(
	  'items' => array(
	  ),
	  ));
	  ?>
	  </div> */ ?>
	<? if (isset($this->breadcrumbs)): ?>
		<?
		$this->widget('zii.widgets.CBreadcrumbs', array(
			'links' => $this->breadcrumbs,
		));
		?>
	<? endif ?>
	<table class="TopAlign FullW">
		<tr>
			<td style="width: 150px; min-width: 150px">
				<div id="divSidebar" rel="<?= \html::AjaxLinks('#divContent:insert') ?>">
					<? /* \CHtml::link(\t2::Admin_Common('Logout'), \Admin\Consts\Routes::Logout, array('rel' => \html::AjaxExcept)); */ ?>
					<?= CHtml::encode(Yii::app()->name); ?>
					<?php
					$this->widget('CTreeView', array(
						'data' => $this->menu,
						'options' => array(
							'collapsed' => true,
						),
						'persist' => 'location',
						'unique' => true,
						'animated' => 'fast',
						'id' => 'widMenu',
					));
					?>
				</div>
			</td>
			<td>
				<div id="divContent">
					<?php echo $content; ?>
				</div>
			</td>
		</tr>
	</table>
<? endif; ?>
<?php $this->endContent(); ?>