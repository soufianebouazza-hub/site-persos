<?php
// Serveur PHP local uniquement ; Apache utilise .htaccess.
$path=parse_url($_SERVER['REQUEST_URI'],PHP_URL_PATH);
$file=realpath(__DIR__.rawurldecode($path));
if($file&&str_starts_with($file,__DIR__.DIRECTORY_SEPARATOR)&&is_file($file)&&preg_match('/\.(css|js|svg|webp|png|jpg|ico)$/i',$file))return false;
require __DIR__.'/index.php';
