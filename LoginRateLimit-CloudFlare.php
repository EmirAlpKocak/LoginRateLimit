<?php
// LoginRateLimit 1.0.0 CloudFlare / Reverse Proxy Compatible Version
// License: Apache-2.0 license
// Coded By: Emir Alp Koçak
// Repository: github.com/EmirAlpKocak/LoginRateLimit
// Website: deuteriumsoftware.com

namespace LoginRateLimit;

class Shield 
{
    private static $storageFolder;
    private static $clientIp;
    private static $identifier;
    private static $maxAttempts = 5;
    private static $window = 300;
    private static $blockTime = 100;
    
    public static function protect(array $settings = [])
    {
        self::$maxAttempts = $settings['max-attempts'] ?? 5;
        self::$blockTime = $settings['block-time'] ?? 100;
        self::$window = $settings['window'] ?? 300;
        
        self::$storageFolder = $settings['storage-folder'] ?? __DIR__ . '/../LoginRateLimitTemp';
       
        if (!is_dir(self::$storageFolder))
        {
            mkdir(self::$storageFolder, 0755, true);
        }
        
                    // following code is for blocking access to the loginratelimit temporary storage folder. You may remove it if you have Option -Indexes or similar.
            $index = self::$storageFolder . '/index.php';
            if (!file_exists($index))
            {
                file_put_contents($index, "<?php http_response_code(403); exit('Access Denied'); ?>");
            }
        
        self::$clientIp = self::getClientIp();
        self::$identifier = md5(self::$clientIp);
        
        self::cleanStorage();
        
        if (self::isBlocked())
        {
            self::openBlockPage();
        }
    }
    
public static function onfail()
{
    $file = self::$storageFolder . '/' . self::$identifier . '.json';
    $now = time();
    $data = 
    [
        'attempts' => 0,
        'window-start' => $now,
        'blocked-until' => 0
    ];
    
    if (file_exists($file))
    {
        $savedData = json_decode(file_get_contents($file), true);
        if ($savedData)
        {
            $data = $savedData;
        }
    }
    
    if (($now - $data['window-start']) >= self::$window)
    {
        $data['attempts'] = 0;
        $data['window-start'] = $now;
    }
    
    $data['attempts']++;
    
    if ($data['attempts'] >= self::$maxAttempts)
    {
        $data['blocked-until'] = $now + self::$blockTime;
        $data['attempts'] = 0;
        $data['window-start'] = $now;
    }
    
    file_put_contents($file, json_encode($data), LOCK_EX);
}
    
    public static function success()
    {
        $file = self::$storageFolder . '/' . self::$identifier . '.json';
        if (file_exists($file))
        {
            unlink($file);
        }
    }
    
    private static function isBlocked(): bool
    {
        $file = self::$storageFolder . '/' . self::$identifier . '.json';
        if (!file_exists($file))
        {
            return false;
        }
        
        $data = json_decode(file_get_contents($file), true);
        $blockedUntil = $data['blocked-until'] ?? 0;
        
        if (time() < $blockedUntil)
        {
            return true;
        }
        
        return false;
    }
    
private static function getClientIp(): string
{
    if (!empty($_SERVER['HTTP_CF_CONNECTING_IP'])) 
    {
        $ip = trim($_SERVER['HTTP_CF_CONNECTING_IP']);
        if (filter_var($ip, FILTER_VALIDATE_IP)) 
        {
            return $ip;
        }
    }
    
    return $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
}
    
    private static function cleanStorage()
    {
        if (mt_rand(1, 100) === 1) 
        {
            $files = glob(self::$storageFolder . '/*.json');
            if ($files) 
            {
                $now = time();
                foreach ($files as $file) 
                {
                    $json = @file_get_contents($file);
                    if ($json) 
                    {
                        $data = json_decode($json, true);
                        $blockedUntil = $data['blocked-until'] ?? 0;
                        $windowStart = $data['window-start'] ?? 0;
                        
                        if ($now >= $blockedUntil && ($now - $windowStart) >= self::$window) 
                        {
                            @unlink($file);
                        }
                    }
                }
            }
        }
    }
    
    private static function openBlockPage()
    {
        http_response_code(429);
        echo "<!DOCTYPE html>
        <html>
        <head><title>Access Denied</title></head>
        <body style='font-family: sans-serif; text-align: center; padding-top: 50px;'>
            <h1 style='color: #d9534f;'>Access Temporarily Blocked</h1>
            <p>Too many failed login attempts from your IP address. Please try again later.</p>
            <p>This login page is protected by <a href='https://github.com/EmirAlpKocak/LoginRateLimit' target='_blank' style='color: #337ab7; text-decoration: none;'>LoginRateLimit!</a></p>
        </body>
        </html>";
        exit;
    }
}
?>
