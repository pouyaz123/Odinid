<?php
/* @var $this \Site\controllers\ProfileController */
/* @var $Model \Site\models\Profile\Skills */
?>
<? $this->beginContent('Site.views.profile.editinfo.layout') ?>
<div id="divEditSkills">
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
					<? $ValueOf_txtSkill = ''; ?>
					<div id="divSkillRates" style="display: none">
						<? foreach ($Model->dtSkills as $idx => $dr): ?>
							<?=
//							\html::activeComboBox($Model, $form, 'ddlRate', $Model->arrRates, array('id'=>''))
//							$form->dropDownList($Model, "ddlRate[$idx]", $Model->arrRates, array('id'=>''))
							\CHtml::dropDownList("UserSkills[ddlRate][$idx]", $dr['SelfRate'], $Model->arrRates, array('id' => ''))
//							\CHtml::dropDownList("UserSkills[ddlRate][]", null, $Model->arrRates)
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
					<div><?= t2::Site_User('TagsHelp') ?></div>
					<script>
						function TagStartup() {
							var $tags = $('#UserSkills_txtSkills').next('ul')
							$tags.find('a').attr('rel', 'AjaxExcept')
							$tags.find('li:has(span.tagit-label)').each(function(idx, elm) {
								var $slct = $('#divSkillRates select:eq(' + idx + ')').attr('TagLabel', $(elm).find('span.tagit-label').html())
								$slct.attr('name', $slct.attr('name').split('][')[0] + '][' + idx + ']')
								if (!$slct.attr('TagItClicked')) {
									$slct.attr('TagItClicked', 1)
									var $slctcln = $slct.clone()
									$slct.get(0).TheCloneJQ = $slctcln
									$slctcln.get(0).TheSlctJQ = $slct
									$(elm).balloon({contents: $slctcln, classname: 'Balloons'})
									$slctcln.attr({size: 4, rel: 'BalloonFormItems'}).get(0).LIElement = elm
								}
							})
						}
						$('#UserSkills_txtSkills').tagit({
							singleFieldNode: $('#UserSkills_txtSkills')
							, allowSpaces: true
							, afterTagAdded: function(evt, ui) {
								if (!ui.duringInitialization) {
									$('#divSkillRates').append(
											$('<?= addslashes(str_replace("\n", '\\n', \CHtml::dropDownList('UserSkills[ddlRate][]', null, $Model->arrRates, array('id' => '')))) ?>')
											.attr('TagLabel', ui.tagLabel))
									TagStartup()
								}
							}
							, afterTagRemoved: function(evt, ui) {
								var $slct = $('#divSkillRates select[TagLabel="' + ui.tagLabel + '"]')
										, TheClnJQ = $slct.get(0).TheCloneJQ
								if (TheClnJQ)
									TheClnJQ.parent().remove()
								$slct.remove()
								delete TheClnJQ, $slct
								TagStartup()
							}
						})
						$('body').delegate('[rel*=BalloonFormItems]', {
							change: function() {
								$('#divSkillRates [name="' + $(this).attr('name') + '"]').attr('value', $(this).attr('value'))
							}, click: function() {
								$(this.LIElement).hideBalloon()
							}
						})
						TagStartup()
					</script>

					<?=
					html::ButtonContainer(
							CHtml::submitButton(\t2::Site_User('Edit')
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
