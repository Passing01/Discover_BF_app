<?php

return [
    // Available: 'gd', 'imagick'
    'renderer' => 'gd',

    // Default format for generated QR: 'png' or 'svg'
    'format' => 'png',

    // Default size in pixels
    'size' => 200,

    // Default margin around the QR
    'margin' => 0,

    // Error correction level: 'low', 'medium', 'quartile', 'high'
    'error_correction' => 'high',
];
