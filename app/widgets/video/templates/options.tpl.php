<?php use InlineCMS\Core\Lang; ?>
<form action="" method="post" class="inlinecms">

    <fieldset>

        <div class="field">
            <label for="url"><?php echo $this->lang('videoUrl'); ?>:</label>
            <input type="text" name="url">
        </div>

        <div class="field">
            <label for="size"><?php echo $this->lang('videoSize'); ?>:</label>
            <select name="size">
                <optgroup label="<?php echo $this->lang('videoRatio4'); ?>">
                    <option value="420x315">420 &times; 315</option>
                    <option value="480x360">480 &times; 360</option>
                    <option value="640x480">640 &times; 480</option>
                    <option value="800x600">800 &times; 600</option>
                    <option value="960x720">960 &times; 720</option>
                </optgroup>
                <optgroup label="<?php echo $this->lang('videoRatio16'); ?>">
                    <option value="560x315">560 &times; 315</option>
                    <option value="640x360">640 &times; 360</option>
                    <option value="853x315">853 &times; 480</option>
                    <option value="1280x315">1280 &times; 720</option>
                </optgroup>
            </select>
        </div>

    </fieldset>

</form>
