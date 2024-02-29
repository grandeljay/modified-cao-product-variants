<?php

/**
 * CAO Product Variants
 *
 * @author  Jay Trees <modified-cao-product-variants@grandels.email>
 * @link    https://github.com/grandeljay/modified-cao-product-variants
 * @package GrandeljayCaoProductVariants
 */

use Grandeljay\CaoProductVariants\Constants;

$translations = [
    /** Module */
    'TITLE'                => 'grandeljay - Variantes del artículo CAO',
    'TEXT_TILE'            => 'Variantes del artículo CAO',
    'LONG_DESCRIPTION'     => 'Permite el uso de variantes de artículos CAO en la tienda.',
    'STATUS_TITLE'         => 'Estado',
    'STATUS_DESC'          => 'Seleccione Sí para activar el módulo y No para desactivarlo.',
    'BUTTON_MIGRATE'       => 'Adoptar variantes',

    /** Dropdown */
    'DROPDOWN_NAME'        => 'Opciones',
    'DROPDOWN_PLACEHOLDER' => 'Elija su variante',
    'DROPDOWN_UNAVAILABLE' => 'Lamentablemente, por el momento no hay variantes disponibles.',
];

foreach ($translations as $key => $text) {
    $constant = Constants::MODULE_PRODUCT_NAME . '_' . $key;

    define($constant, $text);
}
