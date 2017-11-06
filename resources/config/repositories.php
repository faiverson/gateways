<?php
return [
    // if you want to use fractal package (http://fractal.thephpleague.com/)
    'fractal' => false,

    'namespace' => 'App/Providers',

    'paginate' => 10, // how many item from collection you want to display in the paginate method

    // directories where you want to put your classes
    'path' => [
        'repositories' => 'Repositories',
        'interfaces' => 'Repositories/Interfaces',
        'models' => 'Models',
        'transformers' => 'Transformers',
        'gateways' => 'Repositories/Gateways',
    ],

    // meta information for fractal library
    'meta' => [
        "version" => "1.0.0",
        "copyright" => "Copyright " . date('Y') . " Fabian Torres",
        "authors" => [
            "Fabian Torres"
        ]
    ]
];
