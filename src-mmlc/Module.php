<?php

namespace Grandeljay\CaoProductVariants;

trait Module
{
    private array $keys = [
        'CAO_DELIMITER',
    ];

    private function addKeys(): void
    {
        foreach ($this->keys as $key) {
            $this->addKey($key);
        }
    }

    private function installConfiguration(): void
    {
        // $this->installTableShippingStatus();
        // $this->installTableProducts();
        $this->installConfigurationCao();
    }

    private function installTableShippingStatus(): void
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

        $this->addConfiguration(Constants::CONFIGURATION_SHIPPING_STATUS_ID, $shipping_status_id, 6, 1);
    }

    private function installTableProducts(): void
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

    private function installConfigurationCao(): void
    {
        static $delimiter_comma     = ',';
        static $delimiter_semicolon = ';';
        static $delimiter_at        = '@';

        $this->addConfiguration(
            'CAO_DELIMITER',
            $delimiter_at,
            6,
            1,
            \sprintf(
                'xtc_cfg_select_option(["%s", "%s", "%s"]),',
                $delimiter_comma,
                $delimiter_semicolon,
                $delimiter_at
            )
        );
    }

    private function uninstallConfiguration(): void
    {
        foreach ($this->keys as $key) {
            $this->removeConfiguration($key);
        }

        // $this->uninstallTableShippingStatus();
        // $this->uninstallTableProducts();
    }

    private function uninstallTableShippingStatus(): void
    {
        xtc_db_query(
            sprintf(
                'DELETE FROM `%s`
                       WHERE `shipping_status_id` = %s',
                TABLE_SHIPPING_STATUS,
                $this->getConfig(Constants::CONFIGURATION_SHIPPING_STATUS_ID)
            )
        );

        $this->removeConfiguration(Constants::CONFIGURATION_SHIPPING_STATUS_ID);
    }

    private function uninstallTableProducts(): void
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
}
