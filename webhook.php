<?php
// webhook.php
// Add error logging
error_log("Webhook triggered: " . date('Y-m-d H:i:s'));

// Your secret
$secret = "skljndfkhsioa839293u8eh2h3283923j923";

// Paths (update these with your actual values)
$repo_path = '/home/beshfdav/repositories/Be-Sharp2';
$deploy_path = '/home/beshfdav/public_html';

$headers = getallheaders();
if (isset($headers['X-Hub-Signature']) || isset($_SERVER['HTTP_X_HUB_SIGNATURE'])) {
    $signature = isset($headers['X-Hub-Signature']) ? $headers['X-Hub-Signature'] : $_SERVER['HTTP_X_HUB_SIGNATURE'];
    $payload = file_get_contents('php://input');
    
    $expected = 'sha1=' . hash_hmac('sha1', $payload, $secret);
    
    if (hash_equals($expected, $signature)) {
        error_log("Signature verified, pulling repository at: " . $repo_path);
        
        // Step 1: Pull the latest changes
        exec('cd ' . $repo_path . ' && git pull 2>&1', $output, $return_var);
        
        error_log("Git pull output: " . print_r($output, true));
        
        if ($return_var === 0) {
            // Step 2: Deploy changes to public_html (similar to what .cpanel.yml would do)
            error_log("Pull successful, deploying to: " . $deploy_path);
            
            // Create rsync command to copy files (preserves permissions, excludes .git directory)
            $rsync_command = 'rsync -av --exclude=".git" --exclude="webhook.php" ' . $repo_path . '/ ' . $deploy_path . '/ 2>&1';
            
            exec($rsync_command, $deploy_output, $deploy_status);
            
            error_log("Deployment output: " . print_r($deploy_output, true));
            
            if ($deploy_status === 0) {
                echo "Pull and deployment successful!";
            } else {
                echo "Pull successful but deployment failed: " . implode("\n", $deploy_output);
            }
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