<?php 
    use InlineCMS\Core\Lang; 
?>
<form action="#" method="post" class="inlinecms">
	
    <fieldset>
        
        <legend><?php echo Lang::get('profileEmail'); ?></legend>
        
        <div class="field">
            <input type="text" name="email" value="<?php echo $email; ?>">
        </div>
        
    </fieldset>
    
    <fieldset>
        
        <legend><?php echo Lang::get('profileChangePassword'); ?></legend>
        
        <div class="field">
            <label for="title"><?php echo Lang::get('profileNewPassword'); ?>:</label>
            <input type="password" name="new_pass" autocomplete="off">
        </div>
        <div class="field">
            <label for="title"><?php echo Lang::get('profileNewPasswordRepeat'); ?>:</label>
            <input type="password" name="new_pass2" autocomplete="off">
        </div>
        
    </fieldset>
    
    <fieldset>
        
        <legend><?php echo Lang::get('profileOldPassword'); ?></legend>
        
        <div class="field">
            <input type="password" name="password" autocomplete="off">
        </div>
        
    </fieldset>
    
</form>
