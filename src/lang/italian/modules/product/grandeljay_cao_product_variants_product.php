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
    'TITLE'                => 'grandeljay - Varianti di articoli CAO',
    'TEXT_TILE'            => 'Varianti di articoli CAO',
    'LONG_DESCRIPTION'     => 'Abilita l\'uso delle varianti di articoli CAO nel negozio.',
    'STATUS_TITLE'         => 'Stato',
    'STATUS_DESC'          => 'Selezioni Sì per attivare il modulo e No per disattivarlo.',
    'BUTTON_MIGRATE'       => 'Adotta le varianti',

    /** Dropdown */
    'DROPDOWN_NAME'        => 'Opzioni',
    'DROPDOWN_PLACEHOLDER' => 'Scelga la sua variante',
    'DROPDOWN_UNAVAILABLE' => 'Purtroppo, al momento non sono disponibili varianti.',
];

foreach ($translations as $key => $text) {
    $constant = Constants::MODULE_PRODUCT_NAME . '_' . $key;

    define($constant, $text);
}
