# InlineCMS

## Initialization
AddDefaultCharset utf-8
Options -Indexes
RewriteEngine on

## Hide text files
#RewriteRule ^.htaccess$ - [F]

## Skel Themes Support
RewriteCond %{REQUEST_FILENAME} !-f
RewriteRule ^css\/(.*)$ theme/css/$1 [L]
RewriteRule ^js\/(.*)$ theme/js/$1 [L]

## Routing
RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} !-d
RewriteRule ^(.*)$ index.php?uri=%{REQUEST_URI} [QSA,L]
