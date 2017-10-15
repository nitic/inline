<?php use InlineCMS\Core\Config; ?>
<?php use InlineCMS\Core\Lang; ?>
<form action="" method="post" class="inlinecms">

    <div class="tabs">

        <ul>
            <li><a href="#tab-form-fields"><?php echo $this->lang('formFields'); ?></a></li>
            <li><a href="#tab-form-settings"><?php echo $this->lang('formSettings'); ?></a></li>
        </ul>

        <div id="tab-form-fields">

            <fieldset>

                <div class="field">
                    <div class="fields-list">
                        <div class="field-template">
                            <span class="drag-handle"><i class="fa fa-arrows"></i></span>
                            <input type="text" class="field-title" placeholder="<?php echo $this->lang('formFieldTitle'); ?>">
                            <select class="field-type">
                                <option value="text"><?php echo $this->lang('formFieldTypeText'); ?></option>
                                <option value="textarea"><?php echo $this->lang('formFieldTypeTextarea'); ?></option>
                                <option value="email"><?php echo $this->lang('formFieldTypeEmail'); ?></option>
                                <option value="checkbox"><?php echo $this->lang('formFieldTypeCheckbox'); ?></option>
                            </select>
                            <span class="actions">
                                <a href="#" class="b-mandatory" title="<?php echo $this->lang('formFieldMandatorySet'); ?>">
                                    <i class="fa fa-asterisk"></i>
                                </a>
                                <a href="#" class="b-delete"><i class="fa fa-times"></i></a>
                            </span>
                        </div>
                    </div>
                </div>

                <div class="field f-add">
                    <button class="button">
                        <i class="fa fa-plus"></i> <?php echo $this->lang('formFieldAdd'); ?>
                    </button>
                </div>

            </fieldset>

        </div>

        <div id="tab-form-settings">

            <fieldset>

                <div class="field f-email-type">
                    <label for="email_type"><?php echo $this->lang('formEmail'); ?>:</label>
                    <select name="email_type">
                        <option value="default"><?php echo $this->lang('formEmailDefault', Config::get('email')); ?></option>
                        <option value="custom"><?php echo $this->lang('formEmailCustom'); ?></option>
                    </select>
                </div>

                <div class="field f-email">
                    <label for="email"><?php echo $this->lang('formEmailCustomEnter'); ?>:</label>
                    <input type="text" name="email">
                </div>

                <div class="field">
                    <label for="subject"><?php echo $this->lang('formSubject'); ?>:</label>
                    <input type="text" name="subject" placeholder="<?php echo Lang::get('formMessageSubject'); ?>">
                </div>

                <div class="field f-thanks-msg">
                    <label for="thanks_msg"><?php echo $this->lang('formThanksMessage'); ?>:</label>
                    <textarea name="thanks_msg"></textarea>
                </div>

                <div class="field">
                    <label for="submit"><?php echo $this->lang('formSubmit'); ?>:</label>
                    <input type="text" name="submit">
                </div>

                <div class="field">
                    <label for="style"><?php echo $this->lang('formLabelsAlign'); ?>:</label>
                    <select name="style">
                        <option value="s-vertical"><?php echo $this->lang('formLabelsAlignVert'); ?></option>
                        <option value="s-horizontal"><?php echo $this->lang('formLabelsAlignHor'); ?></option>
                    </select>
                </div>

            </fieldset>

        </div>

    </div>

    <style>
        #inlinecms-form-form-options .fields-list .field-template { display:none; }
        #inlinecms-form-form-options .fields-list .form-field { height:35px; line-height: 35px; }
        #inlinecms-form-form-options .fields-list .form-field input { width:230px; }
        #inlinecms-form-form-options .fields-list .form-field select { width:100px; margin-right: 10px; }
        #inlinecms-form-form-options .fields-list .form-field i { font-size:15px; }
        #inlinecms-form-form-options .fields-list .form-field .actions a { color:#bdc3c7; display:inline-block; width:16px; }
        #inlinecms-form-form-options .fields-list .form-field .actions a:hover { color:#7f8c8d; }
        #inlinecms-form-form-options .fields-list .form-field .actions .b-mandatory.active { color:#e74c3c; }
    </style>

</form>
