CKEDITOR.editorConfig = function( config ) {

	config.toolbarGroups = [
		{ name: 'styles', groups: [ 'styles' ] },
		{ name: 'document', groups: [ 'mode', 'document', 'doctools' ] },
		{ name: 'clipboard', groups: [ 'clipboard', 'undo' ] },
		{ name: 'editing', groups: [ 'find', 'selection', 'spellchecker', 'editing' ] },
		{ name: 'insert', groups: [ 'insert' ] },
		{ name: 'links', groups: [ 'links' ] },
		{ name: 'forms', groups: [ 'forms' ] },
		'/',
		{ name: 'basicstyles', groups: [ 'basicstyles', 'cleanup' ] },
		{ name: 'paragraph', groups: [ 'align', 'list', 'indent', 'blocks', 'bidi', 'paragraph' ] },
		{ name: 'colors', groups: [ 'colors' ] },
		'/',
		{ name: 'tools', groups: [ 'tools' ] },
		{ name: 'others', groups: [ 'others' ] },
		{ name: 'about', groups: [ 'about' ] }
	];

	config.removeButtons = 'Save,NewPage,Preview,Print,Templates,Cut,Copy,Paste,PasteText,PasteFromWord,Replace,Find,SelectAll,Scayt,Checkbox,Form,Radio,TextField,Textarea,Select,Button,ImageButton,HiddenField,Strike,Subscript,Superscript,CreateDiv,BidiLtr,BidiRtl,Language,Anchor,Smiley,PageBreak,Iframe,Styles,Maximize,ShowBlocks,About,Outdent,Indent,Blockquote,Redo,Undo,SpecialChar,HorizontalRule,Font,FontSize,RemoveFormat';

    config.format_tags = 'div;p;h1;h2;h3;pre';

    config.removeDialogTabs = 'image:advanced;link:advanced';

    config.removePlugins= 'image,liststyle';
    config.extraPlugins = 'cms_image,cms_source';

};