<?php
$uri = \ltrim(\urldecode(\parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH)), '/');

if (\substr($uri, 0, 6) === 'vendor' && \file_exists(__DIR__ . '/../' . $uri)) {
    $fileExts = [
        'js' => 'application/x-javascript',
        'css' => 'text/css'
    ];
    $pathInfo = \pathinfo($uri);
    \header("Content-Type: " . $fileExts[$pathInfo['extension']]);
    \readfile(__DIR__ . '/../' . $uri);
} elseif ($uri !== 'favicon.ico' && ($uri == null || ! \file_exists(__DIR__ . '/../public/' . $uri))) {
    $_GET['c'] = $uri;
    include_once '_index.php';
    return true;
} else {
    $_GET['c'] = '';
    return false;
}
