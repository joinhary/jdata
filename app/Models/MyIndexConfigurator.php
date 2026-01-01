<?php

namespace App\Models;

use ScoutElastic\IndexConfigurator;
use ScoutElastic\Migratable;

class MyIndexConfigurator extends IndexConfigurator
{
    use Migratable;
    protected $name = 'vi_index';

    /**
     * @var array
     */
    protected $settings = [
        'analysis' => [
            'analyzer' => [
                'my_analyzer' => [
                    'tokenizer' => 'vi_tokenizer',
                    'char_filter' => [
                        'html_strip',
                    ],
                    'filter' => [
                        'icu_folding'
                    ],
                ],
            ],
        ],
    ];
}
