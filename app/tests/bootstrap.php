<?php
use Symfony\Component\Dotenv\Dotenv;

require dirname(__DIR__).'/vendor/autoload.php';

$_SERVER['APP_ENV'] = $_SERVER['APP_ENV'] ?? 'test';
$_ENV['APP_ENV']    = $_ENV['APP_ENV'] ?? 'test';

$envFile = dirname(__DIR__).'/.env';
if (file_exists($envFile)) {
    (new Dotenv())
        ->usePutenv()
        ->loadEnv($envFile, 'APP_ENV', 'test');
}