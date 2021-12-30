=== MChat ===
Contributors: MSolution
plugin name: MChat
Tags: chat, plugin, ajax chat, ajax chat plugn, chat plugin, pure ajax chat, one to one chat, one 2 one chat, shortcode, wordpress chat, wordpress plugin, instant message, messaging, communication, contact, message
Requires at least: 5.0
Tested up to: 5.8.2
Requires PHP: 7.4
Stable tag: 1.0.0
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

MChat Plugin allowing WordPress user a one to one chat between logged in Users! Role based access, Pure Ajax working, Adds No HTML to the theme.

== Description ==

MChat Plugin allowing WordPress user a one to one chat between logged in Users! Role based access, Pure Ajax working, Adds No HTML to the theme.

= List of features: =

* MChat Plugins provides a shortcode which can be placed on any post or pages or author pages, which enables a logged in user to have a direct chat with him.
* **Easy installation**: MChat can be installed using a single shortcode [MCHAT user_id=X], where X is the user_id of the person a logged in user want to chat with. It can also be added to theme files using the do_shortcode function. Check Plugins settings page for more.
* **Multiple chat** installations can be embedded on the same page. Shortcode can be added to individual author pages.
* **Fully Ajax**: MChats adds No HTML to the page, and there for does not slow down the page load in any way. MChat loads completely via Ajax.
* **Customizable Appearance** MChat provides easy customization for colors. Just set the colors in the admin section, and see your MChat blend with the theme.
* **Role Base Access** Mchat Provides Role based access. Only registered users with the given roles can use the chat system.
* **New Message Alert** Users hear a sound when they get a new chat message. 
* **Email alerts** Users get email alerts when they receive a chat message.
* **Multiline messages**: Let your users post long messages in multiple lines.
* **Block Users** Users have an option to block another user.

All settings are available on `Settings -> MChat Settings` page.

The plugin is i18n ready.
 
== Installation ==
1. Upload the MChat Plugin via wp-admin add plugin panel or you could also upload wp_mchat to your blog (in the 'wp-content/plugins' directory)
1. Activate the plugin through the 'Plugins' menu in WordPress.
1. Place a shortcode `[MCHAT user_id=X]` in your posts or pages. Where X is the user_id of the person a logged in user wishes to chat with.
1. Alternatively install it in your templates via `<?php do_shortcode('[MCHAT user_id='.$user_id.']') ?>` code. 

== Frequently Asked Questions ==

= Will this Slow down my site?

No MChat is a purely Ajax loaded plugin on the front end. It loads up after your site has loaded.
MChat adds no HTML to your site, and there by does not effect load times of your site.

= How do i customize MChat to match with my theme?

Goto wp-admin > MChat settings page > Appearance and choose colors as per your theme, and enjoy!

= Is MChatPlugin translatable =
Yes.

= Support =

Just contact us at WordPress support forums for any comments or suggestions.

== Screenshots ==

1. screenshot-1.jpg
2. screenshot-2.jpg
3. screenshot-3.jpg
4. screenshot-4.jpg

== Changelog ==
= 1.0.0 Tuesday, December 28, 2021 4:00 PM =
* Initial release;

== Upgrade Notice ==
= Updating =
* Automatic updates should work smoothly, but we still recommend you back up your site.