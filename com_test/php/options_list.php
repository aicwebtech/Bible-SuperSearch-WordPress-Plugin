<?php

return [
    'legacyManual' => [
        'label'         => 'Legacy User\'s Manual',
        'desc'          => 'Whether to include a link to the legacy User\'s Manual (English only) on the quick start dialog.',
        'type'          => 'checkbox',
        'default'       => false,
        'tab'           => 'general',
    ],    

    'navigation'        => [
        'label'         => 'Navigation',
        'type'          => 'section',
        'tab'           => 'general',
    ],

    'swipePageChapter' => [
        'label'         => 'Touchscreen Swipe',
        'desc'          => 'Enables changing chapter and search page via horizontal touchscreen swipe gesture.',
        'type'          => 'checkbox',
        'default'       => false,
        'tab'           => 'general',
    ],    
    'arrowKeysPageChapter' => [
        'label'         => 'Arrow Keys',
        'desc'          => 'Enables changing chapter and search page via left and right arrow keys.',
        'type'          => 'checkbox',
        'default'       => false,
        'tab'           => 'general',
    ],
    'sideSwipePageChapter' => [
        'label'         => 'Side Buttons',
        'desc'          => 'Enables changing chapter and search page via fade-in side buttons.',
        'type'          => 'checkbox',
        'default'       => false,
        'tab'           => 'general',
    ],    
    'sideSwipeHideWithNavigationButtons' => [
        'label'         => 'Side Buttons Hide With Navigation Buttons',
        'desc'          => 'Hide side buttons when navigation buttons are showing.',
        'type'          => 'checkbox',
        'default'       => false,
        'tab'           => 'general',
    ],    

    'shortcutsShowHidden' => [
        'label'         => 'Show Hidden Shortcuts',
        'desc'          => '(IE: Last Days Prophecy)',
        'type'          => 'checkbox',
        'default'       => false,
        'tab'           => 'general',
    ],    
    'bookmarksEnable' => [
        'label'         => 'Enable Bookmarks',
        'desc'          => '',
        'type'          => 'checkbox',
        'default'       => false,
        'tab'           => 'general',
    ],    
    'bookmarkLimit' => [
        'label'         => 'Bookmark Limit',
        'desc'          => 'Maximum number of bookmarks a user can have.',
        'type'          => 'integer',
        'default'       => 20,
        'tab'           => 'general',
    ],    
    'historyLimit' => [
        'label'         => 'History Limit',
        'desc'          => 'Maximum number of items in history;  older items will be deleted. ',
        'type'          => 'integer',
        'default'       => 50,
        'tab'           => 'general',
    ],

    // Autocomplete Settings
    'autocompleteEnable' => [
        'label'         => 'Autocomplete Enable',
        'desc'          => 'Whether to enable autocomplete on reference fields.',
        'type'          => 'checkbox',
        'default'       => true,
        'tab'           => 'general',
    ],
    'autocompleteThreshold' => [
        'label'         => 'Autocomplete Threshold',
        'desc'          => 'Minimum number of characters before autocomplete is triggered.',
        'type'          => 'integer',
        'default'       => 2,
        'tab'           => 'general',
        'row_classes'   => 'autocomplete_toggle',
    ],
    'autocompleteMatchAnywhere' => [
        'label'         => 'Autocomplete Match Anywhere',
        'desc'          => 'Whether to match anywhere in the given option / Book name.  &nbsp;Otherwise, we only match at the beginning of the name.',
        'type'          => 'checkbox',
        'default'       => false,
        'tab'           => 'general',
        'row_classes'   => 'autocomplete_toggle',
    ],
    'autocompleteMaximumOptions'    => [
        'label'         => 'Autocomplete Maximum Options',
        'desc'          => 'Maximum number of autocomplete options to show at once.',
        'type'          => 'integer',
        'default'       => 10,
        'tab'           => 'general',
        'row_classes'   => 'autocomplete_toggle',
    ],

    // Strongs / hover dialogs
    'hoverDelayThreshold' => [
        'label'         => 'Strong\'s Hover Delay Threshold',
        'desc'          => 'Time, in milliseconds, to wait after hovering before opening a hover dialog (ie Strongs)',
        'type'          => 'integer',
        'default'       => 500,
        'tab'           => 'general',
    ],
    'strongsOpenClick' => [
        'label'         => 'Strong\'s Dialog Open by Click',
        'desc'          => 'Whether clicking on Strong\'s number will open the dialog, otherwise clicking the link will search for the Strong\'s number.',
        'type'          => 'select',
        'default'       => 'mobile',
        'tab'           => 'general',
        'options'   => [
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
        'tab'           => 'general',
    ],

    'sideSwipeNavHideThresholdTop' => [
        'label'         => 'Side Buttons Navigation Hide Threshold: Top',
        'desc'          => 'When Side Buttons Hide With Navigation Buttons is enabled, this pixel value adjusts when the buttons dissappear.',
        'type'          => 'integer',
        'default'       => 0,
        'units'         => 'pixels',
        'tab'           => 'advanced',
    ],    
    'sideSwipeNavHideThresholdBottom' => [
        'label'         => 'Side Buttons Navigation Hide Threshold: Bottom',
        'desc'          => 'When Side Buttons Hide With Navigation Buttons is enabled, this pixel value adjusts when the buttons dissappear.',
        'type'          => 'integer',
        'default'       => 0,
        'units'         => 'pixels',
        'tab'           => 'advanced',
    ],
    'useNavigationShare' => [
        'label'         => 'Share Dialog',
        'desc'          => 'When sharing, whether to use the system share dialog (if available) or our generic share dialog.',
        'type'          => 'select',
        'default'       => 'never',
        'tab'           => 'general',
        'options'   => [
            'never'     => 'Always use generic share dialog',
            'mobile'    => 'Use system share dialog on mobile ONLY, use generic share dialog on desktop',
            'always'    => 'Always use system share dialog (if available), otherwise use generic share dialog (Experimental)',
        ],
    ],
];
