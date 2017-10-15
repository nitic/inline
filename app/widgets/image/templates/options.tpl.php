<?php use InlineCMS\Core\Lang; ?>
<form action="" method="post" class="inlinecms">
	
    <div class="tabs">
    
        <ul>
            <li><a href="#tab-image-file"><?php echo $this->lang('imageFile'); ?></a></li>
            <li><a href="#tab-image-details"><?php echo $this->lang('imageDetails'); ?></a></li>
        </ul>        
        
        <div id="tab-image-file">
    
            <fieldset>

                <div class="field f-image">
                    <label for="image"><?php echo $this->lang('imageUpload'); ?>:</label>
                    <input type="file" name="image">
                </div>                
                
                <div class="field f-url">
                    <label for="url"><?php echo $this->lang('imageUrl'); ?>:</label>
                    <input type="text" name="url">
                </div>

                <div class="field">
                    <label for="style"><?php echo $this->lang('imageStyle'); ?>:</label>
                    <select name="style">
                        <option value=""><?php echo Lang::get('none'); ?></option>
                        <option value="s-rounded"><?php echo $this->lang('imageStyleRounded'); ?></option>                        
                        <option value="s-circle"><?php echo $this->lang('imageStyleCircle'); ?></option>                        
                        <option value="s-frame"><?php echo $this->lang('imageStyleFrame'); ?></option>                        
                        <option value="s-shadow-frame"><?php echo $this->lang('imageStyleShadowFrame'); ?></option>                        
                    </select>
                </div>                 

            </fieldset>
            
        </div>
        
        <div id="tab-image-details">
    
            <fieldset>

                <div class="field">
                    <label for="title"><?php echo $this->lang('imageTitle'); ?>:</label>
                    <input type="text" name="title">
                </div>        

                <div class="field">
                    <label for="link_url"><?php echo $this->lang('imageLinkURL'); ?>:</label>
                    <input type="text" name="link_url">
                </div>        

                <div class="field">
                    <label for="link_url"><?php echo $this->lang('imageAlign'); ?>:</label>
                    <select name="align">
                        <option value="left"><?php echo $this->lang('imageAlignLeft'); ?></option>
                        <option value="center"><?php echo $this->lang('imageAlignCenter'); ?></option>                    
                        <option value="right"><?php echo $this->lang('imageAlignRight'); ?></option>                    
                    </select>
                </div>        

            </fieldset>
            
        </div>
    
    </div>

</form>

