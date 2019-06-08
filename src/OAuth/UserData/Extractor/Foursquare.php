<?php 

namespace OAuth\UserData\Extractor;

use OAuth\UserData\Utils\ArrayUtils;
use OAuth\UserData\Utils\StringUtils;

class Foursquare extends LazyExtractor 
{
	const REQUEST_PROFILE  = 'users/self';
    
    const FIELD_GENDER = 'gender';

	public function __construct()
    {
        parent::__construct(
        	array_merge(self::getDefaultLoadersMap(), array(
                self::FIELD_GENDER => self::FIELD_GENDER
            )),
            array_merge(self::getDefaultNormalizersMap(), array(
                self::FIELD_GENDER => self::FIELD_GENDER
            )),
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
	        self::FIELD_EMAIL,
	        self::FIELD_DESCRIPTION,
            self::FIELD_LOCATION,
	        self::FIELD_IMAGE_URL,
	        self::FIELD_PROFILE_URL,
            self::FIELD_GENDER,
	        self::FIELD_EXTRA
	    );
	}

	protected function profileLoader()
    {
        $response = json_decode($this->service->request(self::REQUEST_PROFILE), true);

        return isset($response['response']['user']) ? $response['response']['user'] : array();
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
        return isset($data['firstName']) ? $data['firstName'] : null;
    }

    protected function lastNameNormalizer($data)
    {
    	return isset($data['lastName']) ? $data['lastName'] : null;
    }

    protected function fullNameNormalizer($data)
    {
        return sprintf('%s %s', $this->getFirstName(), $this->getLastName());
    }

    protected function emailNormalizer($data)
    {
        return isset($data['contact']['email']) ? $data['contact']['email'] : null;
    }

    protected function descriptionNormalizer($data)
    {
        return isset($data['bio']) ? $data['bio'] : null;
    }

    protected function locationNormalizer($data)
    {
        return isset($data['homeCity']) ? $data['homeCity'] : null;
    }

    protected function profileUrlNormalizer($data)
    {
        $id = $this->getField(self::FIELD_UNIQUE_ID);

        if (! is_null($id)) {
            return sprintf('https://foursquare.com/user/%s', $id);
        }
    }

    protected function imageUrlNormalizer($data)
    {
        if (isset($data['photo']['prefix'], $data['photo']['suffix'])) {
        	return $data['photo']['prefix'].'500x500'.$data['photo']['suffix'];
        }
    }

    protected function genderNormalizer($data)
    {
        if (isset($data['gender'])) {
            switch ($data['gender']) {
                case 'female': return 'F';
                case 'male': return 'M';
            }
        }
    }

    protected function extraNormalizer($data)
    {
        return ArrayUtils::removeKeys($data, array(
            'id',
            'firstName',
            'lastName',
            'contact',
            'bio',
            'homeCity',
            'photo',
        ));
    }
}
