=== iQ Block Country ===
Contributors: iqpascal
Donate link: https://www.webence.nl/plugins/donate
Tags: spam, block, country, comments, ban, geo, geo blocking, geo ip, block country, block countries, ban countries, ban country, blacklist, whitelist, security
Requires at least: 3.5.2
Tested up to: 5.0.3
Stable tag: 1.2.2
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html
Requires PHP: 5.6

Allow or disallow visitors from certain countries accessing (parts of) your website


== Description ==

iQ Block Country is a plugin that allows you to limit access to your website content. You can either allow or disallow visitors from defined countries to (parts of) your content.

For instance if you have content that should be restricted to a limited set of countries you can do so.
If you want to block rogue countries that cause issues like for instance hack attempts, spamming of your comments etc you can block them as well.

Do you want secure your WordPress Admin backend site to only your country? Entirely possible! You can even block all countries and only allow your ip address.

And even if you block a country you can still allow certain visitors by whitelisting their ip address just like you can allow a country but blacklist ip addresses from that country.

You can show blocked visitors a message which you can style by using CSS or you can redirect them to a page within your WordPress site. Or you can redirect the visitors to an external website.

You can (dis)allow visitors to blog articles, blog categories or pages or all content.

Stop visitors from doing harmful things on your WordPress site or limit the countries that can access your blog. Add an additional layer of security to your WordPress site.

This plugin uses the GeoLite database from Maxmind. It has a 99.5% accuracy so that is pretty good for a free database. If you need higher accuracy you can buy a license from MaxMind directly.
If you cannot or do not want to download the GeoIP database from Maxmind you can use the GeoIP API website available on https://geoip.webence.nl/

If you want to use the GeoLite database from Maxmind you will have to download the GeoIP database from MaxMind directly and upload it to your site.
The Wordpress license does not allow this plugin to download the MaxMind Geo database for you.

Do you need help with this plugin? Please email support@webence.nl.

= GDPR Information =

This plugin stores data about your visitors in your local WordPress database. The number of days this data is stores can be configured on the settings page. You can also disable logging any data.

Data which is stored of blocked visitors:

- IP Address
- Date and time of the visit
- URL that was requested
- Country of the IP address
- If the block happened on your backend or your frontend

Data which is stored on non blocked visitors:

 - Nothing

If you allow tracking (yeah if you do!) you share some information with us. This is only the IP address of a blocked request on your backend. No other information is send and only the IP address is logged on our systems to gather how many times that IP address have attempted to login to a backend. We do not log which site was visited or which URL just only the IP address So we cannot lead an ip address back to a specific website or user. If an IP address is not blocked again within a month we will remove the IP address from the list.

= Using this plugin with a caching plugin =

 Please note that many of the caching plugins are not compatible with this plugin. The nature of caching is that a dynamically build web page is cached into a static page.
 If a visitor is blocked this plugin sends header data where it supplies info that the page should not be cached. Many plugins however disregard this info and cache the page or the redirect. Resulting in valid visitors receiving a message that they are blocked. This is not a malfunction of this plugin.

Disclaimer: No guarantees are made but after some light testing the following caching plugins seem to work: Comet Cache, WP Super Cache
Plugins that do NOT work: W3 Total Cache, Hyper cache, WPRocket

== Installation ==

1. Unzip the archive and put the `iq-block-country` folder into your plugins folder (/wp-content/plugins/).
2. Download the GeoIP2 Country database from: http://geolite.maxmind.com/download/geoip/database/GeoLite2-Country.tar.gz
3. Unzip the GeoIP2 database and upload the GeoLite2-Country.mmdb file to your upload dir usually /wp-content/uploads/GeoLite2-Country.mmdb
4. If you do not want to or cannot download the MaxMind GeoIP database you can use the GeoIP API.
5. Activate the plugin through the 'Plugins' menu in WordPress
6. Go to the settings page and choose which countries you want to ban. Use the ctrl key to select multiple countries

== Frequently Asked Questions ==

= How come that I still see visitors from countries that I blocked in Statpress or other statistics software? =

It’s true that you might see hits from countries that you have blocked in your statistics software. 

This however does not mean this plugin does not work, it just means somebody tried to access a certain page or pages and that that fact is logged.

If you are worried this plugin does not work you could try to block your own country or your own ip address and afterwards visit your frontend website and see if it actually works. Also if you have access to the logfiles of the webserver that hosts your website  you can see that these visitors are actually denied with a HTTP error 403.

= How come I still see visitors being blocked from other security plugins? =

