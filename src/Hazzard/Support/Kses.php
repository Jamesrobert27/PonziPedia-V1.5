<?php namespace Hazzard\Support;

/**
 * KSES by Ulf Harnhammar
 * http://sourceforge.net/projects/kses/
 * 
 * Version ported from WordPress
 * https://github.com/WordPress/WordPress/blob/master/wp-includes/kses.php
 */

class Kses {

	/**
	 * Allowed tags.
	 * 
	 * @var arrary
	 */
	protected $allowedTags;

	/**
	 * Allowed entities.
	 * 
	 * @var arrary
	 */
	protected $allowedEntities;

	/**
	 * Allowed protocols.
	 * 
	 * @var arrary
	 */
	protected $allowedProtocols;

	/**
	 * Allowed css.
	 * 
	 * @var arrary
	 */
	protected $allowedCss;

	/**
	 * Create a new kses instance.
	 *
	 * @param  array|null  $tags
	 * @param  array|null  $entities
	 * @param  array|null  $css
	 * @param  array|null  $protocols
	 * @return void
	 */
	public function __construct($tags = null, $entities = null, $css = null, $protocols = null)
	{
		$this->allowedTags      = is_array($tags)      ? $tags      : $this->getDefaultAllowedTags();
		$this->allowedEntities  = is_array($entities)  ? $entities  : $this->getDefaultAllowedEntities();
		$this->allowedCss       = is_array($css)       ? $css       : $this->getDefaultAllowedCss();
		$this->allowedProtocols = is_array($protocols) ? $protocols : $this->getDefaultAllowedProtocols();
	}

	/**
	 * Filters content and keeps only allowable HTML elements.
	 * 
	 * @param  string $string
	 * @return string
	 */
	public function parse($string)
	{
		$string = $this->noNull($string);
		$string = $this->jsEntities($string);
		$string = $this->normalizeEntities($string);
		$string = $this->split($string);

		return $string;
	}

	/**
	 * Searches for HTML tags, no matter how malformed.
	 *
	 * @param  string $string
	 * @return string
	 */
	protected function split($string)
	{
		return preg_replace_callback('%(<!--.*?(-->|$))|(<[^>]*(>|$)|>)%', array($this, 'splitCallback'), $string);
	}

	/**
	 * Callback for Kses::split().
	 *
	 * @param  string $match
	 * @return string
	 */
	protected function splitCallback($match)
	{
		$string = $match[0];

		$string = $this->stripslashes($string);

		if (substr($string, 0, 1) != '<') return '&gt;';
		
		if (substr($string, 0, 4) == '<!--') {
			$string = str_replace(array('<!--', '-->'), '', $string);
			
			while ($string != ($newstring = $this->parse($string))) {
				$string = $newstring;
			}

			if ($string == '') return '';

			// Prevent multiple dashes in comments.
			$string = preg_replace('/--+/', '-', $string);
			
			// Prevent three dashes closing a comment.
			$string = preg_replace('/-$/', '', $string);
			
			return $string;
			//return "&lt;!--{$string}--&gt;";
		}
		
		if (!preg_match('%^<\s*(/\s*)?([a-zA-Z0-9]+)([^>]*)>?$%', $string, $matches)) {
			return '';
		}
		
		$slash    = trim($matches[1]);
		$tag      = $matches[2];
		$attrlist = $matches[3];

		if (!isset($this->allowedTags[strtolower($tag)])) {
			return '';
		}
		
		// No attributes are allowed for closing tags.
		if ($slash != '') return "</$tag>";

		return $this->attr($tag, $attrlist);
	}

