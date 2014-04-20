_t.AddToDependencies('cloudinary/jquery.iframe-transport', [
	'jqUI/jquery.ui.widget.min',
])
_t.AddToDependencies('cloudinary/jquery.fileupload', [
	'cloudinary/jquery.iframe-transport',
])
_t.AddToDependencies('cloudinary/jquery.cloudinary', [
	'cloudinary/jquery.fileupload',
])