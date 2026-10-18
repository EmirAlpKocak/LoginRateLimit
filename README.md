# LoginRateLimit

LoginRateLimit is a simple and user-friendly PHP rate limiter script that blocks password brute force on login pages and admin panels.

It uses IP address based rate limiting to secure your login pages and admin panels. You can configure the max failed login attempt and after the threshold value is exceeded, LoginRateLimit automatically bans that IP address from the login page. You can configure the block duration and time window in seconds.

It is designed for beginners, small businesses and solo websites. It works on shared hosting and with any panel including cPanel, Plesk Panel and IIS. This will work on any hosting that supports PHP. No SSH or root access is required.

You don't need programming knowledge to use this script. Just put it in the same directory as your login page and add a few lines of code to your page and it will work.

## Setup

To setup LoginRateLimit, first download the latest version from [Releases page.](https://github.com/EmirAlpKocak/LoginRateLimit/releases)
  * You can also get it from [Deuterium Software website.](https://deuteriumsoftware.com/software/loginratelimit/)

After that, upload LoginRateLimit.php to your website. You can either put into the same directory as your login/admin page or on a directory that you choose.

Then, find your login page or admin panel PHP file and open editing page.

There, after <?php, add this code:

```php
require_once 'LoginRateLimit.php';
use LoginRateLimit\Shield;

Shield::protect([
    'max-attempts' => 5,
    'block-time' => 30,
    'window' => 300
]);
```

After adding that, find the success and fail logic of your login page (usually an if-else statement). If you don't know where is it, search for terms like "Invalid user name or password", "Invalid login", "Login successful" etc.

Add this code to success:

```php
Shield::success();
```

Finally, add this code to failure:

```php
Shield::onfail();
```

After adding these to your login pages, the script should work correctly.

## Configuration

* You can configure max failed login attempts by changing max-attempts option.

* You can configure block duration in seconds by changing block-time option.

* You can configure window duration in seconds by changing window option. This will block X number of failed logins in Y seconds.

* You can configure location of the directory for storing the temporary files by changing storage-folder option. By default it will create a folder named as LoginRateLimitTemp to your website automatically. If all LoginRateLimit scripts in your website uses the same temporary storage folder, if an IP address gets blocked it will be blocked from all login pages that uses LoginRateLimit. If you separate folders, only the attacked login page will block that IP address.

* If you uploaded LoginRateLimit.php somewhere else, you need to change the path at require_once. If you uploaded into the same directory as your login pages, do not change it.

## Customizing Blocked Alert HTML Page

The HTML page shown when an IP is blocked is stored inside the PHP file, at openBlockPage() function. You can customize the HTML code for your own warning message. I would be happy if you leave "This login page is protected by LoginRateLimit!" message. :)

## For CloudFlare Users

If your website uses CloudFlare or a similar service, you need to download the CloudFlare compatible version because since all IP addresses will come from CloudFlare servers, LoginRateLimit won't work properly and will lock everyone from the login page.

## License and Security

LoginRateLimit and this repository is licensed under **Apache-2.0 license**. See more information at LICENSE file.

Please read **SECURITY.md** file before using LoginRateLimit.

## Change Log

* 1.0.0 - Released at 18/10/2026:

  * Initial release.
