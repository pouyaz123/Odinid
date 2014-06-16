//requires MyAutoComplete, tagit, jqui
function tagit_ac(txtMain_JQS, TagLmt, ajaxURL, fncBeforeStartup, fncAfterStartup, fncAfterTagAdded, fncAfterTagRemoved){
		var $obj = $(txtMain_JQS)
		function TagStartup() {
			var $tags = $obj.next('ul')
			$tags.find('a').attr('rel', 'AjaxExcept')
			if(fncAfterStartup)fncAfterStartup($obj,$tags)
		}
		$obj.tagit({
			allowSpaces: true
			, autocomplete: MyAutoComplete(
					$obj, {
						source: ajaxURL
					}, 0, 1, 1, 1)
			, tagLimit: TagLmt
			, afterTagAdded: function(e, ui) {
				if (!ui.duringInitialization) {
					if(fncAfterTagAdded)fncAfterTagAdded(e, ui)
					TagStartup()
				}
			}
			, afterTagRemoved: function(e, ui) {
				if(fncAfterTagRemoved)fncAfterTagRemoved(TagStartup, e, ui)
			}
		})
		if(fncBeforeStartup)fncBeforeStartup()
		TagStartup()
}
function tagit_ac_balloon_select(txtMain_JQS, TagLmt, ajaxURL, divSlctContainer_JQS, SlctSize, SlctHTMLTag){
	var $Container=$(divSlctContainer_JQS)
	tagit_ac(txtMain_JQS, TagLmt, ajaxURL
		, function(){
			$('body').delegate('[rel*=BalloonFormItems]', {
				change: function() {
					$Container.find('[name="' + $(this).attr('name') + '"]').attr('value', $(this).attr('value'))
				}, click: function() {
					$(this.LIElement).hideBalloon()
				}
			})
		}, function($obj,$tags){
			$tags.find('li:has(span.tagit-label)').each(function(idx, elm) {
				var $slct = $Container.find('select:eq(' + idx + ')').attr('TagLabel', $(elm).find('span.tagit-label').html())
				$slct.attr('name', $slct.attr('name').split('][')[0] + '][' + idx + ']')
				if (!$slct.attr('TagItClicked')) {
					$slct.attr('TagItClicked', 1)
					var $slctcln = $slct.clone()
					$slct.get(0).TheCloneJQ = $slctcln
					$slctcln.get(0).TheSlctJQ = $slct
					$(elm).balloon({contents: $slctcln, classname: 'Balloons'})
					$slctcln.attr({size: SlctSize, rel: 'BalloonFormItems'}).get(0).LIElement = elm
				}
			})
		}, function(e, ui){
			$Container.append(
					$(SlctHTMLTag).attr('TagLabel', ui.tagLabel))
		}, function (TagStartup, e, ui){
				var $slct = $Container.find('select[TagLabel="' + ui.tagLabel + '"]')
						, TheClnJQ = $slct.get(0).TheCloneJQ
				if (TheClnJQ)
					TheClnJQ.parent().remove()
				$slct.remove()
				delete TheClnJQ, $slct
				TagStartup()
		}
	)
}
	