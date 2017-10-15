<?php use InlineCMS\Core\Lang; ?>
<form action="" method="post" class="inlinecms">

	<fieldset>

        <div class="field">
            <label><?php echo $this->lang('mapSize'); ?></label>
            <div class="fields-small">
                <label><?php echo Lang::get('width'); ?>: <input type="text" name="width" placeholder="100%"></label>
                <label><?php echo Lang::get('height'); ?>: <input type="text" name="height" placeholder="200"></label>
            </div>
        </div>

		<div class="field field-small">
			<label for="lat"><?php echo $this->lang('mapZoom'); ?>:</label>
			<div class="fields-small">
                <label><?php echo $this->lang('mapZoomLevel'); ?>: <input type="text" name="zoom"></label>
            </div>
		</div>

		<div class="field field-small field-h">
            <label><?php echo $this->lang('mapMarkerPos'); ?> (<a href="#" class="find-coords"><?php echo $this->lang('mapFindPosByAddress'); ?></a>)</label>
            <div class="fields-small">
                <label><?php echo $this->lang('mapLat'); ?>: <input type="text" name="lat" class="m-lat"></label>
                <label><?php echo $this->lang('mapLng'); ?>: <input type="text" name="lng" class="m-lng"></label>
            </div>
        </div>

		<div class="field">
			<label for="title"><?php echo $this->lang('mapMarkerTitle'); ?>:</label>
            <input type="text" name="title">
		</div>

	</fieldset>

</form>