Other wordpress plugins handle the visitors also. They might run before iQ Block Country or they might run after iQ Block Country runs.

This however does not mean this plugin does not work, it just means somebody tried to access a certain page, post or your backend and another plugin also handled the request.

If you are worried this plugin does not work you could try to block your own country or your own ip address and afterwards visit your frontend website and see if it actually works. Also if you have access to the logfiles of the webserver that hosts your website  you can see that these visitors are actually denied with a HTTP error 403.


= This plugin does not work, I blocked a country and still see visitors! =

Well, this plugin does in fact work but is limited to the data MaxMind provides. Also in your statistics software or logfiles you probably will see log entries from countries that you have blocked. See the "How come I still see visitors..." FAQ for that.

If you think you have a visitor from a country you have blocked lookup that specific IP address on the tools tab and see which country MaxMind thinks it is. If this is not the same country you may wish to block the country that MaxMind thinks it is.

= Whoops I made a whoops and blocked my own country from visiting the backend. Now I cannot login... HELP! =

I am afraid this can only be solved by editing your MySQL database,directly editing the rows in the wp_options table. You can use a tool like PHPMyAdmin for that.

If you don't know how to do this please ask your hosting provider if they can help, or ask me if I can help you out!

= Why do you not make something that can override that it blocks my country from the backend. =

Well, if you can use a manual override so can the people that want to 'visit' your backend. 

This plugin is meant to keep people out. Perhaps you keep a key to your house somewhere hidden in your garden but this plugin does not have a key somewhere hidden... So if you locked yourself out you need to call a locksmith (or pick the lock yourself of course!)

= How can I style the banned message? =

You can style the message by using CSS in the textbox. You are also able to include images, so you could visualize that people are banned from your site.

You can also provide a link to another page explaining why they might be banned. Only culprit is that it cannot be a page on the same domain name as people would be banned from that page as well.

You can use for instance:

