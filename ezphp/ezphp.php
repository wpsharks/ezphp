<?php
/*
Version: 131121
Text Domain: ezphp
Plugin Name: ezPHP

Author URI: http://www.websharks-inc.com/
Author: WebSharks, Inc. (Jason Caldwell)

Plugin URI: http://www.websharks-inc.com/product/ezphp/
Description: Evaluates PHP tags in Posts (of any kind, including Pages); and in text widgets. Very lightweight; plus it supports `[php][/php]` shortcodes!
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
			#load_plugin_textdomain('ezphp'); // Not necessary at this time.

			if(EZPHP_INCLUDED_POST_TYPES) // Specific Post Types?
				ezphp::$included_post_types = // Convert these to an array.
					preg_split('/[\s;,]+/', EZPHP_INCLUDED_POST_TYPES, NULL, PREG_SPLIT_NO_EMPTY);
			ezphp::$included_post_types = apply_filters('ezphp_included_post_types', ezphp::$included_post_types);

			if(EZPHP_EXCLUDED_POST_TYPES) // Specific Post Types?
				ezphp::$excluded_post_types = // Convert these to an array.
					preg_split('/[\s;,]+/', EZPHP_EXCLUDED_POST_TYPES, NULL, PREG_SPLIT_NO_EMPTY);
			ezphp::$excluded_post_types = apply_filters('ezphp_excluded_post_types', ezphp::$excluded_post_types);

			add_filter('the_content', 'ezphp::filter', 1);
			add_filter('get_the_excerpt', 'ezphp::filter', 1);
			add_filter('widget_text', 'ezphp::evaluate', 1);
		}

	public static function filter($content_excerpt)
		{
			$post_type = get_post_type();

			if($post_type && ezphp::$included_post_types) // Specific inclusions?
				if(!in_array($post_type, ezphp::$included_post_types, TRUE))
					return $content_excerpt; // Exclude.

			if($post_type && ezphp::$excluded_post_types) // Specific exclusions?
				if(in_array($post_type, ezphp::$excluded_post_types, TRUE))
					return $content_excerpt; // Exclude.

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

	public static function activate()
		{
			ezphp::init(); // Nothing more at this time.
		}

	public static function deactivate()
		{
			// Not necessary at this time.
		}
}

add_action('init', 'ezphp::init', 1);
register_activation_hook(__FILE__, 'ezphp::activate');
register_deactivation_hook(__FILE__, 'ezphp::deactivate');