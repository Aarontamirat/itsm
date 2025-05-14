<?php

$request = $_SERVER['REQUEST_URI'];

switch ($request) {
    case '/':
        $filename = __DIR__ . '/config' . $request . '.php';
        if (file_exists($filename)) {
            require $filename;
        } else {
            http_response_code(404);
            echo '404 Not Found';
        }
        break;

    case '':
        require __DIR__ . '/login.php';
        break;

    case '/admin':
        $filename = __DIR__ . '/admin' . $request . '.php';
        if (file_exists($filename)) {
            require $filename;
        } else {
            http_response_code(404);
            echo '404 Not Found';
        }
        break;
    
    case '/config':
        $filename = __DIR__ . '/config' . $request . '.php';
        if (file_exists($filename)) {
            require $filename;
        } else {
            http_response_code(404);
            echo '404 Not Found';
        }
        break;

    case '/it_staff':
        $filename = __DIR__ . '/it_staff' . $request . '.php';
        if (file_exists($filename)) {
            require $filename;
        } else {
            http_response_code(404);
            echo '404 Not Found';
        }
        break;

    case '/user':
        $filename = __DIR__ . '/user' . $request . '.php';
        if (file_exists($filename)) {
            require $filename;
        } else {
            http_response_code(404);
            echo '404 Not Found';
        }
        break;
    
    default:
         http_response_code(404);
            echo '404 Not Found';
            break;
}

