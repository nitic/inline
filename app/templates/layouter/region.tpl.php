<?php
    use InlineCMS\Core\Lang;
?>
<form action="#" method="post" class="inlinecms">

    <fieldset>

        <div class="field">
            <label for="id"><?php echo Lang::get('regionId'); ?>:</label>
            <input type="text" name="id">
        </div>

        <div class="field f-path">
            <label for="path"><?php echo Lang::get('regionPath'); ?>:</label>
            <input type="text" name="path" disabled="disabled">
        </div>

        <div class="field f-type">
            <label for="type"><?php echo Lang::get('regionType'); ?>:</label>
            <select name="type">
                <option value="fixed"><?php echo Lang::get('regionTypeFixed'); ?></option>
                <option value="content"><?php echo Lang::get('regionTypeContent'); ?></option>
                <option value="collection"><?php echo Lang::get('regionTypeCollection'); ?></option>
            </select>
        </div>

        <div class="field f-type">
            <label for="global"><?php echo Lang::get('regionContent'); ?>:</label>
            <select name="global">
                <option value="no"><?php echo Lang::get('regionContentUnique'); ?></option>
                <option value="yes"><?php echo Lang::get('regionContentGlobal'); ?></option>
            </select>
        </div>

        <div class="field">
            <label class="checkbox-label">
                <input type="checkbox" name="is_scan" value="1"> <?php echo Lang::get('regionScanOtherLayouts'); ?>
            </label>
        </div>

    </fieldset>

</form>
