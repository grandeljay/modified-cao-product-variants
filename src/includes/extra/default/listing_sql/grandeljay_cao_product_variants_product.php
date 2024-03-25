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

if (rth_is_module_disabled(Constants::MODULE_PRODUCT_NAME) || \FILENAME_SPECIALS === \basename($PHP_SELF)) {
    return;
}

/** Show variants and children while filtering */
if (isset($_GET['filter'])) {
    return;
}

$search       = 'WHERE p.products_status = \'1\'';
$variants_sql = 'AND (
       `p`.`products_variants` IS NULL
    OR `p`.`products_variants` = \'[]\'
    OR `p`.`products_variants` LIKE \'%%"parent":""%%\'
)';
$replace_sql  = $search . PHP_EOL . $variants_sql;
$replace      = sprintf($replace_sql, Constants::COLUMN_PRODUCTS_VARIANTS);
$subject      = $listing_sql;

$listing_sql = str_replace($search, $replace, $subject);
