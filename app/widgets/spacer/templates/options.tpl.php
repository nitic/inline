<?php use InlineCMS\Core\Lang; ?>
<form action="" method="post" class="inlinecms">

    <fieldset>

        <div class="field f-size">
            <label for="title"><?php echo $this->lang('spacerSize'); ?>:</label>
            <input type="text" name="size" placeholder="20">
        </div>

    </fieldset>

    <style>
        #inlinecms-form-spacer-options .f-size input {
            width: 80px;
        }
    </style>

</form>
