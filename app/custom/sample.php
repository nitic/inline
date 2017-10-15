<?php

/**
 * This is a sample file which can be included into
 * any site page using "Code" widget on "Elements" tab.
 */

    echo 'Hello from <strong>/app/custom/sample.php</strong>. Date is ' . date('Y-m-d H:i');

    // Code below contains examples and they should not be executed
    return;

/**
 * REQUEST
 */

    // Receiving request variable (requested with any method):
    $foo = InlineCMS\Core\Request::get('foo');

    // Check if variable is in request:
    $isFooInRequest = InlineCMS\Core\Request::has('foo');

    // Get current URL (without host):
    $url = InlineCMS\Core\Request::getUrl();

    // Get current host:
    $host = InlineCMS\Core\Request::getHostUrl();

/**
 * TEMPLATE
 */

    // Add JavaScript file to current page:
    InlineCMS\Core\Layout::addJs($url);

    // Add CSS file to current page:
    InlineCMS\Core\Layout::addCss($url);

    // Get rendered HTML of template in /app/templates folder:
    // (filename should be specified without .tpl.php extension)
    $html = InlineCMS\Core\Layout::renderTemplate('filename', array(
        // Variables passed to template: $foo, $bar
        'foo' => 123,
        'bar' => 'Hello World'
    ));

    // Templates can be organized into subfolders in /app/templates/folder:
    $html = InlineCMS\Core\Layout::renderTemplate('subfolder/filename');

/**
 * EXTERNAL LIBRARIES
 */

    // Include library from /app/libs folder:
    InlineCMS\Loader::loadLibrary('library.php');

    // Include library from /app/libs/subfolder:
    InlineCMS\Loader::loadLibrary('subfolder/library.php');

/**
 * USER
 */

    // Check if user is logged in:
    $isLogged = \InlineCMS\Core\User::isLogged();


/**
 * RESPONSE
 */

    // print HTML code that you want to display
    echo $html; return;
