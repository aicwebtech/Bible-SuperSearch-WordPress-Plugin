<?php

return [
    'display' => [
        'textDisplayDefault' => [
            'label'         => 'Default Text Display',
            'desc'          => 'How to display Bible text by default.  The text display can be changed by the user.',
            'items'         => 'getTextDisplays',
            'type'          => 'select',
            'default'       => 'passage',
        ],               
        'pager' => [
            'label'         => 'Paginator',
            'desc'          => 'Used to browse through multiple pages of search results.',
            'items'         => 'getPagers',
            'type'          => 'select',
            'default'       => 'default',
        ],            
        'pageScroll' => [
            'label'         => 'Page Scrolling',
            'desc'          => 'When the page loads or query executes, how to scroll the page up to top of the results.',
            'items'         => 'getPageScrolls',
            'type'          => 'select',
            'default'       => 'instant',
        ],
        'navigationButtons' => [
            'label'         => 'Navigation Buttons',
            'desc'          => 'Used to browse between chapters and books.',
            'items'         => 'getNavigationButtons',
            'type'          => 'select',
            'default'       => 'default',
        ],
        'formatButtons' => [
            'label'         => 'Formatting Buttons',
            'desc'          => 'The formatting buttons appear below the form, and include size, font, and copy options.',
            'items'         => 'getFormatButtons',
            'type'          => 'select',
            'default'       => 'default',
        ],            
        'formatButtonsToggle' => [
            'label'         => 'Auto-Hide Formatting Buttons',
            'desc'          => 'Formatting buttons will only show when a search is active.',
            'type'          => 'checkbox',
            'default'       => false,
            'section'       => 'display',
            'default'       => 'default',
        ],    

        // :Todo replace with extra buttons separate
        // This will require REMOVING this option from the saved options?
        'extraButtonsSeparate' => [
            'label'         => 'How to Display Extra Buttons',
            'desc'          => 'These include help and download dialog buttons. &nbsp; * Some skins do not support this option.',
            'items'         => 'getExtraButtons',
            'type'          => 'select',
            'default'       => 'default',
        ],

        'contextHelpInline' => [
            'label'         => 'Context Help Inline',
            'desc'          => 'Whether to show context help inline (relatively-positioned) to the form. Otherwise, context help will be shown in a popup tooltip',
            'type'          => 'checkbox',
            'default'       => false,
            'section'       => 'display',
            'default'       => 'default',
        ],   
        'includeTestament' => [
            'label'         => 'Include Testament',
            'desc'          => 'Includes "Old Testament" or "New Testament" verbiage in some references.',
            'type'          => 'checkbox',
            'default'       => false,
            'section'       => 'display',
            'default'       => 'default',
        ],
        'resultsList' => [
            'label'         => 'Results List',
            'desc'          => 'Shows a list of all verses pulled by a search near the paginator.',
            'type'          => 'checkbox',
            'default'       => false,
            'section'       => 'display',
            'default'       => 'default',
        ],
        'resultsListClickScroll' => [
            // 'label'         => 'Results List: Scroll to Clicked Verse',
            'desc'          => 'Scroll results list verse to top when clicking on it.',
            'type'          => 'checkbox',
            'default'       => false,
            'section'       => 'display',
            'default'       => 'default',
            'if_conditions' => 'resultsList',
        ],
        'overrideCss' => [
            'label'         => 'Override Styles',
            'sublabel'      => 'Override WordPress CSS',
            'desc'          => 'Attempts to override some CSS styles from WordPress to make Bible SuperSearch look as was originally designed.',
            'type'          => 'checkbox',
            'default'       => false,
            'section'       => 'display',
            'default'       => 'default',
        ],    
    ],

    'features' => [
        'toggleAdvanced' => [
            'label'         => 'Advanced Search Toggle',
            'desc'          => 'Adds a button to toggle an \'advanced search\' form',
            'type'          => 'checkbox',
            'default'       => false,
            'section'       => 'features',
        ],  
        'limitSearchManual' => [
            'label'         => 'Manual Search Limitation',
            'desc'          => 'Limit Search to Reference only if \'Limit Search To\' has been manually selected.' 
                            . '  Otherwise, search will automatically be limited by reference if a reference has been provided.',
            'type'          => 'checkbox',
            'default'       => false,
            'section'       => 'features',
        ],      
        'saveUserSettings' => [
            'label'         => 'Save User\'s Settings',
            'sublabel'      => ' ',
            'desc'          => 'Whether to save user\'s settings to appear on next page load (saves to LocalStorage, not cookie)',
            'type'          => 'checkbox',
            'default'       => false,
            'section'       => 'features',
        ],   
        'omitUserLanguage' => [
            'sublabel'      => 'Don\'t Save User Language Selection',
            'desc'          => 'If saving user settings, exclude the user\'s selected Language.',
            'type'          => 'checkbox',
            'default'       => false,
            'section'       => 'features',
            'if_conditions' => 'saveUserSettings',
        ],   
        'bookmarksHistory' => [
            'label'         => 'Bookmarks and History',
            'type'          => 'section',
            'default'       => true,
            'section'       => 'features',
        ],
        'bookmarksEnable' => [
            'label'         => 'Enable Bookmarks',
            'desc'          => '',
            'type'          => 'checkbox',
            'default'       => false,
            'section'       => 'features',
        ],    
        'bookmarkLimit' => [
            'label'         => 'Bookmark Limit',
            'desc'          => 'Maximum number of bookmarks a user can have.',
            'type'          => 'integer',
            'default'       => 20,
            'section'       => 'features',
            'rules'        => ['required', 'positiveInteger'],
        ],    
        'historyLimit' => [
            'label'         => 'History Limit',
            'desc'          => 'Maximum number of items in history;  older items will be deleted. ',
            'type'          => 'integer',
            'default'       => 50,
            'section'       => 'features',
            'rules'        => ['required', 'positiveInteger'],
        ],

        // Autocomplete Settings
        'autocomplete' => [
            'label'         => 'Autocomplete',
            'type'          => 'section',
            'default'       => true,
            'section'       => 'features',
        ],
        'autocompleteEnable' => [
            'label'         => 'Autocomplete Enable',
            'desc'          => 'Enable autocomplete on reference fields.',
            'type'          => 'checkbox',
            'default'       => true,
            'section'       => 'features',
        ],
        'autocompleteThreshold' => [
            'label'         => 'Threshold',
            'desc'          => 'Minimum number of characters before autocomplete is triggered.',
            'type'          => 'integer',
            'default'       => 2,
            'section'       => 'features',
            'row_classes'   => 'autocomplete_toggle',
            'rules'        => ['required', 'positiveInteger'],
            'if_conditions' => 'autocompleteEnable',
        ],
        'autocompleteMatchAnywhere' => [
            'label'         => 'Match Anywhere',
            'sublabel'      => 'Loose Matching',
            'desc'          => 'Whether to match anywhere in the given option / Book name.  &nbsp;Otherwise, we only match at the beginning of the name.',
            'type'          => 'checkbox',
            'default'       => false,
            'section'       => 'features',
            'row_classes'   => 'autocomplete_toggle',
            'if_conditions' => 'autocompleteEnable',
        ],
        'autocompleteMaximumOptions'    => [
            'label'         => 'Maximum Options',
            'desc'          => 'Maximum number of autocomplete options to show at once.',
            'type'          => 'integer',
            'default'       => 10,
            'section'       => 'features',
            'row_classes'   => 'autocomplete_toggle',
            'rules'         => ['required', 'positiveInteger'],
            'if_conditions' => 'autocompleteEnable',
        ],

        // Strongs / hover dialogs
        'dialogs' => [
            'label'         => 'Dialogs',
            'type'          => 'section',
            'default'       => true,
            'section'       => 'features',
        ],
        'hoverDelayThreshold' => [
            'label'         => 'Strong\'s Hover Delay Threshold',
            'desc'          => 'Time, in milliseconds, to wait after hovering before opening a hover dialog (ie Strongs)',
            'type'          => 'integer',
            'default'       => 500,
            'section'       => 'features',
            'rules'        => ['required', 'positiveInteger'],
        ],
        'strongsOpenClick' => [
            'label'         => 'Strong\'s Dialog Open by Click',
            'desc'          => 'Whether clicking on Strong\'s number will open the dialog, otherwise clicking the link will search for the Strong\'s number.',
            'type'          => 'select',
            'default'       => 'mobile',
            'section'       => 'features',
            'items'   => [
                'none'      => 'Clicking link always searches',
                'mobile'    => 'Clicking link opens dialog for mobile devices only',
                'always'    => 'Clicking link always opens dialog',
            ],
        ],
        'strongsDialogSearchLink' => [
            'label'         => 'Strong\'s Dialog Search Link',
            'sublabel'      => 'Show link to Strong\'s # Search in dialog.',
            'desc'          => 'When clicking the Strong\'s # opens the dialog, should we show a button to access the original search by Strong\'s # link?',
            'type'          => 'checkbox',
            'default'       => true,
            'section'       => 'features',
        ],
        'useNavigationShare' => [
            'label'         => 'Share Dialog',
            'desc'          => 'When sharing, whether to use the system share dialog (if available) or our generic share dialog.',
            'type'          => 'select',
            'default'       => 'never',
            'section'       => 'features',
            'items'   => [
                'never'     => 'Always use generic share dialog',
                'mobile'    => 'Use system share dialog on mobile ONLY, use generic share dialog on desktop',
                'always'    => 'Always use system share dialog (if available), otherwise use generic share dialog (Experimental)',
            ],
        ],
        'legacyManual' => [
            'label'         => 'Legacy User\'s Manual',
            'desc'          => 'Quick Start Dialog: Include link to the legacy manual (English only).',
            'type'          => 'checkbox',
            'default'       => false,
            'section'       => 'features',
        ],    
    ],
    'general' => [
        '_newConfigSave' => [
            'label'         => 'Have we saved using the new configs yet?',
            'desc'          => 'This does not shut down the old configs page!',
            'type'          => 'checkbox',
            'hidden'        => true,
            'default'       => false,
            'section'       => 'general_top',
            'render'        => false,
        ],
        'interface' => [
            'label'         => 'Default Skin',
            'desc'          => 'Sets the default skin seen on the [biblesupersearch] shortcode.<br />' . 
                                'To preview skins, please visit ' . 
                                '<a href=\'https://www.biblesupersearch.com/client/\' target=\'_NEW\'>https://www.biblesupersearch.com/client/</a>',
            'type'          => 'select',
            'default'       => 'global_default',
            'section'       => 'general_top',
            'items'         => 'getInterfaces',
        ],        
        'language' => [
            'label'         => 'Default Language',
            'desc'          => 'Sets the default display language seen on the [biblesupersearch] shortcode.',
            'type'          => 'select',
            'default'       => 'global_default',
            'section'       => 'general_top',
            'items'         => 'getLanguagesWithGlobalDefault',
        ],
        'enableAllLanguages' => [
            'label'         => 'Display Language(s)',
            'desc'          => 'Enable ALL Languages',
            'type'          => 'checkbox',
            'default'       => true,
            'section'       => 'general',
            'render'        => false, 
        ],
        'languageList' => [
            'label'         => '',
            'desc'          => 'Sets the display language(s) that can be selected by the user.',
            'type'          => 'select',
            'default'       => [],
            'section'       => 'general',
            'items'         => 'language',
            'multiple'      => true,
            'render'        => false, 
            'if_conditions' => 'enableAllLanguages|false',
        ],
        'navigation'        => [
            'label'         => 'Navigation',
            'type'          => 'section',
            'section'       => 'general',
        ],
        'swipePageChapter' => [
            'label'         => 'Touchscreen Swipe',
            'desc'          => 'Change chapter and search page via horizontal touchscreen swipe.',
            'type'          => 'checkbox',
            'default'       => false,
            'section'       => 'general',
        ],    
        'arrowKeysPageChapter' => [
            'label'         => 'Arrow Keys',
            'desc'          => 'Change chapter and search page via left and right arrow keys.',
            'type'          => 'checkbox',
            'default'       => false,
            'section'       => 'general',
        ],
        'sideSwipePageChapter' => [
            'label'         => 'Side Buttons',
            'desc'          => 'Change chapter and search page via fade-in side buttons.',
            'type'          => 'checkbox',
            'default'       => false,
            'section'       => 'general',
        ],    
        'sideSwipeHideWithNavigationButtons' => [
            // 'label'         => 'Side Buttons Hide With Navigation Buttons',
            'desc'          => 'Hide side buttons when navigation buttons are showing.',
            'type'          => 'checkbox',
            'default'       => false,
            'section'       => 'general',
            'if_conditions' => 'sideSwipePageChapter',
        ],            
        'defaultDestinationPage' => [
            'label'         => 'Default Destination Page',
            'desc'          => 'Select a page or post containing the [biblesupersearch] shortcode, ' . 
                                'and all other Bible SuperSearch forms on your site will redirect here.<br /><br /> ' .
                                'This allows you to have the form on one page, but display the results on another.  ' . 
                                'Add the [biblesupersearch] shortcode to any page or post, and it will appear in this list.',
            'type'          => 'select',
            'default'       => '0',
            'section'       => 'general',
            'items'         => 'getLandingPageOptions'
        ],
    ],

    'bible' => [

        // :todo
        'defaultBible' => [
            'label'         => 'Default Bible(s)',
            'desc'          => 'Note: The number of multiple default Bibles is limited by the parallel Bible limit on the selected skin.' .
                                  ' Bible selections beyond the limit will be ignored.',
            'type'          => 'select',
            'default'       => ['kjv'],
            'section'       => 'general',
            'items'         => 'bibles', //  going to need to rebuild bible fetcher or something here
            'multiple'      => true,
            'render'        => false, // todo
            'v_component'   => 'SelectOrdered',
        ],        
        'enableAllBibles' => [
            'label'         => 'Enabled Bibles',
            'desc'          => 'Enable ALL Bibles',
            'type'          => 'checkbox',
            'default'       => true,
            'section'       => 'general',
            'render'        => false, // todo
        ],
        'enabledBibles' => [
            'label'         => '',
            'type'          => 'select',
            'default'       => [],
            'section'       => 'general',
            'items'         => 'bibles', //  going to need to rebuild bible fetcher or something here
            'multiple'      => true,
            'render'        => false, // todo
            'v_component'   => 'SelectGroup',
            'if_conditions' => 'enableAllBibles|false',
        ],
        'bibleGrouping' => [
            'label'         => 'Bible List Grouping',
            'type'          => 'select',
            'default'       => 'language',
            'items'         => 'bibleGrouping',
        ],        
        'bibleSorting' => [
            'label'         => 'Bible List Sorting',
            'desc'          => 'Note: This ONLY controls the Bible sorting in the Bible list; it doesn\'t affect the labels on the options.<br /> ' . 
                               'Note: Rank is a user-defined sort order that is defined on the API.',
            'type'          => 'select',
            'default'       => 'language_english|name',
            'items'         => 'bibleSorting',
        ],
        'bibleDefaultLanguageTop' => [
            'label'         => 'Bible List Default Language',
            'desc'          => 'Places the default language at the TOP of the Bible list.',
            'type'          => 'checkbox',
            'default'       => true,
        ],         
        'bibleChangeUpdateNavigation' => [
            'label'         => 'Bible Change Updates Navigation',
            'sublabel'      => 'Update Navigation Links on Bible Change',
            'desc'          => 'Whether to update navigation (ie paging and browsing buttons) immediately when the selected Bible(s) are changed ' . 
                                'to reflect the new Bible selections. <br />Otherwise, the navigation will not update until the search or look up is performed.',
            'type'          => 'checkbox',
            'default'       => false,
        ], 
        'landingReference' => [
            'label'         => 'Landing Passage(s)',
            'desc'          => 'When app is first loaded, these reference(s) will automatically be retrieved. &nbsp; ' . 
                                'Form will remain blank, and URL will not change.<br />' . 
                                'Takes any valid Bible reference, ie \'John 3:16; Romans 3:23; Genesis 1\'',
            'type'          => 'text',
            'default'       => '',
            'rules'         => ['bibleReference'],
        ],    
        'landingReferenceDefault' => [
            'label'         => 'Use Landing Passage(s) as Default',
            'desc'          => 'If a search is executed with no search keywords or references, should we load the landing passage?',
            'type'          => 'checkbox',
            'default'       => false,
        ], 
        'parallelBibleCleanUpForce' => [
            'label'         => 'Force Parallel Bible Clean Up',
            'sublabel'      => 'Remove Bibles Above Limit',
            'desc'          => 'If the parallel Bible limit is dynamically changed (ie by an expanding interface, or by limits set below)<br />' .
                                'should we remove Bible selections above the new limit?  Otherwise, the selections will remain.',
            'type'          => 'checkbox',
            'default'       => false,
        ],
        'parallelSearchErrorSuppress' => [
            'label'         => 'Suppress Parallel Search Error',
            'desc'          => 'When enabled, the "verses from this Bible have been included for comparison" errors won\'t show. ',
            'type'          => 'checkbox',
            'default'       => false,
        ], 
        'parallelSearchErrorSuppressUserConfig' => [
            'label'         => 'Allow Users to Suppress Parallel Search Error',
            'desc'          => 'When enabled, uses will be able to decide whether to show the "verses from this Bible have been included for comparison" errors. ' 
                                . 'If enabled, Suppress Parallel Search Error will be the default value.',
            'type'          => 'checkbox',
            'default'       => false,
        ], 

        // :todo
        'parallelBibleLimitByWidthEnable' => [
            'label'         => 'Limit Parallel Bibles by Width',
            'desc'          => 'Whether to limit the number of parallel Bibles allowed based on page width.',
            'type'          => 'checkbox',
            'default'       => false,
            'render'        => false,
        ], 
        'parallelBibleLimitByWidth' => [
            'label'         => '', // None
            'type'          => 'json',
            'default'       => [],
            'format'        => 'json',
            'render'        => false,
            'v_component'   => 'BibleLimitsByWidth',
            'v_no_attr'     => true,
            'label_cols'    => 1,
            'comp_cols'     => 7,
            'if_conditions' => 'parallelBibleLimitByWidthEnable',
        ],
        'parallelBibleStartSuperceedsDefaultBibles' => [
            'label'         => 'Initial Number of Parallel Bibles" Superceeds Default Bibles',
            'desc'          => 'Forces the number of parallel Bible selectors displayed initially to always equal the ' . 
                                '"Initial Number of Parallel Bibles,"regardless to the number of Bibles selected as default.',
            'type'          => 'checkbox',
            'default'       => false,
            'render'        => false,
            'if_conditions' => 'parallelBibleLimitByWidthEnable',
        ], 
    ],

    'advanced' => [        
        'apiUrl' => [
            'label'         => 'API URL',
            // :todo, make url in desc dynamic (default_options['apiUrl'])
            'desc'          => 'Leave blank for default of <code>https://api.biblesupersearch.com</code>.',
            'type'          => 'text',
            // 'default'       => dynamic, //
            'id'            => 'biblesupersearch_url',
            'v_component'   => 'ApiUrl',
        ],   
        'pageScrollTopPadding' => [
            'label'         => 'Scroll Top Padding (Desktop / Global)',
            'desc'          => 'Use to adjust the final position of automatic scrolling.  ' . 
                                'A positive value will cause it to scroll further down, negative will scroll it further up.',
            'type'          => 'integer',
            'default'       => 0,
            'units'         => 'pixels',
            'rules'        => ['requiredInteger', 'integer'],
        ],    
        'pageScrollTopPaddingMobile' => [
            'label'         => 'Scroll Top Padding (Mobile)',
            'desc'          => 'Page Scroll Top Padding for mobile devices. ' . 
                                'Overrides Scroll Top Padding (Desktop / Global).',
            'type'          => 'integer',
            'default'       => 0,
            'units'         => 'pixels',
            'rules'        => ['requiredInteger', 'integer'],
        ],    
        'shortcutsShowHidden' => [
            'label'         => 'Show Hidden Shortcuts',
            'desc'          => '(IE: Last Days Prophecy)',
            'type'          => 'checkbox',
            'default'       => false,
        ],    
        'sideSwipeNavHideThresholdTop' => [
            'label'         => 'Side Buttons Navigation Hide Threshold: Top',
            'desc'          => 'When Side Buttons Hide With Navigation Buttons is enabled, this pixel value adjusts when the buttons dissappear.',
            'type'          => 'integer',
            'default'       => 0,
            'units'         => 'pixels',
            'rules'        => ['requiredInteger', 'integer'],
        ],    
        'sideSwipeNavHideThresholdBottom' => [
            'label'         => 'Side Buttons Navigation Hide Threshold: Bottom',
            'desc'          => 'When Side Buttons Hide With Navigation Buttons is enabled, this pixel value adjusts when the buttons dissappear.',
            'type'          => 'integer',
            'default'       => 0,
            'units'         => 'pixels',
            'rules'        => ['requiredInteger', 'integer'],
        ],
        'debug' => [
            'label'         => 'Debug Mode',
            'desc'          => 'Enables a mulitude of debugging messages in the console. ' . 
                                'Enabling this will slow things down considerably, and is not recommended for a production site.',
            'type'          => 'checkbox',
            'default'       => false,
        ],         
        'extraCss' => [
            'label'         => 'Extra CSS',
            'desc'          => 'Will be applied everywhere the <code>[biblesuperseach]</code> shortcode is found.',
            'type'          => 'textarea',
            'default'       => '',
        ], 
    ],
];
