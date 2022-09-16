<?php

namespace Helpers;

class Express
{
    const DIRECTORY_SEPERATOR = '/';
    const DETAILS_FILE = 'object_details.json';

    public static function getOneToManyDetails()
    {
        $filePath = dirname(__FILE__) . self::DIRECTORY_SEPERATOR . self::DETAILS_FILE;

        $json = file_get_contents($filePath);
        $decoded = json_decode($json, true);

        return $decoded;
    }
}