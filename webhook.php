<?php

$secret = "gh7Hg56JKl9pqRs2vWx4sdj334fhsdfhu8w823fdg831h"

$repo_path = '/home/beshfdav/repositories/BeSharp2';

if (isset($_SERVER['HTTP_X_HUB_SIGNATURE'])) {
    $signature = $_SERVER['HTTP_X_HUB_SIGNATURE'];
    $payload = file_get_contents('php://input');
    $expected = 'sha1=' . hash_hmac('sha1', $payload, $secret);
    
    if (hash_equals($expected, $signature)) {
        // Execute git pull
        exec('cd ' . $repo_path . ' && git pull 2>&1', $output, $return_var);
        
        if ($return_var === 0) {
            echo "Pull successful!";
        } else {
            echo "Pull failed: " . implode("\n", $output);
        }
    }
}
?>