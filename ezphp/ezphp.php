<?php
/*
Version: 130924
Text Domain: ezphp
Plugin Name: ezPHP

Author URI: http://www.websharks-inc.com/
Author: WebSharks, Inc. (Jason Caldwell)

Plugin URI: http://www.s2member.com/kb/ezphp-plugin
Description: Evaluates PHP tags in Posts (of any kind, including Pages); and also in text widgets. A very lightweight plugin!
*/
if(!defined('WPINC')) // MUST have WordPress.
	exit('Do NOT access this file directly: '.basename(__FILE__));

if(!defined('EZPHP_INCLUDED_POST_TYPES')) define('EZPHP_INCLUDED_POST_TYPES', '');
if(!defined('EZPHP_EXCLUDED_POST_TYPES')) define('EZPHP_EXCLUDED_POST_TYPES', '');

class ezphp // PHP execution plugin for WordPress.
{
	public static $included_post_types = array(); // Inclusions array.
	public static $excluded_post_types = array(); // Exclusions array.

	public static function init() // Initialize plugin :-)
		{
			// load_plugin_textdomain('ezphp'); // Not necessary at this time.

			if(EZPHP_INCLUDED_POST_TYPES) ezphp::$included_post_types = // ONE time only.
				preg_split('/[\s;,]+/', EZPHP_INCLUDED_POST_TYPES, NULL, PREG_SPLIT_NO_EMPTY);

			if(EZPHP_EXCLUDED_POST_TYPES) ezphp::$excluded_post_types = // ONE time only.
				preg_split('/[\s;,]+/', EZPHP_EXCLUDED_POST_TYPES, NULL, PREG_SPLIT_NO_EMPTY);

			add_filter('the_content', 'ezphp::filter', 1);
			add_filter('get_the_excerpt', 'ezphp::filter', 1);
			add_filter('widget_text', 'ezphp::evaluate', 1);
		}

	public static function filter($content_excerpt)
		{
			if(($post_type = get_post_type())) // Check inclusions/exclusions.
				{
					if(ezphp::$included_post_types) // Specific inclusions?
						if(!in_array($post_type, ezphp::$included_post_types, TRUE))
							return $content_excerpt; // Exclude.

					if(ezphp::$excluded_post_types) // Specific exclusions?
						if(in_array($post_type, ezphp::$excluded_post_types, TRUE))
							return $content_excerpt; // Exclude.
				}
			return ezphp::evaluate($content_excerpt);
		}

	public static function evaluate($string)
		{
			if(!$string || stripos($string, 'php') === FALSE)
				return $string; // Saves time.

			if(stripos($string, '[php]') !== FALSE) // PHP shortcode tags?
				$string = str_ireplace(array('[php]', '[/php]'), array('<?php ', ' ?>'), $string);

			if(stripos($string, '< ?php') !== FALSE) // WP `force_balance_tags()` does this.
				$string = str_ireplace('< ?php', '<?php ', $string); // Quick fix.

			ob_start(); // Output buffer PHP code execution to collect echo/print calls.
			eval('?>'.trim($string).'<?php '); // Evaluate PHP tags (the magic happens here).
			$string = ob_get_clean(); // Collect output buffer.

			if(stripos($string, '!php') !== FALSE) // PHP code samples; e.g. <!php !> tags.
				$string = preg_replace(array('/\< ?\!php(\s+)/i', '/(\s+)\!\>/'), array('<?php${1}', '${1}?>'), $string);

			return $string; // All done :-)
		}
}

add_action('init', 'ezphp::init', 1);