function MyAutoComplete($obj, objOpt, multi, Ajax, MinLen) {
	function split(val) {
		return val.split(/,\s*/);
	}
	function extractLast(term) {
		return split(term).pop();
	}
	var opt = {
		open: function(event, ui) {
			var $this = $(this), $uac = $('.ui-autocomplete')
			if ($this.hasClass('ltr'))
				$uac.addClass('ltr')
			if ($this.hasClass('rtl'))
				$uac.addClass('rtl')
		},
		close: function() {
			$('.ui-autocomplete').removeClass('ltr rtl')
		},
		source: objOpt.source,
		minLength: MinLen && MinLen.toString().length ? MinLen : 1,
		delay: 750
	}
	if (objOpt.appendTo)
		opt.appendTo = objOpt.appendTo
	if (multi) {
		$obj.keydown(function(event) {
			if (event.keyCode === $.ui.keyCode.TAB && $(this).data("autocomplete").menu.active)
				event.preventDefault();
		})
		opt.focus = function() {
			return false;
		}
		opt.select = function(event, ui) {
			var terms = split(this.value);
			terms.pop();
			terms.push(ui.item.value);
			terms.push("");
			this.value = terms.join(", ");
			return false;
		}
		opt.source = function(request, response) {
			response($.ui.autocomplete.filter(objOpt.source, extractLast(request.term)));
		}
	}
	if (Ajax) {
		opt.source = function(request, response) {
			function myResp(r) {
				response(r)
				var $uiac=$('.ui-autocomplete')
				setTimeout(function() {
					$uiac.find('li a').each(function() {
						var $a = $(this)
						$a.html(htmlTagDecode($a.html()))
						var $href = $a.find('[rel*="href:"]')
						if ($href.length) {
							$a.attr({rel: 'superbox[ajax][' + $href.attr('rel').find2find_substr('href:') + '][autoxauto]'})
						}
						var $separators=$a.find('.__uiausep')
						if($separators.length)
							$a.replaceWith($($a.html()))
						var $BlankTargets=$a.find('[rel*=__btarget]')
						if($BlankTargets.length)
							$a.attr({'target':'_blank', 'href':$BlankTargets.attr('rel').find2find_substr('__btarget:',' ')}).unbind('click')
					})
					$uiac.css('width', 'auto')
					SuperBox_Reconstruct()
				}, 100)
			}
			$.getJSON(objOpt.source, {
				term: extractLast(request.term)
			}, myResp);
		}
		opt.search = function() {
			if (extractLast(this.value).length < MinLen)
				return false;
		}
	}
	$obj.autocomplete(opt)
}
function MyAutoComplete_Construct() {
	function Construct() {
		var fnc //MyAutoCompleteFNCs is created in Tools
		for (fnc in MyAutoCompleteFNCs) {
			if (MyAutoCompleteFNCs[fnc])
				MyAutoCompleteFNCs[fnc]()
			MyAutoCompleteFNCs[fnc] = null
			delete MyAutoCompleteFNCs[fnc]
		}
		return true
	}
	function Run() {
		PostBack.AddInHTMLAjaxComplete('MyAutoComplete', Construct)
		Construct()
	}
	if (typeof(PostBack) != 'undefined')
		Run()
	else
		PBDocComplete.push(Run)
}
MyAutoComplete_Construct()
