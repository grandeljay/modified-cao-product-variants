<?php

namespace Grandeljay\CaoProductVariants;

use grandeljay_cao_product_variants_product as CaoProductVariants;

class Installer
{
    private static CaoProductVariants $moduleCaoProductVariants;
    private static int $shipping_status_id;

    public static function install(CaoProductVariants $moduleCaoProductVariants): void
    {
        self::$moduleCaoProductVariants = $moduleCaoProductVariants;

        self::installTableShippingStatus();
        self::installTableProducts();
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

        /**
         * Migrate old variant columns
         */
        $variants_query = xtc_db_query(
            sprintf(
                'SELECT *
                   FROM `%s`
                  WHERE `products_var_parent_artnum` <> ""
                     OR `products_var_id`            <> ""
                     OR `products_varname`           <> ""
                     OR `products_vartext`           <> ""
                     OR `products_var_langtext`      <> ""',
                TABLE_PRODUCTS
            )
        );

        while ($product = xtc_db_fetch_array($variants_query)) {
            // {
            //     "names": {
            //         "2": "H\u00f6he (mm) - Volumen (L) - ECE 67R-01",
            //         "11": "Altezza (mm) - Volume (L) - ECE 67R-01",
            //         "13": "Altura (mm) - Volumen (L) - ECE 67R-01",
            //         "10": "Hauteur (mm) - Volume (L) - ECE 67R-01",
            //         "1": "Height (mm) - Volume (L) - ECE 67R-01"
            //     },
            //     "texts": {
            //         "2": "W\u00e4hlen Sie Ihre Variante",
            //         "11": "Scegli la tua variante",
            //         "13": "Elija su variante",
            //         "10": "\rChoisissez votre variante",
            //         "1": "Select your variant"
            //     },
            //     "parent": "",
            //     "ids": [
            //         "4515",
            //         "4999",
            //         "5000",
            //         "5001",
            //         "5002",
            //         "5003"
            //     ],
            //     "values": [
            //         "220mm - 47L",
            //         "230mm - 51L",
            //         "240mm - 53L - E20",
            //         "250mm - 54L",
            //         "270mm - 59L - E20"
            //     ]
            // }

            $products_variants = addslashes(
                json_encode(
                    array(
                        'names'  => empty($product['products_varname'])           ? array() : unserialize($product['products_varname']),
                        'texts'  => empty($product['products_vartext'])           ? array() : unserialize($product['products_vartext']),
                        'parent' => empty($product['products_var_parent_artnum']) ? ''      : $product['products_var_parent_artnum'],
                        'ids'    => empty($product['products_var_id'])            ? array() : explode(',', $product['products_var_id']),
                        'values' => empty($product['products_var_langtext'])      ? array() : explode(',', unserialize($product['products_var_langtext'])[2]),
                    )
                )
            );

            xtc_db_query(
                sprintf(
                    'UPDATE `%1$s`
                        SET `%2$s`                  = "%3$s",
                            `products_shippingtime` = %4$s
                      WHERE `products_id` = %5$s',
                    TABLE_PRODUCTS,
                    Constants::COLUMN_PRODUCTS_VARIANTS,
                    $products_variants,
                    self::$shipping_status_id,
                    $product['products_id']
                )
            );
        }
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

            $sql_data_array = array(
                'shipping_status_id'    => $shipping_status_id,
                'language_id'           => $language['languages_id'],
                'shipping_status_name'  => $shipping_status_name,
                'shipping_status_image' => 'grandeljay/cao-product-variants/light-blue.svg',
                'sort_order'            => 0,
            );

            xtc_db_perform(TABLE_SHIPPING_STATUS, $sql_data_array);
        }

        self::$shipping_status_id = $shipping_status_id;
        self::$moduleCaoProductVariants->addConfiguration(Constants::CONFIGURATION_SHIPPING_STATUS_ID, $shipping_status_id, 6, 1);
    }

    public static function uninstall(CaoProductVariants $moduleCaoProductVariants): void
    {
        self::$moduleCaoProductVariants = $moduleCaoProductVariants;

        self::uninstallTableProducts();
        self::uninstallTableShippingStatus();
    }

    private static function uninstallTableProducts(): void
    {
        // xtc_db_query(
        //     sprintf(
        //         'ALTER TABLE `%s`
        //          DROP COLUMN `%s`',
        //         TABLE_PRODUCTS,
        //         Constants::COLUMN_PRODUCTS_VARIANTS
        //     )
        // );
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
}
