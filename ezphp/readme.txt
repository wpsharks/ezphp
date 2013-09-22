=== ezPHP for WordPress® ===

Contributors: WebSharks
Donate link: http://www.s2member.com/donate/
Tags: post, pages, posts, code, php, eval, exec, eval php, exec php, easy php, ez php, variables

License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Stable tag: 130922
Requires at least: 3.2
Tested up to: 3.7-alpha

Evaluates PHP tags in Posts (of any kind, including Pages); and also in text widgets. A very lightweight plugin!

== Description ==

This plugin is VERY simple. There is only ONE configurable option. You can define this PHP constant inside your `/wp-config.php` file (optional).

	define('EZPHP_EXCLUDED_POST_TYPES', ''); // A comma-delimited list of Post Types to exclude.

For example, if you don't want PHP tags evaluated in Posts, only in Pages.

	define('EZPHP_EXCLUDED_POST_TYPES', 'post');

#### Quick Tip: Writing PHP Code Samples?

It is now possible to use `<!php !>` when writing code samples, to avoid having certain PHP tags evaulated. When you write `<!php !>`, it is translated into `<?php ?>` in the final output; but never actually evaluated by the internal PHP parser. Of course, it's ALSO possible to accomplish this with HTML entities; e.g. `&lt;?php ?&gt;`.

== Installation ==

= ezPHP is very easy to install (instructions) =
1. Upload the `/ezphp` folder to your `/wp-content/plugins/` directory.
2. Activate the plugin through the `Plugins` menu in WordPress®.
3. Use PHP tags in your Posts/Pages/Widgets.

== Changelog ==

= v130922 =

* It is now possible to use `<!php !>` when writing code samples, to avoid having certain PHP tags evaulated. When you write `<!php !>`, it is translated into `<?php ?>` in the final output; but never actually evaluated by the internal PHP parser. Of course, it's ALSO possible to accomplish this with HTML entities; e.g. `&lt;?php ?&gt;`.

= v130123 =

 * Initial release.