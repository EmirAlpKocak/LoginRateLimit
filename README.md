# LoginRateLimit

Simple and user-friendly PHP rate limiter script that blocks password brute force on login pages and admin panels.

It uses IP address based rate limiting to secure your login pages and admin panels. You can configure the max failed login attempt and after the threshold value is exceeded, LoginRateLimit automatically bans that IP address from the login page. You can configure the block duration in seconds.

It is designed for beginners, small businesses and solo websites. It works on sharing hosting and with any panel including cPanel, Plesk Panel and IIS. This will work on any hosting that supports PHP. No SSH or root access is required.

You don't need programming knowledge to use this script. Just put it in the same directory as your login page and add a few lines of code to your page and it will work.

## Setup and Configuration

To setup LoginRateLimit, first download the latest version from HERE.

After that, upload LoginRateLimit.php to your website. You can either put into the same directory as your login/admin page or on a directory that you choose.

Then, find your login page or admin panel PHP file and open editing page.

There, after <?php, add this code:

```php
require_once 'LoginRateLimit.php'; // You can change the file location.
use SafeLogin\Shield;

Shield::protect([
    'max_attempts' => 5,
    'block-time' => 30
]);
```
