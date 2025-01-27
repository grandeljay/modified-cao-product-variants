<?php

/**
 * CAO Product Variants
 *
 * @author  Jay Trees <modified-cao-product-variants@grandels.email>
 * @link    https://github.com/grandeljay/modified-cao-product-variants
 * @package GrandeljayCaoProductVariants
 *
 * @phpcs:disable PSR1.Classes.ClassDeclaration.MissingNamespace
 * @phpcs:disable Squiz.Classes.ValidClassName.NotCamelCaps
 */

use Grandeljay\CaoProductVariants\Constants;
use Grandeljay\CaoProductVariants\Module;
use Grandeljay\CaoProductVariants\Variant;
use RobinTheHood\ModifiedStdModule\Classes\StdModule;

class grandeljay_cao_product_variants_product extends StdModule
{
    use Module;

    public const VERSION = Constants::MODULE_PRODUCT_VERSION;

    public function __construct()
    {
        parent::__construct(Constants::MODULE_PRODUCT_NAME);

        $this->checkForUpdate(true);
        $this->addKeys();
    }

    public function install(): void
    {
        parent::install();

        $this->installConfiguration();
    }

    protected function updateSteps(): int
    {
        if (version_compare($this->getVersion(), self::VERSION, '<')) {
            $this->setVersion(self::VERSION);

            return self::UPDATE_SUCCESS;
        }

        return self::UPDATE_NOTHING;
    }

    public function remove()
    {
        parent::remove();

        $this->uninstallConfiguration();
    }

    /**
     * Extends the modified-shop product method `buildDataArray`.
     *
     * @param array  $product_data_smarty The product data with capitalised
     *                                    keys.
     * @param array  $product_data        The product data.
     * @param string $image               Unknown. Probably product image size.
     *
     * @return array
     */
    public function buildDataArray(array $product_data_smarty, array $product_data, string $image): array
    {
        if (!$this->getEnabled()) {
            return $product_data_smarty;
        }

        $variant = new Variant($product_data);

        if (!$variant->isValid()) {
            return $product_data_smarty;
        }

        if (!$variant->isParent()) {
            return $product_data_smarty;
        }

        $variant_price_lowest  = $variant->getLowestPrice();
        $variant_price_highest = $variant->getHighestPrice();

        $xtcPrice = new xtcPrice($_SESSION['currency'], $_SESSION['customers_status']['customers_status_id']);

        $product_data_smarty['GRANDELJAY_CAO_PRODUCT_VARIANTS']['PRICE_LOWEST']            = $variant_price_lowest;
        $product_data_smarty['GRANDELJAY_CAO_PRODUCT_VARIANTS']['PRICE_LOWEST_FORMATTED']  = $xtcPrice->xtcFormatCurrency($variant_price_lowest);
        $product_data_smarty['GRANDELJAY_CAO_PRODUCT_VARIANTS']['PRICE_HIGHEST']           = $variant_price_highest;
        $product_data_smarty['GRANDELJAY_CAO_PRODUCT_VARIANTS']['PRICE_HIGHEST_FORMATTED'] = $xtcPrice->xtcFormatCurrency($variant_price_highest);

        return $product_data_smarty;
    }
}
