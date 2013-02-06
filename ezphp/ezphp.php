<?php
/*
Version: 130123
Plugin Name: ezPHP
Plugin URI: http://www.s2member.com/kb/ezphp-plugin
Description: Evaluates PHP tags in Posts (of any kind, including Pages); and also in text widgets. A very lightweight plugin!
Author URI: http://www.s2member.com
Author: s2Member® / WebSharks, Inc.
*/

if(!defined('WPINC'))
	exit('Please do NOT access this file directly.');

if(!defined('EZPHP_EXCLUDED_POST_TYPES'))
	define('EZPHP_EXCLUDED_POST_TYPES', '');

add_action('init', 'ezphp::init', 1);

class ezphp
	{
		public static function init()
			{
				add_filter('the_content', 'ezphp::filter', 1);
				add_filter('get_the_excerpt', 'ezphp::filter', 1);
				add_filter('widget_text', 'ezphp::evaluate', 1);
			}

		public static function filter($content_excerpt)
			{
				$excluded_post_types = preg_split('/[\s;,]+/', EZPHP_EXCLUDED_POST_TYPES, NULL, PREG_SPLIT_NO_EMPTY);

				if(isset($GLOBALS['post']->post_type) && !in_array($GLOBALS['post']->post_type, $excluded_post_types, TRUE))
					return ezphp::evaluate($content_excerpt);

				return $content_excerpt;
			}

		public static function evaluate($string)
			{
				ob_start();
				eval('?>'.trim((string)$string).'<?php ');
				return ob_get_clean();
			}
	}