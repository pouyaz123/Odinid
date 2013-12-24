(function($) {
	$.widget("ui.combobox", {
		_create: function() {
			var $input,
					self = this,
					$slct = this.element.hide(),
					IsUserInputAllowed = false,
					$selected = $slct.children(":selected"),
					value = $selected.val() ? $selected.text() : "",
					$wrapper = this.wrapper = $("<span>")
					.addClass("ui-combobox")
					.insertAfter($slct);
			$input = this.element.data('inputTag')
			if ($input) {
				$input = $($input)
				IsUserInputAllowed = true
			} else
				$input = $("<input rel='Symbolic'>")
			$input
					.appendTo($wrapper)
					.val(value)
					.addClass("ui-state-default ui-combobox-input")
			var ACOptions = {
				delay: 0,
				minLength: 0,
				source: function(request, response) {
					var matcher = new RegExp($.ui.autocomplete.escapeRegex(request.term), "i");
					response($slct.children("option").map(function() {
						var text = $(this).text();
						if (this.value && (!request.term || matcher.test(text)))
							return {
								label: text.replace(
										new RegExp(
												"(?![^&;]+;)(?!<[^<>]*)(" +
												$.ui.autocomplete.escapeRegex(request.term) +
												")(?![^<>]*>)(?![^&;]+;)", "gi"
												), "<strong>$1</strong>"),
								value: text,
								option: this
							};
					}));
				},
			}
			function ChangeHandle(txt, ui) {
				var valid = false, $opt, $txt = $(txt)
				if (ui.item) {
					$opt = $(ui.item.option)
					valid = true
				} else {
					var matcher = new RegExp("^" + $.ui.autocomplete.escapeRegex($txt.val()) + "$", "i")
					$slct.children("option").each(function() {
						var $this = $(this)
						if ($(this).text().match(matcher)) {
							valid = true;
							$opt = $(this)
							return false;
						}
					});
				}
				if (valid) {
					if ($opt.not(':selected')) {
						$slct.val($opt.val())
						$slct.find('option').attr('selected', null)
						$opt.attr('selected', 'selected')
						$slct.change()
						$txt.change()
					}
				} else {
					// remove invalid value, as it didn't match anything
					var $currentOpt = $slct.find('option:selected')
							, $opts = $slct.val(null).find('option').attr('selected', null)
					if (!IsUserInputAllowed) {
						$txt.data("autocomplete").term = "";
						$txt.val($currentOpt.length ? $currentOpt.text() : '')
						$slct.val($currentOpt.val())
						$currentOpt.attr('selected', 'selected')
					} else {
						$slct.val('')
						$opts.filter('[value=""]:first').attr('selected', 'selected')
						$slct.change()
					}
					return false;
				}
			}
			ACOptions.select = function(event, ui) {
				ChangeHandle(this, ui)
				this.ChangedBySelect = true
			}
			ACOptions.change = function(event, ui) {
				if (!this.ChangedBySelect)
					ChangeHandle(this, ui)
				this.ChangedBySelect = false
			}
			$input.autocomplete(ACOptions)
					.addClass("ui-widget ui-widget-content ui-corner-left");

			$input.data("autocomplete")._renderItem = function(ul, item) {
				return $("<li></li>")
						.data({"item.autocomplete": item})
						.append("<a>" + item.label + "</a>")
						.appendTo(ul);
			};

			$("<a>")
					.attr("tabIndex", -1)
					.attr("title", "Show All Items")
					.appendTo($wrapper)
					.button({
						icons: {
							primary: "ui-icon-triangle-1-s"
						},
						text: false
					})
					.removeClass("ui-corner-all")
					.addClass("ui-corner-right ui-combobox-toggle")
					.click(function() {
						// close if already visible
						if ($input.autocomplete("widget").is(":visible")) {
							$input.autocomplete("close");
							return;
						}

						// work around a bug (likely same cause as #5265)
						$(this).blur();

						// pass empty string as value to search for, displaying all results
						$input.autocomplete("search", "");
						$input.focus();
					});
		},
		destroy: function() {
			this.wrapper.remove();
			this.element.show();
			$.Widget.prototype.destroy.call(this);
		}
	});
})(jQuery);

