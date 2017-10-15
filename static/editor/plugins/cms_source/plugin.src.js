CKEDITOR.plugins.add('cms_source', {
    init: function( editor ) {

        editor.ui.addButton( 'source', {
            label: 'HTML',
            command: 'insertSource',
            toolbar: 'forms,0'
        });

        editor.addCommand( 'insertSource', {
            exec: function( editor ) {

                parent.cms.showSourceDialog({html: editor.getData()}, function(html){
                    editor.setData(html);
                });

            }
        });

    }
});
