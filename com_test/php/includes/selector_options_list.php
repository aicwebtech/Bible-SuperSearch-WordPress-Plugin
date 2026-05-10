<?php

$default_item_text = \BibleSuperSearch\Common\OptionsAbstract::DEFAULT_ITEM_TEXT;

return [
    'bibleGrouping' => [
        'none'                  => 'None',
        'language'              => 'Language - Endonym',
        'language_english'      => 'Language - English Name',
        'language_and_english'  => 'Language - Endonym and English Name',
    ],        
    'bibleSorting' => [
        'language_english|name'             => 'Language - English Name / Full Name',
        'language_english|shortname'        => 'Language - English Name / Short Name',
        'language_english|rank|name'        => 'Language - English Name / Rank / Full Name',
        'language_english|rank|shortname'   => 'Language - English Name / Rank / Short Name',
        'language_english|rank'             => 'Language - English Name / Rank', 
        'rank'                              => 'Rank',
        'name'                              => 'Full Name',
        'shortname'                         => 'Short Name', 
    ],
    'interfaces' => [
                // 'TwentyTwenty' => [
        //     'name'  => 'Twenty Twenty', 
        //     'class' => 'twentytwenty'
        // ),
        // 'Classic' => [
        //     'name'  => 'Classic (Default Classic Skin)', 
        //     'class' => 'classic',
        // ),
        'Expanding' => [
            'name'  => 'Expanding', 
            'class' => 'expanding',
        ],
        'ExpandingLargeInput' => [
            'name'  => 'Expanding - Large Input', 
            'class' => 'expanding',
        ],                          
        'BrowsingBookSelector' => [
            'name'  => 'Browsing with Book Selector', 
            'class' => 'browsing',
        ],              
        'BrowsingBookSelectorHorizontal' => [
            'name'  => 'Browsing with Book Selector, Horizontal Form', 
            'class' => 'browsing',
        ],              
        'Classic' => [
            'name'  => 'Classic (alias of Classic - User Friendly 2)',  // alias ClassicUserFriendly2
            'class' => 'classic',
        ],            
        'ClassicUserFriendly1' => [
            'name'  => 'Classic - User Friendly 1', 
            'class' => 'classic',
        ],                  
        'ClassicUserFriendly2' => [
            'name'  => 'Classic - User Friendly 2', 
            'class' => 'classic',
        ],            
        'ClassicParallel2' => [
            'name'  => 'Classic - Parallel 2', 
            'class' => 'classic',
        ],
        'ClassicAdvanced' => [
            'name'  => 'Classic - Advanced', 
            'class' => 'classic',
        ],                 
        'Minimal' => [
            'name'  => 'Minimal', 
            'class' => 'minimal'
        ],              
        'MinimalWithBible' => [
            'name'  => 'Minimal with Bible', 
            'class' => 'minimal'
        ],               
        'MinimalWithBibleWide' => [
            'name'  => 'Minimal with Bible - Wide', 
            'class' => 'minimal'
        ],              
        'MinimalWithShortBible' => [
            'name'  => 'Minimal with Short Bible', 
            'class' => 'minimal'
        ],              
        'MinimalWithParallelBible' => [
            'name'  => 'Minimal with Parallel Bible', 
            'class' => 'minimal'
        ],               
        'MinimalGoRandom' => [
            'name'  => 'Minimal Go Random', 
            'class' => 'minimal'
        ],                
        'MinimalGoRandomBible' => [
            'name'  => 'Minimal Go Random with Bible', 
            'class' => 'minimal'
        ],            
        'MinimalGoRandomParallelBible' => [
            'name'  => 'Minimal Go Random with Parallel Bible', 
            'class' => 'minimal'
        ],
        'CustomUserFriendly2BookSel' => [
            'name'  => 'Custom - User Friendly 2 with Book Selector', 
            'class' => 'classic',
        ],   
    ],
    
    'language' => [
        'global_default'        => 'Global Default',

        // 'en_pirate'             => 'English - Pirate', // (for debugging purposes)
        'am'                    => 'اአማርኛ / Amharic',
        'af'                    => 'Afrikaans / Afrikaans',
        'ar'                    => 'العربية  / Arabic',
        'bn'                    => 'বাংলা / Bengali',
        'de'                    => 'Deutsch / German',
        'en'                    => 'English',
        'es'                    => 'Español / Spanish',
        'et'                    => 'Eesti / Estonian',
        'fa'                    => 'فارسی  / Persian',
        'fr'                    => 'Français / French',
        'gu'                    => 'ગુજરાતી / Gujarati',
        'ha'                    => '(Hausa) هَوُسَ',
        'he'                    => 'עברית  / Hebrew',
        'hi'                    => 'हिन्दी, हिंदी / Hindi',
        'hu'                    => 'Magyar / Hungarian',
        'id'                    => 'Bahasa Indonesia / Indonesian',
        'it'                    => 'Italiano / Italian',
        'ja'                    => '日本語 (にほんご) / Japanese',
        'kn'                    => 'ಕನ್ನಡ / Kannada',
        'ko'                    => '한국어 / Korean',
        'lv'                    => 'Latviešu / Latvian',
        'mi'                    => 'Te reo Māori / Maori',
        'mr'                    => 'मराठी / Marathi',
        'my'                    => 'ဗမာစာ / Burmese / Myanmar',
        'lt'                    => 'Lietuvių Kalba / Lithuanian',
        'nl'                    => 'Nederlands, Vlaams / Dutch, Flemish',
        'ne'                    => 'नेपाली / Nepali',
        'pa'                    => 'ਪੰਜਾਬੀ / Punjabi, Panjabi',
        'pl'                    => 'Polski / Polish',
        'pt'                    => 'Português / Portuguese',
        'ro'                    => 'Română / Romanian',
        'ru'                    => 'Русский / Russian',
        'so'                    => 'Soomaaliga, af Soomaali / Somali',
        'sq'                    => 'Shqip / Albanian',
        'sw'                    => 'Kiswahili / Swahili',
        'ta'                    => 'தமிழ் / Tamil',
        'te'                    => 'తెలుగు / Telugu',
        'tg'                    => 'тоҷикӣ / Tajiki / Tajik',
        'th'                    => 'ไทย / Thai',
        'tl'                    => 'Wikang Tagalog / Tagalog',
        'tr'                    => 'Türkçe / Turkish',
        'vi'                    => 'Tiếng Việt / Vietnamese',
        'ug'                    => 'ئۇيغۇرچە, Uyghurche / Uyghur',
        'ur'                    => 'اردو / Urdu',
        'zh_TW'                 => '繁體中文 / Chinese - Traditional',
        'zh_CN'                 => '简体中文 / Chinese - Simplified',
    ],
    
    // Todo - pull from here, not the options class methods ... 
    'text_displays' => [
        'paragraph'         => ['name' => 'Paragraph'],
        'passage'           => ['name' => 'Passage'],
        'verse'             => ['name' => 'Verse'],
        'verse_passage'     => ['name' => 'Verse as Passage Display'],
    ],
    'pagers' => [
        'default'   => ['name' => $default_item_text],
        'Classic'   => ['name'  => 'Classic'],            
        'Clean'     => ['name'  => 'Clean'],
    ],
    'page_scrolls' => [
        'instant'   => ['name' => 'Instant'],
        'smooth'    => ['name'  => 'Smooth'],            
        'none'      => ['name'  => 'None - No scrolling'],
    ],
    'navigation_buttons' => [
        'default'   => ['name' => $default_item_text],
        'Classic'   => ['name'  => 'Classic'],            
        'Stylable'  => ['name'  => 'Stylable'],
    ],
    'format_buttons' => [
        'default'           => ['name' => $default_item_text],
        'Classic'           => ['name'  => 'Classic (Old icons from v2 - deprecated)'],            
        'Stylable'          => ['name'  => 'Stylable - Wide'],            
        'StylableNarrow'    => ['name'  => 'Stylable - Narrow'],           
        'StylableMinimal'   => ['name'  => 'Stylable - Minimal buttons, with settings dialog.'],
        'none'              => ['name'  => 'None'],
    ],
    'extra_buttons' => [
        'default'   => ['name' => $default_item_text],
        'false'     => ['name'  => 'With Formatting Buttons'],
        'true'      => ['name'  => 'Separate from Formatting Buttons *'],
        'none'      => ['name'  => 'None - Do not display'],
    ],
    'extra_buttons_display' => [
        'default'   => ['name' => $default_item_text],
        'format'    => ['name' => 'Display with Formatting Buttons'],
        'separate'  => ['name' => 'Display Separatly on the form.  (Some skins may not support this)'],
        'none'      => ['name' => 'Do not display'],
    ],
    'landing_pages' => null, // populated dynamically
];