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
_t.RunScriptAfterLoad('tagit/tag-it.min', function() {
	_t.RunScriptAfterLoad('balloon/jquery.balloon.min', function() {
		_t.RunScriptAfterLoad('MyJuiAutoComplete/MyAutoComplete', function() {
			var $obj = $('#UserTools_txtTools')
			function TagStartup() {
				var $tags = $obj.next('ul')
				$tags.find('a').attr('rel', 'AjaxExcept')
				$tags.find('li:has(span.tagit-label)').each(function(idx, elm) {
					var $slct = $('#divToolRates select:eq(' + idx + ')').attr('TagLabel', $(elm).find('span.tagit-label').html())
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
							source: '<?= Tools\HTTP::URL_InsertGetParams($_SERVER['REQUEST_URI'], "__AjaxPostKW=AutoComplete_UserTools_txtTools") ?>'
						}, 0, 1, 1, 1)
				, tagLimit: <?= $Model->MaxItems ?>
				, afterTagAdded: function(e, ui) {
					if (!ui.duringInitialization) {
						$('#divToolRates').append(
								$('<?= addslashes(str_replace("\n", '\\n', \CHtml::dropDownList('UserTools[ddlRate][]', null, $Model->arrRates, array('id' => '')))) ?>')
								.attr('TagLabel', ui.tagLabel))
						TagStartup()
					}
				}
				, afterTagRemoved: function(e, ui) {
					var $slct = $('#divToolRates select[TagLabel="' + ui.tagLabel + '"]')
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
					$('#divToolRates [name="' + $(this).attr('name') + '"]').attr('value', $(this).attr('value'))
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
