function tagit_ac_companies(txtMain_JQS, hdnIDs_JQS, txtURL_JQS, ajaxURL, tagLimit) {
	tagLimit = tagLimit ? tagLimit : 1
	var $hdnIDs = $(hdnIDs_JQS)
	function XLinks_AjaxEx($obj) {
		$($obj).next('ul').find('a').attr('rel', 'AjaxExcept')
	}
	var $obj = $(txtMain_JQS)
	function pushShiftID(pushID, wipeIdx) {
		var IDs = $hdnIDs.attr('value')
		if (!IDs)
			IDs = '';
		IDs = IDs.split(',')
		if (pushID)
			IDs.push(pushID)
		else if (wipeIdx)
			IDs = $.merge($.merge([], IDs.slice(0, wipeIdx)), IDs.slice(wipeIdx + 1))
		$hdnIDs.attr('value', IDs.join(','))
	}
	var ACOpts = MyAutoComplete(
			$obj, {
				source: ajaxURL
				, select: function(e, ui) {
					if (ui.item.label) {
						var dr = $.parseJSON($(ui.item.label).attr('rel'))
						if (dr['ID'])
							pushShiftID(dr['ID'])
						if (txtURL_JQS && dr['URL'])
							$(txtURL_JQS).tagit('createTag', dr['URL'])
					}
				}
			}, 0, 1, 1, 1)

	$obj.tagit({
		allowSpaces: true
		, autocomplete: ACOpts
		, tagLimit: tagLimit
		, afterTagAdded: function(e, ui) {
			XLinks_AjaxEx($(this))
			pushShiftID(0)
		}
		, afterTagRemoved: function() {
			if (txtURL_JQS)
				$(txtURL_JQS).tagit('removeAll')
			$hdnIDs.attr('value', '')
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