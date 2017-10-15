<!doctype html>
<html class="no-js" lang="">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="x-ua-compatible" content="ie=edge">
        <title>InlineCMS <?php echo $lang['setupWizard']; ?></title>
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link href='https://fonts.googleapis.com/css?family=Play&subset=latin,cyrillic' rel='stylesheet' type='text/css'>
        <link href='https://fonts.googleapis.com/css?family=PT+Sans&subset=latin,cyrillic' rel='stylesheet' type='text/css'>
        <link rel=stylesheet href="<?php echo ROOT_URL; ?>/static/font-awesome/css/font-awesome.css">
        <link rel=stylesheet href="<?php echo ROOT_URL; ?>/static/jquery/jquery-ui.css">
        <link rel=stylesheet href="<?php echo ROOT_URL; ?>/static/reset.css">
        <link rel=stylesheet href="<?php echo ROOT_URL; ?>/static/setup/setup.css">
        <script src="<?php echo ROOT_URL; ?>/static/jquery/jquery.js"></script>
        <script src="<?php echo ROOT_URL; ?>/static/jquery/jquery-ui.js"></script>
        <script src="<?php echo ROOT_URL; ?>/static/jquery/clipboard/jquery.clipboard.js"></script>
        <script src="<?php echo ROOT_URL; ?>/static/setup/setup.js"></script>        
    </head>
    <body>
        
        <div id="wrapper">
        
            <header>
                <h1>InlineCMS <span><?php echo $lang['setupWizard']; ?></span></h1>
            </header>
            
            <section>
                <article>

                    <div id="steps">
                        <ul>
                            <?php foreach ($steps as $stepId=>$stepTitle) { ?>
                                <li<?php if ($stepId==$currentStep) { ?> class="active"<?php } ?> data-step="<?php echo $stepId; ?>"><?php echo $stepTitle; ?></li>
                            <?php } ?>
                        </ul>
                    </div>
                    
                    <div id="content"><?php echo $step; ?></div>
                    
                    <div id="buttons" class="buttons">
                        <button class="b-prev"><i class="fa fa-caret-left"></i> <?php echo $lang['prevStep']; ?></button>
                        <button class="b-next"><?php echo $lang['nextStep']; ?> <i class="fa fa-caret-right"></i></button>
                        <button class="b-finish"><?php echo $lang['finishContinue']; ?> <i class="fa fa-caret-right"></i></button>
                    </div>
                    
                </article>
                <div id="loading-indicator"><i class="fa fa-cog fa-spin"></i></div>
            </section>
                       
            <footer>
                <p>InlineCMS Team &copy; <?php echo date('Y'); ?></p>
            </footer>
            
        </div>
        
        <div id="steps-cache" style="display:none"></div>

        <script>
            var setup;
            $(document).ready(function(){
                setup = new SetupWizard({
                    backendUrl: '<?php echo ROOT_URL . '/backend.php'; ?>',
                    rootUrl: '<?php echo ROOT_URL; ?>',
                    lang: '<?php echo $langId; ?>',
                    title: '<?php echo $lang['setupWizard']; ?>'
                });
                setup.start();
            });
        </script>
        
    </body>
</html>
