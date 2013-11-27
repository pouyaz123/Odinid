PBDocComplete.push(function() {
	function FocusConstruct() {
		var $Focus = $('[rel*="FocusMe"]:not([FocusAlreadySet=1]):first')
		$Focus.attr('FocusAlreadySet', 1)
		if ($Focus.is('input, select, textarea')) {
			if ($Focus.is('[rel*="Currency"]'))
				$Focus = $Focus.siblings('[rel*="Symbolic"]')
			$Focus.focus()
		}
	}
	FocusConstruct()
	PostBack.AddInHTMLAjaxComplete('FocusConstruct', FocusConstruct)

	$('body').delegate('[rel*="DirectFocusInPage"]', {
		click: function() {
			var Displacement = [0, -50]

			var ID = this.href.find2find_substr('#')
					, $Obj = $('#' + ID)
					, Offset = $Obj.offset()
			window.scrollTo(Offset.left + Displacement[0], Offset.top + Displacement[1])
			$Obj.focus()
			if ($Obj.is('[disabled]'))
				$Obj.attr('disabled', null)
			$Obj.trigger('TabsFocus')
			return false
		}
	})
})
