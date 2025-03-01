<?php
// webhook.php
// Add error logging
error_log("Webhook triggered: " . date('Y-m-d H:i:s'));

// Your secret
$secret = "skljndfkhsioa839293u8eh2h3283923j923";

// Log all request headers for debugging
$headers = getallheaders();
error_log("Headers: " . print_r($headers, true));

// Check if we received the signature
if (isset($headers['X-Hub-Signature']) || isset($_SERVER['HTTP_X_HUB_SIGNATURE'])) {
    $signature = isset($headers['X-Hub-Signature']) ? $headers['X-Hub-Signature'] : $_SERVER['HTTP_X_HUB_SIGNATURE'];
    $payload = file_get_contents('php://input');
    
    error_log("Payload received: " . substr($payload, 0, 100) . "...");
    error_log("Signature received: " . $signature);
    
    $expected = 'sha1=' . hash_hmac('sha1', $payload, $secret);
    error_log("Expected signature: " . $expected);
    
    if (hash_equals($expected, $signature)) {
        // Path to your repository
        $repo_path = '/home/beshfdav/repositories/Be-Sharp2';
        
        error_log("Signature verified, pulling repository at: " . $repo_path);
        
        // Execute git pull
        exec('cd ' . $repo_path . ' && git pull 2>&1', $output, $return_var);
        
        error_log("Git pull output: " . print_r($output, true));
        error_log("Git pull return code: " . $return_var);
        
        if ($return_var === 0) {
            echo "Pull successful!";
        } else {
            echo "Pull failed: " . implode("\n", $output);
        }
    } else {
        error_log("Signature verification failed");
        echo "Signature verification failed";
    }
} else {
    error_log("No signature found in request");
    echo "No signature found in request";
}
?>