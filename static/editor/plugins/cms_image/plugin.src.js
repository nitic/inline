CKEDITOR.plugins.add('cms_image', {
    init: function( editor ) {

        editor.ui.addButton( 'image', {
            label: editor.lang.common.image,
            command: 'insertImage',
            toolbar: 'insert,0'
        });

        if ( editor.contextMenu ) {
            editor.addMenuGroup( 'image' );
            editor.addMenuItem( 'image', {
                label: editor.lang.image.menu,
                command: 'insertImage',
                group: 'image'
            });
            editor.contextMenu.addListener( function( element ) {
                if (element.getAscendant( 'img', true )) {
                    return { image: CKEDITOR.TRISTATE_OFF };
                }
            });
        }

        editor.on( 'doubleclick', function( evt ) {
            var element = editor.getSelection().getSelectedElement();
            var isImageSelected = element && element.getName() == 'img';
            if (isImageSelected){
                editor.execCommand('insertImage');
                evt.stop();
            }
        });

        editor.addCommand( 'insertImage', {
            exec: function( editor ) {

                var element = editor.getSelection().getSelectedElement();
                var isImageSelected = element && element.getName() == 'img';

                if (!isImageSelected){ element = new CKEDITOR.dom.element('img'); }

                var link = element.getAscendant('a');
                var src = isImageSelected ? element.getAttribute('src') : '';
                var url = link ? link.getAttribute('href') : '';
                var style = '';
                var align = '';

                if (element.hasClass('s-rounded')) { style = 's-rounded'; }
                if (element.hasClass('s-circle')) { style = 's-circle'; }
                if (element.hasClass('s-frame')) { style = 's-frame'; }
                if (element.hasClass('s-shadow-frame')) { style = 's-shadow-frame'; }

                if (element.getAttribute('align')){
                    align = element.getAttribute('align');
                } else {
                    if (element.$.style.margin == '0px auto'){
                        align = 'center';
                    }
                }

                var width = isImageSelected ? element.$.clientWidth : '';
                var height = isImageSelected ? element.$.clientHeight : '';

                parent.cms.showImageDialog({
                    url: src,
                    link_url: url,
                    style: style,
                    align: align,
                    title: element.getAttribute('alt'),
                    width: width,
                    height: height,
                    resize: isImageSelected ? 1 : 0
                }, function(image, values){

                    if (isImageSelected){

                        if (link){
                            if (values.link_url){
                                link.setAttribute('href', values.link_url);
                                link.setAttribute('title', values.title);
                            } else {
                                link.remove(true);
                            }
                        }

                        element.setAttribute('src', values.url);
                        element.setAttribute('alt', values.title);

                        if (values.align == 'left' || values.align == 'right'){
                            element.setAttribute('align', values.align);
                        } else {
                            element.removeAttribute('align');
                        }

                        element.$.style.margin = values.align == 'center' ? '0 auto' : '';
                        element.$.style.display = values.align == 'center' ? 'block' : '';

                        element.removeClass('s-rounded').removeClass('s-circle').removeClass('s-frame').removeClass('s-shadow-frame');

                        if (values.style){
                            element.addClass(values.style);
                        }

                        element.removeAttribute('data-cke-saved-src');

                        return;

                    }

                    editor.insertElement(new CKEDITOR.dom.element(image));
                    return;

                });

            }
        });

    }
});
