<?php 

namespace OAuth\UserData\Extractor;

use OAuth\UserData\Utils\ArrayUtils;
use OAuth\UserData\Utils\StringUtils;

class Microsoft extends LazyExtractor 
{
	const REQUEST_PROFILE  = 'https://apis.live.net/v5.0/me';

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
        	self::FIELD_BIRTHDAY => self::FIELD_BIRTHDAY,
            self::FIELD_LOCALE => self::FIELD_LOCALE,
        ));
    }

    protected static function geNormalizersMap()
    {
    	return array_merge(self::getDefaultNormalizersMap(), array(
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
            self::FIELD_LOCATION,
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

    protected function fullNameNormalizer($data)
    {
        return isset($data['name']) ? $data['name'] : null;
    }

    protected function emailNormalizer($data)
    {
    	return isset($data['emails']['account']) ? $data['emails']['account'] : null;
    }

    protected function locationNormalizer($data)
    {
        if (isset($data['addresses']['personal'])) {
        	$address = $data['addresses']['personal'];

        	$addr = '';
        	$addr .= !empty($address['street']) ? $address['street'].', ' : '';
        	$addr .= !empty($address['state']) ? $address['state'].', ' : '';
        	$addr .= !empty($address['city']) ? $address['city'].', ' : '';
        	$addr .= $address['region'];

        	return $addr;
        }
    }

    protected function birthdayNormalizer($data)
    {
    	if (isset($data['birth_year'], $data['birth_month'], $data['birth_day'])) {
	    	return $data['birth_year'].'-'.$data['birth_month'].'-'.$data['birth_day'];
	    }
    }

    protected function localeNormalizer($data)
    {
    	return isset($data['locale']) ? substr($data['locale'], 0, 2) : null;
    }

    protected function extraNormalizer($data)  
	{
	    return ArrayUtils::removeKeys($data, array(
            'id',
            'name',
            'first_name',
            'last_name',
            'emails',
            'addresses',
            'birth_year',
            'birth_month',
            'birth_day'
	    ));
	}
}
