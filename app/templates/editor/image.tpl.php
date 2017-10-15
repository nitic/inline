<?php use InlineCMS\Core\Lang; ?>
<form action="" method="post" class="inlinecms">

    <div class="tabs">

        <ul>
            <li><a href="#tab-image-file"><?php echo Lang::get('imageFile'); ?></a></li>
            <li><a href="#tab-image-details"><?php echo Lang::get('imageDetails'); ?></a></li>
        </ul>

        <div id="tab-image-file">

            <fieldset>

                <div class="field f-url">
                    <label for="url"><?php echo Lang::get('imageUrl'); ?>:</label>
                    <input type="text" name="url">
                </div>

            </fieldset>

            <fieldset>

                <legend><?php echo Lang::get('imageUpload'); ?></legend>

                <div class="field">
                    <label class="checkbox-label">
                        <input type="checkbox" name="resize" class="t-resize" value="1"> <?php echo Lang::get('imageResizeMatch'); ?>:
                    </label>
                    <div class="fields-small">
                        <label><?php echo Lang::get('width'); ?>: <input type="text" name="width" class="t-width"></label>
                        <label><?php echo Lang::get('height'); ?>: <input type="text" name="height" class="t-height"></label>
                    </div>
                </div>

                <div class="field f-image">
                    <input type="file" name="image">
                </div>

            </fieldset>

        </div>

        <div id="tab-image-details">

            <fieldset>

                <div class="field">
                    <label for="title"><?php echo Lang::get('imageTitle'); ?>:</label>
                    <input type="text" name="title">
                </div>

                <div class="field">
                    <label for="link_url"><?php echo Lang::get('imageLinkURL'); ?>:</label>
                    <input type="text" name="link_url">
                </div>

                <div class="field">
                    <label for="align"><?php echo Lang::get('imageAlign'); ?>:</label>
                    <select name="align">
                        <option value=""><?php echo Lang::get('none'); ?></option>
                        <option value="left"><?php echo Lang::get('imageAlignLeft'); ?></option>
                        <option value="center"><?php echo Lang::get('imageAlignCenter'); ?></option>
                        <option value="right"><?php echo Lang::get('imageAlignRight'); ?></option>
                    </select>
                </div>

                <div class="field">
                    <label for="style"><?php echo Lang::get('imageStyle'); ?>:</label>
                    <select name="style">
                        <option value=""><?php echo Lang::get('none'); ?></option>
                        <option value="s-rounded"><?php echo Lang::get('imageStyleRounded'); ?></option>
                        <option value="s-circle"><?php echo Lang::get('imageStyleCircle'); ?></option>
                        <option value="s-frame"><?php echo Lang::get('imageStyleFrame'); ?></option>
                        <option value="s-shadow-frame"><?php echo Lang::get('imageStyleShadowFrame'); ?></option>
                    </select>
                </div>

            </fieldset>

        </div>

    </div>

    <style>
        #inlinecms-form-image .fields-small label {
            text-align: left;
            width:160px;
        }
    </style>

</form>
