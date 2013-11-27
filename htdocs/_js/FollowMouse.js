function followMouse(obj, e, time, plc, scrlDoc) {
	//conf
	var _default_animate_time = 500, scrlDoc = !scrlDoc ? document : scrlDoc

	if (!plc) plc = [0, 0]
	var $Obj = $(obj), e = e ? e : event, time = (time || time === 0) ? time : _default_animate_time,
		mybody = (scrlDoc == document ? document.body : scrlDoc),
		X = e.pageX + plc[0],
		Y = e.pageY + plc[1]

	if (scrlDoc != document) {
		X -= $(mybody).offset().left
		Y -= $(mybody).offset().top
	}

	var outerX = $(mybody).innerWidth() - (X + $Obj.outerWidth(true)),
		outerY = $(mybody).innerHeight() - (Y + $Obj.outerHeight(true))

	X = X + Math.min(0, outerX) + $(scrlDoc).scrollLeft() + 'px'
	Y = Y + Math.min(0, outerY) + $(scrlDoc).scrollTop() + 'px'

	$Obj.css({ position: 'absolute' })
	if (time)
		$Obj.animate({ left: X, top: Y }, time)
	else
		$Obj.css({ left: X, top: Y })
}