<?php
/* @var $this \Site\controllers\ProfileController */
/* @var $Model \Site\models\Profile\Skills */
?>
<? $this->beginContent('Site.views.profile.editinfo.layout') ?>
<div id="divEditSkills">
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
					<? $ValueOf_txtSkill = ''; ?>
					<div id="divSkillRates" style="display: none">
						<? foreach ($Model->dtSkills as $idx => $dr): ?>
							<?=
							\CHtml::dropDownList("UserSkills[ddlRate][$idx]", $dr['SelfRate'], $Model->arrRates, array('id' => ''))
							?>
							<? $ValueOf_txtSkill.=',' . $dr['Skill'] ?>
						<? endforeach; ?>
					</div>
					<?=
					html::FieldContainer(
							$form->textField($Model, 'txtSkills')
							, $form->labelEx($Model, 'txtSkills')
							, $form->error($Model, 'txtSkills'))
					?>
					<div><?= t2::site_site('TagsHelp') ?></div>
<script>
_t.RunScriptAfterLoad(['tagit/tag-it.min', 'MyJuiAutoComplete/MyAutoComplete', 'tagit/ac_lib', 'balloon/jquery.balloon.min'], function() {
	tagit_ac_balloon_select(
		'#UserSkills_txtSkills', <?= $Model->MaxItems ?>
		, '<?= Tools\HTTP::URL_InsertAjaxKW("AutoComplete_UserSkills_txtSkills") ?>'
		, '#divSkillRates', 4
		, '<?= addslashes(str_replace("\n", '\\n', \CHtml::dropDownList('UserSkills[ddlRate][]', null, $Model->arrRates, array('id' => '')))) ?>')
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
