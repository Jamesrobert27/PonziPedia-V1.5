<?php 

namespace OAuth\UserData\Extractor;

use OAuth\UserData\Utils\ArrayUtils;
use OAuth\UserData\Utils\StringUtils;

class SoundCloud extends LazyExtractor 
{
	const REQUEST_PROFILE  = 'me.json';

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
	        self::FIELD_FULL_NAME,
	        self::FIELD_FIRST_NAME,
	        self::FIELD_LAST_NAME,
	        self::FIELD_USERNAME,
	        self::FIELD_DESCRIPTION,
            self::FIELD_LOCATION,
	        self::FIELD_WEBSITES,
            self::FIELD_WEBSITE,
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
        return isset($data['username']) ? $data['username'] : null;
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

    protected function descriptionNormalizer($data)
    {
        return isset($data['description']) ? $data['description'] : null;
    }

    protected function websitesNormalizer($data)
    {
        return isset($data['website']) ? $data['website'] : array();
    }

    protected function websiteNormalizer($data)
    {
        if (isset($data['website'])) {
            return $data['website'];
        }
    }

    protected function profileUrlNormalizer($data)
    {
        return isset($data['permalink_url']) ? $data['permalink_url'] : null;
    }

    protected function imageUrlNormalizer($data)
    {
        return isset($data['avatar_url']) ? $data['avatar_url'] : null;
    }

    protected function locationNormalizer($data)
    {
    	if (! empty($data['city']) && ! empty($data['country'])) {
			return $data['city'].', '.$data['country'];
		}

		if (! empty($data['city'])) {
            return $data['city'];
        }

		if (! empty($data['country'])) {
            return $data['country'];
        }
    }

    protected function extraNormalizer($data)  
	{
	    return ArrayUtils::removeKeys($data, array(
            'id',
            'username',
            'first_name',
            'last_name',
            'full_name',
            'description',
           	'website',
            'permalink_url',
            'avatar_url',
            'city',
            'country',
	    ));
	}
}
