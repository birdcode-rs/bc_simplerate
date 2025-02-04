<?php

$iconList = [];

foreach ([
    'ext-bc-simplerate-plugin-pi1' => 'star-rating-icon.svg',
] as $identifier => $path) {
    $iconList[$identifier] = [
        'provider' => \TYPO3\CMS\Core\Imaging\IconProvider\SvgIconProvider::class,
        'source' => 'EXT:bc_simplerate/Resources/Public/Icons/' . $path,
    ];
}

return $iconList;
