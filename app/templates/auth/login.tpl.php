<?php use InlineCMS\Core\Lang; ?>
<!doctype html>
<html>
    <head>
        <meta charset="utf-8">
        <meta http-equiv="x-ua-compatible" content="ie=edge">
        <title><?php echo Lang::get('authLogin'); ?></title>
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link href='https://fonts.googleapis.com/css?family=PT+Sans&subset=latin,cyrillic' rel='stylesheet' type='text/css'>
        <link rel=stylesheet href="<?php echo ROOT_URL; ?>/static/reset.css">
        <link rel=stylesheet href="<?php echo ROOT_URL; ?>/static/cms/css/login.css">
    </head>
    <body>

        <div id="wrapper">

            <div id="form">

                <?php if (PRODUCT_ATTRIBUTION) { ?>
                    <h3><?php echo PRODUCT_TITLE; ?></h3>
                <?php } ?>

                <form action="" method="post">

                    <?php if (!empty($error)){ ?>
                        <div class="error"><?php echo $error; ?></div>
                    <?php } ?>

                    <div class="field">
                        <label for="auth_email"><?php echo Lang::get('authEmail'); ?>:</label>
                        <input type="text" id="auth_email" name="auth_email">
                    </div>

                    <div class="field">
                        <label for="auth_password"><?php echo Lang::get('authPassword'); ?>:</label>
                        <input type="password" id="auth_password" name="auth_password">
                    </div>

                    <div class="buttons">
                        <input type="submit" name="submit" value="<?php echo Lang::get('authLogin'); ?>">
                    </div>

                </form>

            </div>

        </div>

    </body>
</html>
