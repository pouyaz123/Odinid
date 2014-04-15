<?php
/* @var $this \Site\controllers\ProfileController */
/* @var $Model \Site\models\Profile\Languages */
?>
<? $this->beginContent('Site.views.profile.editinfo.layout') ?>
<div id="divEditLanguages">
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
					<div><?= t2::Site_User('TagsHelp') ?></div>
					<script>
_t.RunScriptAfterLoad('tagit/tag-it.min', function() {
	_t.RunScriptAfterLoad('balloon/jquery.balloon.min', function() {
		_t.RunScriptAfterLoad('MyJuiAutoComplete/MyAutoComplete', function() {
			var $obj = $('#UserLanguages_txtLanguages')
			function TagStartup() {
				var $tags = $obj.next('ul')
				$tags.find('a').attr('rel', 'AjaxExcept')
				$tags.find('li:has(span.tagit-label)').each(function(idx, elm) {
					var $slct = $('#divLanguageRates select:eq(' + idx + ')').attr('TagLabel', $(elm).find('span.tagit-label').html())
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
			$obj.tagit({
				allowSpaces: true
				, autocomplete: MyAutoComplete(
						$obj, {
							source: '<?= Tools\HTTP::URL_InsertGetParams($_SERVER['REQUEST_URI'], "__AjaxPostKW=AutoComplete_UserLanguages_txtLanguages") ?>'
						}, 0, 1, 1, 1)
				, afterTagAdded: function(e, ui) {
					if (!ui.duringInitialization) {
						$('#divLanguageRates').append(
								$('<?= addslashes(str_replace("\n", '\\n', \CHtml::dropDownList('UserLanguages[ddlRate][]', null, $Model->arrRates, array('id' => '')))) ?>')
								.attr('TagLabel', ui.tagLabel))
						TagStartup()
					}
				}
				, afterTagRemoved: function(e, ui) {
					var $slct = $('#divLanguageRates select[TagLabel="' + ui.tagLabel + '"]')
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
					$('#divLanguageRates [name="' + $(this).attr('name') + '"]').attr('value', $(this).attr('value'))
				}, click: function() {
					$(this.LIElement).hideBalloon()
				}
			})
			TagStartup()
		})
	})
})
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
