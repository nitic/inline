<h2><?php echo $lang['siteSettings']; ?></h2>

<form action="">
    <table>
        <tbody>
            <tr>
                <td>
                    <label for="site_name"><?php echo $lang['siteName']; ?>:</label>
                </td>
                <td width="65%">
                    <input type="text" name="site_name" id="site_name" value="">
                </td>
            </tr>
            <tr>
                <td>
                    <label for="site_desc"><?php echo $lang['siteDescription']; ?>:</label>
                </td>
                <td>
                    <textarea name="site_desc" id="site_desc"></textarea>
                </td>
            </tr>
            <tr>
                <td>
                    <label><?php echo $lang['siteLangs']; ?>:</label>
                </td>
                <td>
                    <div id="site-languages">
                        <ul>
                            <li class="lang-<?php echo $currentLang; ?>" data-lang="<?php echo $currentLang; ?>">
                                <span class="code"><?php echo $currentLang; ?></span>
                                <span class="name"><?php echo $currentName; ?></span> 
                                <span class="actions">
                                    <a href="#remove"><i class="fa fa-times"></i></a>
                                </span>
                            </li>
                        </ul>
                        <a id="add-language" href="#add-language"><?php echo $lang['siteLangAdd']; ?></a>                        
                        <div id="add-language-form">
                            <label>
                                <?php echo $lang['siteLangHint']; ?>: 
                                <input type="text" id="language-code" maxlength="2">
                                <span class="actions">
                                    <a href="#save" class="save"><i class="fa fa-check"></i></a>
                                    <a href="#cancel" class="cancel"><i class="fa fa-times"></i></a>
                                </span>
                            </label>
                        </div>
                    </div>
                    <input type="hidden" id="site_langs" name="site_langs" value="<?php echo $currentLang; ?>">
                </td>
            </tr>
        </tbody>
    </table>
</form>
<script>
    $(document).ready(function(){
        $('#add-language').click(function(e){
            e.preventDefault();
            $(this).hide();
            $('#add-language-form').show();
            $('#add-language-form #language-code').focus();
        });
        $('#add-language-form .actions .save').click(function(e){
            e.preventDefault();
            var code = $('#language-code').val();
            if (code.length < 2) { cancelLangAdding(); return; }
            if ($('#site-languages ul li.lang-' + code).length > 0){ cancelLangAdding(); return; }
            var li = $('#site-languages ul li').eq(0).clone();
            li.removeClass();
            li.addClass('lang-'+code);
            li.data('lang', code);
            li.attr('data-lang', code);
            $('.code', li).html(code);
            $('.name', li).html('<i class="fa fa-spinner fa-spin"></i>');
            li.hide().appendTo('#site-languages ul').fadeIn();            
            toggleLangActions();
            cancelLangAdding();
            setup.runModule('setup', 'loadLangName', {code: code}, function(result){
                $('#site-languages ul li.lang-' + code+' .name').html(result.name);
            });
            buildLangsString();
        });
        $('#add-language-form input').keyup(function(e){
            if (e.which == 13){
                $('#add-language-form .actions .save').click();
            }
        });
        $('#add-language-form .actions .cancel').click(function(e){
            e.preventDefault();
            cancelLangAdding();            
        });
        $('#site-languages ul').on('click', 'li .actions a', function(e){
            e.preventDefault();
            var li = $(this).parents('li');
            li.remove();
            buildLangsString();
            toggleLangActions();            
        });
        function buildLangsString(){
            var langs = [];
            $('#site-languages ul li').each(function(){
                langs.push($(this).data('lang'));
            });
            $('#site_langs').val(langs.join(','));
        }
        function cancelLangAdding(){
            $('#add-language').show();
            $('#add-language-form').hide();
            $('#add-language-form #language-code').val('');
        };
        function toggleLangActions(){            
            var count = $('#site-languages > ul li').length;
            console.log(count);
            $('#site-languages > ul li .actions').toggle(count > 1);
        };
    });
</script>
