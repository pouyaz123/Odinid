tinyMCE.init({
	// General options
	mode: "specific_textareas",
	editor_selector: "mceBox_Full",
	theme: "advanced",
	skin: "o2k7",
	language: "en",
	plugins: "autolink,lists,pagebreak,style,layer,table,advhr,advimage,advlink,emotions,iespell,inlinepopups,insertdatetime,preview,media,searchreplace,print,contextmenu,paste,directionality,fullscreen,noneditable,visualchars,nonbreaking,xhtmlxtras,template,wordcount,advlist,autosave",
	// Theme options
	theme_advanced_buttons1: "newdocument,print,preview,fullscreen,|,search,replace,|,cut,copy,paste,pastetext,pasteword,|,undo,redo,restoredraft",
	theme_advanced_buttons2: "styleprops,styleselect,formatselect,fontselect,fontsizeselect,|,bold,italic,underline,strikethrough,|,justifyleft,justifycenter,justifyright,justifyfull,|,ltr,rtl",
	theme_advanced_buttons3: "sub,sup,|,bullist,numlist,outdent,indent,|,forecolor,backcolor,|,link,unlink,anchor,|,image,media,template,|,nonbreaking,visualchars,charmap,emotions,insertdate,inserttime,iespell,|,cleanup,removeformat,|,code,|,help",
	theme_advanced_buttons4: "tablecontrols,|,insertlayer,moveforward,movebackward,absolute,visualaid,|,blockquote,cite,abbr,acronym,del,ins,attribs,|,hr,advhr,pagebreak",
	theme_advanced_toolbar_location: "top",
	theme_advanced_toolbar_align: "left",
	theme_advanced_statusbar_location: "bottom",
	width: '830px',
	height: '400px',
	theme_advanced_resizing: true,
	// TODO3: MCE Full system
	// Drop Down lists for link/image/media/template dialogs
//	template_external_list_url : "lists/template_list.js",
//	external_link_list_url : "lists/link_list.js",
//	external_image_list_url : "lists/image_list.js",
//	media_external_list_url : "lists/media_list.js",

	// Style formats
	style_formats: [
		{title: 'Marker: Gold', inline: 'span', styles: {background: '#fc0', display:'inline-block'}},
		{title: 'Marker: Pistachio', inline: 'span', styles: {background: '#cf0', display:'inline-block'}},
		{title: 'Bold text', inline: 'b'},
		{title: 'Blue text', inline: 'span', styles: {color: '#00f'}},
		{title: 'Red text', inline: 'span', styles: {color: '#f00'}},
		{title: 'Blue header', block: 'h1', styles: {color: '#00f'}},
		{title: 'Red header', block: 'h1', styles: {color: '#f00'}}
	],
	relative_urls: false
//	,font_size_classes : "fontSize1,fontSize2,fontSize3,fontSize4,fontSize5,fontSize6,fontSize7"
	,content_css : "/_js/tiny_mce/MyConfigs/classes.css"
});