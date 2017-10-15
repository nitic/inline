<?php
    use InlineCMS\Core\Config;
    use InlineCMS\Core\Lang;
?>
<!doctype html>
<html lang="<?php echo Config::get('ui_lang'); ?>">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="x-ua-compatible" content="ie=edge">
        <title>InlineCMS - <?php echo Lang::get('layoutEditor'); ?></title>
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel=stylesheet href="<?php echo ROOT_URL; ?>/static/font-awesome/css/font-awesome.css">
        <link rel=stylesheet href="<?php echo ROOT_URL; ?>/static/reset.css">
        <link rel=stylesheet href="<?php echo ROOT_URL; ?>/static/jquery/jquery-ui.css">
        <link rel=stylesheet href="<?php echo ROOT_URL; ?>/static/cms/css/editor.css">
        <link rel=stylesheet href="<?php echo ROOT_URL; ?>/static/cms/css/layouter.css">
        <?php foreach(self::$insertions['head'] as $tag) {?>
            <?php echo $tag; ?>
        <?php } ?>
    </head>
    <body>

        <iframe id="page-frame" frameborder="0"></iframe>

        <?php echo $panel; ?>

        <script src="<?php echo ROOT_URL; ?>/static/jquery/jquery.js"></script>
        <script src="<?php echo ROOT_URL; ?>/static/jquery/jquery-ui.js"></script>
        <script src="<?php echo ROOT_URL; ?>/static/cms/core.js"></script>
        <script src="<?php echo ROOT_URL; ?>/static/cms/layouter.js"></script>

        <script>
            var cms = new InlineCMSLayoutEditor(<?php echo json_encode($options); ?>);
        </script>

        <?php foreach(self::$insertions['body'] as $tag) {?>
            <?php echo $tag . "\n"; ?>
        <?php } ?>

    </body>
</html>
