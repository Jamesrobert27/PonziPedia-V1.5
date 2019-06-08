<?php

/**
 * Display captcha.
 *
 * @param  bool $jsLinkOnly
 * @param  bool $tagOnly
 * @return void
 */
function display_captcha($jsLinkOnly = false, $tagOnly = false)
{
	$lang = app('translator')->getLocale();
	$config = app('config')->get('services.recaptcha');
	
	$captcha = new \Hazzard\Support\Recaptcha($config['private_key'], $config['public_key'], $lang);

	if ($jsLinkOnly) {
		echo '<script src="'.$captcha->getJsLink().'" async defer></script>'."\n";
	} else {
		echo $captcha->display(array(), $tagOnly);
	}
}

/**
 * Display captcha tag only.
 *
 * @return void
 */
function display_captcha_tag()
{
	return display_captcha(false, true);
}

/**
 * Output JSON formated message.
 *
 * @param  string  $message
 * @param  bool    $success
 * @return void
 */
function json_message($message = null, $success = true)
{
	header('Content-Type: application/json');

	echo json_encode(compact('message', 'success'));

	exit;
}

/**
 * Redirect to given URL.
 *
 * @param  string  $url
 * @return void
 */
function redirect_to($url, array $flash = array())
{
	foreach ($flash as $key => $value) {
		app('session')->flash($key, $value);
	}

	if (headers_sent()) {
		echo '<html><body onload="redirect_to(\''.$url.'\');"></body>'.
			'<script type="text/javascript">function redirect_to(url) {window.location.href = url}</script>'.
			'</body></html>';
	} else {
		header('Location:' . $url);
	}

	exit;
}

/**
 * Get the current url.
 *
 * @return string
 */
function get_current_url()
{
	$https = !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off';
    return ($https ? 'https://' : 'http://') . (!empty($_SERVER['REMOTE_USER']) ? 
		$_SERVER['REMOTE_USER'].'@' : '') . (isset($_SERVER['HTTP_HOST']) ? 
		$_SERVER['HTTP_HOST'] : ($_SERVER['SERVER_NAME'] . 
		($https && $_SERVER['SERVER_PORT'] === 443 || $_SERVER['SERVER_PORT'] === 80 ? '' : 
		':'.$_SERVER['SERVER_PORT']))).$_SERVER['REQUEST_URI'];
}

/**
 * Check if is an ajax request.
 *
 * @param  string  $url
 * @return void
 */
