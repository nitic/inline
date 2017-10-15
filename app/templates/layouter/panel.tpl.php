<?php
    use InlineCMS\Core\Lang;
?>
<div id="inlinecms-panel" class="inlinecms">
    <div class="title">
        <h3><?php echo PRODUCT_ATTRIBUTION ? PRODUCT_TITLE.' - ' : '',  Lang::get('layoutEditor'); ?></h3>
        <div class="toolbuttons">
			<a class="tb-collapse" href="#"><i class="fa fa-caret-up"></i></a>
		</div>
    </div>
	<div class="body">
            <div class="pane">
                <select name="layout" id="layout-selector">
                    <?php foreach($layouts as $layoutFile=>$layoutTitle) { ?>
                        <option value="<?php echo $layoutFile; ?>"<?php if ($currentLayout == $layoutFile) { ?>selected="selected"<?php } ?>>
                            <?php echo $layoutTitle; ?>
                        </option>
                    <?php } ?>
                </select>
                <a href="#" id="update-layout" title="<?php echo Lang::get('layoutUpdate'); ?>"><i class="fa fa-refresh"></i></a>
            </div>
            <div class="pane picker-hint">
                <ul>
                    <li><?php echo Lang::get('layoutPickerHint1'); ?></li>
                    <li><?php echo Lang::get('layoutPickerHint2'); ?></li>
                    <li><?php echo Lang::get('layoutPickerHint3'); ?></li>
                </ul>
            </div>
            <div id="save-buttons">
                <button class="btn-save green" id="b-save-layout">
                    <i class="fa fa-check"></i> <?php echo Lang::get('saveLayout'); ?>
                </button>
                <button class="btn-done" id="b-done">
                    <i class="fa fa-sign-out"></i> <?php echo Lang::get('done'); ?>
                </button>
            </div>
        </div>
	</div>
</div>
