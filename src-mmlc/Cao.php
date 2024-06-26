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

        /** The module is not active in CAO, skip removing association. */
        if (
            !isset(
                $_POST['products_variantname'],
                $_POST['products_varianttext'],
                $_POST['products_var_parent_artnum'],
                $_POST['products_var_id'],
                $_POST['products_var_text'],
            )
        ) {
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
        $product              = [];

        if (false === $product_exists) {
            return;
        }

        if (isset($_POST['products_sort']) && \is_numeric($_POST['products_sort'])) {
            $product['products_sort'] = $_POST['products_sort'];
        }

        /**
         * @see https://www.cao-faktura.de/produkte/erweiterungen/modul-artikelvarianten/
         */
        $products_variant = [];

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
        if (isset($_POST['products_var_text'], $_POST['products_var_langtext'])) {
            $variant_values = Variant::getValues($_POST['products_var_text'], $_POST['products_var_langtext']);

            $products_variant['values'] = $variant_values;
        }

        $product['products_shippingtime']             = constant(Constants::MODULE_PRODUCT_NAME . '_' . Constants::CONFIGURATION_SHIPPING_STATUS_ID);
        $product['products_last_modified']            = 'NOW()';
        $product[Constants::COLUMN_PRODUCTS_VARIANTS] = json_encode($products_variant);

        if (Variant::isEmpty($products_variant)) {
            $product[Constants::COLUMN_PRODUCTS_VARIANTS] = 'null';
        }

        xtc_db_perform(TABLE_PRODUCTS, $product, 'update', 'products_id = ' . $_POST['pID']);
    }
}
