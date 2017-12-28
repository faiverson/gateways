<?php
return [
    // where to put the provider repository file
    'namespace' => 'App/Providers',

    'paginate' => 10, // how many item from collection you want to display in the paginate method

    // directories where you want to put your classes inside App
    'path' => [
        'repositories' => 'Repositories',
        'interfaces' => 'Repositories/Interfaces',
        'models' => 'Models',
        'gateways' => 'Repositories/Gateways',
    ]
];
