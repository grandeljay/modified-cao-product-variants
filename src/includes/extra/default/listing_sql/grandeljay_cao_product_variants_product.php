<?php

/**
 * CAO Product Variants
 *
 * Only show products in the category overview which aren't variants or are
 * variant parents.
 *
 * @author  Jay Trees <modified-cao-product-variants@grandels.email>
 * @link    https://github.com/grandeljay/modified-cao-product-variants
 * @package GrandeljayCaoProductVariants
 */

namespace Grandeljay\CaoProductVariants;

if (rth_is_module_disabled(Constants::MODULE_PRODUCT_NAME)) {
    return;
}


$search  = 'WHERE p.products_status = \'1\'';
$replace = sprintf($search . PHP_EOL . 'AND p.%s LIKE \'%%"parent":""%%\'', Constants::COLUMN_PRODUCTS_VARIANTS);
$subject = $listing_sql;

$listing_sql = str_replace($search, $replace, $subject);
