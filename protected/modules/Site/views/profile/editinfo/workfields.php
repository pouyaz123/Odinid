<?php
/* @var $this \Site\controllers\ProfileController */
/* @var $Model \Site\models\Profile\WorkFields */
?>
<? $this->beginContent('Site.views.profile.editinfo.layout') ?>
<div id="divEditWorkFields">
	<?
	if ($form = $this->beginWidget('Widgets\ActiveForm', array(
		'id' => 'ProfileInfo',
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
					<? $ValueOf_txtWorkField = ''; ?>
					<div id="divWorkFieldRates" style="display: none">
						<? foreach ($Model->dtWorkFields as $idx => $dr): ?>
							<?/*=
							\CHtml::dropDownList("UserWorkFields[ddlRate][$idx]", $dr['SelfRate'], $Model->arrRates, array('id' => ''))
							*/?>
							<? $ValueOf_txtWorkField.=',' . $dr['WorkField'] ?>
						<? endforeach; ?>
					</div>
					<?=
					html::FieldContainer(
							$form->textField($Model, 'txtWorkFields')
							, $form->labelEx($Model, 'txtWorkFields')
							, $form->error($Model, 'txtWorkFields'))
					?>
					<div><?= t2::site_site('TagsHelp') ?></div>
					<script>
_t.RunScriptAfterLoad('tagit/tag-it.min', function() {
	_t.RunScriptAfterLoad('MyJuiAutoComplete/MyAutoComplete', function() {
		var $obj = $('#UserWorkFields_txtWorkFields')
		function TagStartup() {
			var $tags = $obj.next('ul')
			$tags.find('a').attr('rel', 'AjaxExcept')
		}
		$obj.tagit({
			allowSpaces: true
			, autocomplete: MyAutoComplete(
					$obj, {
						source: '<?= Tools\HTTP::URL_InsertGetParams($_SERVER['REQUEST_URI'], "__AjaxPostKW=AutoComplete_UserWorkFields_txtWorkFields") ?>'
					}, 0, 1, 1, 1)
			, tagLimit: <?= $Model->MaxItems ?>
			, afterTagAdded: function(e, ui) {
				if (!ui.duringInitialization) {
					TagStartup()
				}
			}
		})
		TagStartup()
	})
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
