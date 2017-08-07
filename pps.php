<?php
/*
Plugin Name: Plastic Post Style
Version: 1.1.3
Plugin URI: http://www.wembley.jp/wp-plugins/plastic-post-style
Author: MIO
Author URI: http://www.wembley.jp
Description: 記事本文と抜粋の出力を調整・カスタマイズします。Configure content&excerpt styles of all posts with various configurations.
*/

/**
 *  "Plastic Post Style"
 *    Copyright 2010 MIO(www.wembley.jp) (email : post@wembley.jp)
 *
 *  This program is free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 2 of the License, or
 *  (at your option) any later version.
 *
 *  This program is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  You should have received a copy of the GNU General Public License
 *  along with this program; if not, write to the Free Software
 *  Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
 *
 *
 *  "HTMLParser class"
 *  @version    1.2.1 (stable) issued May 17, 2007
 *  @author     ucb.rcdtokyo http://www.rcdtokyo.com/ucb/
 *  @license    GNU LGPL v2.1+ http://www.gnu.org/licenses/lgpl.html
 *  @see        http://pear.php.net/package/XML_HTMLSax3
*/


/*******Core Functions*******/

load_plugin_textdomain( 'pps_lang',
			'wp-content/plugins/plastic-post-style/lang',
			'plastic-post-style/lang' );

// Define paths
define('PPS_PATH', dirname(__FILE__));
define('PPS_URL', WP_PLUGIN_URL . '/plastic-post-style');


// Install and Uninstall
register_activation_hook(__FILE__, 'pps_install');

//Install plugin
function pps_install(){
	add_option('pps_xmlparser_content','disable');
	add_option('pps_xmlparser_excerpt','enable');
	add_option('pps_br_content','disable');
	add_option('pps_br_excerpt','disable');
	add_option('pps_allowedtags','all');
	add_option('pps_length',400);
	add_option('pps_moretext','...read more');
	add_option('pps_nofollow','Yes');
	add_option('pps_separater','Paragraph');
	add_option('pps_header','');
	add_option('pps_footer','');
}

function pps_get_settings() {
	$PPSsettings = array (
    'xmlparser_content'       => get_option('pps_xmlparser_content'),
    'xmlparser_excerpt'       => get_option('pps_xmlparser_excerpt'),
    'br_content'              => get_option('pps_br_content'),
    'br_excerpt'              => get_option('pps_br_excerpt'),
    'moretext'                => get_option('pps_moretext'),
    'allowedtags'             => get_option('pps_allowedtags'),
    'nofollow'                => get_option('pps_nofollow'),
    'separater'               => get_option('pps_separater'),
    'header'                  => get_option('pps_header'),
    'footer'                  => get_option('pps_footer'),
    'length'                  => get_option('pps_length')
	);
	
	return $PPSsettings;
}
  $settings = pps_get_settings();

//Remove default formatting functions
	remove_filter('get_the_excerpt', 'wp_trim_excerpt');
  remove_filter('the_excerpt','wpautop');
  remove_filter('the_content','wpautop');

//Add PPS filters
  add_filter('the_content','pps_content_filter');
  add_filter('the_excerpt','pps_excerpt_filter');

//Add admin menu
  add_action('admin_menu', 'pps_admin_menu');
  add_action('admin_init', 'pps_admin_init');


/*******Main Actions*******/

//Compare number of letters with & without tags
function plain_count($txt_length, $txt) {  //Count letters without allowed tags
  $pointer = 0;
  $letters = 0;
  $result_text = "";
  while ($letters < $txt_length) {
    $lastchar = mb_substr($txt,$pointer,1);
    if (strcmp($lastchar, "<" ) == 0){
      $pointer= mb_strpos($txt, ">" ,$pointer);
    } else {
      $letters++;
    }
    $pointer++;
  }
  return $pointer;
}

//Add <br /> instead of <p>
function pps_autobr($text_br) {
	$text_br = str_replace(array("\r\n", "\r"), "\n", $text_br);
	$text_br = str_replace("\n", "<br />\n", $text_br);
	$text_br = preg_replace('!(</?(?:table|img|thead|tfoot|caption|tbody|tr|td|th|div|dl|dd|dt|ul|ol|li|pre|select|form|textarea|input|blockquote|address|p|math|p|script|h[1-6])[^>]*>)\s*<br />!', "$1", $text_br);
	/*$text_br = preg_replace('|<blockquote([^>]*)>|i', "<br />\n<blockquote$1>", $text_br);
	$text_br = str_replace('</blockquote>', "</blockquote>\n<br />", $text_br);*/
	$text_br = preg_replace('/(<script.*?>)(.*?)<\/script>/ise', "clr_br('$0')", $text_br);
	$text_br = preg_replace('/(<pre.*?>)(.*?)<\/pre>/ise', "clr_br('$0')", $text_br);
	$text_br = preg_replace('/(<form.*?>)(.*?)<\/form>/ise', "clr_br('$0')", $text_br);
	$text_br = preg_replace('/(<style.*?>)(.*?)<\/style>/ise', "clr_br('$0')", $text_br);
	$text_br = "\n".$text_br."\n";
	return $text_br;
}
function clr_br($str) {
	$str  = str_replace("<br />","",$str);
	$str  = str_replace('\"','"',$str);
	return $str;
}

//HTMLParser Class
function xmlparserclass($text) {
  require_once('inc/HTMLParser.class.php');
  $parser = new HTMLParser;
  $parser->setRuleFile('xhtml1-transitional_dtd.inc.php');
  $parser->setRoot('html');
  $parser->parse($text);
  $text = $parser->dump('UTF-8');
  $search_array=array("<html>","<body>","</body>","</html>");
  return $text = str_replace($search_array,"",$text);
}