	/**
	 * Removes all attributes, if none are allowed for this element.
	 *
	 * @param  string $element
	 * @param  string $attr
	 * @return string
	 */
	protected function attr($element, $attr)
	{
		$xhtmlSlash = '';
		
		if (preg_match('%\s*/\s*$%', $attr)) $xhtmlSlash = ' /';

		if (!isset($this->allowedTags[strtolower($element)]) || count($this->allowedTags[strtolower($element)]) == 0) {
			return "<$element$xhtmlSlash>";
		}

		$attrarr = $this->hair($attr);

		$attr2 = '';

		$allowedAttr = $this->allowedTags[strtolower($element)];
		
		foreach ($attrarr as $arreach) {
			if (!isset($allowedAttr[strtolower($arreach['name'])])) {
				continue;
			}

			$current = $allowedAttr[strtolower($arreach['name'])];
			
			if ($current == '') {
				continue;
			}

			if (strtolower($arreach['name']) == 'style') {
				$origValue = $arreach['value'];
				$value = $this->safeCssFilterAttr($origValue);

				if (empty($value)) continue;

				$arreach['value'] = $value;
				$arreach['whole'] = str_replace($origValue, $value, $arreach['whole']);
			}

			if (!is_array($current)) {
				$attr2 .= ' '.$arreach['whole'];
			} else {
				$ok = true;
				
				foreach ($current as $currkey => $currval) {
					if (!$this->checkAttrVal($arreach['value'], $arreach['vless'], $currkey, $currval)) {
						$ok = false;
						break;
					}
				}

				if ($ok) {
					$attr2 .= ' '.$arreach['whole'];
				}
			}
		}

		$attr2 = preg_replace('/[<>]/', '', $attr2);

		return "<$element$attr2$xhtmlSlash>";
	}

	/**
	 * Builds an attribute list from string containing attributes.
	 *
	 * @param  string $attr
	 * @return array 
	 */
	protected function hair($attr)
	{
		$attrarr = array();
		$mode = 0;
		$attrname = '';
		$uris = array('xmlns', 'profile', 'href', 'src', 'cite', 'classid', 
						'codebase', 'data', 'usemap', 'longdesc', 'action');

		// Loop through the whole attribute list.
		while (strlen($attr) != 0) {
			$working = 0;

			switch ($mode) {
				// Attribute name
				case 0:
					if (preg_match('/^([-a-zA-Z:]+)/', $attr, $match)) {
						$attrname = $match[1];
						$working  = $mode = 1;
						$attr     = preg_replace('/^[-a-zA-Z:]+/', '', $attr);
					}
				break;

				// Equals sign or valueless
				case 1:
					// Equals sign
					if (preg_match('/^\s*=\s*/', $attr)) {
						$working = 1;
						$mode    = 2;
						$attr    = preg_replace('/^\s*=\s*/', '', $attr);
						
						break;
					}

					// Valueless
					if (preg_match('/^\s+/', $attr)) {
						$working = 1;
						$mode = 0;
						
						if (array_key_exists($attrname, $attrarr) === false) {
							$attrarr[$attrname] = array(
								'name'  => $attrname,
								'value' => '',
								'whole' => $attrname,
								'vless' => 'y'
							);
						}
						
						$attr = preg_replace('/^\s+/', '', $attr);
					}
				break;

				// Attribute value
				case 2:
					if (preg_match('%^"([^"]*)"(\s+|/?$)%', $attr, $match)) {
						$thisval = $match[1];
						
						if (in_array(strtolower($attrname), $uris)) {
							$thisval = $this->badProtocol($thisval);
						}

						if (array_key_exists($attrname, $attrarr) === false) {
							$attrarr[$attrname] = array(
								'name'  => $attrname,
								'value' => $thisval,
								'whole' => "$attrname=\"$thisval\"",
								'vless' => 'n'
							);
						}
						
						$working = 1;
						$mode    = 0;
						$attr    = preg_replace('/^"[^"]*"(\s+|$)/', '', $attr);
						
						break;
					}

					if (preg_match("%^'([^']*)'(\s+|/?$)%", $attr, $match)) {
						$thisval = $match[1];

						if (in_array(strtolower($attrname), $uris)) {
							$thisval = $this->badProtocol($thisval);
						}

						if (array_key_exists($attrname, $attrarr) === false) {
							$attrarr[$attrname] = array(
								'name'  => $attrname,
								'value' => $thisval,
								'whole' => "$attrname='$thisval'",
								'vless' => 'n'
							);
						}
						
						$working = 1;
						$mode    = 0;
						$attr    = preg_replace("/^'[^']*'(\s+|$)/", '', $attr);
						
						break;
					}

					if (preg_match("%^([^\s\"']+)(\s+|/?$)%", $attr, $match)) {
						$thisval = $match[1];
						
						if (in_array(strtolower($attrname), $uris)) {
							$thisval = $this->badProtocol($thisval);
						}

						if (array_key_exists($attrname, $attrarr) === false) {
							$attrarr[$attrname] = array(
								'name'  => $attrname, 
								'value' => $thisval, 
								'whole' => "$attrname=\"$thisval\"", 
								'vless' => 'n'
							);
						}

						$working = 1;
						$mode    = 0;
						$attr    = preg_replace("%^[^\s\"']+(\s+|$)%", '', $attr);
					}
				break;
			}

			if ($working == 0) {
				$attr = $this->htmlError($attr);
				$mode = 0;
			}
		}

		if ($mode == 1 && array_key_exists($attrname, $attrarr) === false) {
			$attrarr[$attrname] = array (
				'name'  => $attrname, 
				'value' => '', 
				'whole' => $attrname, 
				'vless' => 'y'
			);
		}

		return $attrarr;
	}

