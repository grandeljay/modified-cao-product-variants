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
    'TITLE'            => 'grandeljay - CAO Artikelvarianten',
    'TEXT_TILE'        => 'CAO Artikelvarianten',
    'LONG_DESCRIPTION' => 'Ermöglicht das verwenden von CAO Artikelvarianten im Shop.',
    'STATUS_TITLE'     => 'Status',
    'STATUS_DESC'      => 'Wählen Sie Ja um das Modul zu aktivieren und Nein um es zu deaktivieren.',
);

foreach ($translations as $key => $text) {
    $constant = Constants::MODULE_PRODUCT_NAME . '_' . $key;

    define($constant, $text);
}
