var MyDDL = new function() {
	var Conf = {
		ShowAnimTime: 250
				, HideAnimTime: 0
	}
	this.Conf = Conf
	function Construct() {
		$('select.MyDDL:not([MyDDLAdded=1])').attr('MyDDLAdded', 1).hide().each(function(idx, slc) {
			var Searchable = $(this).is('[rel*="Searchable"]')
			var $ddlContainer = $('<div class="MyDDL"></div>')
					, $slc = $(slc)
					, $input = $('<input type="text" rel="Symbolic"' + (!Searchable ? ' readonly="readonly" style="cursor:pointer"' : '') + '/>')
					, $table = $('<a href="javascript:void(0);" class="DropLink"><table><tr><td></td><td class="DropButton"><div class="DropArrow"></div></td></tr></table></a>')

			$ddlContainer.attr({'class': $slc.attr('class')})
			$ddlContainer[0].RelatedSelect = slc

			var currentLevel = 0
					, tree = '\n<ul>'
					, firstLI = true
					, arrOptions = new Array(), rpterr=0
			$slc.find('option').each(function(idx, opt) {
				var $opt = $(opt)
						, level = parseInt($opt.attr('rel'))
				if (level > currentLevel) {
					tree += '\n<div class="SubArrow"></div><ul>'
					currentLevel++
				} else if (level < currentLevel) {
					tree += '\n</li>'
					while (level < currentLevel) {
						tree += '\n</ul>\n</li>'
						currentLevel--
					}
				} else if (!firstLI)
					tree += '\n</li>'
				var title = $opt.attr('title')
				if (!title)
					title = ''
				tree += '\n<li>\n<a href="javascript:void(0);" rel="' + $opt.val() + '" title="' + title + '">' + $opt.html() + '</a>'
				if(arrOptions[$opt.val()])rpterr=1
				arrOptions[$opt.val()] = $opt.html().Lower()
				firstLI = false
			})
			if(rpterr)alert('error myddl('+$slc.attr('id')+'):reptd idval')
			var i
			for (i = 0; i < currentLevel; i++) {
				tree += '\n</li>\n</ul>'
			}
			tree += '\n</li>\n</ul>'

			$table.find('td:first').append($input)
			$ddlContainer.append($table)
			$ddlContainer.append(tree)
			$slc.before($ddlContainer)

			$input.keyup(function(e) {
				if (e.keyCode == 27 || e.keyCode == 13) {
					$input.blur()
					$ddlContainer.click()
					return
				}
				if (!Searchable)
					return
				var ValKey, Found = false
				for (ValKey in arrOptions) {
					if (arrOptions[ValKey].indexOf(this.value.Lower()) > -1) {
						Found = true
						break
					}
				}
				if (Found) {
					var $UL = $ddlContainer.find('ul:first'),
							$Item = $UL.find('a[rel="' + ValKey + '"]:first')
					if ($Item.length) {
						$UL.scrollTop($Item.parent().position().top + $UL.scrollTop())
						var Val = this.value
						if (!$slc.is('[rel*="AjaxElement"]'))
							$slc[0].SetSelectedItem($Item[0])
						this.value = Val
					}
				}
			}).blur(function() {
				$(this).val($slc.find('option:selected').html())
			}).focus(function() {
				if (Searchable)
					$(this).val('')
			})

			var $li = $ddlContainer.find('li'), $a = $li.find('>a')
			$li.hover(function() {
				$('>ul', this).show(Conf.ShowAnimTime)
				$(this).addClass('hover')
			}, function() {
				$('>ul', this).hide(Conf.HideAnimTime)
				$(this).removeClass('hover')
			})
			$slc[0].SetSelectedItem = function(__this) {
				var $this = $(__this)
				if (slc.DDLIsSet && $slc.val() === $this.attr('rel'))
					return
				$a.removeClass('Current')
				$this.parent('li').find('ul').hide()
				$this.add($this.parent().parents('li:has(.SubArrow)').find('>a')).addClass('Current')
				var Title = $this.attr('title')
				if (!Title)
					Title = $this.attr('titlerTitle')
				if (!Title)
					Title = null
				$input.val($this.html()).attr('titlerTitle', Title)
				var rel = $this.attr('rel')
				rel = typeof(rel) != 'undefined' ? rel : ''
				$slc.find('option').attr('selected', null).filter('[value="' + rel + '"]').attr('selected', 'selected')
				if (slc.DDLIsSet)
					$slc.change()
			}
			$a.click(function() {
				$slc[0].SetSelectedItem(this)
			})

			$a.filter('[rel="' + $slc.find('option:selected').val() + '"]').click()
			slc.DDLIsSet = true

			$ddlContainer.click(function() {
				var $rootUL = $('ul:first', this), $DDLField = $slc.parent('.Field.DropDown')
				if ($rootUL.is(':visible')) {
					$rootUL.hide(Conf.HideAnimTime)
					$slc.blur()
				}
				else
					$rootUL.show(Conf.ShowAnimTime, function() {
						var $Current = $('.Current', this)
						$(this).scrollTop($Current.length ? $Current.parent().position().top : 0)
					})
				$slc.focus()
			}).hover(function() {
				$(this).attr('hovered', 1)
			}, function() {
				$(this).attr('hovered', 0)
			})

			var $Label, slcID = $slc.attr('id')
			if (slcID) {
				$Label = $('label[for="' + slcID + '"]')
				if ($Label.length) {
					$Label.click(function() {
						$ddlContainer.click()
					}).hover(function() {
						$ddlContainer.mouseover()
					}, function() {
						$ddlContainer.mouseout()
					})
				}
			}
		})
		$('body:not([MyDDLAdded])').attr('MyDDLAdded', 1).click(function() {
			$('div.MyDDL:not([hovered=1]):has(ul:visible)').find('ul:visible').hide(Conf.HideAnimTime)
		})
	}
	if (typeof(PostBack) != 'undefined')
		PostBack.AddInHTMLAjaxComplete('myddl', Construct)
	PBDocComplete.push(function() {
		PostBack.AddInHTMLAjaxComplete('myddl', Construct)
		Construct()
	})
	Construct()
}