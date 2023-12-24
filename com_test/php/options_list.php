<?php

return [


    // Autocomplete Settings
    'autocompleteEnable' => [
        'label'         => 'Autocomplete Enable',
        'desc'          => 'Whether to enable autocomplete on reference fields',
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
        'label'         => 'Match Anywhere',
        'desc'          => 'Whether to match anywhere in the given option / Book name.  &nbsp;Otherwise, we only match at the beginning of the name.',
        'type'          => 'checkbox',
        'default'       => false,
        'tab'           => 'general',
        'row_classes'   => 'autocomplete_toggle',
    ],
    'autocompleteMaximumOptions'    => [
        'label'         => 'Maximum Options',
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
];
