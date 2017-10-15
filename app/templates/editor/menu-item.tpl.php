<?php
    use InlineCMS\Core\Lang;
?>
<form action="#" method="post" class="inlinecms">

    <div id="inlinecms-menu-item-pages">
        <div id="inlinecms-menu-item-pages-tree"></div>
    </div>

    <fieldset>

        <?php if ($mode == 'edit') { ?>
            <div class="field field-hidden">
                <input type="hidden" name="menu" value="">
            </div>
            <div class="field field-hidden">
                <input type="hidden" name="id" value="">
            </div>
        <?php } ?>

        <div class="field">
            <label for="title"><?php echo Lang::get('menuItemTitle'); ?>:</label>
            <input type="text" name="title">
        </div>

        <?php if ($mode == 'add') { ?>
            <div class="field field-hidden f-node-id">
                <input type="hidden" name="menu_node_id" value="">
            </div>
            <div class="field f-menu-id">
                <label for="menu"><?php echo Lang::get('menuItemMenu'); ?>:</label>
                <select name="menu">
                    <?php foreach($menus as $nodeId=>$menuId) { ?>
                        <option value="<?php echo $menuId; ?>" data-node-id="<?php echo $nodeId; ?>"><?php echo htmlspecialchars($menuId); ?></option>
                    <?php } ?>
                </select>
            </div>
        <?php } ?>

        <div class="field f-type">
            <label for="type"><?php echo Lang::get('menuItemAction'); ?>:</label>
            <select name="type">
                <option value="page"><?php echo Lang::get('menuItemActionPage'); ?></option>
                <option value="url"><?php echo Lang::get('menuItemActionURL'); ?></option>
            </select>
        </div>

        <div class="field f-page">
            <label for="pages"><?php echo Lang::get('menuItemPage'); ?>:</label>
            <div class="input">
                <i class="fa fa-file-o"></i>
                <a href="#select-page" title="<?php echo Lang::get('select'); ?>"><?php echo Lang::get('homePage'); ?></a>
            </div>
            <input type="hidden" name="page" value="/">
        </div>

        <div class="field f-url" style="display:none">
            <label for="url"><?php echo Lang::get('menuItemURL'); ?>:</label>
            <input type="text" name="url">
        </div>

        <div class="field">
            <label for="target"><?php echo Lang::get('menuItemTarget'); ?>:</label>
            <select name="target">
                <option value="_self"><?php echo Lang::get('menuItemTargetSelf'); ?></option>
                <option value="_blank"><?php echo Lang::get('menuItemTargetBlank'); ?></option>
            </select>
        </div>

    </fieldset>

</form>
