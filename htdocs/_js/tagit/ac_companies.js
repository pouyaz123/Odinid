function tagit_ac_companies(txtMain_JQS, hdnIDs_JQS, txtURL_JQS, ajaxURL, tagLimit) {
	tagLimit = tagLimit ? tagLimit : 1
	var $hdnIDs = $(hdnIDs_JQS)
	function XLinks_AjaxEx($obj) {
		$($obj).next('ul').find('a').attr('rel', 'AjaxExcept')
	}
	var $obj = $(txtMain_JQS)
	function pushShiftID(pushID, wipeIdx) {
		var IDs = $hdnIDs.attr('value')
		IDs = IDs ? IDs.split(',') : [];
		if (pushID !== null)
			IDs.push(pushID)
		else if (wipeIdx!=null)
			IDs = $.merge($.merge([], IDs.slice(0, wipeIdx)), IDs.slice(wipeIdx + 1))
		$hdnIDs.attr('value', IDs.join(','))
	}
	var LastDR;
	function wipeLastDR(){
		LastDR=null
	}
	var ACOpts = MyAutoComplete(
			$obj, {
				source: ajaxURL
				, focus: function(e, ui) {
					if (ui.item.label)
						LastDR = $.parseJSON($(ui.item.label).attr('rel'))
				}
				, search: wipeLastDR
				, close: function(){setTimeout(wipeLastDR, 100)}
				, open: wipeLastDR
			}, 0, 1, 1, 1)

	$obj.tagit({
		allowSpaces: true
		, autocomplete: ACOpts
		, tagLimit: tagLimit
		, afterTagAdded: function(e, ui) {
			XLinks_AjaxEx($(this))
			if (LastDR) {
				if (LastDR['ID'])
					pushShiftID(LastDR['ID'])
				if (txtURL_JQS && LastDR['URL'])
					$(txtURL_JQS).tagit('createTag', LastDR['URL'])

			} else
				pushShiftID(0)
		}
		, beforeTagRemoved: function(e, ui) {
			if (txtURL_JQS)
				$(txtURL_JQS).tagit('removeAll')
			pushShiftID(null, $(ui.tag).parent().find('li').index(ui.tag))
		}
	})
	XLinks_AjaxEx($obj)
	if (txtURL_JQS) {
		XLinks_AjaxEx($(txtURL_JQS).tagit({
			allowSpaces: true
			, tagLimit: 1
			, afterTagAdded: function(e, ui) {
				XLinks_AjaxEx($(this))
			}
			, afterTagRemoved: function() {
				$hdnIDs.attr('value', '')
			}
		}))
	}

}