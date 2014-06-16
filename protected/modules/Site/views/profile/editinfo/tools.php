<?php
/* @var $this \Site\controllers\ProfileController */
/* @var $Model \Site\models\Profile\Tools */
?>
<? $this->beginContent('Site.views.profile.editinfo.layout') ?>
<div id="divEditTools">
	<?
	if ($form = $this->beginWidget('Widgets\ActiveForm', array(
		'id' => 'formProfileInfo',
		'method' => 'POST',
		'enableClientValidation' => true,
		'clientOptions' => array(
			'validateOnSubmit' => true,
		),
			))):
		/* @var $form Widgets\ActiveForm */
		?>
		<table class="FullW">
			<tr>
				<td style="width: 350px">
					<? $ValueOf_txtTool = ''; ?>
					<div id="divToolRates" style="display: none">
						<? foreach ($Model->dtTools as $idx => $dr): ?>
							<?=
							\CHtml::dropDownList("UserTools[ddlRate][$idx]", $dr['SelfRate'], $Model->arrRates, array('id' => ''))
							?>
							<? $ValueOf_txtTool.=',' . $dr['Tool'] ?>
						<? endforeach; ?>
					</div>
					<?=
					html::FieldContainer(
							$form->textField($Model, 'txtTools')
							, $form->labelEx($Model, 'txtTools')
							, $form->error($Model, 'txtTools'))
					?>
					<div><?= t2::site_site('TagsHelp') ?></div>
<script>
_t.RunScriptAfterLoad(['tagit/tag-it.min', 'MyJuiAutoComplete/MyAutoComplete', 'tagit/ac_lib', 'balloon/jquery.balloon.min'], function() {
	tagit_ac_balloon_select(
		'#UserTools_txtTools', <?= $Model->MaxItems ?>
		, '<?= Tools\HTTP::URL_InsertAjaxKW("AutoComplete_UserTools_txtTools") ?>'
		, '#divToolRates', 4
		, '<?= addslashes(str_replace("\n", '\\n', \CHtml::dropDownList('UserTools[ddlRate][]', null, $Model->arrRates, array('id' => '')))) ?>')
})
</script>

					<?=
					html::ButtonContainer(
							CHtml::submitButton(\t2::site_site('Edit')
									, array(
								'name' => 'btnSaveEdit',
								'rel' => \html::AjaxElement('#divEditInfo') . ' ' . \html::OnceClick,
								'onclick' => "$('.Balloons').remove()"
									)
					))
					?>
				</td>
				<td class="BtmAlign">
					<?= $form->errorSummary($Model) ?>
				</td>
			</tr>
		</table>
	<? endif; ?>
	<? $this->endWidget(); ?>
</div>
<? $this->endContent(); ?>
