=== aaOnepage ===
Contributors: davkell
Tags: crm, onepagecrm, ambient age
Requires at least: 3.2.1
Tested up to: 3.5
Stable tag: 0.3.2

Push leads to your onepagecrm.com account

== Description ==

Push leads to your onepagecrm account through a contact form on your site


== Installation ==

1. Upload `plugin-name.php` to the `/wp-content/plugins/` directory
1. Activate the plugin through the 'Plugins' menu in WordPress
1. Visit the plugin settings and sign into your onepage account
1. use the shortcode [aa_onepage_form] or place `<?php aa_onepage_form(); ?>` in your template

== Frequently Asked Questions ==

== Screenshots ==


== Changelog ==

= 0.3.2 =
* Fix for onepage system tags not displaying in plugin options.

= 0.3.1 =
* Login form bug fix
* Config form bug fixes

= 0.3 =
* Fix for login after api update ( from GET to POST)
* Minor updates to cleanup error messages when Debug mode is enabled,
  mainly variable declaration

= 0.2 = 
* Compatabiltiy fixes for  > Wordpress 3.3 (enqueue scripts action)
* Typo fix in frontend form - quotes on action

= 0.1.1 = 
* Shortcode bug fix (no longer echoing result)

= 0.1 = 
* Added validation front/back-end
* Ability to choose fields to display & require on front-end form

= 0.0.1 =
* Initial Release. Probably best not to use on a production site just yet.


== Upgrade Notice ==

