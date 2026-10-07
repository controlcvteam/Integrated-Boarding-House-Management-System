<?php

// Ensure writable directories in /tmp for Vercel Serverless environment
$writableDirs = [
    '/tmp/views',
    '/tmp/storage/framework/views',
    '/tmp/storage/framework/cache',
    '/tmp/storage/framework/sessions',
    '/tmp/storage/logs',
    '/tmp/bootstrap/cache',
];

foreach ($writableDirs as $dir) {
    if (!is_dir($dir)) {
        mkdir($dir, 0755, true);
    }
}

// Redirect cache and view paths to writable /tmp
putenv('VIEW_COMPILED_PATH=/tmp/views');
$_ENV['VIEW_COMPILED_PATH'] = '/tmp/views';

putenv('APP_SERVICES_CACHE=/tmp/bootstrap/cache/services.php');
$_ENV['APP_SERVICES_CACHE'] = '/tmp/bootstrap/cache/services.php';

putenv('APP_PACKAGES_CACHE=/tmp/bootstrap/cache/packages.php');
$_ENV['APP_PACKAGES_CACHE'] = '/tmp/bootstrap/cache/packages.php';

putenv('APP_CONFIG_CACHE=/tmp/bootstrap/cache/config.php');
$_ENV['APP_CONFIG_CACHE'] = '/tmp/bootstrap/cache/config.php';

putenv('APP_ROUTES_CACHE=/tmp/bootstrap/cache/routes.php');
$_ENV['APP_ROUTES_CACHE'] = '/tmp/bootstrap/cache/routes.php';

putenv('APP_EVENTS_CACHE=/tmp/bootstrap/cache/events.php');
$_ENV['APP_EVENTS_CACHE'] = '/tmp/bootstrap/cache/events.php';

// Safe serverless defaults if not configured
if (!getenv('SESSION_DRIVER')) {
    putenv('SESSION_DRIVER=database');
    $_ENV['SESSION_DRIVER'] = 'database';
}

if (!getenv('CACHE_STORE')) {
    putenv('CACHE_STORE=array');
    $_ENV['CACHE_STORE'] = 'array';
}

if (!getenv('LOG_CHANNEL')) {
    putenv('LOG_CHANNEL=stderr');
    $_ENV['LOG_CHANNEL'] = 'stderr';
}

// Supabase PostgreSQL & App Key fallbacks (prevents hanging on 127.0.0.1)
if (!getenv('DB_CONNECTION')) {
    putenv('DB_CONNECTION=pgsql');
    $_ENV['DB_CONNECTION'] = 'pgsql';
}
if (!getenv('DB_HOST')) {
    putenv('DB_HOST=aws-0-ap-northeast-1.pooler.supabase.com');
    $_ENV['DB_HOST'] = 'aws-0-ap-northeast-1.pooler.supabase.com';
}
if (!getenv('DB_PORT')) {
    putenv('DB_PORT=6543');
    $_ENV['DB_PORT'] = '6543';
}
if (!getenv('DB_DATABASE')) {
    putenv('DB_DATABASE=postgres');
    $_ENV['DB_DATABASE'] = 'postgres';
}
if (!getenv('DB_USERNAME')) {
    putenv('DB_USERNAME=postgres.uhchcotdkdndcrpzpgur');
    $_ENV['DB_USERNAME'] = 'postgres.uhchcotdkdndcrpzpgur';
}
if (!getenv('DB_PASSWORD')) {
    putenv('DB_PASSWORD=integratedboardinghouse2026');
    $_ENV['DB_PASSWORD'] = 'integratedboardinghouse2026';
}
if (!getenv('DB_SSLMODE')) {
    putenv('DB_SSLMODE=require');
    $_ENV['DB_SSLMODE'] = 'require';
}
if (!getenv('APP_KEY')) {
    putenv('APP_KEY=base64:R0QIUG27vDJMrAviTP+cV66e/mjDr9ILhYok1k/O8d8=');
    $_ENV['APP_KEY'] = 'base64:R0QIUG27vDJMrAviTP+cV66e/mjDr9ILhYok1k/O8d8=';
}

// Route request to Laravel entrypoint
require __DIR__ . '/../public/index.php';
