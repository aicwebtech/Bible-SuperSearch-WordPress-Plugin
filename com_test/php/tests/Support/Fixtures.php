<?php

namespace BibleSuperSearch\Common\Tests\Support;

/**
 * Canned API data, shaped like the payload getStatics() returns.
 */
class Fixtures
{
    /**
     * @return array
     */
    public static function statics()
    {
        return [
            'version'   => '5.0.0',
            'timestamp' => 1700000000,
            'bibles'    => static::bibles(),
        ];
    }

    /**
     * Four Bibles in three languages, deliberately stored out of alphabetical
     * order so sorting assertions mean something.
     *
     * @return array
     */
    public static function bibles()
    {
        return [
            'rvg' => static::bible('rvg', 'Reina Valera Gomez', 'RVG', 'Spanish', 'es', 'Espanol', 1, 2010),
            'web' => static::bible('web', 'World English Bible', 'WEB', 'English', 'en', 'English', 2, 2000),
            'lut' => static::bible('lut', 'Luther Bible', 'LUT', 'German', 'de', 'Deutsch', 1, 1912),
            'kjv' => static::bible('kjv', 'King James Version', 'KJV', 'English', 'en', 'English', 1, 1611),
        ];
    }

    /**
     * @return array
     */
    public static function bible($module, $name, $shortname, $lang, $lang_short, $lang_native, $rank, $year)
    {
        return [
            'module'        => $module,
            'name'          => $name,
            'shortname'     => $shortname,
            'lang'          => $lang,
            'lang_short'    => $lang_short,
            'lang_native'   => $lang_native,
            'rank'          => $rank,
            'year'          => $year,
        ];
    }

    /**
     * Landing pages in the shape fetchLandingPageOptions() returns.
     *
     * @return array
     */
    public static function landingPages()
    {
        return [
            ['value' => '12', 'label' => 'Bible Search'],
            ['value' => '34', 'label' => 'Study Page'],
        ];
    }
}
