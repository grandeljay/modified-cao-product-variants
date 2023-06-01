<?php

/**
 * CAO Product Variants
 *
 * @author  Jay Trees <modified-cao-product-variants@grandels.email>
 * @link    https://github.com/grandeljay/modified-cao-product-variants
 * @package GrandeljayCaoProductVariants
 */

namespace Grandeljay\CaoProductVariants;

class Variant
{
    /**
     * The modified-shop product data.
     *
     * @var array
     */
    private array $product_data;

    /**
     * The decoded variant data from the database.
     *
     * @var array
     */
    private array $product_data_variant;

    /**
     * The variant's ids
     *
     * @var array
     */
    private array $ids = array();

    /**
     * The variant's name.
     *
     * @var string
     */
    private string $name = '';

    /**
     * The variant's dropdown name.
     *
     * @var string
     */
    private string $dropdownName = '';

    /**
     * Whether the variant is a parent.
     *
     * @var boolean
     */
    private bool $isParent;

    /**
     * The variant's values
     *
     * @var array
     */
    private array $values = array();

    /**
     * Construct
     *
     * @param array $product_data The modified-shop product data.
     */
    public function __construct(array $product_data)
    {
        $this->product_data = $product_data;

        $language_id_current  = $_SESSION['languages_id'];
        $language_id_english  = 1;
        $language_id_fallback = $language_id_english;

        $this->product_data_variant = json_decode($this->product_data[Constants::COLUMN_PRODUCTS_VARIANTS], true);
        $this->isParent             = isset($this->product_data_variant['parent']) && '' === $this->product_data_variant['parent'];

        if (!$this->isParent) {
            $this->product_data_variant = $this->getVariantsFromParent($this->product_data_variant['parent']);
        }

        $this->name         = $this->product_data_variant['names'][$language_id_current] ?? $this->product_data_variant['names'][$language_id_fallback] ?? 'Options';
        $this->dropdownName = $this->product_data_variant['texts'][$language_id_current] ?? $this->product_data_variant['texts'][$language_id_fallback] ?? 'Select your variant';
        $this->values       = $this->product_data_variant['values'] ?? array();
        $this->ids          = $this->product_data_variant['ids']    ?? array();
    }

    /**
     * Returns whether the current variant is valid.
     *
     * @return boolean
     */
    public function isValid(): bool
    {
        if (!isset($this->product_data[Constants::COLUMN_PRODUCTS_VARIANTS])) {
            return false;
        }

        return true;
    }

    /**
     * Returns the variant's dropdown values.
     *
     * @return array
     */
    private function getDropdownValues(): array
    {
        $variant_dropdown_values = array();

        foreach ($this->ids as $index => $id) {
            $variant_product = new \product($id);

            if ('1' !== $variant_product->data['products_status']) {
                continue;
            }

            $variant_dropdown_values[] = array(
                'id'    => $id,
                'text'  => $this->values[$index]
                        ?? $variant_product->data['products_name']
                        ?? sprintf('Unknown description for product %s', $id),
                'order' => $variant_product->data['products_sort'] ?? '1',
            );
        }
        usort(
            $variant_dropdown_values,
            function ($dropdown_value_a, $dropdown_value_b) {
                return $dropdown_value_a['order'] <=> $dropdown_value_b['order'];
            }
        );

        return $variant_dropdown_values;
    }

    /**
     * Returns the variant name.
     *
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Returns the variant dropdown HTML.
     *
     * @return string
     */
    public function getDropdown(): string
    {
        $variant_dropdown_attribute_name = 'grandeljay_cao_product_variants_product';

        ob_start();
        ?>
        <script defer>
        document.addEventListener('DOMContentLoaded', function() {
            let dropdown_variants = document.querySelector('[name="<?= $variant_dropdown_attribute_name ?>"]');

            dropdown_variants.addEventListener('change', function() {
                let products_id = this.value;
                let variant_url = '<?= HTTPS_SERVER ?>/product_info.php?products_id=' + products_id;

                if (products_id && location.href !== variant_url) {
                    location.href = variant_url;
                }
            });
        });

        /**
         * Force the dropdown selection for when the user hits the back/forward
         * button in the browser.
         */
        window.addEventListener('pageshow', function() {
            function jQueryCheck() {
                let dropdown_variants = document.querySelector('[name="<?= $variant_dropdown_attribute_name ?>"]');

                if (window.jQuery && dropdown_variants.sumo) {
                    let products_id = '<?= $this->product_data['products_id'] ?>';

                    dropdown_variants.sumo.selectItem(products_id);
                }
                else {
                    setTimeout(jQueryCheck, 10);
                }
            }

            setTimeout(jQueryCheck, 10);
        });
        </script>
        <select name="<?= $variant_dropdown_attribute_name ?>">
            <option <?= $this->isParent ? 'selected="selected"' : '' ?> disabled="disabled" value=""><?= '-- ' . $this->dropdownName . ' --' ?></option>

            <?php foreach ($this->getDropdownValues() as $dropdown_value) { ?>
                <?php if ($dropdown_value['id'] === $this->product_data['products_id']) { ?>
                    <option selected="selected" value="<?= $dropdown_value['id'] ?>"><?= $dropdown_value['text'] ?></option>
                <?php } else { ?>
                    <option value="<?= $dropdown_value['id'] ?>"><?= $dropdown_value['text'] ?></option>
                <?php } ?>
            <?php } ?>
        </select>
        <?php
        $variant_dropdown = ob_get_clean();

        return $variant_dropdown;
    }

    /**
     * Returns the lowest price of all variants.
     *
     * @return float
     */
    public function getLowestPrice(): float
    {
        $lowest_price = 0;

        foreach ($this->ids as $id) {
            $product        = new \product($id);
            $products_price = $product->data['products_price'];

            if (0 === $products_price || null === $products_price) {
                continue;
            }

            if (0 === $lowest_price) {
                $lowest_price = $products_price;

                continue;
            }

            $lowest_price = min($lowest_price, $products_price);
        }

        return $lowest_price;
    }

    /**
     * Returns the highest price of all variants.
     *
     * @return float
     */
    public function getHighestPrice(): float
    {
        $highest_price = 0;

        foreach ($this->ids as $id) {
            $product        = new \product($id);
            $products_price = $product->data['products_price'];

            if (0 === $products_price || null === $products_price) {
                continue;
            }

            if (0 === $highest_price) {
                $highest_price = $products_price;

                continue;
            }

            $highest_price = max($highest_price, $products_price);
        }

        return $highest_price;
    }

    /**
     * Returns variant data for the specified parent.
     *
     * @param string $products_model The modified-shop product model.
     *
     * @return array
     */
    private function getVariantsFromParent(string $products_model): array
    {
        $parent_query = xtc_db_query(
            sprintf(
                'SELECT `%1$s`
                   FROM `%2$s`
                  WHERE `products_model`  = "%3$s"
                    AND `products_status` = 1',
                Constants::COLUMN_PRODUCTS_VARIANTS,
                TABLE_PRODUCTS,
                $products_model
            )
        );
        $parent       = xtc_db_fetch_array($parent_query);
        $variant_data = json_decode($parent[Constants::COLUMN_PRODUCTS_VARIANTS], true);

        return $variant_data;
    }
}
