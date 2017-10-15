<h2><?php echo $lang['adminCreate']; ?></h2>

<form id="step-admin">
    <table>
        <tbody>
            <tr>
                <td>
                    <label for="admin_email"><?php echo $lang['adminEmail']; ?>:</label>
                </td>
                <td width="65%">
                    <input type="text" name="admin_email" id="admin_email">
                </td>
            </tr>
            <tr class="auto">
                <td>
                    <label for="admin_pass"><?php echo $lang['adminPassword']; ?>:</label>
                </td>
                <td width="65%">
                    <div class="auto-password">
                        <span class="value"><?php echo $password; ?></span> <span class="copied"><?php echo $lang['adminPasswordCopied']; ?></span>
                        <div class="links">
                            <span class="link pass-copy">
                                <i class="fa fa-copy"></i> <a href="#"><?php echo $lang['adminPasswordCopy']; ?></a>                                
                            </span>
                            <span class="link pass-regen">
                                <i class="fa fa-refresh"></i> <a href="#"><?php echo $lang['adminPasswordRegen']; ?></a>
                            </span>
                            <span class="link pass-edit">
                                <i class="fa fa-pencil"></i> <a href="#"><?php echo $lang['adminPasswordManual']; ?></a>
                            </span>
                        </div>
                    </div>
                </td>
            </tr>
            <tr class="manual">
                <td>
                    <label for="admin_pass"><?php echo $lang['adminPassword']; ?>:</label>
                </td>
                <td width="65%">
                    <input type="password" name="admin_password" id="admin_pass" value="<?php echo $password; ?>">
                </td>
            </tr>
            <tr class="manual">
                <td>
                    <label for="admin_pass2"><?php echo $lang['adminPasswordRepeat']; ?>:</label>
                </td>
                <td width="65%">
                    <input type="password" name="admin_password2" id="admin_pass2" value="<?php echo $password; ?>">
                </td>
            </tr>
            <tr class="manual">
                <td>
                    &nbsp;
                </td>
                <td width="65%">
                    <a href="#" class="pass-auto"><?php echo $lang['adminPasswordAuto']; ?></a>
                </td>
            </tr>
        </tbody>
    </table>
</form>
<script>
    $(document).ready(function(){
        
        $('#step-admin .pass-edit a').click(function(e){
            e.preventDefault();
            $('#step-admin #admin_pass').val('');
            $('#step-admin #admin_pass2').val('');
            $('#step-admin .auto').hide();
            $('#step-admin .manual').show();
            $('#step-admin #admin_pass').focus();
        });
        $('#step-admin .pass-auto').click(function(e){
            e.preventDefault();          
            $('#step-admin .manual').hide();
            createAutoPassword();
            $('#step-admin .auto').show();            
        });
        $('#step-admin .pass-regen a').click(function(e){
            e.preventDefault();          
            createAutoPassword();
        });      
        $('#step-admin .pass-copy a').on('click', function(e) {
            e.preventDefault();
        }).clipboard({
            path: '<?php echo ROOT_URL; ?>/static/jquery/clipboard/jquery.clipboard.swf',
            copy: function() {
                var hint = $('#step-admin .copied');
                hint.fadeIn(100, function(){
                    setTimeout(function(){
                        hint.fadeOut();
                    }, 800);
                });
                return $('#step-admin .auto-password .value').text();
            }
        });
        
    });
    function createAutoPassword(){
        var pass = generatePassword(10);
        var field = $('#step-admin .auto-password .value');
        field.fadeOut(100, function(){
            field.html(pass);
            $('#step-admin #admin_pass').val(pass);
            $('#step-admin #admin_pass2').val(pass);                
            field.fadeIn(100);
        });            
    }
    function generatePassword(length){        
        var chars = 'abcdefghjkmnpqrstuvwxyzABCDEFGHJKLMNPQRSTUVWXYZ23456789<>_^%&$#+@?';
        var count = chars.length-1;
        var result = '';
        var i = 0;
        while (i < length) {
            var index = Math.floor(Math.random() * (count + 1));
            result += chars.substr(index, 1);
            i++;
        }
        return result;
    }     
</script>