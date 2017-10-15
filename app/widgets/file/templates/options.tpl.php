<?php use InlineCMS\Core\Lang; ?>
<form action="" method="post" class="inlinecms">

    <fieldset>

        <div class="field field-hidden f-url">
            <input type="hidden" name="url">
        </div>

        <div class="field field-hidden f-name">
            <input type="hidden" name="name">
        </div>

        <div class="field field-hidden f-size">
            <input type="hidden" name="size">
        </div>

        <div class="field field-hidden f-uploaded">
            <label><?php echo $this->lang('fileUploaded'); ?>:</label>
            <div class="filename">
                <i class="fa fa-file-o"></i> <span></span> <a href="#"><?php echo Lang::get('delete'); ?></a>
            </div>
        </div>

        <div class="field f-upload">
            <label for="file"><?php echo $this->lang('fileUpload'); ?>:</label>
            <input type="file" name="file">
        </div>

        <div class="field f-title">
            <label for="title"><?php echo $this->lang('fileTitle'); ?>:</label>
            <input type="text" name="title">
        </div>

        <div class="field">
            <label class="checkbox-label">
                <input type="checkbox" name="is_size" value="1"> <?php echo $this->lang('fileShowSize'); ?>
            </label>
        </div>

    </fieldset>

    <style>
        #inlinecms-form-file-options .f-uploaded .filename {
            height:28px;
            line-height:28px;
        }
        #inlinecms-form-file-options .f-uploaded .filename span {
            color:#474e4f; padding-right:10px;
        }
        #inlinecms-form-file-options .f-uploaded .filename a {
            color:#2980b9;
            text-decoration: none;
            border-bottom:dashed 1px #2980b9;
        }
        #inlinecms-form-file-options .f-uploaded .filename a:hover {
            color: #4aa3df;
            border-bottom:dashed 1px #4aa3df;
        }
    </style>

</form>