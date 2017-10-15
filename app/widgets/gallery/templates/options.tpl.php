<?php use InlineCMS\Core\Lang; ?>
<form action="" method="post" class="inlinecms">

    <div class="tabs">

        <ul>
            <li><a href="#tab-gallery-images"><?php echo $this->lang('galleryImages'); ?></a></li>
            <li><a href="#tab-gallery-details"><?php echo $this->lang('galleryDetails'); ?></a></li>
        </ul>

        <div id="tab-gallery-images">

            <fieldset class="fs-thumbs">

                <legend><?php echo $this->lang('galleryThumbnails'); ?></legend>

                <div class="field f-thumbs-size">
                    <div class="fields-small">
                        <label><?php echo Lang::get('width'); ?>: <input type="text" name="width" class="t-width" placeholder="<?php echo $this->defaultThumbnailSize; ?>"></label>
                        <label><?php echo Lang::get('height'); ?>: <input type="text" name="height" class="t-height" placeholder="<?php echo $this->defaultThumbnailSize; ?>"></label>
                        <label class="checkbox-label">
                            <input type="checkbox" name="is_square" class="t-square" checked="checked">
                            <?php echo $this->lang('galleryThumbnailsSquare'); ?>
                        </label>
                    </div>
                </div>

            </fieldset>

            <fieldset>

                <legend><?php echo $this->lang('galleryImagesList'); ?></legend>

                <div class="field">
                    <div class="images-list">
                        <div class="item-template">
                            <img src="" data-url="" data-title="">
                            <div class="actions">
                                <a href="#" class="b-rename"><i class="fa fa-pencil"></i></a>
                                <a href="#" class="b-delete"><i class="fa fa-times"></i></a>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="field f-upload">
                    <span class="file-button button">
                        <i class="fa fa-plus"></i> <span data-title="<?php echo $this->lang('galleryUpload'); ?>"><?php echo $this->lang('galleryUpload'); ?></span>
                        <input type="file" id="gallery-file-upload" name="image" multiple>
                    </span>
                </div>

            </fieldset>

        </div>

        <div id="tab-gallery-details">

            <fieldset>

                <div class="field">
                    <label for="open_in"><?php echo $this->lang('galleryImageOpen'); ?>:</label>
                    <select name="open_in">
                        <option value="lightbox"><?php echo $this->lang('galleryImageOpenLightbox'); ?></option>
                        <option value="tab"><?php echo $this->lang('galleryImageOpenTab'); ?></option>
                    </select>
                </div>

                <div class="field">
                    <label for="style"><?php echo $this->lang('galleryStyle'); ?>:</label>
                    <select name="style">
                        <option value=""><?php echo Lang::get('none'); ?></option>
                        <option value="s-rounded"><?php echo $this->lang('galleryStyleRounded'); ?></option>
                        <option value="s-circle"><?php echo $this->lang('galleryStyleCircle'); ?></option>
                        <option value="s-frame"><?php echo $this->lang('galleryStyleFrame'); ?></option>
                        <option value="s-shadow-frame"><?php echo $this->lang('galleryStyleShadowFrame'); ?></option>
                    </select>
                </div>

                <div class="field">
                    <label class="checkbox-label">
                        <input type="checkbox" name="is_titles" checked="checked">
                        <?php echo $this->lang('galleryImageTitles'); ?>
                    </label>
                </div>
                <div class="field">
                    <label class="checkbox-label">
                        <input type="checkbox" name="is_center">
                        <?php echo $this->lang('galleryCenter'); ?>
                    </label>
                </div>

            </fieldset>

        </div>

    </div>

    <style>
        #inlinecms-form-gallery-options .fs-thumbs legend { margin-bottom:0; }
        #inlinecms-form-gallery-options .fields-small label { width:125px; }
        #inlinecms-form-gallery-options .fields-small input { width:45px; }
        #inlinecms-form-gallery-options .fields-small input[type=checkbox] { width:auto; }
        #inlinecms-form-gallery-options .images-list {
            max-height:205px;
            overflow: hidden;
            overflow-y: auto;
        }
        #inlinecms-form-gallery-options .images-list .item {
            overflow:hidden;
            position:relative;
            width:65px;
            height:65px;
            float:left;
            margin:0 5px 5px 0;
            padding:1px;
            border: solid 1px #95a5a6;
        }
        #inlinecms-form-gallery-options .images-list .item img {
            max-width: 65px;
            max-height: 65px;
            display:block;
            cursor: move;
        }
        #inlinecms-form-gallery-options .images-list .item .actions{
            display:none;
            position:absolute;
            width: 100%;
            bottom:0;
            left:-1px;
            height:25px;
            background:rgba(44, 62, 80,.9);
            overflow:hidden;
            text-align: center;
            transition: display 0.2s;
        }
        #inlinecms-form-gallery-options .images-list .item:hover .actions{
            display:block;
        }
        #inlinecms-form-gallery-options .images-list .item .actions a {
            font-size:14px;
            color:#bdc3c7;
            display:inline-block;
            height:25px;
            line-height:25px;
            padding:0 5px;
        }
        #inlinecms-form-gallery-options .images-list .item .actions a:hover { color:#FFF; }
        #inlinecms-form-gallery-options .images-list .item-template { display:none; }
    </style>

</form>
