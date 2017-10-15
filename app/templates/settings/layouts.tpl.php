<?php 
    use InlineCMS\Core\Core; 
    use InlineCMS\Core\Lang; 
?>
<form action="#" method="post" class="inlinecms">
	
    <div class="tabs">
    
        <ul>
            <li><a href="#tab-ml-list"><?php echo Lang::get('layoutsList'); ?></a></li>
            <li><a href="#tab-ml-add"><?php echo Lang::get('layoutsAdd'); ?></a></li>
        </ul>        
        
        <div id="tab-ml-list">
    
            <fieldset>

                <table class="layouts-list field">
                    <tbody>
                        <?php foreach ($layouts as $file=>$name) { ?>
                            <tr>
                                <td>
                                    <label><?php echo $file; ?></label>
                                </td>
                                <td>
                                    <input name="layout:<?php echo str_replace('.', '_', $file); ?>" type="text" value="<?php echo htmlspecialchars($name); ?>" placeholder="<?php echo $file; ?>">
                                </td>
                                <td>
                                    <a href="<?php echo Core::getLayouterUrl($file); ?>" title="<?php echo Lang::get('openInLayoutEditor'); ?>"><i class="fa fa-edit"></i></a>
                                </td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>

            </fieldset>
            
        </div>
        
        <div id="tab-ml-add">
            
            <?php if (!$newLayouts) { ?>
                <p><?php echo Lang::get('layoutsAddNone'); ?></p>
            <?php } ?>
                
            <?php if ($newLayouts) { ?>
                <fieldset>

                    <div class="field f-layout-file">
                        <label for="layout_file"><?php echo Lang::get('layoutFileAddSelect'); ?>:</label>
                        <select name="layout_file">
                            <option value=""></option>
                            <?php foreach ($newLayouts as $file) { ?>
                                <option value="<?php echo $file; ?>"><?php echo $file; ?></option>
                            <?php } ?>
                        </select>
                    </div>

                    <div class="field f-layout-name">
                        <label for="layout_name"><?php echo Lang::get('layoutName'); ?>:</label>
                        <input type="text" name="layout_name" value="">
                    </div>
                    
                    <div class="field f-layout-open">
                        <label class="checkbox-label">
                            <input type="checkbox" name="is_open" value="1" checked="checked"> <?php echo Lang::get('layoutOpen'); ?>
                        </label>
                    </div>
                    
                </fieldset>
            <?php } ?>
                
        </div>
    
    </div>
    
</form>
