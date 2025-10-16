<?php
function classAutoloader($class) {
    $directories = [
        'models/',
        'controllers/',
        'config/'
    ];
    
    foreach ($directories as $directory) {
        $file = __DIR__ . '/../' . $directory . $class . '.php';
        if (file_exists($file)) {
            require_once $file;
            return;
        }
    }
}

spl_autoload_register('classAutoloader');
?>