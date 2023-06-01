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

$filename = 'includes/css/grandeljay_cao_product_variants_product.css';
$version  = hash_file('crc32c', DIR_FS_ADMIN . $filename);
?>
<link rel="stylesheet" type="text/css" href="<?php echo DIR_WS_ADMIN . $filename ?>?v=<?php echo $version ?>" />