//Check if the last tag is blockelement or not
function is_blockelement($text) {
	$blockelements = array(
		'h1', 'h2', 'h3', 'h4', 'h5', 'h6', 'div', 'p', 'ul', 'li', 'ol', 'dl', 'dd',
		'blockquote', 'address', 'hr', 'pre', 'table', 'tr', 'td', 'th');
	$text = (explode ( '<', $text));
	$text = trim(end($text));
	if (substr($text, -1) !== '>'){
		return false;
	}
	if (strpos($text, ' ')) {
		$text = explode(' ', $text);
		$text = $text[0];
	}
	$element = str_replace(array('/','>'), '', $text);
	if(in_array($element,$blockelements)){
		return true;
	} else {
		return false;
	}
}

//Make excerption
function pps_excerpt_filter($text) {
  $settings = pps_get_settings();
  $text = get_the_content();

	//If the content contains "<!--more-->" link
	if ( strpos ($text, __( '(more...)' )) ) {
		$text = explode(__( '(more...)' ), $text);
		$text = $text[0];
		$is_morelink = true;
	}
	
	if ($settings['br_content'] === 'enable' xor $settings['br_excerpt'] === 'enable') {
  	remove_filter('the_content','pps_content_filter');
  	$is_notfiltered = true;
	}
	
  $text = apply_filters('the_content', $text);
	
  if ($is_notfiltered == true ) {
	  if ($settings['br_excerpt'] === 'enable' ) {
	  	$text = pps_autobr($text);
	  } elseif ($settings['br_excerpt'] !== 'enable' ) {
	    $text = wpautop($text);
	  }
  }
  //Trim off the excerpt if number of letters is over
  if (mb_strlen(strip_tags($text,"<pre>")) > $settings['length'] && $is_morelink != true) {

    $is_over = true;
  	$length = plain_count($settings['length'],$text);
    $text = mb_substr($text, 0, $length, 'UTF-8');
    if ($settings['allowedtags'] !== "all"){
	    $settings['allowedtags'] = '<'.str_replace(',','>,<',$settings['allowedtags']).'>,<br>,<p>';
    	$text = str_replace(']]>', ']]&gt;', $text);
    	$text = strip_tags($text, $settings['allowedtags']);
    }
    //Set separater
    if ($settings['separater'] !== "None") {
      if ($settings['separater'] === "Period") {
        $separater = ".";
      } elseif ($settings['separater'] === "Touten") {
        $separater = "。";
      } elseif ($settings['separater'] === "Space") {
        $separater = " ";
      } elseif ($settings['separater'] === "Paragraph") {
        if ($settings['br_excerpt'] !== "enable") {
          $separater = "</p>";
        } else {
          $separater = "<br />";
        }
      }
      //Trim off
      $text = explode($separater,$text);
      $textend = array_pop($text);
      $text = implode($separater,$text);
      $text = rtrim($text);

			if ($is_notfiltered == true ) {
			  $text = $settings['header'].$text.$settings['footer'];
			}

      while ( mb_substr ( $text, -1 * mb_strlen( $separater )) === $separater ) {
      	$text = rtrim ( mb_substr ( $text, 0 , -1 * mb_strlen( $separater )));
  		}
			if ( $separater !== "<br />" || is_blockelement($text) == false ) {
				$text = $text.$separater;
				}
    }
  }
  
	if ( $is_notfiltered == true ) {
	  $text = $settings['header'].$text.$settings['footer'];
	}
  if ( $settings['xmlparser_excerpt'] === 'enable') {
    $text = xmlparserclass($text);
  }
  
  //Add read more link
  if ($settings['nofollow'] === 'Yes') {
    $nofollow = ' rel="nofollow"';
  } else {
    $nofollow = '';
  }
	if ($is_over == true || $is_morelink == true) {
    $text = $text.'<a href="'.get_permalink().'"'.$nofollow.'>'.$settings['moretext'].'</a>';
  }

  return $text;
//	return $tester;
}

//Add header & footer
function pps_content_filter($text) {
  $settings = pps_get_settings();
  if ($settings['xmlparser_content'] === 'enable' ) {
    $text = xmlparserclass($text);
  }
  if ($settings['br_content'] === 'enable') {
    $text = pps_autobr($text);
  } elseif ($settings['br_content'] !== 'enable') {
		$text = wpautop($text);
	}
  return $text = $settings['header'].$text.$settings['footer'];
}


/*******Admin Menu*******/

//Install admin menu
function pps_admin_menu() {

	require PPS_PATH . '/admin.php';

	add_submenu_page('options-general.php', 'Plastic Post Style Options', 'Plastic Post Style', 8, 'pps_options', 'pps_options');
}

//Register settings
function pps_admin_init() {
		register_setting('pps_options', 'pps_xmlparser_content');
		register_setting('pps_options', 'pps_xmlparser_excerpt');
		register_setting('pps_options', 'pps_br_content');
		register_setting('pps_options', 'pps_br_excerpt');
		register_setting('pps_options', 'pps_moretext');
		register_setting('pps_options', 'pps_allowedtags');
		register_setting('pps_options', 'pps_nofollow');
		register_setting('pps_options', 'pps_separater');
		register_setting('pps_options', 'pps_header');
		register_setting('pps_options', 'pps_footer');
		register_setting('pps_options', 'pps_length');
}

?>