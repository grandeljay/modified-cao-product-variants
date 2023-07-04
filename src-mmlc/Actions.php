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

        while ($product_data = xtc_db_fetch_array($variants_query)) {
            $products_variants_data =  array(
                'names'  => empty($product_data['products_varname'])           ? array() : unserialize($product_data['products_varname']),
                'texts'  => empty($product_data['products_vartext'])           ? array() : unserialize($product_data['products_vartext']),
                'parent' => empty($product_data['products_var_parent_artnum']) ? ''      : $product_data['products_var_parent_artnum'],
                'ids'    => empty($product_data['products_var_id'])            ? array() : Variant::getItems($product_data['products_var_id']),
                'values' => empty($product_data['products_var_langtext'])      ? array() : Variant::getItems(unserialize($product_data['products_var_langtext'])[2]),
            );
            $products_variants      = addslashes(json_encode($products_variants_data));

            xtc_db_query(
                sprintf(
                    'UPDATE `%1$s`
                        SET `%2$s` = "%3$s"
                      WHERE `products_id` = %4$s',
                    TABLE_PRODUCTS,
                    Constants::COLUMN_PRODUCTS_VARIANTS,
                    $products_variants,
                    $product_data['products_id']
                )
            );
        }
    }
}
