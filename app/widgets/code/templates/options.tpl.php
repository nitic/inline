<?php use InlineCMS\Core\Lang; ?>
<form action="" method="post" class="inlinecms">

    <div class="tabs">

        <ul>
            <li><a href="#tab-code-html">HTML</a></li>
            <li><a href="#tab-code-js">JavaScript</a></li>
            <li><a href="#tab-code-css">CSS</a></li>
            <li><a href="#tab-code-php">PHP</a></li>
        </ul>

        <div id="tab-code-html">
            <fieldset>
                <div class="field f-html">
                    <textarea name="html"></textarea>
                </div>
            </fieldset>
        </div>

        <div id="tab-code-js">
            <fieldset>
                <div class="field f-js">
                    <textarea name="js"></textarea>
                </div>
            </fieldset>
        </div>

        <div id="tab-code-css">
            <fieldset>
                <div class="field f-css">
                    <textarea name="css"></textarea>
                </div>
            </fieldset>
        </div>

        <div id="tab-code-php">
            <fieldset>
                <div class="field f-php">
                    <label for="php"><?php echo $this->lang('codePhpFile'); ?>:</label>
                    <input type="text" name="php" placeholder="myscript.php">
                    <div class="hint"><?php echo $this->lang('codePhpFileHint'); ?></div>
                    <div class="hint"><?php echo $this->lang('codePhpFileSample'); ?></div>
                </div>
            </fieldset>
        </div>

    </div>

    <style>
        #inlinecms-form-code-options fieldset { margin:0; }
        #inlinecms-form-code-options textarea { min-height:350px; }
        #inlinecms-form-code-options .CodeMirror{ border: solid 1px #bdc3c7; min-height:350px; max-width: 610px; padding:5px; }
    </style>

</form>
