<?php

header('Content-Type: text/plain; charset=utf-8');

echo "PHP INI: " . php_ini_loaded_file() . PHP_EOL;
echo "cURL CA: " . ini_get('curl.cainfo') . PHP_EOL;
echo "OpenSSL CA: " . ini_get('openssl.cafile') . PHP_EOL;

$ruta = ini_get('curl.cainfo');

echo "Existe: " . (is_file($ruta) ? 'SI' : 'NO') . PHP_EOL;
echo "Legible: " . (is_readable($ruta) ? 'SI' : 'NO') . PHP_EOL;