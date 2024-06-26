<?php

/**
 * CAO Product Variants
 *
 * @author  Jay Trees <modified-cao-product-variants@grandels.email>
 * @link    https://github.com/grandeljay/modified-cao-product-variants
 * @package GrandeljayCaoProductVariants
 */

namespace Grandeljay\CaoProductVariants;

if (rth_is_module_disabled(Constants::MODULE_PRODUCT_NAME)) {
    return;
}

if (!$_SESSION['languages_id']) {
    return;
}

$variant = new Variant($product->data);

if (!$variant->isValid()) {
    return;
}

if ($variant->isParent()) {
    $info_smarty->assign('GRANDELJAY_CAO_PRODUCT_VARIANTS_IS_PARENT', true);
}

if (isset($_SESSION['customers_status']['customers_status_id']) && DEFAULT_CUSTOMERS_STATUS_ID_GUEST !== $_SESSION['customers_status']['customers_status_id']) {
    $info_smarty->assign('GRANDELJAY_CAO_PRODUCT_VARIANTS_NAME', $variant->getName());
    $info_smarty->assign('GRANDELJAY_CAO_PRODUCT_VARIANTS_DROPDOWN', $variant->getDropdown());
}
