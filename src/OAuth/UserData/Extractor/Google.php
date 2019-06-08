<?php 

namespace OAuth\UserData\Extractor;

use OAuth\UserData\Utils\ArrayUtils;
use OAuth\UserData\Utils\StringUtils;

class Google extends LazyExtractor 
{
	const REQUEST_PROFILE  = 'https://www.googleapis.com/plus/v1/people/me';
	const REQUEST_USERINFO = 'https://www.googleapis.com/userinfo/v2/me';

	const FIELD_BIRTHDAY = 'birthday';
	const FIELD_LOCALE   = 'locale';

	public function __construct()
    {
        parent::__construct(
        	self::getLoadersMap(),
            self::geNormalizersMap(),
        	self::getSupportedFields()
        );
    }

    protected static function getLoadersMap()
    {
        return array_merge(self::getDefaultLoadersMap(), array(
        	self::FIELD_VERIFIED_EMAIL => self::FIELD_VERIFIED_EMAIL,
            self::FIELD_BIRTHDAY => self::FIELD_BIRTHDAY,
            self::FIELD_LOCALE => self::FIELD_LOCALE,
        ));
    }

    protected static function geNormalizersMap()
    {
    	return array_merge(self::getDefaultNormalizersMap(), array(
    		self::FIELD_VERIFIED_EMAIL => self::FIELD_VERIFIED_EMAIL,
    		self::FIELD_BIRTHDAY => self::FIELD_BIRTHDAY,
            self::FIELD_LOCALE => self::FIELD_LOCALE,
        ));
    }

    protected static function getSupportedFields()  
	{
	    return array(
	        self::FIELD_UNIQUE_ID,
	        self::FIELD_FULL_NAME,
	        self::FIELD_FIRST_NAME,
	        self::FIELD_LAST_NAME,
	        self::FIELD_EMAIL,
	        self::FIELD_VERIFIED_EMAIL,
	        self::FIELD_DESCRIPTION,
            self::FIELD_LOCATION,
	        self::FIELD_WEBSITES,
            self::FIELD_WEBSITE,
	        self::FIELD_IMAGE_URL,
	        self::FIELD_PROFILE_URL,
	        self::FIELD_BIRTHDAY,
	        self::FIELD_LOCALE,
	        self::FIELD_EXTRA
	    );
	}

	protected function profileLoader()
    {
        return json_decode($this->service->request(self::REQUEST_PROFILE), true);
    }

    protected function birthdayLoader()
    {
    	return $this->getExtras();
    }

    protected function localeLoader()
    {
    	return $this->getExtras();
    }

    protected function verifiedEmailLoader()
    {
    	return json_decode($this->service->request(self::REQUEST_USERINFO), true);
    }

    protected function uniqueIdNormalizer($data)
    {
        return isset($data['id']) ? $data['id'] : null;
    }

    protected function firstNameNormalizer($data)
    {
        return isset($data['name']['givenName']) ? $data['name']['givenName'] : null;
    }

    protected function lastNameNormalizer($data)
    {
        return isset($data['name']['familyName']) ? $data['name']['familyName'] : null;
    }

    protected function fullNameNormalizer($data)
    {
        return isset($data['displayName']) ? $data['displayName'] : null;
    }

    protected function emailNormalizer($data)
    {
    	if (! isset($data['emails'])) {
            return;
        }

    	foreach ((array) $data['emails'] as $email) {
    		if ($email['type'] === 'account') {
    			return $email['value'];
    		}
    	}
    }

    public function verifiedEmailNormalizer($data)
    {
    	return !empty($data['verified_email']);
    }

    protected function descriptionNormalizer($data)
    {
        if (isset($data['aboutMe'])) {
            return $data['aboutMe'];
        }

        if (isset($data['tagline'])) {
            return $data['tagline'];
        }
    }

    protected function websitesNormalizer($data)
    {
        return isset($data['urls']) ? $data['urls'] : array();
    }

    protected function websiteNormalizer($data)
    {
        $websites = $this->getWebsites();

        foreach ($websites as $website) {
            if ($website['type'] === 'other') {
                return $website['value'];
            }
        }

        foreach ($websites as $website) {
            if ($website['type'] === 'otherProfile') {
                return $website['value'];
            }
        }
    }

    protected function profileUrlNormalizer($data)
    {
        return isset($data['url']) ? $data['url'] : null;
    }

    protected function imageUrlNormalizer($data)
    {
        return isset($data['image']['url']) ? $data['image']['url'] : null;
    }

    protected function locationNormalizer($data)
    {
        if (! isset($data['placesLived'])) {
            return;
        }

        foreach ((array) $data['placesLived'] as $location) {
            if (isset($location['primary'])) {
                return $location['value'];
            }
        }
    }

    protected function birthdayNormalizer($data)
    {
    	return isset($data['birthday']) ? $data['birthday'] : null;
    }

    protected function localeNormalizer($data)
    {
    	return isset($data['language']) ? substr($data['language'], 0, 2) : null;
    }

    protected function extraNormalizer($data)  
	{
	    return ArrayUtils::removeKeys($data, array(
            'id',
            'name',
            'displayName',
            'emails',
            'aboutMe',
            'urls',
            'url',
            'image'
	    ));
	}
}
