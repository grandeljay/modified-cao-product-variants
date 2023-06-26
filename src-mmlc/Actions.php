<?php

namespace Grandeljay\CaoProductVariants;

class Actions
{
    public static function actionMigrate(): void
    {
        /**
         * Method is otherwise called six times.
         */
        if (isset($_SESSION['grandeljay']['cao-product-variants']['actionMigrateOnce'])) {
            return;
        }

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

        while ($product_data = xtc_db_fetch_array($variants_query)) {
            $products_variants_data =  array(
                'names'  => empty($product_data['products_varname'])           ? array() : unserialize($product_data['products_varname']),
                'texts'  => empty($product_data['products_vartext'])           ? array() : unserialize($product_data['products_vartext']),
                'parent' => empty($product_data['products_var_parent_artnum']) ? ''      : $product_data['products_var_parent_artnum'],
                'ids'    => empty($product_data['products_var_id'])            ? array() : Variant::getItems($product_data['products_var_id']),
                'values' => empty($product_data['products_var_langtext'])      ? array() : Variant::getItems(unserialize($product_data['products_var_langtext'])[2]),
            );
            $products_variants      = addslashes(json_encode($products_variants_data));

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

            $product_is_variant                 = '' !== $products_variants_data['parent'] || array() !== $products_variants_data['ids'];
            $product_shippingtime_is_of_variant = (int) $shipping_status_id === (int) $product_data['products_shippingtime'];

            if (!$product_is_variant && $product_shippingtime_is_of_variant) {
                $shipping_status_id = DEFAULT_SHIPPING_STATUS_ID;
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
                    $product_data['products_id']
                )
            );
        }

        $_SESSION['grandeljay']['cao-product-variants']['actionMigrateOnce'] = true;
    }
}