	/**
	 * Performs different checks for attribute values.
	 *
	 * @param  string $value
	 * @param  string $vless
	 * @param  string $checkname
	 * @param  mixed  $checkvalue
	 * @return bool
	 */
	protected function checkAttrVal($value, $vless, $checkname, $checkvalue)
	{
		switch (strtolower($checkname)) {
			case 'maxlen' :
				if (strlen($value) > $checkvalue) return false;

			case 'minlen' :
				if (strlen($value) < $checkvalue) return false;

			case 'maxval' :
				if (!preg_match('/^\s{0,6}[0-9]{1,6}\s{0,6}$/', $value)) return false;
				if ($value > $checkvalue) return false;

			case 'minval' :
				if (!preg_match('/^\s{0,6}[0-9]{1,6}\s{0,6}$/', $value)) return false;
				if ($value < $checkvalue) return false;

			case 'valueless' :
				if (strtolower($checkvalue) != $vless) return false;
		}

		return true;
	}

	/**
	 * Sanitize string from bad protocols.
	 *
	 * @param  string $string
	 * @return string
	 */
	protected function badProtocol($string)
	{
		$string = $this->noNull($string);
		$iterations = 0;

		do {
			$originalString = $string;
			$string = $this->badProtocolOnce($string);
		} while ($originalString != $string && ++$iterations < 6);

		if ($originalString != $string) return '';

		return $string;
	}

	/**
	 * Sanitizes content from bad protocols and other characters.
	 *
	 * @param  string $string
	 * @return string
	 */
	protected function badProtocolOnce($string, $count = 1)
	{
		$string2 = preg_split( '/:|&#0*58;|&#x0*3a;/i', $string, 2 );
		
		if (isset($string2[1]) && ! preg_match('%/\?%', $string2[0])) {
			$string = trim( $string2[1] );
			$protocol = $this->badProtocolOnce2($string2[0]);
			
			if ('feed:' == $protocol) {
				if ($count > 2) return '';
				
				$string = $this->badProtocolOnce($string, ++$count);
				
				if (empty($string)) return $string;
			}

			$string = $protocol . $string;
		}

		return $string;
	}

	/**
	 * Callback for Kses::badProtocolOnce() regular expression.
	 *
	 * @param  string $string
	 * @return string
	 */
	protected function badProtocolOnce2($string)
	{
		$string2 = $this->decodeEntities($string);
		$string2 = preg_replace('/\s/', '', $string2);
		$string2 = $this->noNull($string2);
		$string2 = strtolower($string2);

		$allowed = false;

		foreach ((array) $this->allowedProtocols as $protocol) {
			if (strtolower($protocol) == $string2 ) {
				$allowed = true;
				break;
			}
		}

		return $allowed ? "$string2:" : '';
	}

	/**
	 * Handles parsing errors in Kses::hair().
	 *
	 * @param  string $string
	 * @return string
	 */
	protected function htmlError($string)
	{
		return preg_replace('/^("[^"]*("|$)|\'[^\']*(\'|$)|\S)*\s*/', '', $string);
	}

	/**
	 * Convert all entities to their character counterparts.
	 *
	 * @param  string $string
	 * @return string
	 */
	protected function decodeEntities($string)
	{
		$string = preg_replace_callback('/&#([0-9]+);/', function($match) {
			return chr($match[1]);
		}, $string);
		
		$string = preg_replace_callback('/&#[Xx]([0-9A-Fa-f]+);/', function($match) {
			return chr(hexdec($match[1]));
		}, $string);

		return $string;
	}

