function AllChecker() {
	function Construct() {
		var KW = '[rel*="AllChecker"]', KWContainer = '[rel*="AllCheckerCon"]'
		$(KWContainer).not('[AllCheckerAdded]').attr('AllCheckerAdded', 1).each(function(idx, obj) {
			var $checks = $(obj).find(':checkbox:not(' + KW + ')')
					, $AllChecker = $(obj).find(KW)
			$AllChecker.click(function() {
				$checks.attr('checked', $(this).is(':checked') ? 'checked' : null)
			})
			$checks.click(function() {
				if ($checks.filter(':not(:checked)').length != 0)
					$AllChecker.attr('checked', null)
			})
			if ($AllChecker.filter(':checked').length > 0)
				$checks.attr('checked', 'checked')
		})
	}
	Construct()
	if (typeof(PostBack) != 'undefined')
		PostBack.AddInHTMLAjaxComplete('AllChecker', Construct)
	PBDocComplete.push(function() {
		PostBack.AddInHTMLAjaxComplete('AllChecker', Construct)
		Construct()
	})
}
AllChecker()