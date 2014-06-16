<?php
/* @var $this \Site\controllers\ProfileController */
/* @var $Model \Site\models\Profile\Languages */
?>
<? $this->beginContent('Site.views.profile.editinfo.layout') ?>
<div id="divEditLanguages">
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
					<? $ValueOf_txtLanguage = ''; ?>
					<div id="divLanguageRates" style="display: none">
						<? foreach ($Model->dtLanguages as $idx => $dr): ?>
							<?=
							\CHtml::dropDownList("UserLanguages[ddlRate][$idx]", $dr['SelfRate'], $Model->arrRates, array('id' => ''))
							?>
							<? $ValueOf_txtLanguage.=',' . $dr['Language'] ?>
						<? endforeach; ?>
					</div>
					<?=
					html::FieldContainer(
							$form->textField($Model, 'txtLanguages')
							, $form->labelEx($Model, 'txtLanguages')
							, $form->error($Model, 'txtLanguages'))
					?>
					<div><?= t2::site_site('TagsHelp') ?></div>
<script>
_t.RunScriptAfterLoad(['tagit/tag-it.min', 'MyJuiAutoComplete/MyAutoComplete', 'tagit/ac_lib', 'balloon/jquery.balloon.min'], function() {
	tagit_ac_balloon_select(
		'#UserLanguages_txtLanguages', <?= $Model->MaxItems ?>
		, '<?= Tools\HTTP::URL_InsertAjaxKW("AutoComplete_UserLanguages_txtLanguages") ?>'
		, '#divLanguageRates', 4
		, '<?= addslashes(str_replace("\n", '\\n', \CHtml::dropDownList('UserLanguages[ddlRate][]', null, $Model->arrRates, array('id' => '')))) ?>')
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
