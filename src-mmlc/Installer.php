<?php

namespace Grandeljay\CaoProductVariants;

use grandeljay_cao_product_variants_product as CaoProductVariants;

class Installer
{
    private static CaoProductVariants $moduleCaoProductVariants;

    public static function install(CaoProductVariants $moduleCaoProductVariants): void
    {
        self::$moduleCaoProductVariants = $moduleCaoProductVariants;

        // self::installTableShippingStatus();
        // self::installTableProducts();
        self::installConfiguration();
    }

    private static function installTableProducts(): void
    {
        /**
         * For some reason adding another column could silently fail. I believe
         * the SQL error `#1118 - Row size too large` is related to this.
         *
         * I couldn't find the meaning or cause of the error, only this
         * "solution" to silence it.
         */
        xtc_db_query(
            sprintf(
                'ALTER TABLE `%s`
                  ROW_FORMAT = DYNAMIC',
                TABLE_PRODUCTS
            )
        );

        xtc_db_query(
            sprintf(
                'ALTER TABLE `%s`
                  ADD COLUMN `%s` JSON NULL DEFAULT NULL COMMENT "%s"',
                TABLE_PRODUCTS,
                Constants::COLUMN_PRODUCTS_VARIANTS,
                constant(Constants::MODULE_PRODUCT_NAME . '_TITLE')
            )
        );
    }

    private static function installTableShippingStatus(): void
    {
        $shipping_status_id_query  = xtc_db_query(
            sprintf(
                'SELECT MAX(`shipping_status_id`) AS "shipping_status_id_count"
                   FROM `%s`',
                TABLE_SHIPPING_STATUS
            )
        );
        $shipping_status_id_result = xtc_db_fetch_array($shipping_status_id_query);
        $shipping_status_id_count  = $shipping_status_id_result['shipping_status_id_count'];
        $shipping_status_id        = $shipping_status_id_count + 1;

        $languages_query = xtc_db_query(
            sprintf(
                'SELECT *
                   FROM `%s`',
                TABLE_LANGUAGES
            )
        );

        while ($language = xtc_db_fetch_array($languages_query)) {
            $shipping_status_name = match ($language['code']) {
                'de'    => 'Variante wählen',
                'es'    => 'Elija variante',
                'fr'    => 'Choisir une variante',
                'it'    => 'Scelga la variante',
                default => 'Select variant',
            };

            $sql_data_array = [
                'shipping_status_id'    => $shipping_status_id,
                'language_id'           => $language['languages_id'],
                'shipping_status_name'  => $shipping_status_name,
                'shipping_status_image' => 'grandeljay/cao-product-variants/light-blue.svg',
                'sort_order'            => 0,
            ];

            xtc_db_perform(TABLE_SHIPPING_STATUS, $sql_data_array);
        }

        self::$moduleCaoProductVariants->addConfiguration(Constants::CONFIGURATION_SHIPPING_STATUS_ID, $shipping_status_id, 6, 1);
    }

    private static function installConfiguration(): void
    {
        self::$moduleCaoProductVariants->addConfiguration(
            'CAO_DELIMITER',
            '@',
            6,
            1,
            'xtc_cfg_select_option(array(\',\', \';\', \'@\'),'
        );
    }

    public static function uninstall(CaoProductVariants $moduleCaoProductVariants): void
    {
        self::$moduleCaoProductVariants = $moduleCaoProductVariants;

        // self::uninstallTableShippingStatus();
        // self::uninstallTableProducts();
        self::uninstallConfiguration();
    }

    private static function uninstallTableProducts(): void
    {
        xtc_db_query(
            sprintf(
                'ALTER TABLE `%s`
                 DROP COLUMN `%s`',
                TABLE_PRODUCTS,
                Constants::COLUMN_PRODUCTS_VARIANTS
            )
        );
    }

    private static function uninstallTableShippingStatus(): void
    {
        xtc_db_query(
            sprintf(
                'DELETE FROM `%s`
                       WHERE `shipping_status_id` = %s',
                TABLE_SHIPPING_STATUS,
                self::$moduleCaoProductVariants->getConfig(Constants::CONFIGURATION_SHIPPING_STATUS_ID)
            )
        );

        self::$moduleCaoProductVariants->removeConfiguration(Constants::CONFIGURATION_SHIPPING_STATUS_ID);
    }

    private static function uninstallConfiguration(): void
    {
        self::$moduleCaoProductVariants->removeConfiguration('CAO_DELIMITER');
    }
}