	/**
	 * Inline CSS filter.
	 *
	 * @param  string $css
	 * @return string
	 */
	protected function safeCssFilterAttr($css) 
	{
		$css = $this->noNull($css);
		$css = str_replace(array("\n","\r","\t"), '', $css);

		if (preg_match('%[\\(&=}]|/\*%', $css)) return '';

		$cssArray = explode(';', trim($css));

		if (empty($this->allowedCss)) return $css;

		$css = '';

		foreach ($cssArray as $cssItem) {
			if ($cssItem == '') continue;
			
			$cssItem = trim($cssItem);
			$found = false;
			
			if (strpos($cssItem, ':') === false) {
				$found = true;
			} else {
				$parts = explode( ':', $cssItem);
				
				if (in_array(trim($parts[0]), $this->allowedCss)) {
					$found = true;
				}
			}
			if ($found) {
				if ($css != '') $css .= ';';

				$css .= $cssItem;
			}
		}

		return $css;
	}

	/**
	 * Converts and fixes HTML entities.
	 * Only accepts valid named entity references, which are finite,
 	 * case-sensitive, and highly scrutinized by HTML and XML validators.
	 *
	 * @param  string $string
	 * @return string
	 */
	protected function normalizeEntities($string) 
	{
		$string = str_replace('&', '&amp;', $string);

		$string = preg_replace_callback('/&amp;([A-Za-z]{2,8}[0-9]{0,2});/', array($this, 'namedEntities'),  $string);
		$string = preg_replace_callback('/&amp;#(0*[0-9]{1,7});/', 			 array($this, 'namedEntities2'), $string);
		$string = preg_replace_callback('/&amp;#[Xx](0*[0-9A-Fa-f]{1,6});/', array($this, 'namedEntities3'), $string);

		return $string;
	}

	/**
	 * Callback for Kses::normalizeEntities() regular expression.
	 * Only accepts valid named entity references, which are finite,
 	 * case-sensitive, and highly scrutinized by HTML and XML validators.
	 *
	 * @param  array $matches
	 * @return string
	 */
	protected function namedEntities($matches)
	{
		if (empty($matches[1])) return '';

		$i = $matches[1];

		return in_array($i, $this->allowedEntities) ? "&$i;" : "&amp;$i;";
	}

	/**
	 * Callback for Kses::normalizeEntities() regular expression.
	 * Only accept 16-bit values and nothing more for &#number; entities.
	 *
	 * @param  array $matches
	 * @return string
	 */
	protected function namedEntities2($matches)
	{
		if (empty($matches[1])) return '';

		$i = $matches[1];
		
		if ($this->validUnicode($i)) {
			$i = str_pad(ltrim($i,'0'), 3, '0', \STR_PAD_LEFT);
			$i = "&#$i;";
		} else {
			$i = "&amp;#$i;";
		}

		return $i;
	}

	/**
	 * Callback for Kses::normalizeEntities() regular expression.
	 * Only accept valid Unicode numeric entities in hex form.
	 *
	 * @param  array  $matches
	 * @return string
	 */
	protected function namedEntities3($matches)
	{
		if (empty($matches[1])) return '';

		$hexchars = $matches[1];

		return $this->validUnicode(hexdec($hexchars)) ? '&#x'.ltrim($hexchars,'0').';' : "&amp;#x$hexchars;";
	}

	/**
	 * Removes any invalid control characters and the '\0' string.
	 *
	 * @param  string $string
	 * @return string
	 */
	protected function noNull($string)
	{
		$string = preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F]/', '', $string);
		$string = preg_replace('/(\\\\0)+/', '', $string);