<style type="text/css">
  body {
    color: red;
    background-color: #ffffff; }
    h1 {
    font-family: Helvetica, Geneva, Arial,
          SunSans-Regular, sans-serif }
  </style>

<h1>Go away!</h1>

you basicly can use everything as within a normal HTML page. Including images for instance.

= Does this plugin also work with IPv6? =

Since v1.0.7 this plugin supports IPv6. IPv6 IP addresses are more and more used because there are no new IPv4 IP addresses anymore.

If your webhosting company supplies your with both IPv4 and IPv6 ip addresses please also download the GeoIPv6 database or use the GeoIP API service.

If your webhosting company does not supply an IPv6 IP address yet please ask them when they are planning to.

= Why is the GeoLite database not downloaded anymore ? =

The Wordpress guys have contacted me that the license of the MaxMind GeoLite database and the Wordpress license conflicted. So it was no longer
allowed to include the GeoLite database or provide an automatic download or download button. Instead users should download the database themselves
and upload them to the website.

Wordpress could be held liable for any license issue. So that is why the auto download en update was removed from this plugin.

= Does this plugin work with caching? =

In some circumstances: No

The plugin does it best to prevent caching of the "You are blocked" message. However most caching software can be forced to cache anyway. You may or may not be able to control the behavior of the caching method.

The plugin does it bests to avoid caching but under circumstances the message does get cached.
Either change the behavior of your caching software or disable the plugin.

= How can I select multiple countries at once? =

You can press the CTRL key and select several countries.

Perhaps also a handy function is that you can type in a part of the name of the country!

You can select/deselect all countries by selecting "(de)select all countries..."

If you just want to allow some countries you can also use the invert function by selecting the countries you want to allow and select invert this selection.

= How can I get a new version of the GeoIP database? =

You can download the database(s) directly from MaxMind and upload them to your website.

1. Download the GeoIP2 Country database from: http://geolite.maxmind.com/download/geoip/database/GeoLite2-Country.tar.gz
2. Unzip the GeoIP2 database and upload the GeoLite2-Country.mmdb file to your upload dir usually /wp-content/uploads/GeoLite2-Country.mmdb

Maxmind updates the GeoLite database every month.

= I get "Cannot modify header information - headers already sent" errors =

This is possible if another plugin or your template sends out header information before this plugin does. You can deactivate and reactivate this plugin, it will try to load as the first plugin upon activation.

If this does not help you out deselect "Send headers when user is blocked". This will no longer send headers but only display the block message. This however will mess up your website if you use caching software for your website.
This also does not work if you use a page or url redirect as that relies on sending headers for redirecting the visitor to another page or URL.

= What data get sends to you when I select "Allow tracking"? =

If you select this option each hour the plugin checks if it has new data to send back to the central server. 

This data consists of each IP address that has tried to login to your backend and how many attempts were made since the last check.

If storing or sharing an IP address is illegal in your country do not select this feature.

= The laws in my country do not allow storing IP addresses as it is personal information. =

You can select the option on the home tab "Do not log IP addresses" to stop iQ Block Country from logging IP addresses. This will however also break the statistics.

= I have moved my WordPress site to another host. Now iQ Block Country cannot find the GeoIP databases anymore =

Somewhere in your WordPress database there is a wp_options table. In the wp_options table is an option_name called 'upload_path'.

There probably is an (old) path set as option_value. If you know your way around MySQL (via PHPMyAdmin for instance) you can empty the option_value.
This should fix your problem.

Please note that your wp_options table may be called differently depending on your installation choices.

= Jetpack does not work anymore with your plugin! =

Jetpack uses xmlrpc.php to communicate with your site. xmlrpc.php is considered as a backend url and therefore blocked if needed.

You can allow Jetpack by selecting "Jetpack by wordpress.com" as a search engine on the services tab.

= I only want to block certain posts with a specific tag =

As the basic rule is to block all and every post you have to configure this in a special way:

- Select the countries you want to block on the frontend tab
- Select the option "Block visitors from visiting the frontend of your website" on the frontend tab
- Select the option "Do you want to block individual categories" on the categories tab.
- Do not select any categories (unless you want to of course)
- Select "Do you want to block individual tags" on the tags tab.
- Select any tag you want to block.

= Is the new GeoIP2 database format supported? = 

Yes since v1.2.0 the new GeoIP2 Country database is supported. For now the old GeoIP lite database will also still be supported.
These databases are however not updated anymore by MaxMind. If you have the new database and the old database the new one will
be used.


== Changelog ==

= 1.2.2 =

* New: Added MOZ as service.
* New: Added SEMrush as service.
* New: Added SEOkicks as service.
* New: Added EU2 and EU3 servers for GeoIP API
* New: Added support for WPS Hide Login
* Change: Deleted Asia server due to bad performance
* Change: Altered behavior of flushing the buffer

= 1.2.1 =

* New: Added Link Checker (https://validator.w3.org/checklink) as service.
* New: Added Dead Link Checker as a service.
* New: Added Broken Link Check as a service.
* New: Added Pingdom as a service
* Change: Adjusted loading chosen library (Credits to Uzzal)
* Change: Display error when only the legacy GeoIP database exists and not the new GeoIP2 version

= 1.2.0 =

* New: Added support for GeoIP2 country database
* New: Added Pinterest as service

= 1.1.51 =

* New: Added new GeoIP API server in Florida
* New: Added new GeoIP API server in Asia

= 1.1.50 =

* Bugfix: Fix for SQL error in rare conditions
* New: Added AppleBot, Feedburner and Alexa to the services you can allow
* Change: Added some more work for the upcoming GeoIP2 support.

= 1.1.49 =

* Change: Changed when the buffer is flushed (if selected) (Thanks to Nextendweb)
* Change: Changed cleanup on debug logging table.

= 1.1.48 =

* Bugfix: Fixed small bug

= 1.1.47 =

* Change: You can now also enter IP Ranges in the black & whitelist in CIDR format.
* Change: Altered logging clean up a little bit

= 1.1.46 =

* Bugfix: Added extra aiwop checking due to a notice error.
* Change: Renamed Search Engines tab to Services tab as more non-search engines are added to the list.
* New: Added Feedly to services.
* New: Added Google Feed to services.
* New: Changes are made for supporting the new GeoIP2 database format of MaxMind.

= 1.1.45 =

* Bugfix: (un)blocking individual pages and categories did not work anymore.

= 1.1.44 =

* Change: Removed Asia API Key server.
* Change: Small change when frontend blocking is fired up.
* Change: Adds server ip address (the IP address where your website is hosted) to the frontend whitelist so if you block the country your website is hosted it can still access wp-cron for instance.

= 1.1.43 =

* Change: Altered address for Asia API Key server

= 1.1.42 =

* Bugfix: Temp fix for some people who had issues being blocked from the backend.

= 1.1.41 =

* Change: Removed unnecessary code.
* New: New GeoIP API location added at the west coast of the United States
* New: Limit the number of days the logging is kept between 7 days and 90 days.
* New: Disable host lookup on the logging tab. In some circumstances this may speed up the logging tab.

= 1.1.40 =

* Bugfix: Fix for bug in not blocking/allowing post types.
* New: Moved GeoIP API to secure https
* New: Logging DB optimization (Thanks to Arjen Lentz)
* Change: Changed support option from forum to mail.

= 1.1.38 =

* Bugfix: Only shows warning of incompatible caching plugin if frontend blocking is on.
* Change: Better error handling 

= 1.1.37 =

* Change: Small adjustment to prevent wp_mail declaration as much as possible.

= 1.1.36 =

* Bugfix: Smashed bug on backend

= 1.1.35 =

* Change: Added WPRocket to list of caching plugins that are not compatible with iQ Block Country (thanks to Mike Reed for supplying the info)
* New: Added Baidu to Search Engines list
* New: Added Google Site Verification to the search engines list
* New: Added Google Search Console to the search engines list
* Change: Only displays warning about incompatible caching plugins in case frontend blocking is selected.
* New: You can now also block individual post tags
* Change: Fixed small security issue with downloading the statistics as CSV file (Thanks to Benjamin Pick for reporting)

= 1.1.33 =

* Bugfix: Bug smashed on tag page

= 1.1.32 =

* Bugfix: Bug smashed on tag page

= 1.1.31 = 

* Change: Small changes in GeoIP API calls
* New: A warning is displayed for known caching plugins that ignore the no caching headers.
* Change: Small changes
* Change: Moved some of the urls to https, more to follow.
* New: Added option to block / unblock tag pages.

= 1.1.30 =

* Change: Added new GeoIP API location for Asia-Pacific region.
* Change: Added some missing country icons.

= 1.1.29 = 

* Change: Small changes in GeoIP API calls
* New: Added database information to tools tab.
* New: Added support for rename wp-login plugin

= 1.1.28 =

* Bugfix: Altered mysql_get_client_info check as in some setups this gave a fatal error.
* New: Added Wordpress Jetpack as search engine. You can allow Jetpack to communicate with your site if you have Jetpack installed.
* New: Added option to allow admin-ajax.php visits if you use backend blocking.

= 1.1.27 =

* Bugfix: Fixed small bug

= 1.1.26 =

* New: xmlrpc.php is now handled the same way as other backend pages.
* Change: Updated chosen library to latest version.
* Change: Added a (de)select all countries to the backend en frontend country list.
* Change: Changed order of how the plugin detects the ip address.
* Change: Added detection of more header info that can contain the proper ip address
* New: Added support forum to the site.
* Change: Added download urls on database is too old message.

= 1.1.25 =

* Bugfix: Altered checking for Simple Security Firewall

= 1.1.24 =

* New: Added support for Lockdown WordPress Admin
* New: Added support for WordPress Security Firewall (Simple Security Firewall)
* Change: Various small changes

= 1.1.23 =

* Bugfix: Fixed bug if cURL was not present in PHP version
* New: When local GeoIP database present it checks if database is not older than 3 months and alerts users in a non-intrusive way.

= 1.1.22 =

* Bugfix: Category bug squashed
* Change: Altered text-domain
* New: Added export of all logging data to csv. This exports max of 1 month of blocked visitors from frontend & backend.

= 1.1.21 =

* Change: Minor improvements
* New: Added check to detect closest location for GeoIP API users
* Bugfix: Fixed an error if you lookup an ip on the tools tab while using the inverse function it sometimes would not display correctly if a country was blocked or not.
* New: Added support for All in one WP Security Change Login URL. If you changed your login URL iQ Block Country will detect this setting and use it with your backend block settings.

= 1.1.20 =

* New: Added Google Ads to search engines
* New: Added Redirect URL (Basic code supplied by Stefan)
* New: Added inverse selection on frontend. (Basic code supplied by Stefan)
* New: Added inverse selection on backend.
* New: Validated input on the tools tab.

= 1.1.19 =

* Bugfix: Check if MaxMind databases actually exist.
* New: Unzip MaxMind database(s) if gzip file is found.
* New: Block post types
* New: Added option to select if you want to block your search page.
* New: When (re)activating the plugin it now adds the IP address of the person activating the plugin to the backend whitelist if the whitelist is currently empty.

= 1.1.18 =

* Change: Changed working directory for the GeoIP database to /wp-content/uploads

= 1.1.17 =

* Change: Due to a conflict of the license where Wordpress is released under and the license the MaxMind databases are released under I was forced to remove all auto downloads of the GeoIP databases. You now have to manually download the databases and upload them yourself.
* New: Added Webence GeoIP API lookup. See https://geoip.webence.nl/ for more information about this API.


== Upgrade Notice ==

= 1.1.19 =

This plugin no longer downloads the MaxMind database. You have to download manually or use the GeoIP API.