<?php
    use InlineCMS\Core\Lang;
?>
<div id="inlinecms-context-menu" class="inlinecms">
    <ul>
        <li class="i-cancel">
            <i class="fa fa-times"></i> <?php echo Lang::get('cancelSelection'); ?>
        </li>
        <li class="separator"></li>
        <li class="i-add-region">
            <i class="fa fa-pencil-square-o"></i> <?php echo Lang::get('addRegion'); ?>
        </li>
        <li class="i-add-menu">
            <i class="fa fa-bars"></i> <?php echo Lang::get('addMenu'); ?>
        </li>
        <li class="separator"></li>
        <li class="i-style">
            <i class="fa fa-expand"></i> <?php echo Lang::get('styleSelection'); ?>
        </li>
        <li class="i-delete">
            <i class="fa fa-cut"></i> <?php echo Lang::get('deleteSelection'); ?>
        </li>
    </ul>
</div>
