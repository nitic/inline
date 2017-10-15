<?php 
    use InlineCMS\Core\Lang; 
    use InlineCMS\Core\Config; 
    $t = Config::get('mail_transport', 'mail');
    $e = Config::get('mail_smtp_enc');
?>
<form action="#" method="post" class="inlinecms">
	
    <fieldset>

        <div style="display: none">
            <?php /* fake inputs for Chrome to autofill them instead of SMTP credentials */ ?>
            <input type="text"><input type="password">
        </div>
        
        <div class="field">
            <label for="mail_from"><?php echo Lang::get('mailFrom'); ?>:</label>
            <input type="text" name="mail_from" placeholder="<?php echo Config::get('email'); ?>" value="<?php echo Config::get('mail_from'); ?>">
            <div class="hint"><?php echo Lang::get('mailFromHint'); ?></div>
        </div>
        
        <div class="field">
            <label for="mail_transport"><?php echo Lang::get('mailTransport'); ?>:</label>
            <select name="mail_transport">
                <option value="mail"<?php if ($t=='mail'){ ?> selected="selected"<?php } ?>><?php echo Lang::get('mailTransportMail'); ?></option>
                <option value="smtp"<?php if ($t=='smtp'){ ?> selected="selected"<?php } ?>><?php echo Lang::get('mailTransportSmtp'); ?></option>                    
            </select>
        </div>
        
        <div class="field f-smtp">
            <label for="mail_smtp_host"><?php echo Lang::get('mailSmtpHost'); ?>:</label>
            <input type="text" name="mail_smtp_host" value="<?php echo Config::get('mail_smtp_host'); ?>">
        </div>
        
        <div class="field f-smtp">
            <label for="mail_smtp_port"><?php echo Lang::get('mailSmtpPort'); ?>:</label>
            <input type="text" name="mail_smtp_port" style="width:60px" value="<?php echo Config::get('mail_smtp_port', 25); ?>">
        </div>
        
        <div class="field f-smtp">
            <label for="mail_smtp_user"><?php echo Lang::get('mailSmtpUser'); ?>:</label>
            <input type="text" name="mail_smtp_user" value="<?php echo Config::get('mail_smtp_user'); ?>" autocomplete="off">
        </div>
        
        <div class="field f-smtp">
            <label for="mail_smtp_pass"><?php echo Lang::get('mailSmtpPass'); ?>:</label>
            <input type="password" name="mail_smtp_pass" value="<?php echo Config::get('mail_smtp_pass'); ?>" autocomplete="off">
        </div>
        
        <div class="field f-smtp">
            <label for="mail_smtp_enc"><?php echo Lang::get('mailSmtpEncryption'); ?>:</label>
            <select name="mail_smtp_enc">
                <option value=""><?php echo Lang::get('mailSmtpEncryptionNo'); ?></option>
                <option value="ssl"<?php if ($e=='ssl'){ ?> selected="selected"<?php } ?>><?php echo Lang::get('mailSmtpEncryptionSsl'); ?></option>                    
                <option value="tls"<?php if ($t=='tls'){ ?> selected="selected"<?php } ?>><?php echo Lang::get('mailSmtpEncryptionTls'); ?></option>                    
            </select>
        </div>        
        
    </fieldset>
    
</form>
