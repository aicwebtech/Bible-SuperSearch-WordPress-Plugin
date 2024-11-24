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
        'extraButtonsSeparate' => [
            'label'         => 'How to Display Extra Buttons',
            'desc'          => 'These include help and download dialog buttons. &nbsp; * Some skins do not support this option.',
            'items'         => 'getExtraButtons',
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
        'includeTestament' => [
            'label'         => 'Include Testament',
            'desc'          => 'Includes "Old Testament" or "New Testament" verbiage in some references.',
            'type'          => 'checkbox',
            'default'       => false,
            'section'       => 'display',
            'default'       => 'default',
        ],        
        'overrideCss' => [
            'label'         => 'Override Styles',
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
        'legacyManual' => [
            'label'         => 'Legacy User\'s Manual',
            'desc'          => 'Whether to include a link to the legacy User\'s Manual (English only) on the quick start dialog.',
            'type'          => 'checkbox',
            'default'       => false,
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
        ],    
        'historyLimit' => [
            'label'         => 'History Limit',
            'desc'          => 'Maximum number of items in history;  older items will be deleted. ',
            'type'          => 'integer',
            'default'       => 50,
            'section'       => 'features',
        ],

        // Autocomplete Settings
        'autocompleteEnable' => [
            'label'         => 'Autocomplete Enable',
            'desc'          => 'Whether to enable autocomplete on reference fields.',
            'type'          => 'checkbox',
            'default'       => true,
            'section'       => 'features',
        ],
        'autocompleteThreshold' => [
            'label'         => 'Autocomplete Threshold',
            'desc'          => 'Minimum number of characters before autocomplete is triggered.',
            'type'          => 'integer',
            'default'       => 2,
            'section'       => 'features',
            'row_classes'   => 'autocomplete_toggle',
        ],
        'autocompleteMatchAnywhere' => [
            'label'         => 'Autocomplete Match Anywhere',
            'desc'          => 'Whether to match anywhere in the given option / Book name.  &nbsp;Otherwise, we only match at the beginning of the name.',
            'type'          => 'checkbox',
            'default'       => false,
            'section'       => 'features',
            'row_classes'   => 'autocomplete_toggle',
        ],
        'autocompleteMaximumOptions'    => [
            'label'         => 'Autocomplete Maximum Options',
            'desc'          => 'Maximum number of autocomplete options to show at once.',
            'type'          => 'integer',
            'default'       => 10,
            'section'       => 'features',
            'row_classes'   => 'autocomplete_toggle',
        ],

        // Strongs / hover dialogs
        'hoverDelayThreshold' => [
            'label'         => 'Strong\'s Hover Delay Threshold',
            'desc'          => 'Time, in milliseconds, to wait after hovering before opening a hover dialog (ie Strongs)',
            'type'          => 'integer',
            'default'       => 500,
            'section'       => 'features',
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
    ],
    'general' => [
        
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

        // :todo
        // 'enableAllLanguages' => [
        //     'label'         => 'Display Language(s)',
        //     'desc'          => 'Enable ALL Languages',
        //     'type'          => 'checkbox',
        //     'default'       => true,
        //     'section'       => 'general',
        //     'render'        => false, // todo
        // ],
        // 'languageList' => [
        //     'label'         => '',
        //     'desc'          => 'Sets the display language(s) that can be selected by the user.',
        //     'type'          => 'select',
        //     'default'       => [],
        //     'section'       => 'general',
        //     'items'         => 'language',
        //     'multiple'      => true,
        //     'render'        => false, // todo
        // ],

        'navigation'        => [
            'label'         => 'Navigation',
            'type'          => 'section',
            'section'       => 'general',
        ],
        'swipePageChapter' => [
            'label'         => 'Touchscreen Swipe',
            'desc'          => 'Enables changing chapter and search page via horizontal touchscreen swipe gesture.',
            'type'          => 'checkbox',
            'default'       => false,
            'section'       => 'general',
        ],    
        'arrowKeysPageChapter' => [
            'label'         => 'Arrow Keys',
            'desc'          => 'Enables changing chapter and search page via left and right arrow keys.',
            'type'          => 'checkbox',
            'default'       => false,
            'section'       => 'general',
        ],
        'sideSwipePageChapter' => [
            'label'         => 'Side Buttons',
            'desc'          => 'Enables changing chapter and search page via fade-in side buttons.',
            'type'          => 'checkbox',
            'default'       => false,
            'section'       => 'general',
        ],    
        'sideSwipeHideWithNavigationButtons' => [
            'label'         => 'Side Buttons Hide With Navigation Buttons',
            'desc'          => 'Hide side buttons when navigation buttons are showing.',
            'type'          => 'checkbox',
            'default'       => false,
            'section'       => 'general',
        ],            
        'defaultDestinationPage' => [
            'label'         => 'Default Destination Page',
            'desc'          => 'Select a page or post containing the [biblesupersearch] shortcode, ' . 
                                'and all other Bible SuperSearch forms on your site will redirect here.<br /><br /> ' .
                                'This allows you to have the form on one page, but display the results on another.  ' . 
                                'Add the [biblesupersearch] shortcode to any page or post, and it will appear in this list.',
            'type'          => 'select',
            'default'       => '0',
            'section'       => 'features',
            'items'         => 'getLandingPageOptions'
        ],
    ],

    'bible' => [

        // :todo
        // 'enableAllBibles' => [
        //     'label'         => 'Enabled Bibles',
        //     'desc'          => 'Enable ALL Bibles',
        //     'type'          => 'checkbox',
        //     'default'       => true,
        //     'section'       => 'general',
        //     'render'        => false, // todo
        // ],
        // 'languageList' => [
        //     'label'         => '',
        //     'type'          => 'select',
        //     'default'       => [],
        //     'section'       => 'general',
        //     'items'         => 'getBiblesList ??', //  going to need to rebuild bible fetcher or something here
        //     'multiple'      => true,
        //     'render'        => false, // todo
        // ],


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
        ],    
        'landingReferenceDefault' => [
            'label'         => 'Use Landing Passage(s) as Default',
            'desc'          => 'If a search is executed with no search keywords or references, should we load the landing passage?',
            'type'          => 'checkbox',
            'default'       => false,
        ], 
        'parallelBibleCleanUpForce' => [
            'label'         => 'Force Parallel Bible Clean Up',
            'desc'          => 'If the parallel Bible limit is dynamically changed (ie by an expanding interface, or by limits set below)<br />' .
                                'should we remove Bible selections above the new limit?  Otherwise, the selections will remain.',
            'type'          => 'checkbox',
            'default'       => false,
        ], 

        // :todo
        // 'parallelBibleLimitByWidth' => [
        //     'label'         => 'Limit Parallel Bibles by Width',
        //     'type'          => 'custom',
        //     'render'        => false,
        //     'default'       => [],
        //     'format'        => 'json',
        // ],
    ],

    'advanced' => [        
        'apiUrl' => [
            'label'         => 'API URL',
            // :todo, make url in desc dynamic (default_options['apiUrl'])
            'desc'          => 'Leave blank for default of <code>https://api.biblesupersearch.com</code>.',
            'type'          => 'text',
            // 'default'       => dynamic, //
            'id'            => 'biblesupersearch_url'
        ],   
        'pageScrollTopPadding' => [
            'label'         => 'Scroll Top Padding',
            'desc'          => 'Use to adjust the final position of automatic scrolling.  ' . 
                                'A positive value will cause it to scroll further down, negative will scroll it further up.',
            'type'          => 'integer',
            'default'       => 0,
            'units'         => 'pixels',
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
        ],    
        'sideSwipeNavHideThresholdBottom' => [
            'label'         => 'Side Buttons Navigation Hide Threshold: Bottom',
            'desc'          => 'When Side Buttons Hide With Navigation Buttons is enabled, this pixel value adjusts when the buttons dissappear.',
            'type'          => 'integer',
            'default'       => 0,
            'units'         => 'pixels',
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