		return $string;
	}

	/**
	 * Removes the HTML JavaScript entities.
	 *
	 * @param  string $string
	 * @return string
	 */
	protected function jsEntities($string) 
	{
		return preg_replace('%&\s*\{[^}]*(\}\s*;?|$)%', '', $string);
	}

	/**
	 * Strips slashes from in front of quotes.
	 *
	 * @param  string $string
	 * @return string
	 */
	protected function stripslashes($string) 
	{
		return preg_replace('%\\\\"%', '"', $string);
	}

	/**
	 * Determine if a Unicode value is valid.
	 *
	 * @param  int  $i
	 * @return bool
	 */
	protected function validUnicode($i) 
	{
		return  ($i == 0x9 || $i == 0xa || $i == 0xd ||
				($i >= 0x20 && $i <= 0xd7ff) ||
				($i >= 0xe000 && $i <= 0xfffd) ||
				($i >= 0x10000 && $i <= 0x10ffff));
	}

	/**
	 * Set the allowed tags.
	 * 
	 * @param  array $tags
	 * @return self
	 */
	public function setAllowedTags(array $tags)
	{
		$this->allowedTags = $tags;

		return $this;
	}

	/**
	 * Set the allowed entities.
	 * 
	 * @param  array $entities
	 * @return self
	 */
	public function setAllowedEntities(array $entities)
	{
		$this->allowedEntities = $entities;
		
		return $this;
	}

	/**
	 * Set the allowed protocols.
	 * 
	 * @param  array $protocols
	 * @return self
	 */
	public function setAllowedProtocols(array $protocols)
	{
		$this->allowedProtocols = $protocols;
		
		return $this;
	}

	/**
	 * Set the allowed css.
	 * 
	 * @param  array $css
	 * @return self
	 */
	public function setAllowedCss(array $css)
	{
		$this->allowedCss = $css;
		
		return $this;
	}

	/**
	 * Get the defaut allowed tags.
	 * 
	 * @return array
	 */
	public function getDefaultAllowedTags()
	{
		return array(
		'address' => array(),
		'a' => array(
			'href'   => true,
			'rel'    => true,
			'rev'    => true,
			'name'   => true,
			'target' => true,
		),
		'abbr' => array(),
		'acronym' => array(),
		'area' => array(
			'alt'    => true,
			'coords' => true,
			'href'   => true,
			'nohref' => true,
			'shape'  => true,
			'target' => true,
		),
		'article' => array(
			'align' => true,
			'dir'   => true,
			'lang'  => true,
		),
		'aside' => array(
			'align' => true,
			'dir' => true,
			'lang' => true,
		),
		'b' => array(),
		'big' => array(),
		'blockquote' => array(
			'cite' => true,
			'lang' => true,
		),
		'br' => array(),
		'button' => array(
			'disabled' => true,
			'name'     => true,
			'type'     => true,
			'value'    => true,
		),
		'caption' => array(
			'align' => true,
		),
		'cite' => array(
			'dir'  => true,
			'lang' => true,
		),
		'code' => array(),
		'col' => array(
			'align'   => true,
			'char'    => true,
			'charoff' => true,
			'span'    => true,
			'dir'     => true,
			'valign'  => true,
			'width'   => true,
		),
		'del' => array(
			'datetime' => true,
		),
		'dd' => array(),
		'dfn' => array(),
		'details' => array(
			'align' => true,
			'dir'   => true,
			'lang'  => true,
			'open'  => true,
		),
		'div' => array(
			'align' => true,
			'dir'   => true,
			'lang'  => true,
		),
		'dl' => array(),
		'dt' => array(),
		'em' => array(),
		'fieldset' => array(),
		'figure' => array(
			'align' => true,
			'dir'   => true,
			'lang'  => true,
		),
		'figcaption' => array(
			'align' => true,
			'dir'   => true,
			'lang'  => true,
		),
		'font' => array(
			'color' => true,
			'face'  => true,
			'size'  => true,
		),
		'footer' => array(
			'align' => true,
			'dir'   => true,
			'lang'  => true,
		),
		'form' => array(
			'action'  => true,
			'accept'  => true,
			'accept-charset' => true,
			'enctype' => true,
			'method'  => true,
			'name'    => true,
			'target'  => true,
		),
		'h1' => array(
			'align' => true,
		),
		'h2' => array(
			'align' => true,
		),
		'h3' => array(
			'align' => true,
		),
		'h4' => array(
			'align' => true,
		),
		'h5' => array(
			'align' => true,
		),
		'h6' => array(
			'align' => true,
		),
		'header' => array(
			'align' => true,
			'dir'   => true,
			'lang'  => true,
		),
		'hgroup' => array(
			'align' => true,
			'dir'   => true,
			'lang'  => true,
		),
		'hr' => array(
			'align'   => true,
			'noshade' => true,
			'size'    => true,
			'width'   => true,
		),
		'i' => array(),
		'img' => array(
			'alt'      => true,
			'align'    => true,
			'border'   => true,
			'height'   => true,
			'hspace'   => true,
			'longdesc' => true,
			'vspace'   => true,
			'src'      => true,
			'usemap'   => true,
			'width'    => true,
		),
		'ins' => array(
			'datetime' => true,
			'cite'     => true,
		),
		'kbd' => array(),
		'label'   => array(
			'for' => true,
		),
		'legend' => array(
			'align' => true,
		),
		'li' => array(
			'align' => true,
			'value' => true,
		),
		'map' => array(
			'name' => true,
		),
		'mark' => array(),
		'menu' => array(
			'type' => true,
		),
		'nav' => array(
			'align' => true,
			'dir'   => true,
			'lang'  => true,
		),
		'p' => array(
			'align' => true,
			'dir'   => true,
			'lang'  => true,
		),
		'pre' => array(
			'width' => true,
		),
		'q' => array(
			'cite' => true,
		),
		's' => array(),
		'samp' => array(),
		'span' => array(
			'dir'   => true,
			'align' => true,
			'lang'  => true,
		),
		'section' => array(
			'align' => true,
			'dir'   => true,
			'lang'  => true,
		),
		'small' => array(),
		'strike' => array(),
		'strong' => array(),
		'sub' => array(),
		'summary' => array(
			'align' => true,
			'dir'   => true,
			'lang'  => true,
		),
		'sup' => array(),
		'table' => array(
			'align'   => true,
			'bgcolor' => true,
			'border'  => true,
			'cellpadding' => true,
			'cellspacing' => true,
			'dir'     => true,
			'rules'   => true,
			'summary' => true,
			'width'   => true,
		),
		'tbody' => array(
			'align'   => true,
			'char'    => true,
			'charoff' => true,
			'valign'  => true,
		),
		'td' => array(
			'abbr'    => true,
			'align'   => true,
			'axis'    => true,
			'bgcolor' => true,
			'char'    => true,
			'charoff' => true,
			'colspan' => true,
			'dir'     => true,
			'headers' => true,
			'height'  => true,
			'nowrap'  => true,
			'rowspan' => true,
			'scope'   => true,
			'valign'  => true,
			'width'   => true,
		),
		'textarea' => array(
			'cols' => true,
			'rows' => true,
			'disabled' => true,
			'name'     => true,
			'readonly' => true,
		),
		'tfoot' => array(
			'align'   => true,
			'char'    => true,
			'charoff' => true,
			'valign'  => true,
		),
		'th' => array(
			'abbr'    => true,
			'align'   => true,
			'axis'    => true,
			'bgcolor' => true,
			'char'    => true,
			'charoff' => true,
			'colspan' => true,
			'headers' => true,
			'height'  => true,
			'nowrap'  => true,
			'rowspan' => true,
			'scope'   => true,
			'valign'  => true,
			'width'   => true,
		),
		'thead' => array(
			'align'   => true,
			'char'    => true,
			'charoff' => true,
			'valign'  => true,
		),
		'title' => array(),
		'tr' => array(
			'align'   => true,
			'bgcolor' => true,
			'char'    => true,
			'charoff' => true,
			'valign'  => true,
		),
		'tt' => array(),
		'u' => array(),
		'ul' => array(
			'type' => true,
		),
		'ol' => array(
			'start' => true,
			'type'  => true,
		),
		'var' => array(),
		);
	}

	/**
	 * Get the defaut allowed entities.
	 * 
	 * @return array
	 */
	public function getDefaultAllowedEntities()
	{
		return array(
			'nbsp',    'iexcl',  'cent',    'pound',  'curren', 'yen',
			'brvbar',  'sect',   'uml',     'copy',   'ordf',   'laquo',
			'not',     'shy',    'reg',     'macr',   'deg',    'plusmn',
			'acute',   'micro',  'para',    'middot', 'cedil',  'ordm',
			'raquo',   'iquest', 'Agrave',  'Aacute', 'Acirc',  'Atilde',
			'Auml',    'Aring',  'AElig',   'Ccedil', 'Egrave', 'Eacute',
			'Ecirc',   'Euml',   'Igrave',  'Iacute', 'Icirc',  'Iuml',
			'ETH',     'Ntilde', 'Ograve',  'Oacute', 'Ocirc',  'Otilde',
			'Ouml',    'times',  'Oslash',  'Ugrave', 'Uacute', 'Ucirc',
			'Uuml',    'Yacute', 'THORN',   'szlig',  'agrave', 'aacute',
			'acirc',   'atilde', 'auml',    'aring',  'aelig',  'ccedil',
			'egrave',  'eacute', 'ecirc',   'euml',   'igrave', 'iacute',
			'icirc',   'iuml',   'eth',     'ntilde', 'ograve', 'oacute',
			'ocirc',   'otilde', 'ouml',    'divide', 'oslash', 'ugrave',
			'uacute',  'ucirc',  'uuml',    'yacute', 'thorn',  'yuml',
			'quot',    'amp',    'lt',      'gt',     'apos',   'OElig',
			'oelig',   'Scaron', 'scaron',  'Yuml',   'circ',   'tilde',
			'ensp',    'emsp',   'thinsp',  'zwnj',   'zwj',    'lrm',
			'rlm',     'ndash',  'mdash',   'lsquo',  'rsquo',  'sbquo',
			'ldquo',   'rdquo',  'bdquo',   'dagger', 'Dagger', 'permil',
			'lsaquo',  'rsaquo', 'euro',    'fnof',   'Alpha',  'Beta',
			'Gamma',   'Delta',  'Epsilon', 'Zeta',   'Eta',    'Theta',
			'Iota',    'Kappa',  'Lambda',  'Mu',     'Nu',     'Xi',
			'Omicron', 'Pi',     'Rho',     'Sigma',  'Tau',    'Upsilon',
			'Phi',     'Chi',    'Psi',     'Omega',  'alpha',  'beta',
			'gamma',   'delta',  'epsilon', 'zeta',   'eta',    'theta',
			'iota',    'kappa',  'lambda',  'mu',     'nu',     'xi',
			'omicron', 'pi',     'rho',     'sigmaf', 'sigma',  'tau',
			'upsilon', 'phi',    'chi',     'psi',    'omega',  'thetasym',
			'upsih',   'piv',    'bull',    'hellip', 'prime',  'Prime',
			'oline',   'frasl',  'weierp',  'image',  'real',   'trade',
			'alefsym', 'larr',   'uarr',    'rarr',   'darr',   'harr',
			'crarr',   'lArr',   'uArr',    'rArr',   'dArr',   'hArr',
			'forall',  'part',   'exist',   'empty',  'nabla',  'isin',
			'notin',   'ni',     'prod',    'sum',    'minus',  'lowast',
			'radic',   'prop',   'infin',   'ang',    'and',    'or',
			'cap',     'cup',    'int',     'sim',    'cong',   'asymp',
			'ne',      'equiv',  'le',      'ge',     'sub',    'sup',
			'nsub',    'sube',   'supe',    'oplus',  'otimes', 'perp',
			'sdot',    'lceil',  'rceil',   'lfloor', 'rfloor', 'lang',
			'rang',    'loz',    'spades',  'clubs',  'hearts', 'diams',
			'sup1',    'sup2',   'sup3',    'frac14', 'frac12', 'frac34',
			'there4',
		);
	}

	/**
	 * Get the defaut allowed protocols.
	 * 
	 * @return array
	 */
	public function getDefaultAllowedProtocols()
	{
		return array(
			'http', 'https', 'ftp', 'ftps', 'mailto', 'news', 'irc', 
			'gopher', 'nntp', 'feed', 'telnet', 'mms', 'rtsp', 'svn', 
			'tel', 'fax', 'xmpp',
		);
	}

	/**
	 * Get the defaut allowed css attributes.
	 * 
	 * @return array
	 */
	public function getDefaultAllowedCss()
	{
		return array('text-align', 'margin', 'color', 'float',
			'border', 'background', 'background-color', 'border-bottom', 'border-bottom-color',
			'border-bottom-style', 'border-bottom-width', 'border-collapse', 'border-color', 'border-left',
			'border-left-color', 'border-left-style', 'border-left-width', 'border-right', 'border-right-color',
			'border-right-style', 'border-right-width', 'border-spacing', 'border-style', 'border-top',
			'border-top-color', 'border-top-style', 'border-top-width', 'border-width', 'caption-side',
			'clear', 'cursor', 'direction', 'font', 'font-family', 'font-size', 'font-style',
			'font-variant', 'font-weight', 'height', 'letter-spacing', 'line-height', 'margin-bottom',
			'margin-left', 'margin-right', 'margin-top', 'overflow', 'padding', 'padding-bottom',
			'padding-left', 'padding-right', 'padding-top', 'text-decoration', 'text-indent', 'vertical-align',
			'width'
		);
	}
}