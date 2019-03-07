=== Show modified Date in admin lists ===
Contributors: apasionados
Donate link: http://apasionados.es/
Tags: modified date, last modified, last updated, modified, modified time, page modified, post modified, post update, page update
Requires at least: 3.0.1
Tested up to: 4.8
Stable tag: 1.1
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Show modified date column in the lists of pages and posts in the WordPress admin panel.

== Description ==

This plugin shows a new, sortable, column with the modified date in the lists of pages and posts in the WordPress admin panel. It also shows the username that did the last update.

We needed this functionality for one of our websites and didn't find a suitable plugin for it. The idea of our plugin is based on the plugin <a href="https://wordpress.org/plugins/sort-by-modified/" target="_blank">Sort by modified</a> which has some problems displaying the information correctly in latest WordPress versiones. We love <a href="https://wordpress.org/plugins/codepress-admin-columns/" target="_blank">Admin Columns</a>, but the free version doesn't allow sorting the columns.

We also have included translations which are important for us as we are based in Valencia (Spain).

= SHOW MODIFIED DATE IN ADMIN LISTS in your Language! =
This first release is avaliable in English and Spanish. In the languages folder we have included the necessary files to translate this plugin.

If you would like the plugin in your language and you're good at translating, please drop us a line at [Contact us](http://apasionados.es/contacto/index.php?desde=show-modified-date-in-admin-lists-home).

= Further Reading =
You can access the description of the plugin in Spanish at: [Show modified Date in admin lists](http://apasionados.es/blog/).

== Installation ==

1. Upload the `show-modified-date-in-admin-lists` folder to the `/wp-content/plugins/` directory (or to the directory where your WordPress plugins are located)
1. Activate the SHOW MODIFIED DATE IN ADMIN LISTS plugin through the 'Plugins' menu in WordPress.

Please use with *WordPress MultiSite* at your own risk, as it has not been tested.

== Frequently Asked Questions ==

= What is SHOW MODIFIED DATE IN ADMIN LISTS good for? =
This plugin shows a new, sortable, column with the modified date in the lists of pages and posts in the WordPress admin panel. It also shows the username that did the last update.

= Does SHOW MODIFIED DATE IN ADMIN LISTS make changes to the database? =
No.

= How can I check out if the plugin works for me? =
Install and activate. Go to the pages or post list in the administration of WordPress. There you should see a new column with the modified date.

= How can I remove SHOW MODIFIED DATE IN ADMIN LISTS? =
You can simply activate, deactivate or delete it in your plugin management section.

= Are there any known incompatibilities? =
Please don't use it with *WordPress MultiSite*, as it has not been tested.

= Do you make use of SHOW MODIFIED DATE IN ADMIN LISTS yourself? = 
Of course we do. ;-)

== Screenshots ==

1. WordPress Admin post list with modified date
2. WordPress Admin page list with modified date


== Changelog ==

= 1.1 =
* Update to show correctly the user that has done last modification.

= 1.0 =
* First release.


== Upgrade Notice ==

= 1.1 =
Update to show correctly the user that has done last modification.

== Contact ==

For further information please send us an [email](http://apasionados.es/contacto/index.php?desde=show-modified-date-in-admin-lists-contact).

== Translating WordPress Plugins ==

The steps involved in translating a plugin are:

1. Run a tool over the code to produce a POT file (Portable Object Template), simply a list of all localizable text. Our plugins allready havae this POT file in the /languages/ folder.
1. Use a plain text editor or a special localization tool to generate a translation for each piece of text. This produces a PO file (Portable Object). The only difference between a POT and PO file is that the PO file contains translations.
1. Compile the PO file to produce a MO file (Machine Object), which can then be used in the theme or plugin.

In order to translate a plugin you will need a special software tool like [poEdit](http://www.poedit.net/), which is a cross-platform graphical tool that is available for Windows, Linux, and Mac OS X.

The naming of your PO and MO files is very important and must match the desired locale. The naming convention is: `language_COUNTRY.po` and plugins have an additional naming convention whereby the plugin name is added to the filename: `pluginname-fr_FR.po`

That is, the plugin name name must be the language code followed by an underscore, followed by a code for the country (in uppercase). If the encoding of the file is not UTF-8 then the encoding must be specified. 

For example:

* en_US – US English
* en_UK – UK English
* es_ES – Spanish from Spain
* fr_FR – French from France
* zh_CN – Simplified Chinese

A list of language codes can be found [here](http://en.wikipedia.org/wiki/ISO_639), and country codes can be found [here](http://en.wikipedia.org/wiki/ISO_3166-1_alpha-2). A full list of encoding names can also be found at [IANA](http://www.iana.org/assignments/character-sets).
