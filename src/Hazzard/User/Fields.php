<?php namespace Hazzard\User;

use Hazzard\Auth\Auth;
use Hazzard\Translation\Translator;

class Fields {

	/**
	 * The auth instance.
	 * 
	 * @var \Hazzard\Auth\Auth
	 */
	protected $auth;

	/**
	 * The Translator instance.
	 *
	 * @var \Hazzard\Translation\Translator
	 */
	protected $translator;

	/**
	 * The fields.
	 *
	 * @var array
	 */
	protected $fields;

	/**
	 * The data for the fields.
	 *
	 * @var array
	 */
	protected $data;

	/**
	 * Create a new fields instance.
	 *
	 * @param  array  $fields
	 * @param  \Hazzard\Auth\Auth  $auth
	 * @param  \Hazzard\Translation\Translator  $translator
	 * @return void
	 */
	public function __construct(array $fields = array(), Auth $auth, Translator $translator)
	{
		$this->data = array();

		$this->auth = $auth;

		$this->fields = $fields;
		
		$this->translator = $translator;

		$this->sortFields();
	}

	/*
	 * Sort the fields by "order" option.
	 *
	 * @return void
	 */
	protected function sortFields()
	{
		$i = 1;
		foreach ($this->fields as $key => $field) {
			if (!isset($field['order'])) {
				$this->fields[$key]['order'] = $i;
			}
			$i++;
		}
		
		uasort($this->fields, function($a, $b) {
			if ((int) $a['order'] == (int) $b['order']) {
				return 0;
			}

			return ((int) $a['order'] < (int) $b['order']) ? -1 : 1;
		});
	}

	/**
	 * Check if field exists.
	 *
	 * @param  string  $key
	 * @return bool
	 */
	public function has($key)
	{
		return isset($this->fields[$key]);
	}

	/**
	 * Check if field has data.
	 *
	 * @param  string  $key
	 * @return bool
	 */
	public function hasData($key)
	{
		return isset($this->data[$key]);
	}

	/**
	 * Get single field.
	 *
	 * @param  string  $key
	 * @return array|null
	 */
	public function get($key)
	{
		if ($this->has($key)) {
			return $this->fields[$key];
		}

		return null;
	}

	/**
	 * Get all fields.
	 *
	 * @param  string|null  $assignment
	 * @return string
	 */
	public function all($assignment = null, $fixRadio = true)
	{
		$fields = array();

		foreach ($this->fields as $key => $field) {
			if (isset($field['assignment'], $field['role_id']) && in_array('user', (array) $field['assignment']) 
				&& $this->auth->check() && !in_array((int) $this->auth->user()->role_id, (array) $field['role_id'])) {
				continue;
			}

			if (!$assignment || ($assignment && isset($field['assignment']) && in_array($assignment, (array) $field['assignment']))) {
				if ($fixRadio && $field['type'] == 'radio') $key = $this->fixRadioName($key);
				$fields[$key] = $field;
			}
		}

		return $fields;
	}

	/**
	 * Build the HTML for all fields.
	 *
	 * @param  string|null  $assignment
	 * @return string
	 */
	public function build($assignment = null)
	{
		$html = '';

		foreach ($this->all($assignment, false) as $key => $field) {
			$html .= $this->buildField($key)."\n";
		}

		return $html;
	}

	/**
	 * Build the HTML for all the fields.
	 *
	 * @param  string  $key
	 * @return string
	 */
	public function buildField($key)
	{
		if (!$this->has($key)) return '';

		$field = $this->fields[$key];

		$type = $field['type'];

		$attributes = isset($field['attributes']) ? $field['attributes'] : array();

		$attributes['name'] = $this->fixRadioName($key);

		if (!isset($attributes['id'])) {
			$attributes['id'] = 'usermeta-'.$key;
		}

		$attributes['value'] = isset($attributes['value']) ? $attributes['value'] : '';

		if (in_array($type, array('text', 'textarea', 'select'))) {
			$attributes['value'] = set_value($key, $attributes['value']);

			if ($this->hasData($key)) {
				$attributes['value'] = $this->data[$key];
			}
		}

		if ($type == 'checkbox' || $type == 'radio') {
			if ($this->hasData($attributes['name'])) {
				if ((string) $this->data[$attributes['name']] == (string) $attributes['value']) {
					$attributes['checked'] = 'checked';
				}
			} elseif (set_checkbox($attributes['name'], $attributes['value']) != '') {
				$attributes['checked'] = 'checked';
			}
		}

		$id = $attributes['id'];
		$value = '';

		if ($type == 'textarea' || $type == 'select') {
			$value = $attributes['value'];
			unset($attributes['value']);
		}

		$label = !empty($field['label']) ? $field['label'] : $this->translator->trans('userfields.'.$attributes['name']);

		if ($type == 'select' && isset($attributes['options'])) {
			unset($attributes['options']);
		}
		
		$attributes = $this->buildAttributes($attributes);

		$html = $this->contentBefore($key);

		switch ($type) {
			case 'text':
				$html .= "<label for=\"{$id}\">{$label}</label>";
				$html .= "<input type=\"text\"{$attributes}>";
			break;

			case 'textarea':
				$html .= "<label for=\"{$id}\">{$label}</label>";
				$html .= "<textarea{$attributes}>{$value}</textarea>";
			break;

			case 'select':
				$html .= "<label for=\"{$id}\">{$label}</label>";
				$html .= "<select{$attributes}>";
				$html .= $this->buildSelectOptions($key);
				$html .= "</select>";
			break;

			case 'checkbox':
				$html .= "<label for=\"{$id}\">";
				$html .= "<input type=\"checkbox\"{$attributes}>";
				$html .= $label;
				$html .= "</label>";
			break;

			case 'radio':
				$html .= "<label for=\"{$id}\">";
				$html .= "<input type=\"radio\"{$attributes}>";
				$html .= $label;
				$html .= "</label>";
			break;
		}

		$html .= $this->contentAfter($key);

		return $html;
	}

