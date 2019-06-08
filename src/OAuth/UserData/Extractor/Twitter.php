<?php

/*
 * This file is part of the Oryzone PHPoAuthUserData package <https://github.com/Oryzone/PHPoAuthUserData>.
 *
 * (c) Oryzone, developed by Luciano Mammino <lmammino@oryzone.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace OAuth\UserData\Extractor;

use OAuth\UserData\Utils\ArrayUtils;

class Twitter extends LazyExtractor
{
    const REQUEST_PROFILE = '/account/verify_credentials.json';

    const FIELD_LOCALE = 'locale';

    public function __construct()
    {
        parent::__construct(
            array_merge(self::getDefaultLoadersMap(), array(
                self::FIELD_LOCALE => self::FIELD_LOCALE
            )),
            array_merge(self::getDefaultNormalizersMap(), array(
                self::FIELD_LOCALE => self::FIELD_LOCALE
            )),
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
            self::FIELD_DESCRIPTION,
            self::FIELD_LOCATION,
            self::FIELD_PROFILE_URL,
            self::FIELD_IMAGE_URL,
            self::FIELD_WEBSITES,
            self::FIELD_WEBSITE,
            self::FIELD_LOCALE,
            self::FIELD_EXTRA
        );
    }

    protected function profileLoader()
    {
        return json_decode($this->service->request(self::REQUEST_PROFILE), true);
    }

    protected function localeLoader()
    {
        return $this->getExtras();
    }

    protected function uniqueIdNormalizer($data)
    {
        return $data['id'];
    }

    protected function usernameNormalizer($data)
    {
        return isset($data['screen_name']) ? $data['screen_name'] : null;
    }

    protected function fullNameNormalizer($data)
    {
        return isset($data['name']) ? $data['name'] : null;
    }

    protected function firstNameNormalizer()
    {
        $fullName = $this->getField(self::FIELD_FULL_NAME);
        
        if ($fullName) {
            $names = explode(' ', $fullName);

            return $names[0];
        }
    }

    protected function lastNameNormalizer()
    {
        $fullName = $this->getField(self::FIELD_FULL_NAME);
        
        if ($fullName) {
            $names = explode(' ', $fullName);

            return $names[sizeof($names) - 1];
        }
    }

    protected function descriptionNormalizer($data)
    {
        return isset($data['description']) ? $data['description'] : null;
    }

    protected function locationNormalizer($data)
    {
        return isset($data['location']) ? $data['location'] : null;
    }

    protected function imageUrlNormalizer($data)
    {
        $url = isset($data['profile_image_url']) ? $data['profile_image_url'] : null;
        
        if (strpos($url, 'http://') === 0) {
            $url = str_replace('http://', 'https://', $url);
        }

        return $url;
    }

    protected function profileUrlNormalizer($data)
    {
        return isset($data['screen_name']) ? sprintf('https://twitter.com/%s', $data['screen_name']) : null;
    }

    protected function websitesNormalizer($data)
    {
        $websites = array();
        
        if (isset($data['url'])) {
            $websites[] = $data['url'];
        }

        if (isset($data['entities']['url']['urls'])) {
            foreach ($data['entities']['url']['urls'] as $urlData) {
                $websites[] = $urlData['expanded_url'];
            }
        }

        return array_unique($websites);
    }

    protected function websiteNormalizer($data)
    {
        $websites = $this->getWebsites();

        if (count($websites)) {
            return $websites[0];
        }
    }

    protected function localeNormalizer($data)
    {
        return isset($data['lang']) ? $data['lang'] : null;
    }

    protected function extraNormalizer($data)
    {
        return ArrayUtils::removeKeys($data, array(
            'id',
            'screen_name',
            'name',
            'description',
            'location',
            'url',
            'profile_image_url',
        ));
    }
}
