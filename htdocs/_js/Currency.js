function Currency() {
	function Construct() {
		var KW = 'input:text[rel*="Currency"]'
		$(KW).not('[CurrencyAdded]').attr('CurrencyAdded', 1).each(function(idx, obj) {
			var $obj = $(obj), $input = $('<input rel="Symbolic" type="text" />')
			$input.keyup(function() {
				var val = this.value
				obj.value = val
				if (!val || val.length <= 3)
					return
				val = this.value.replaceAll(',', '')
				obj.value = val
				var commas = Math.floor(val.length / 3), i, result = ''
				for (i = 0; i < commas; i++) {
					result = val.substr(val.length - 3) + (result ? ',' : '') + result
					val = val.substr(0, val.length - 3)
				}
				result = val + (val ? ',' : '') + result
				this.value = result
			})
			$obj.css({display: 'none'}).parent().append($input)
			if ($obj.is(':disabled'))
				$input.attr('disabled', true)
			$input[0].className = obj.className
			$input[0].value = obj.value
			$input.keyup()
		})
	}
	function Run() {
		PostBack.AddInHTMLAjaxComplete('Currency', Construct)
		Construct()
	}
	if (typeof(PostBack) != 'undefined')
		Run()
	else
		PBDocComplete.push(Run)
}
Currency()