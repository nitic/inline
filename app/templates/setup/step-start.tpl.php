<form id="step-start">
    <h2><?php echo $lang['welcome']; ?></h2>

    <p><?php echo $lang['welcomeText']; ?></p>

    <p><?php echo $lang['selectLang']; ?>:</p>

    <ul id="languages">
        <?php foreach ($langs as $langId=>$details) { ?>
            <?php $isCurrent = $currentLang == $langId; ?>
            <li<?php if ($isCurrent) { ?> class="active"<?php } ?>>
                <a href="?lang=<?php echo $langId; ?>">
                    <?php if ($isCurrent) { ?>
                        <i class="fa fa-check"></i> 
                    <?php } ?>
                    <?php echo $details['language']; ?>
                </a>
            </li>
        <?php } ?>
    </ul>
</form>