function is_ajax_request()
{
	return (!empty($_SERVER['HTTP_X_REQUESTED_WITH'])
        && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest');
}

/**
 * Get the url to the asset file
 *
 * @param   string  $path
 * @return  string
 */
function asset_url($path = '')
{
	return app()->url("assets/{$path}");
}

/**
 * Get Gravatar URL for a specified email address.
 *
 * @param  string  $email
 * @param  string  $size
 * @param  string  $default  Default imageset to use [ 404 | mm | identicon | monsterid | wavatar ]
 * @param  string  $rating   Maximum rating (inclusive) [ g | pg | r | x ]
 * @return string
 */
function get_gravatar($email, $size = 80, $default = 'mm', $rating = 'g')
{   
    $url = 'http://www.gravatar.com/avatar/';
    
    $url .= md5(strtolower(trim($email)));
    
    $url .= "?s=$size&d=$default&r=$rating";

    return $url;
}

/**
 * Get the root Facade application instance.
 *
 * @param  string  $make
 * @return mixed
 */
function app($make = null)
{
	if (!is_null($make)) {
		return app()->make($make);
	}

	return Hazzard\Support\Facades\Facade::getFacadeApplication();
}

/**
 * Get the path to the application folder.
 *
 * @param   string  $path
 * @return  string
 */
function app_path($path = '')
{
	return app('path').($path ? '/'.$path : $path);
}

/**
 * Get the path to the storage folder.
 *
 * @param   string $path
 * @return  string
 */
function storage_path($path = '')
{
	return app('path.storage').($path ? '/'.$path : $path);
}

/**
 * Get the value from the POST array.
 *
 * @param	string	$field
 * @param	string	$default
 * @param	bool	$escape
 * @return	string
 */
function set_value($field, $default = '', $escape = true)
{
	if (isset($_POST[$field])) {
		return $escape ? escape($_POST[$field]) : $_POST[$field];
	}

	return $default;
}

/**
 * Get te selected value of <select> from the POST array.
 *
 * @param	string  $field
 * @param	string  $value
 * @param	bool    $default
 * @return	string
 */
function set_select($field, $value = '', $default = false)
{
	if (isset($_POST[$field]) && $_POST[$field] == (string) $value) {
		return ' selected="selected"';
	}

	return $default ? ' selected="selected"' : '';
}

/**
 * Get the selected value of a checkbox input from the POST array.
 *
 * @param	string  $field
 * @param	string  $value
 * @param	bool    $default
 * @return	string
 */
function set_checkbox($field = '', $value, $default = false)
{
	if (isset($_POST[$field]) && $_POST[$field] == (string) $value) {
		return ' checked="checked"';
	}

	return $default ? ' checked="checked"' : '';
}

/**
 * Get the selected value of a radio input from the POST array.
 *
 * @param	string  $field
 * @param	string  $value
 * @param	bool    $default
 * @return	string
 */
function set_radio($field, $value = '', $default = false)
{
	if (isset($_POST[$field]) && $_POST[$field] == (string) $value) {
		return ' checked="checked"';
	}

	return $default ? ' checked="checked"' : '';
}

/**
 * Escape HTML entities in a string.
 *
 * @param  string  $value
 * @return string
 */
function escape($value)
{
	return trim(htmlentities($value, ENT_QUOTES, 'UTF-8'));
}

/**
 * Echo the CSRF input.
 *
 * @return mixed
 */
function csrf_input()
{
	echo '<input type="hidden" name="_token" value="'.csrf_token().'">';
}

/**
 * Get the CSRF token value.
 *
 * @return string
 */
function csrf_token()
{
	return app('session')->token();
}

/**
 * Check if input token match session token.
 *
 * @return string
 */
function csrf_filter() 
{
	if (app('config')->get('app.csrf')) return true;
	
	$check = isset($_POST['_token']) && $_POST['_token'] == csrf_token();
	
	app('session')->regenerateToken();

	return $check;
}

/**
 * Translate and echo the given message.
 *
 * @param  string  $id
 * @param  array   $parameters
 * @param  string  $locale
 * @return string
 */
function _e($id, $parameters = array(), $locale = null)
{
	echo app('translator')->trans($id, $parameters, $locale);
}

/**
 * Translate the given message.
 *
 * @param  string  $id
 * @param  array   $parameters
 * @param  string  $locale
 * @return string
 */
function trans($id, $parameters = array(), $locale = null)
{
	return app('translator')->trans($id, $parameters, $locale);
}

/**
 * Sanitizes a string key.
 *
 * @param string $key String key
 * @return string Sanitized key
 */
function sanitize_key($key)
{
	$key = strtolower($key);
	$key = preg_replace('/[^a-z0-9_\-]/', '', $key);

	return $key;
}

/**
 * Decode value only if it was encoded to JSON.
 *
 * @param  string  $original
 * @param  bool    $assoc
 * @return mixed
 */
function maybe_decode($original, $assoc = true)
{
	if (is_numeric($original)) return $original;
	
	$data = json_decode($original, $assoc);
	
	return (is_object($data) || is_array($data)) ? $data : $original;
}

/**
 * Encode data to JSON, if needed.
 *
 * @param  mixed  $data
 * @return mixed
 */
// function maybe_encode($data)
// {
// 	if (is_array($data) || is_object($data)) {
// 		return json_encode($data);
// 	}
	
// 	return $data;
// }

/**
 * Check value to find if it was serialized.
 *
 * @param  string  $data
 * @param  bool    $strict
 * @return bool
 */
function is_serialized( $data, $strict = true ) 
{
	if (!is_string($data)) return false;
	
	$data = trim($data);
 	
 	if ('N;' == $data) return true;
	if (strlen($data) < 4) return false;
	if (':' !== $data[1]) return false;
	
	if ($strict) {
		$lastc = substr($data, -1);
		
		if (';' !== $lastc && '}' !== $lastc) {
			return false;
		}
	} else {
		$semicolon = strpos($data, ';');
		$brace     = strpos($data, '}');
		
		if (false === $semicolon && false === $brace) return false;
		if (false !== $semicolon && $semicolon < 3) return false;
		if (false !== $brace && $brace < 4) return false;
	}
	
	$token = $data[0];
	
	switch ($token) {
		case 's' :
			if ($strict) {
				if ('"' !== substr($data, -2, 1)) {
					return false;
				}
			} elseif (false === strpos($data, '"')) {
				return false;
			}
		case 'a' :
		case 'O' :
			return (bool) preg_match("/^{$token}:[0-9]+:/s", $data);
		case 'b' :
		case 'i' :
		case 'd' :
			$end = $strict ? '$' : '';
			return (bool) preg_match("/^{$token}:[0-9.E-]+;$end/", $data);
	}

	return false;
}

/**
 * Unserialize value only if it was serialized.
 *
 * @param  string $original
 * @return mixed
 */
function maybe_unserialize($original) 
{
	if (is_serialized($original)) {
		return @unserialize( $original );
	}

	return $original;
}

/**
 * Serialize data, if needed.
 *
 * @param  mixed  $data
 * @return mixed
 */
function maybe_serialize($data) 
{
	if (is_array($data) || is_object($data)) {
		return serialize($data);
	}

	return $data;
}

/**
 * Return the first element in an array passing a given truth test.
 *
 * @param  array    $array
 * @param  Closure  $callback
 * @param  mixed    $default
 * @return mixed
 */
function array_first($array, $callback, $default = null)
{
	foreach ($array as $key => $value) {
		if (call_user_func($callback, $key, $value)) return $value;
	}

	return value($default);
}

/**
 * Get an item from an array using "dot" notation.
 *
 * @param  array   $array
 * @param  string  $key
 * @param  mixed   $default
 * @return mixed
 */
function array_get($array, $key, $default = null)
{
	if (is_null($key)) return $array;

	if (isset($array[$key])) return $array[$key];

	foreach (explode('.', $key) as $segment) {
		if (!is_array($array) || ! array_key_exists($segment, $array)) {
			return $default;
		}

		$array = $array[$segment];
	}

	return $array;
}

/**
 * Set an array item to a given value using "dot" notation.
 *
 * If no key is given to the method, the entire array will be replaced.
 *
 * @param  array   $array
 * @param  string  $key
 * @param  mixed   $value
 * @return array
 */
function array_set(&$array, $key, $value)
{
	if (is_null($key)) return $array = $value;

	$keys = explode('.', $key);

	while (count($keys) > 1) {
		$key = array_shift($keys);

		if ( ! isset($array[$key]) || ! is_array($array[$key])) {
			$array[$key] = array();
		}

		$array =& $array[$key];
	}

	$array[array_shift($keys)] = $value;

	return $array;
}

/**
 * Remove an array item from a given array using "dot" notation.
 *
 * @param  array   $array
 * @param  string  $key
 * @return void
 */
function array_forget(&$array, $key)
{
	$keys = explode('.', $key);

	while (count($keys) > 1) {
		$key = array_shift($keys);

		if ( ! isset($array[$key]) || ! is_array($array[$key])) {
			return;
		}

		$array =& $array[$key];
	}

	unset($array[array_shift($keys)]);
}

/**
 * Get the first element of an array.
 *
 * @param  array  $array
 * @return mixed
 */
function head($array)
{
	return reset($array);
}

/**
 * Determine if a given string matches a given pattern.
 *
 * @param  string  $pattern
 * @param  string  $value
 * @return bool
 */
function str_is($pattern, $value)
{
	return Hazzard\Support\Str::is($pattern, $value);
}

/**
 * Determine if a given string contains a given substring.
 *
 * @param  string        $haystack
 * @param  string|array  $needles
 * @return bool
 */
function str_contains($haystack, $needles)
{
	return Hazzard\Support\Str::contains($haystack, $needles);
}

/**
 * Convert a value to studly caps case.
 *
 * @param  string  $value
 * @return string
 */
function studly_case($value)
{
	return Hazzard\Support\Str::studly($value);
}

/**
 * Convert a string to snake case.
 *
 * @param  string  $value
 * @param  string  $delimiter
 * @return string
 */
function snake_case($value, $delimiter = '_')
{
	return Hazzard\Support\Str::snake($value, $delimiter);
}

/**
 * Determine if a given string starts with a given substring.
 *
 * @param  string 		 $haystack
 * @param  string|array  $needle
 * @return bool
 */
function starts_with($haystack, $needles)
{
	return Hazzard\Support\Str::contains($haystack, $needles);
}

/**
 * Generate a "random" alpha-numeric string.
 *
 * @param  int     $length
 * @return string
 */
function str_random($length = 16)
{
	return Hazzard\Support\Str::random($length);
}

if (!function_exists('mb_strlen')) 
{
	/**
	 * Get string length.
	 *
	 * @param  string  $string
	 * @return string
	 */
	function mb_strlen($string)
	{
		return strlen($string);
	}
}

if (!function_exists('mb_substr')) 
{
	/**
	 * Get part of string.
	 *
	 * @param  string  $string
	 * @param  int     $start
	 * @param  int     $length
	 * @return string
	 */
	function mb_substr($string, $start, $length)
	{
		return substr($string, $start, $length);
	}
}

/**
 * Return the given object.
 *
 * @param  mixed  $object
 * @return mixed
 */
function with($object)
{
	return $object;
}

/**
 * Return ajax comments view.
 * 
 * @param  string  $page
 * @param  string  $pageTitle
 * @param  string  $pageUrl
 * @return string
 */
function ajax_comments($page = null, $pageTitle = null, $pageUrl = null) 
{
	$view = app('view')->make('comments');
	
	if ($page) {
		$view->with('page', $page);
	}

	if ($pageTitle) {
		$view->with('page_title', $pageTitle);
	}

	if ($pageUrl) {
		$view->with('page_title', $pageUrl);
	}

	return $view->render();
}

/**
 * Convert a value to non-negative integer.
 * 
 * @param  mixed $value
 * @return int
 */
function absint($value) 
{
	return abs(intval($value));
}

/**
 * Convert plaintext URI to HTML links.
 * 
 * Code from WordPress 
 * https://github.com/WordPress/WordPress/blob/master/wp-includes/formatting.php
 *
 * @param  string $text
 * @return string
 */
function make_clickable($text)
{
	$urlPattern = '~
		([\\s(<.,;:!?])
		(
			[\\w]{1,20}+://
			(?=\S{1,2000}\s)
			[\\w\\x80-\\xff#%\\~/@\\[\\]*(+=&$-]*+
			(?:
				[\'.,;:!?)]
				[\\w\\x80-\\xff#%\\~/@\\[\\]*(+=&$-]++
			)*
		)
		(\)?)
	~xS';
	$ftpPattern   = '#([\s>])((www|ftp)\.[\w\\x80-\\xff\#$%&~/.\-;:=,?@\[\]+]+)#is';
	$emailPattern = '#([\s>])([.0-9a-z_+-]+)@(([0-9a-z-]+\.)+[0-9a-z]{2,})#i';

	$r = '';
	$textarr = preg_split('/(<[^<>]+>)/', $text, -1, PREG_SPLIT_DELIM_CAPTURE);
	$nested = 0;
	
	foreach ($textarr as $piece) {
		if (preg_match( '|^<code[\s>]|i', $piece) || preg_match('|^<pre[\s>]|i', $piece)) {
			$nested++;
		} elseif ((strtolower($piece) === '</code>' || strtolower($piece) === '</pre>') && $nested) {
			$nested--;
		}

		if ($nested || empty($piece) || ($piece[0] === '<' && !preg_match('|^<\s*[\w]{1,20}+://|', $piece))) {
			$r .= $piece;
			continue;
		}

		if (strlen($piece) > 10000) {
			foreach (_split_str_by_whitespace($piece, 2100) as $chunk) {
				if (strlen($chunk) > 2101) {
					$r .= $chunk;
				} else {
					$r .= make_clickable($chunk);
				}
			}
		} else {
			$ret = " $piece ";
			$ret = preg_replace_callback($urlPattern, '_make_url_clickable_cb', $ret);
			$ret = preg_replace_callback($ftpPattern, '_make_web_ftp_clickable_cb', $ret);
			$ret = preg_replace_callback($emailPattern, '_make_email_clickable_cb', $ret);
			$ret = substr($ret, 1, -1);

			$r .= $ret;
		}
	}

	return preg_replace('#(<a([ \r\n\t]+[^>]+?>|>))<a [^>]+?>([^>]+?)</a></a>#i', "$1$3</a>", $r);
}

/**
 * Helper for @make_clickable();
 */
function _make_url_clickable_cb($matches)
{
	$url = $matches[2];

	if ($matches[3] && strpos($url, '(') == ')') {
		$url .= $matches[3];
		$suffix = '';
	} else {
		$suffix = $matches[3];
	}

	while (substr_count($url, '(') < substr_count($url, ')')) {
		$suffix = strrchr($url, ')') . $suffix;
		$url = substr($url, 0, strrpos($url, ')'));
	}

	return $matches[1] . "<a href=\"$url\" rel=\"nofollow\">$url</a>" . $suffix;
}

/**
 * Helper for @make_clickable();
 */
function _make_web_ftp_clickable_cb($matches)
{
	$ret = '';
	$dest = $matches[2];
	$dest = 'http://' . $dest;

	if (in_array(substr($dest, -1), array('.', ',', ';', ':', ')')) === true) {
		$ret  = substr($dest, -1);
		$dest = substr($dest, 0, strlen($dest)-1);
	}

	return $matches[1] . "<a href=\"$dest\" rel=\"nofollow\">$dest</a>$ret";
}

/**
 * Helper for @make_clickable();
 */
function _make_email_clickable_cb($matches)
{
	$email = $matches[2] . '@' . $matches[3];
				return $matches[1] . "<a href=\"mailto:$email\">$email</a>";
}

/**
 * Helper for @make_clickable();
 */
function _split_str_by_whitespace($string, $goal)
{
	$chunks    = array();
	$nullspace = strtr($string, "\r\n\t\v\f ", "\000\000\000\000\000\000");

	while (strlen($nullspace) > $goal) {
		$pos = strrpos(substr($nullspace, 0, $goal + 1), "\000");

		if ($pos === false) {
			$pos = strpos($nullspace, "\000", $goal + 1);
			
			if ($pos === false) {
				break;
			}
		}

		$chunks[]  = substr($string, 0, $pos + 1);
		$string    = substr($string, $pos + 1);
		$nullspace = substr($nullspace, $pos + 1);
	}

	if ($string) {
		$chunks[] = $string;
	}

	return $chunks;
}

/**
 * Convert text equivalent of smilies to images.
 *
 * @param  string $text
 * @return string
 */
function convert_smilies($text)
{
	$smiliestrans = app('config')->get('smilies', array());
	
	$spaces = '[\r\n\t ]|\xC2\xA0|&nbsp;';

	$smiliessearch = '/(?<='.$spaces.'|^)';

	$subchar = '';
	foreach ($smiliestrans as $smiley => $img) {
		$firstchar = substr($smiley, 0, 1);
		$rest = substr($smiley, 1);

		if ($firstchar != $subchar) {
			if ($subchar != '') {
				$smiliessearch .= ')(?='.$spaces.'|$)';
				$smiliessearch .= '|(?<='.$spaces.'|^)';
			}
			
			$subchar = $firstchar;
			$smiliessearch .= preg_quote($firstchar, '/') . '(?:';
		} else {
			$smiliessearch .= '|';
		}
		$smiliessearch .= preg_quote($rest, '/');
	}

	$smiliessearch .= ')(?='.$spaces.'|$)/m';

 	//echo $smiliessearch;

	$output  = '';
	$textarr = preg_split('/(<.*>)/U', $text, -1, PREG_SPLIT_DELIM_CAPTURE);
		
	$ignoreTags  = 'code|pre|style|script|textarea';
	$ignoreBlock = '';

	for ($i = 0; $i < count($textarr); $i++) {
		$content = $textarr[$i];
		
		if ($ignoreBlock == '' && preg_match('/^<('.$ignoreTags.')>/', $content, $matches))  {
			$ignoreBlock = $matches[1];
		}

		if ($ignoreBlock == '' && strlen($content) > 0 && $content[0] != '<') {
			$content = preg_replace_callback($smiliessearch, '_translate_smiley', $content);
		}

		if ($ignoreBlock != '' && $content == '</'.$ignoreBlock.'>') {
			$ignoreBlock = '';
		}

		$output .= $content;
	}

	return $output;
}
/**
 * Helper for @convert_smilies().
 */
function _translate_smiley($matches)
{
	if (count($matches) == 0) {
		return '';
	}

	$smilies = app('config')->get('smilies');
	$smiley  = trim(reset($matches));

	if (!isset($smilies[$smiley])) {
		return '';
	}

	$img = $smilies[$smiley];
	$src = asset_url("img/smilies/$img");

	return sprintf('<img src="%s" alt="%s" class="comment-smiley">', $src, $smiley);
}
