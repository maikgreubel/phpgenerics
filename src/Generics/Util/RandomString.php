<?php
/**
 * This file is part of the PHP Generics package.
 *
 * @package Generics
 */
namespace Generics\Util;

/**
 * This utility class helps to generate a random string
 *
 * @author Maik Greubel <greubel@nkey.de>
 *
 */
class RandomString
{

    /**
     * ISO Characters
     *
     * @final
     *
     */
    const ISO = 1;

    /**
     * ASCII Characters
     *
     * @final
     *
     */
    const ASCII = 2;

    /**
     * Generate a random string with specific length
     *
     * @param number $length
     *            The length of string to generate
     * @param int $allowed
     *            Type of allowed characters
     * @param boolean $repeatable
     *            Whether a character may be reused
     *
     * @return string The generated string
     */
    public static function generate($length = 8, $allowed = RandomString::ASCII, $repeatable = true)
    {
        $allowedChars = array();

        $currentLocale = setlocale(LC_ALL, "0");
        if ($allowed == RandomString::ASCII) {
            setlocale(LC_ALL, "C");
        }

        for ($i = 32; $i < 256; $i ++) {
            if ($allowed == RandomString::ASCII && ! ctype_alnum(chr($i))) {
                continue;
            }
            if (! ctype_print(chr($i))) {
                continue;
            }
            $allowedChars[] = $i;
        }

        self::resetLocaleTo($currentLocale);

        $used = array();

        $string = "";
        $i = $length;
        while ($i > 0) {
            $index = mt_rand(0, count($allowedChars) - 1);
            if (! $repeatable && in_array($index, $used)) {
                continue;
            }
            $string .= chr($allowedChars[$index]);
            $used[] = $i;
            $i --;
        }

        return $string;
    }

    /**
     * Reset the locale settings back to saved vars
     *
     * @param string $localeSaved
     *            String containing the locale infos obtained using setlocale(LC_ALL, '');
     */
    private static function resetLocaleTo($localeSaved)
    {
        $localeData = explode(';', $localeSaved);

        foreach ($localeData as $identifier) {
            list ($type, $value) = explode("=", $identifier);
            switch ($type) {
                case 'LC_ALL':
                    setlocale(LC_ALL, $value);
                    break;

                case 'LC_COLLATE':
                    setlocale(LC_COLLATE, $value);
                    break;

                case 'LC_CTYPE':
                    setlocale(LC_CTYPE, $value);
                    break;

                case 'LC_MONETARY':
                    setlocale(LC_MONETARY, $value);
                    break;

                case 'LC_NUMERIC':
                    setlocale(LC_NUMERIC, $value);
                    break;

                case 'LC_TIME':
                    setlocale(LC_TIME, $value);
                    break;

                case 'LC_MESSAGES':
                    setlocale(LC_MESSAGES, $value);
                    break;

                default:
                    ;
                    break;
            }
        }
    }
}
