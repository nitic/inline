<?php
    use InlineCMS\Core\Core;
    use InlineCMS\Core\Lang;
?>
<div id="inlinecms-panel" class="inlinecms">
    <div class="title">
        <h3><?php echo PRODUCT_ATTRIBUTION ? PRODUCT_TITLE : Lang::get('pageEditor'); ?></h3>
		<div class="toolbuttons">
			<a class="tb-collapse" href="#"><i class="fa fa-caret-up"></i></a>
			<a class="tb-logout" href="?exit"><i class="fa fa-times"></i></a>
		</div>
        <div class="lang">
            <select<?php if ($isHaveAllLangs){ ?> class="all-langs"<?php } ?>>
                <?php foreach ($pageLangs as $lang => $langData) { ?>
                    <option value="<?php echo $lang; ?>"<?php if ($lang==Core::getCurrentLang()){ ?> selected="selected"<?php } ?> <?php if ($langData['isNew']) { ?>data-new="yes"<?php } ?>><?php echo $langData['title']; ?></option>
                <?php } ?>
            </select>
        </div>
    </div>
	<div class="body">
		<div id="tabs">
			<ul>
				<li class="active"><a href="#tab-elements"><?php echo Lang::get('tabElements'); ?></a></li>
				<li><a href="#tab-pages"><?php echo Lang::get('tabPages'); ?></a></li>
				<li><a href="#tab-menus"><?php echo Lang::get('tabMenus'); ?></a></li>
				<li><a href="#tab-settings" class="small"><i class="fa fa-gear"></i></a></li>
			</ul>
			<div id="tab-elements" class="tab">
				<div class="list">
					<ul></ul>
				</div>
			</div>
			<div id="tab-pages" class="tab">
				<div class="buttons">
					<button class="btn-create" title="<?php echo Lang::get('create'); ?>"><i class="fa fa-plus"></i></button>
					<button class="btn-open page-only" title="<?php echo Lang::get('open'); ?>"><i class="fa fa-external-link"></i></button>
					<button class="btn-settings page-only" title="<?php echo Lang::get('settings'); ?>"><i class="fa fa-gear"></i></button>
					<button class="btn-delete" title="<?php echo Lang::get('delete'); ?>"><i class="fa fa-times"></i></button>
				</div>
				<div id="inlinecms-pages-tree" class="pane"></div>
			</div>
			<div id="tab-menus" class="tab">
				<div class="buttons">
					<button class="btn-create" title="<?php echo Lang::get('create'); ?>"><i class="fa fa-plus"></i></button>
					<button class="btn-settings item-only" title="<?php echo Lang::get('settings'); ?>"><i class="fa fa-gear"></i></button>
					<button class="btn-delete item-only" title="<?php echo Lang::get('delete'); ?>"><i class="fa fa-times"></i></button>
					<span class="delimiter"></span>
					<button class="btn-move-up item-only" title="<?php echo Lang::get('moveUp'); ?>"><i class="fa fa-arrow-up"></i></button>
					<button class="btn-move-down item-only" title="<?php echo Lang::get('moveDown'); ?>"><i class="fa fa-arrow-down"></i></button>
				</div>
                <div id="inlinecms-menus-tree" class="pane"></div>
			</div>
            <div id="tab-settings" class="tab">
                <ul class="links">
                    <li>
                        <i class="fa fa-fw fa-edit"></i>
                        <a class="s-layouts" href="#"><?php echo Lang::get('settingsLayouts'); ?></a>
                    </li>
                    <li>
                        <i class="fa fa-fw fa-code"></i>
                        <a class="s-code" href="#"><?php echo Lang::get('settingsGlobalCode'); ?></a>
                    </li>
                    <li>
                        <i class="fa fa-fw fa-user"></i>
                        <a class="s-user" href="#"><?php echo Lang::get('settingsUser'); ?></a>
                    </li>
                    <li>
                        <i class="fa fa-fw fa-envelope-o"></i>
                        <a class="s-mail" href="#"><?php echo Lang::get('settingsMail'); ?></a>
                    </li>
                    <?php if (PRODUCT_ATTRIBUTION) { ?>
                        <li>
                            <i class="fa fa-fw fa-cloud-download"></i>
                            <a class="s-updates" href="http://inlinecms.com/check-updates?v=<?php echo Core::getVersion(); ?>" target="_blank"><?php echo Lang::get('settingsUpdates'); ?></a>
                        </li>
                    <?php } ?>
                </ul>
                <div id="core-version"><span>v<?php echo Core::getVersion(); ?></span></div>
            </div>
		</div>
		<div id="save-buttons">
			<button class="btn-save">
				<i class="fa fa-fw fa-check"></i><?php echo Lang::get('save'); ?>
			</button>
			<button class="btn-save-and-exit">
				<i class="fa fa-fw fa-sign-out"></i><?php echo Lang::get('saveAndExit'); ?>
			</button>
		</div>
	</div>
</div>