	/**
	 * Build select options HTML.
	 *
	 * @param  array  $attributes
	 * @return string
	 */
	protected function buildSelectOptions($key)
	{	
		if (!isset($this->fields[$key]['attributes']['options'])) return '';

		$i = 0;
		$html = '';

		foreach ((array) $this->fields[$key]['attributes']['options'] as $option) {
			if (!is_array($option)) continue;

			$attributes = array();

			$option['value'] = isset($option['value']) ? $option['value'] : '';
			
			$attributes['value'] = $option['value'];
			
			if (isset($option['disabled'])) $attributes['disabled'] = 'disabled';

			if ($this->hasData($key)) {
				if ((string) $this->data[$key] == (string) $option['value']) {
					$attributes['selected'] = 'selected';
				}
			} elseif (set_select($key, $option['value']) != '') {
				$attributes['selected'] = 'selected';
			}

			$attributes = $this->buildAttributes($attributes);

			$i++;

			$text = isset($option['text']) ? $option['text'] : $this->translator->trans("userfields.{$key}_".$i);

			$html .= "<option{$attributes}>{$text}</option>";
		}

		return $html;
	}

	/**
	 * Get the content before a field.
	 *
	 * @param  string  $key
	 * @return string
	 */
	protected function contentBefore($key)
	{
		if ($this->has($key) && isset($this->fields[$key]['content_before'])) {
			return $this->fields[$key]['content_before'];
		}
	}

	/**
	 * Get the content after a field.
	 *
	 * @param  string  $key
	 * @return string
	 */
	protected function contentAfter($key)
	{
		if ($this->has($key) && isset($this->fields[$key]['content_after'])) {
			return $this->fields[$key]['content_after'];
		}
	}

	/**
	 * Build an HTML attribute string from an array.
	 *
	 * @param  array  $attributes
	 * @return string
	 */
	public function buildAttributes($attributes)
	{
		$html = array();

		foreach ((array) $attributes as $key => $value) {
			$element = $this->buildAttributeElement($key, $value);

			if (!is_null($element)) $html[] = $element;
		}

		return count($html) > 0 ? ' '.implode(' ', $html) : '';
	}

	/**
	 * Build a single attribute element.
	 *
	 * @param  string  $key
	 * @param  string  $value
	 * @return string
	 */
	protected function buildAttributeElement($key, $value)
	{
		if (is_numeric($key)) $key = $value;

		if (!is_null($value)) return $key.'="'.$value.'"';
	}

	protected function fixRadioName($name)
	{
		return preg_replace('/__\d+$/', '', $name);
	}

	/**
	 * Add a piece of data to the fields.
	 *
	 * @param  string|array  $key
	 * @param  mixed   $value
	 * @return \Hazzard\User\Fields
	 */
	public function with($key, $value = null)
	{
		if (is_array($key)) {
			$this->data = array_merge($this->data, $key);
		} else {
			$this->data[$key] = $value;
		}

		return $this;
	}

	/**
	 * Add data the fields.
	 *
	 * @param  array  $data
	 * @return \Hazzard\User\Fields
	 */
	public function setData(array $data = array())
	{
		$this->data = $data;
		return $this;
	}

	/**
	 * Get the string representation of the fields.
	 *
	 * @return string
	 */
	public function __toString()
	{
		return $this->build();
	}
}
