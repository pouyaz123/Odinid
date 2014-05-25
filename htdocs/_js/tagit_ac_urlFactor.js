function tagit_ac_urlFactor(txtTitle_JQS, hdnID_JQS, txtURL_JQS, ajaxURL) {
	function TagStartup($obj) {
		$($obj).next('ul').find('a').attr('rel', 'AjaxExcept')
	}
	var $obj = $(txtTitle_JQS)
			, ACOpts = MyAutoComplete(
					$obj, {
						source: ajaxURL
								, select: function(e, ui) {
									if (ui.item.label) {
										var dr = $.parseJSON($(ui.item.label).attr('rel'))
										if (dr['ID'])
											$(hdnID_JQS).attr('value', dr['ID'])
										if (dr['URL'])
											$(txtURL_JQS).tagit('createTag', dr['URL'])
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
			$(txtURL_JQS).tagit('removeAll')
			$(hdnID_JQS).attr('value', '')
		}
	})
	TagStartup($obj)
	TagStartup($(txtURL_JQS).tagit({
		allowSpaces: true
		, tagLimit: 1
		, afterTagAdded: function(e, ui) {
			TagStartup($(this))
		}
		, afterTagRemoved: function() {
			$(hdnID_JQS).attr('value', '')
		}
	}))

}