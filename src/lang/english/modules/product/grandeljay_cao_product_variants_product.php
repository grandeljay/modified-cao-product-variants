<?php

/**
 * CAO Product Variants
 *
 * @author  Jay Trees <modified-cao-product-variants@grandels.email>
 * @link    https://github.com/grandeljay/modified-cao-product-variants
 * @package GrandeljayCaoProductVariants
 */

use Grandeljay\CaoProductVariants\Constants;

$translations = array(
    /** Module */
    'TITLE'                => 'grandeljay - CAO Product Variants',
    'TEXT_TILE'            => 'CAO Product Variants',
    'LONG_DESCRIPTION'     => 'Enables the use of CAO product variants in the shop.',
    'STATUS_TITLE'         => 'Status',
    'STATUS_DESC'          => 'Select Yes to activate the module and No to deactivate it.',
    'BUTTON_MIGRATE'       => 'Migrate variants',

    /** Dropdown */
    'DROPDOWN_NAME'        => 'Options',
    'DROPDOWN_PLACEHOLDER' => 'Choose your variant',
    'DROPDOWN_UNAVAILABLE' => 'Unfortunately, there are no variants available at the moment.',
);

foreach ($translations as $key => $text) {
    $constant = Constants::MODULE_PRODUCT_NAME . '_' . $key;

    define($constant, $text);
}
