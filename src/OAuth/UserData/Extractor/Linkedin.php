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

class Linkedin extends LazyExtractor
{   
    const FIELD_BIRTHDAY = 'birthday';
    const FIELD_LOCALE   = 'locale';

    public function __construct()
    {
        parent::__construct(
            self::getLoadersMap(),
            self::getNormalizersMap(),
            self::getSupportedFields()
        );
    }

    public static function createProfileRequestUrl()
    {
        $fields = array(
            'id',
            'summary',
            'member-url-resources',
            'email-address',
            'first-name',
            'last-name',
            'headline',
            'location',
            'industry',
            'picture-url',
            'public-profile-url',
            'date-of-birth'
        );

        return sprintf('/people/~:(%s)?format=json', implode(",", $fields));
    }

    protected static function getLoadersMap()
    {
        return array_merge(self::getDefaultLoadersMap(), array(
            self::FIELD_BIRTHDAY => self::FIELD_BIRTHDAY,
            self::FIELD_LOCALE => self::FIELD_LOCALE,
        ));
    }

    public static function getNormalizersMap()
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
            self::FIELD_FIRST_NAME,
            self::FIELD_LAST_NAME,
            self::FIELD_FULL_NAME,
            self::FIELD_EMAIL,
            self::FIELD_DESCRIPTION,
            self::FIELD_LOCATION,
            self::FIELD_PROFILE_URL,
            self::FIELD_IMAGE_URL,
            self::FIELD_WEBSITES,
            self::FIELD_VERIFIED_EMAIL,
            self::FIELD_EXTRA,
            self::FIELD_BIRTHDAY,
            self::FIELD_LOCALE,
        );
    }

    protected function profileLoader()
    {
        return json_decode($this->service->request(self::createProfileRequestUrl()), true);
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
        return $data['id'];
    }

    protected function firstNameNormalizer($data)
    {
        return isset($data['firstName']) ? $data['firstName'] : null;
    }

    protected function lastNameNormalizer($data)
    {
        return isset($data['lastName']) ? $data['lastName'] : null;
    }

    protected function fullNameNormalizer()
    {
        return sprintf('%s %s', $this->getFirstName(), $this->getLastName());
    }

    protected function emailNormalizer($data)
    {
        return isset($data['emailAddress']) ? $data['emailAddress'] : null;
    }

    protected function descriptionNormalizer($data)
    {
        return isset($data['summary']) ? $data['summary'] : null;
    }

    protected function imageUrlNormalizer($data)
    {
        return isset($data['pictureUrl']) ? $data['pictureUrl'] : null;
    }

    protected function profileUrlNormalizer($data)
    {
        return isset($data['publicProfileUrl']) ? $data['publicProfileUrl'] : null;
    }

    protected function locationNormalizer($data)
    {
        return isset($data['location']['name']) ? $data['location']['name'] : null;
    }

    protected function websitesNormalizer($data)
    {
        $websites = array();
        
        if (isset($data['memberUrlResources'], $data['memberUrlResources']['values'])) {
            foreach ($data['memberUrlResources']['values'] as $resource) {
                if (isset($resource['url'])) {
                    $websites[] = $resource['url'];
                }
            }
        }

        return $websites;
    }

    protected function birthdayNormalizer($data)
    {
        if (isset($data['dateOfBirth']['day'], $data['dateOfBirth']['month'], $data['dateOfBirth']['year'])) {
            $month = $data['dateOfBirth']['month'];
            $day = $data['dateOfBirth']['day'];

            if (strlen($month) < 2) {
                $month = "0$month";
            }

            if (strlen($day) < 2) {
                $day = "0$day";
            }

            return $data['dateOfBirth']['year'].'-'.$month.'-'.$day;
        }

        return null;
    }

    protected function localeNormalizer($data)
    {
        return isset($data['location']['country']['code']) ? $data['location']['country']['code'] : null;
    }

    public function verifiedEmailNormalizer()
    {
        return true; // Linkedin users who have access to OAuth v2 always have a verified email
    }

    protected function extraNormalizer($data)
    {
        return ArrayUtils::removeKeys($data, array(
            'id',
            'firstName',
            'lastName',
            'emailAddress',
            'summary',
            'pictureUrl',
            'publicProfileUrl',
        ));
    }
}
