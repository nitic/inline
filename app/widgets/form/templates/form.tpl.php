<form action="" method="post" class="<?php echo $options['style']; ?>">

    <input type="hidden" name="form_id" value="<?php echo $form_id; ?>">

    <?php foreach($options['fields'] as $index => $field){ ?>

        <?php $value = empty($values[$index]) ? '' : $values[$index]; ?>

        <div class="field<?php if(!empty($errors[$index])) { ?> error<?php } ?>">

            <?php if ($field['type'] == 'checkbox') { ?>
                <label>
                    <input type="checkbox" name="fields[<?php echo $index; ?>]" value="1" <?php if ($value) { ?>checked="checked"<?php } ?>>
                    <?php echo $field['title']; ?>
                </label>
            <?php } ?>

            <?php if (in_array($field['type'], array('text', 'textarea', 'email'))) { ?>

                <label>
                    <?php echo $field['title']; ?>
                    <?php if ($field['isMandatory']) { ?>
                        <span>*</span>
                    <?php } ?>
                </label>

                <?php if (in_array($field['type'], array('text', 'email'))){ ?>
                    <input type="text" name="fields[<?php echo $index; ?>]" value="<?php echo htmlspecialchars($value); ?>">
                <?php } ?>

                <?php if ($field['type'] == 'textarea'){ ?>
                    <textarea name="fields[<?php echo $index; ?>]"><?php echo htmlspecialchars($value); ?></textarea>
                <?php } ?>

            <?php } ?>

        </div>
    <?php } ?>

    <div class="buttons">
        <input type="submit" name="submit"<?php if ($options['submit']) { ?> value="<?php echo htmlspecialchars($options['submit']); ?>"<?php } ?>>
    </div>

</form>
