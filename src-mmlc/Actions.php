<?php

namespace Grandeljay\CaoProductVariants;

class Actions
{
    public static function actionMigrate(): void
    {
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

            $shipping_status_id          = 0;
            $shipping_status_id_constant = Constants::MODULE_PRODUCT_NAME . '_' . Constants::CONFIGURATION_SHIPPING_STATUS_ID;

            if (defined($shipping_status_id_constant)) {
                $shipping_status_id = constant($shipping_status_id_constant);
            } else {
                $shipping_status_id_query = xtc_db_query(
                    sprintf(
                        'SELECT *
                          FROM `%s`
                         WHERE `configuration_key` = "%s"',
                        TABLE_CONFIGURATION,
                        $shipping_status_id_constant
                    )
                );

                $shipping_status_id = xtc_db_fetch_array($shipping_status_id_query)['configuration_value'];
            }

            xtc_db_query(
                sprintf(
                    'UPDATE `%1$s`
                        SET `%2$s`                  = "%3$s",
                            `products_shippingtime` = %4$s
                      WHERE `products_id` = %5$s',
                    TABLE_PRODUCTS,
                    Constants::COLUMN_PRODUCTS_VARIANTS,
                    $products_variants,
                    $shipping_status_id,
                    $product['products_id']
                )
            );
        }
    }
}
