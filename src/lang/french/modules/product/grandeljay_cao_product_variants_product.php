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
    'TITLE'                => 'grandeljay - CAO Variantes d\'articles',
    'TEXT_TILE'            => 'CAO Variantes d\'articles',
    'LONG_DESCRIPTION'     => 'Permet d\'utiliser les variantes d\'articles CAO dans la boutique.',
    'STATUS_TITLE'         => 'Statut',
    'STATUS_DESC'          => 'Sélectionnez Oui pour activer le module et Non pour le désactiver.',
    'BUTTON_MIGRATE'       => 'Reprendre des variantes',

    /** Dropdown */
    'DROPDOWN_NAME'        => 'Options',
    'DROPDOWN_PLACEHOLDER' => 'Choisissez votre variante',
    'DROPDOWN_UNAVAILABLE' => 'Malheureusement, aucune variante n\'est disponible pour le moment.',
);

foreach ($translations as $key => $text) {
    $constant = Constants::MODULE_PRODUCT_NAME . '_' . $key;

    define($constant, $text);
}
