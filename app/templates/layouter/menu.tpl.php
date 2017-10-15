<?php
    use InlineCMS\Core\Lang;
?>
<form action="#" method="post" class="inlinecms" id="inlinecms-form-menu">

    <fieldset>

        <div class="field f-menu-id">
            <label for="id"><?php echo Lang::get('menuId'); ?>:</label>
            <div class="input">
                <input type="text" name="id">
                <select name="exists_id">
                    <?php foreach($menus as $menuId) { ?>
                        <option value="<?php echo $menuId; ?>"><?php echo $menuId; ?></option>
                    <?php } ?>
                </select>
                <button class="button"><i class="fa fa-navicon"></i></button>
            </div>
        </div>

        <div class="field f-path">
            <label for="path"><?php echo Lang::get('regionPath'); ?>:</label>
            <input type="text" name="path" disabled="disabled">
        </div>

        <div class="field f-items">
            <label for="active_item_index"><?php echo Lang::get('menuActiveItem'); ?>:</label>
            <select name="active_item_index"></select>
            <div class="hint"><?php echo Lang::get('menuActiveItemHint'); ?></div>
        </div>

    </fieldset>

    <style>
        #inlinecms-form-menu .input { overflow: hidden; }
        #inlinecms-form-menu .input input,
        #inlinecms-form-menu .input select { float:left; width:375px; }
        #inlinecms-form-menu .input select { display: none; }
        #inlinecms-form-menu .input button { height:28px; float:right; margin:0; }
    </style>

</form>
