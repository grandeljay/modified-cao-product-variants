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

use RobinTheHood\ModifiedStdModule\Classes\StdModule;
use Grandeljay\CaoProductVariants\{Actions, Constants, Installer, Variant};

class grandeljay_cao_product_variants_product extends StdModule
{
    public const VERSION = Constants::MODULE_PRODUCT_VERSION;

    public function __construct()
    {
        parent::__construct(Constants::MODULE_PRODUCT_NAME);

        $this->checkForUpdate(true);

        $this->addAction(
            'actionMigrate',
            $this->getConfig('BUTTON_MIGRATE')
        );
    }

    public function install(): void
    {
        parent::install();

        Installer::install($this);
    }

    protected function updateSteps()
    {
        if (-1 === version_compare($this->getVersion(), self::VERSION)) {
            $this->setVersion(self::VERSION);

            return self::UPDATE_SUCCESS;
        }

        return self::UPDATE_NOTHING;
    }

    public function remove()
    {
        parent::remove();

        Installer::uninstall($this);
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

        $variant_price_lowest  = $variant->getLowestPrice();
        $variant_price_highest = $variant->getHighestPrice();

        $xtcPrice = new xtcPrice($_SESSION['currency'], $_SESSION['customers_status']['customers_status_id']);

        $product_data_smarty['PRODUCTS_PRICE_ARRAY'][0]['GRANDELJAY_CAO_PRODUCT_VARIANTS']['PRICE_LOWEST']  = $xtcPrice->xtcFormatCurrency($variant_price_lowest);
        $product_data_smarty['PRODUCTS_PRICE_ARRAY'][0]['GRANDELJAY_CAO_PRODUCT_VARIANTS']['PRICE_HIGHEST'] = $xtcPrice->xtcFormatCurrency($variant_price_highest);

        return $product_data_smarty;
    }

    protected function invokeActionMigrate(): void
    {
        call_user_func(Actions::class . '::actionMigrate');
    }
}
