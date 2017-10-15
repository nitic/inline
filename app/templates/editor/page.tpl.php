<?php 
    use InlineCMS\Core\Config; 
    use InlineCMS\Core\Core; 
    use InlineCMS\Core\Lang; 
    use InlineCMS\Core\Request; 
?>
<form action="#" method="post" class="inlinecms">
	
    <div class="tabs">
    
        <ul>
            <li><a href="#tab-page-basic"><?php echo Lang::get('pageOptionsTabBasic'); ?></a></li>
            <li><a href="#tab-page-seo"><?php echo Lang::get('pageOptionsTabDetails'); ?></a></li>
        </ul>        
        
        <div id="tab-page-basic">
            <fieldset>

                <div class="field">
                    <label for="title"><?php echo Lang::get('pageTitle'); ?>:</label>
                    <input type="text" name="title">
                </div>

                <div class="field f-uri">
                    <label for="uri"><?php echo Lang::get('pageUrl'); ?>:</label>
                    <input type="text" name="uri">
                    <div class="hint">
                        <?php echo Request::getHostUrl(); ?><span class="lang"></span><span class="uri"></span>
                    </div>
                </div>

                <?php if ($mode == 'add'){ ?>
                
                    <div class="field f-lang">
                        <label for="lang"><?php echo Lang::get('pageLang'); ?>:</label>
                        <select name="lang">
                            <?php foreach(Config::get('langs') as $lang) { ?>
                                <option value="<?php echo $lang; ?>"><?php echo mb_strtoupper($lang); ?></option>
                            <?php } ?>
                        </select>
                    </div>

                    <div class="field">
                        <label for="layout"><?php echo Lang::get('pageLayout'); ?>:</label>
                        <select name="layout">
                            <?php $layoutsList = Core::getLayoutsList(); ?>
                            <?php foreach($layoutsList as $file => $title) { ?>
                                <option value="<?php echo $file; ?>"><?php echo htmlspecialchars($title); ?></option>
                            <?php } ?>
                        </select>
                    </div>

                    <div class="field">
                        <label for="mode"><?php echo Lang::get('pageInitialContent'); ?>:</label>
                        <select name="mode">
                            <option value="default"><?php echo Lang::get('pageInitialContentTemplate'); ?></option>
                            <option value="clear"><?php echo Lang::get('pageInitialContentClear'); ?></option>
                            <option value="copy"><?php echo Lang::get('pageInitialContentCopy'); ?></option>
                        </select>
                    </div>
                
                <?php } ?>
                
                <?php if ($mode == 'edit'){ ?>
                    
                    <div class="field field-hidden f-lang">
                        <input type="hidden" name="lang" value="">
                    </div>
                
                <?php } ?>

            </fieldset>
        </div>
        
        <div id="tab-page-seo">
            <fieldset>
                
                <div class="field">
                    <label for="description"><?php echo Lang::get('pageDesc'); ?>:</label>
                    <textarea name="description"></textarea>
                </div>

                <div class="field">
                    <label for="keywords"><?php echo Lang::get('pageKeys'); ?>:</label>
                    <input type="text" name="keywords">
                </div>
                
            </fieldset>
        </div>
        
    </div>
    
</form>
