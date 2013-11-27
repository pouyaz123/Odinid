//title 1.0
/**
 * add the Titler class to an exact element or a container under the <body>
 * div#TooltipArea>div.TooltipContent
 */
PBDocComplete.push(function() {
	Titler = new function() {
		var AnimSpeed = 500
				, TitlerKW = '.Titler'
				, Displacement = [15, 20]
				, Delay = 15000

		var $Tooltip = $('<div id="TooltipArea"><div class="TooltipContent"></div></div>')
				, $Doc = $(document)
		$('body').prepend($Tooltip)

		function Construct() {
			$(TitlerKW).delegate("[title][title!='']:not(iframe), [titlerTitle]", {
				mouseover: function(e) {
					$Tooltip[0].visible = 1
					if (this.title) {
						$(this).attr('titlerTitle', this.title)
						this.title = ''
						delete(this.title)
					}
					var titlerTitle = $(this).attr('titlerTitle')
					$Tooltip.find('>.TooltipContent').html(titlerTitle ? titlerTitle : '')
					$Tooltip.css('direction', $(this).css('direction'))
					followMouse($Tooltip, e, 0, [Displacement[0] - $Doc.scrollLeft(), Displacement[1] - $Doc.scrollTop()])
					$Tooltip.stop().css({
						height: 'auto',
						width: 'auto',
						opacity: '1',
						display: 'none'
					}).show()
					if ($Tooltip[0].timeout)
						clearTimeout($Tooltip[0].timeout)
					$Tooltip[0].timeout = setTimeout(function() {
						$Tooltip[0].visible = 0
						if (!$Tooltip[0].visible)
							$Tooltip.hide(AnimSpeed)
					}, Delay)
				}
				, mouseout: function(e) {
					$Tooltip[0].visible = 0
					if ($Tooltip[0].timeout)
						clearTimeout($Tooltip[0].timeout)
					$Tooltip[0].timeout = setTimeout(function() {
						if (!$Tooltip[0].visible)
							$Tooltip.hide(AnimSpeed, function() {
								$(this).find('.TooltipContent').html('')
							})
					}, 200)
				}
				, mousemove: function(e) {
					followMouse($Tooltip, e, 0, [Displacement[0] - $Doc.scrollLeft(), Displacement[1] - $Doc.scrollTop()])
				}
			}
			)
		}
		Construct()
	}
	//live is deprecated
//	$('.mceEditor').live('mouseover', function(){
//		if(this.TitlerConstructed)
//			return
//		this.TitlerConstructed=1
//		Titler.Construct()
//	})
})