<?php
echo "PHP BINARY: " . PHP_BINARY . "\n";
echo "UPLOAD MAX: " . ini_get("upload_max_filesize") . "\n";
echo "POST MAX: " . ini_get("post_max_size") . "\n";
echo "CONFIG PATH: " . php_ini_loaded_file() . "\n";
echo "MEMORY LIMIT: " . ini_get("memory_limit") . "\n";
