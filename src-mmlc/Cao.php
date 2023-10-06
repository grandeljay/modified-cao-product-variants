<?php

namespace Grandeljay\CaoProductVariants;

class Cao
{
    public static function new(): self
    {
        return new self();
    }

    public function __construct()
    {
    }

    /**
     * Updates the product variant using the payload.
     *
     * @uses $_POST The request payload.
     *
     * @return void
     */
    public function productsUpdate(): void
    {
        if (!isset($_POST['pID'])) {
            return;
        }

        $product_exists_query = xtc_db_query(
            sprintf(
                'SELECT *
                   FROM `%1$s`
                  WHERE `products_id` = %2$s',
                TABLE_PRODUCTS,
                $_POST['pID']
            )
        );
        $product_exists       = xtc_db_fetch_array($product_exists_query);
        $product              = array();

        if (false === $product_exists) {
            return;
        }

        if (isset($_POST['products_sort']) && is_int($_POST['products_sort'])) {
            $product['products_sort'] = $_POST['products_sort'];
        }

        /**
         * @see https://www.cao-faktura.de/produkte/erweiterungen/modul-artikelvarianten/
         */
        $products_variant = array();

        /**
         * Variant names
         *
         * @var array A key => value array pair where the key represents the CAO
         *            language id and the value the name (for instance "Height
         *            (mm) - Volume (L) - ECE 67R-01").
         */
        if (isset($_POST['products_varname'])) {
            $products_variant['names'] = $_POST['products_varname'];
        }

        /**
         * Variant texts
         *
         * @var array A key => value array pair where the key represents the CAO
         *            language id and the value the text (for instance "Select
         *            your variant").
         */
        if (isset($_POST['products_vartext'])) {
            $products_variant['texts'] = $_POST['products_vartext'];
        }

        /**
         * Variant parent
         *
         * @var string The product variant parent model (for instance "109921").
         */
        if (isset($_POST['products_var_parent_artnum'])) {
            $products_variant['parent'] = $_POST['products_var_parent_artnum'];
        }

        /**
         * Variant IDs
         *
         * @var string The product variant ids.
         */
        if (isset($_POST['products_var_id'])) {
            $variant_shop_ids = array_map(
                function ($shop_id) {
                    return trim($shop_id);
                },
                Variant::getItems($_POST['products_var_id'])
            );
            $variant_shop_ids = \array_filter(
                $variant_shop_ids,
                function ($shop_id) {
                    return !empty($shop_id);
                }
            );

            $products_variant['ids'] = $variant_shop_ids;
        }

        /**
         * Variant values
         *
         * @var string The product variant values.
         */
        if (isset($_POST['products_var_text'])) {
            $variant_values = array_map(
                function ($shop_id) {
                    return trim($shop_id);
                },
                Variant::getItems($_POST['products_var_text'])
            );

            $products_variant['values'] = $variant_values;
        }

        $product['products_shippingtime']             = constant(Constants::MODULE_PRODUCT_NAME . '_' . Constants::CONFIGURATION_SHIPPING_STATUS_ID);
        $product['products_last_modified']            = 'NOW()';
        $product[Constants::COLUMN_PRODUCTS_VARIANTS] = json_encode($products_variant);

        xtc_db_perform(TABLE_PRODUCTS, $product, 'update', 'products_id = ' . $_POST['pID']);
    }
}
