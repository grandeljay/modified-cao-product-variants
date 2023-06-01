<?php

namespace Grandeljay\CaoProductVariants;

if (rth_is_module_disabled(Constants::MODULE_PRODUCT_NAME)) {
    return;
}

$additional_fields = array(
    'p.products_variants',
);

$add_select_default = array_merge($add_select_default, $additional_fields);
$add_select_search  = array_merge($add_select_search, $additional_fields);
$add_select_product = array_merge($add_select_product, $additional_fields);
