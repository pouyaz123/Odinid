/** By Abbas Ali Hashemian<tondarweb@gmail.com>*/ 
function print_r(collection, DontShowDetails, specialMaxLevel, JustReturn) {
	if (!collection)
		alert('print_r(no collection has been passed in. typeof : ' + (typeof collection) + ')')
	var $result, level = 0, maxLevel = 1, rowNum = 0, AltRowColor = '#fff'
	if (specialMaxLevel)
		maxLevel = specialMaxLevel
	function recursive(collection) {
		if (typeof collection != 'object' && typeof collection != 'array')
			return $('<span>' + (collection + '').replaceAll('<', '&lt;').replaceAll('<', '&gt;') + '</span>')
		else if (level >= maxLevel)
			return $('<a style="text-decoration:underline" href="javascript:;">' + collection + '</a>').click(function() {
				print_r(collection, DontShowDetails)
			})
		level++
		var x, $div = $('<div></div>')
		$div.append(''.Fill('\t', level - 1) + '{\n')
		for (x in collection) {
			$div.append($('<div></div>')
					.css({
				background: (rowNum % 2 ? AltRowColor : 'transparent')
			})
					.append(''.Fill('\t', level) + '<span style="background:#ff0">' + x + '</span>' + (!DontShowDetails ? ' : ' : ''))
					.append(!DontShowDetails ? recursive(collection[x]) : '')
					.append('\n')
					)
			rowNum++;
		}
		$div.append(''.Fill('\t', level - 1) + '}')
		level--
		return $div
	}
	$result = recursive(collection)
	if (!JustReturn) {
		$(document.body).append(
				$('<pre dir="ltr" style="direction:ltr; text-align:left; position:fixed; height:100%; width:100%; top:0px; left:0px; background:#ffc; z-index:99999; margin:0px; overflow:auto"></pre>').append($result)
				.css({
			opacity: 0.9
		})
				.append(
				$('<span style="position:absolute; top:0px; right:0px; color:#fff; background:#800; margin:5px; padding:3px 10px; cursor:pointer; font-wieght:bold; font-size:16">X</span>')
				.click(function() {
			$(this).parent('pre').remove()
		}))
				)
	}
	return $result;
}
