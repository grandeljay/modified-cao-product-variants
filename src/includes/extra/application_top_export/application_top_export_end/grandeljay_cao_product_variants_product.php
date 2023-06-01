<?php

/**
 * CAO Product Variants
 *
 * @author  Jay Trees <modified-cao-product-variants@grandels.email>
 * @link    https://github.com/grandeljay/modified-cao-product-variants
 * @package GrandeljayCaoProductVariants
 */

namespace Grandeljay\CaoProductVariants;

if (rth_is_module_enabled(Constants::MODULE_PRODUCT_NAME)) {
    Cao::new()->productsUpdate();
}
