<?php 

namespace OAuth\UserData\Extractor;

use OAuth\UserData\Utils\ArrayUtils;
use OAuth\UserData\Utils\StringUtils;

class Vkontakte extends LazyExtractor 
{
	const FIELD_BIRTHDAY = 'birthday';
	const FIELD_GENDER = 'gender';

	public function __construct()
	{
		parent::__construct(
			self::getLoadersMap(),
			self::getNormalizersMap(),
			self::getSupportedFields()
		);
	}

    protected static function getLoadersMap()
    {
		return array_merge(self::getDefaultLoadersMap(), array(
			self::FIELD_BIRTHDAY => self::FIELD_BIRTHDAY,
			self::FIELD_GENDER => self::FIELD_GENDER,
		));
    }

    public static function getNormalizersMap()
	{
		return array_merge(self::getDefaultNormalizersMap(), array(
			self::FIELD_BIRTHDAY => self::FIELD_BIRTHDAY,
			self::FIELD_GENDER => self::FIELD_GENDER,
		));
	}

	protected static function getSupportedFields()
	{
		return array(
			self::FIELD_UNIQUE_ID,
			self::FIELD_FIRST_NAME,
			self::FIELD_LAST_NAME,
			self::FIELD_FULL_NAME,
			self::FIELD_DESCRIPTION,
			self::FIELD_LOCATION,
			self::FIELD_PROFILE_URL,
			self::FIELD_IMAGE_URL,
			self::FIELD_WEBSITES,
			self::FIELD_WEBSITE,
			self::FIELD_EXTRA,
			self::FIELD_BIRTHDAY,
			self::FIELD_GENDER
		);
	}

	public static function createProfileRequestUrl()
	{
		// See https://vk.com/dev/fields
		$fields = array(
			'uid',
			'first_name',
			'last_name',
			'sex',
			'bdate',
			'city',
			'country',
			'photo_200_orig',
			'photo_400_orig',
			'site',
			'about',
			'email',
		);

		return sprintf('users.get?v=5.23&lang=en&fields=%s', implode(',', $fields));
	}

	protected function profileLoader()
	{
		$response = json_decode($this->service->request(self::createProfileRequestUrl()), true);

		return isset($response['response'][0]) ? $response['response'][0] : array();
	}

	protected function birthdayLoader()
	{
		return $this->getExtras();
	}

	protected function genderLoader()
	{
		return $this->getExtras();
	}

	protected function uniqueIdNormalizer($data)
	{
		return isset($data['id']) ? $data['id'] : null;
	}

	protected function firstNameNormalizer($data)
	{
		return isset($data['first_name']) ? $data['first_name'] : null;
	}

	protected function lastNameNormalizer($data)
	{
		return isset($data['last_name']) ? $data['last_name'] : null;
	}

	protected function fullNameNormalizer()
	{
		return sprintf('%s %s', $this->getFirstName(), $this->getLastName());
	}

	protected function descriptionNormalizer($data)
	{
		return isset($data['about']) ? $data['about'] : null;
	}

	protected function imageUrlNormalizer($data)
	{
		if (isset($data['photo_400_orig'])) {
			return $data['photo_400_orig'];
		}

		if (isset($data['photo_200_orig'])) {
			return $data['photo_200_orig'];
		}
	}

	protected function profileUrlNormalizer($data)
	{
		$id = $this->getField(self::FIELD_UNIQUE_ID);

        if (! is_null($id)) {
            return sprintf('https://vk.com/id%s', $id);
        }
	}

	protected function locationNormalizer($data)
	{
		if (! empty($data['city']['title']) && ! empty($data['country']['title'])) {
			return $data['city']['title'].', '.$data['country']['title'];
		}

		if (! empty($data['city']['title'])) {
			return $data['city']['title'];
		}

		if (! empty($data['country']['title'])) {
			return $data['country']['title'];
		}
	}

	protected function websitesNormalizer($data)
	{
		return isset($data['site']) ? StringUtils::extractUrls($data['site']) : array();
	}

	protected function websiteNormalizer($data)
	{
		if (isset($data['site'])) {
			return $data['site'];
		}
	}

	protected function birthdayNormalizer($data)
	{
		if (! isset($data['bdate'])) {
			return;
		}
		
		$pieces = explode('.', $data['bdate']);
        
		if (strlen($pieces[0]) < 2) {
			$pieces[0] = "0".$pieces[0];
		}

		if (strlen($pieces[1]) < 2) {
			$pieces[1] = "0".$pieces[1];
		}

		return implode('-', array_reverse($pieces));
	}

	protected function genderNormalizer($data)
	{
		if (isset($data['sex'])) {
			switch ($data['sex']) {
				case '1': return 'F';
				case '2': return 'M';
			}
		}
	}

	protected function extraNormalizer($data)
	{
		return ArrayUtils::removeKeys($data, array(
			'id',
			'first_name',
			'last_name',
			'about',
			'photo_400_orig',
			'photo_200_orig',
			'city',
			'country',
			'site'
		));
	}
}
