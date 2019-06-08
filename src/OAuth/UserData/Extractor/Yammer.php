<?php 

namespace OAuth\UserData\Extractor;

use OAuth\UserData\Utils\ArrayUtils;
use OAuth\UserData\Utils\StringUtils;

class Yammer extends LazyExtractor 
{
	const REQUEST_PROFILE  = 'users/current.json';

	public function __construct()
	{
		parent::__construct(
			self::getDefaultLoadersMap(),
			self::getDefaultNormalizersMap(),
			self::getSupportedFields()
		);
	}

	protected static function getSupportedFields()  
	{
		return array(
			self::FIELD_UNIQUE_ID,
			self::FIELD_USERNAME,
			self::FIELD_FULL_NAME,
			self::FIELD_FIRST_NAME,
			self::FIELD_LAST_NAME,
			self::FIELD_EMAIL,
			self::FIELD_DESCRIPTION,
			self::FIELD_LOCATION,
			self::FIELD_WEBSITES,
			self::FIELD_IMAGE_URL,
			self::FIELD_PROFILE_URL,
			self::FIELD_EXTRA
		);
	}

	protected function profileLoader()
	{
		return json_decode($this->service->request(self::REQUEST_PROFILE), true);
	}

	protected function uniqueIdNormalizer($data)
	{
		return isset($data['id']) ? $data['id'] : null;
	}

	protected function usernameNormalizer($data)
	{
		return isset($data['name']) ? $data['name'] : null;
	}

	protected function firstNameNormalizer($data)
	{
		return isset($data['first_name']) ? $data['first_name'] : null;
	}

	protected function lastNameNormalizer($data)
	{
		return isset($data['last_name']) ? $data['last_name'] : null;
	}

	protected function fullNameNormalizer($data)
	{
		return isset($data['full_name']) ? $data['full_name'] : null;
	}

	protected function emailNormalizer($data)
	{
		if (! isset($data['contact']['email_addresses'])) {
			return;
		}
		
		foreach ($data['contact']['email_addresses'] as $email) {
			if ($email['type'] === 'primary') {
				return $email['address'];
			}
		}
	}

	protected function descriptionNormalizer($data)
	{
		return isset($data['summary']) ? $data['summary'] : null;
	}

	protected function imageUrlNormalizer($data)
	{
		if (isset($data['mugshot_url_template'])) {
			return str_replace('{width}x{height}', '300x300', $data['mugshot_url_template']);
		}
	}

	protected function profileUrlNormalizer($data)
	{
		return isset($data['web_url']) ? $data['web_url'] : array();
	}

	protected function locationNormalizer($data)
	{
		return isset($data['location']) ? $data['location'] : null;
	}

	protected function websitesNormalizer($data)
	{
		return isset($data['external_urls']) ? $data['external_urls'] : array();
	}

	protected function extraNormalizer($data)
	{
		return ArrayUtils::removeKeys($data, array(
			'id',
			'name',
			'first_name',
			'last_name',
			'full_name',
			'contact',
			'summary',
			'mugshot_url_template',
			'web_url',
			'location',
			'external_urls',
		));
	}
}
