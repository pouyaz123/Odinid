<?php
/* @var $this \Site\controllers\ProfileController */
/* @var $Model \Site\models\Profile\Awards */
?>
<div id="divEditAwards">
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
		<?= $Model->scenario == 'Edit' ? \CHtml::link(t2::site_site('Add new'), \Site\Consts\Routes::User_EditAwards()) : '' ?>
		<table class="FullW">
			<tr>
				<td style="width: 350px">
					<?= $form->hiddenField($Model, 'hdnAwardID') ?>
					<?= $form->hiddenField($Model, 'hdnOrganizationID') ?>
					<?=
					html::FieldContainer(
							$form->textField($Model, 'txtOrganizationTitle')
							, $form->labelEx($Model, 'txtOrganizationTitle')
							, $form->error($Model, 'txtOrganizationTitle'))
					?>
					<?=
					html::FieldContainer(
							$form->textField($Model, 'txtOrganizationURL')
							, $form->labelEx($Model, 'txtOrganizationURL')
							, $form->error($Model, 'txtOrganizationURL'))
					?>
					<script>
						_t.RunScriptAfterLoad('tagit/tag-it.min', function() {
							_t.RunScriptAfterLoad('MyJuiAutoComplete/MyAutoComplete', function() {
								function TagStartup($obj) {
									$($obj).next('ul').find('a').attr('rel', 'AjaxExcept')
								}
								var $obj = $('#UserAwards_txtOrganizationTitle')
										, ACOpts = MyAutoComplete(
												$obj, {
													source: '<?= Tools\HTTP::URL_InsertGetParams($_SERVER['REQUEST_URI'], "__AjaxPostKW=AutoComplete_UserAwards_txtOrganizationTitle") ?>'
													, select: function(e, ui) {
														if (ui.item.label) {
															var dr = $.parseJSON($(ui.item.label).attr('rel'))
															if (dr['ID'])
																$('#UserAwards_hdnOrganizationID').attr('value', dr['ID'])
															if (dr['URL'])
																$('#UserAwards_txtOrganizationURL').tagit('createTag', dr['URL'])
														}
													}
												}, 0, 1, 1, 1)

								$obj.tagit({
									allowSpaces: true
									, autocomplete: ACOpts
									, tagLimit: 1
									, afterTagAdded: function(e, ui) {
										TagStartup($(this))
									}
									, afterTagRemoved: function() {
										$('#UserAwards_txtOrganizationURL').tagit('removeAll')
										$('#UserAwards_hdnOrganizationID').attr('value', '')
									}
								})
								TagStartup($obj)
								TagStartup($('#UserAwards_txtOrganizationURL').tagit({
									allowSpaces: true
									, tagLimit: 1
									, afterTagAdded: function(e, ui) {
										TagStartup($(this))
									}
									, afterTagRemoved: function() {
										$('#UserAwards_hdnOrganizationID').attr('value', '')
									}
								}))
							})
						})
					</script>
					<?=
					html::FieldContainer(
							$form->textField($Model, 'txtTitle')
							, $form->labelEx($Model, 'txtTitle')
							, $form->error($Model, 'txtTitle'))
					?>
					<?=
					html::FieldContainer(
							\html::activeComboBox($Model, $form, 'ddlYear', $Model->arrYears
									, array('prompt' => ''))
							, $form->labelEx($Model, 'ddlYear')
							, $form->error($Model, 'ddlYear'))
					?>
					<?=
					html::FieldContainer(
							$form->textArea($Model, 'txtDescription')
							, $form->labelEx($Model, 'txtDescription')
							, $form->error($Model, 'txtDescription'))
					?>

					<?=
					html::ButtonContainer(
							CHtml::submitButton(\t2::site_site($Model->scenario == 'Edit' ? 'Edit' : 'Add')
									, array(
								'name' => $Model->scenario == 'Edit' ? 'btnSaveEdit' : 'btnAdd',
								'rel' => \html::AjaxElement('#divEditInfo') . ' ' . \html::OnceClick
									)
					))
					?>
					<?=
					$Model->scenario == 'Edit' ?
							html::ButtonContainer(
									CHtml::button(\t2::site_site('Delete')
											, array(
										'name' => 'btnDelete',
										'rel' => \html::AjaxElement('#divEditInfo') . ' ' . html::OnceClick,
										'onclick' => \html::PostbackConfirm_OnClick('Are you sure?'),
											)
							)) : ''
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