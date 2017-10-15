<?php
    use InlineCMS\Core\Core;
    use InlineCMS\Core\Config;
?>
<!doctype html>
<html lang="<?php echo Config::get('ui_lang'); ?>">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="x-ua-compatible" content="ie=edge">
        <title><?php echo $page->getTitle(); ?></title>
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel=stylesheet href="<?php echo ROOT_URL; ?>/static/font-awesome/css/font-awesome.css">
        <link rel=stylesheet href="<?php echo ROOT_URL; ?>/static/reset.css">
        <link rel=stylesheet href="<?php echo ROOT_URL; ?>/static/jquery/jquery-ui.css">
        <link rel=stylesheet href="<?php echo ROOT_URL; ?>/static/jquery/jstree/themes/default/style.css">
        <link rel=stylesheet href="<?php echo ROOT_URL; ?>/static/cms/css/editor.css">
        <?php foreach(self::$insertions['head'] as $tag) {?>
            <?php echo $tag; ?>
        <?php } ?>
    </head>
    <body>

        <iframe id="page-frame" frameborder="0"></iframe>

        <?php echo self::renderTemplate('editor/panel', array(
            'page' => $page,
            'pageLangs' => $pageLangs,
            'isHaveAllLangs'=> $isHaveAllLangs,
            'menus' => $menus
        )); ?>

        <script src="<?php echo ROOT_URL; ?>/static/jquery/jquery.js"></script>
        <script src="<?php echo ROOT_URL; ?>/static/jquery/jquery-ui.js"></script>
        <script src="<?php echo ROOT_URL; ?>/static/jquery/jstree/jstree.min.js"></script>
        <script src="<?php echo ROOT_URL; ?>/static/jquery/upload/jquery.fileupload.js"></script>
        <script src="<?php echo ROOT_URL; ?>/static/cms/core.js"></script>
        <script src="<?php echo ROOT_URL; ?>/static/cms/editor.js"></script>

        <script>
            var cms = new InlineCMS(<?php echo json_encode($options); ?>);
        </script>

        <?php foreach(self::$insertions['body'] as $tag) {?>
            <?php echo $tag . "\n"; ?>
        <?php } ?>

    </body>
</html>
