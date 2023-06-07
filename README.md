# CAO Product Variants

Allows using the [CAO Artikelvarianten](https://www.cao-faktura.de/doku/cao-faktura/ErweiterungVarianten.html) module with your shop.

## Preperation

Before installing the module in modified, some preperational steps are required to ensure everything works properly.

1. Rename template directory

    Rename the directory `/templates/tpl_modified_responsive_6` to match your template name. If you already use this template you don't have to do anything.

1. Edit product page template

    Next open up the product template (for instance `/templates/tpl_modified_responsive_6/module/product_info/product_info_tabs_v1.html`) and insert the dropdown used for variants.

    Here's an example:

    ```html
    {if $GRANDELJAY_CAO_PRODUCT_VARIANTS_NAME}
    <div class="pd_infobox cf">
        <div class="pd_inforow">
            <span class="pd_strong"
                >{$GRANDELJAY_CAO_PRODUCT_VARIANTS_NAME}:</span
            >{$GRANDELJAY_CAO_PRODUCT_VARIANTS_DROPDOWN}
        </div>
    </div>
    {/if}
    ```

1. Edit price templates

    If you would like to show price ranges for variants you can also edit `/templates/tpl_modified_responsive_6/module/includes/price_info.html` and `/templates/tpl_modified_responsive_6/module/includes/price_box.html` by adding the following code to the **beginning** of the if/then/else statement:

    ### `price_box.html`

    ```html
    {if $price_data.GRANDELJAY_CAO_PRODUCT_VARIANTS.PRICE_LOWEST && $price_data.GRANDELJAY_CAO_PRODUCT_VARIANTS.PRICE_HIGHEST}
        {* Start variants price *}
        <span class="uvp_price" itemprop="offers" itemscope itemtype="http://schema.org/AggregateOffer">
            <span class="small_price">{$smarty.const.YOUR_PRICE}</span>
            <small>{$smarty.const.FROM}</small> {$price_data.GRANDELJAY_CAO_PRODUCT_VARIANTS.PRICE_LOWEST}

            <meta itemprop="lowPrice" content="{$price_data.GRANDELJAY_CAO_PRODUCT_VARIANTS.PRICE_LOWEST}" />
            <meta itemprop="highPrice" content="{$price_data.GRANDELJAY_CAO_PRODUCT_VARIANTS.PRICE_HIGHEST}" />
        </span>
        {* End variants price *}
    {elseif isset($price_data.GRANDELJAY_CAO_PRODUCT_VARIANTS.PRICE_LOWEST)}
        {* Start variants price *}
        <span class="uvp_price" itemprop="offers" itemscope itemtype="http://schema.org/AggregateOffer>
            {$smarty.const.GRADUATED_PRICE_MAX_VALUE} {$price_data.GRANDELJAY_CAO_PRODUCT_VARIANTS.PRICE_LOWEST}

            <meta itemprop="lowPrice" content="{$price_data.GRANDELJAY_CAO_PRODUCT_VARIANTS.PRICE_LOWEST}" />
        </span>
        {* End variant price *}
    [...]
    ```

    ### `price_info.html`

    ```smarty
    {if $price_data.GRANDELJAY_CAO_PRODUCT_VARIANTS.PRICE_LOWEST && $price_data.GRANDELJAY_CAO_PRODUCT_VARIANTS.PRICE_HIGHEST}
        {* Start variants price *}
        <span class="uvp_price" itemprop="offers" itemscope itemtype="http://schema.org/AggregateOffer">
            <span class="small_price">{$smarty.const.YOUR_PRICE}</span>
            {$price_data.GRANDELJAY_CAO_PRODUCT_VARIANTS.PRICE_LOWEST} - {$price_data.GRANDELJAY_CAO_PRODUCT_VARIANTS.PRICE_HIGHEST}

            <meta itemprop="lowPrice" content="{$price_data.GRANDELJAY_CAO_PRODUCT_VARIANTS.PRICE_LOWEST}" />
            <meta itemprop="highPrice" content="{$price_data.GRANDELJAY_CAO_PRODUCT_VARIANTS.PRICE_HIGHEST}" />
        </span>
        {* End variants price *}
    [...]
    ```

## Installation

Enable the module under _Class Extensions Modules_ in the _product_ tab (`/admin/modules.php?set=product&module=grandeljay_cao_product_variants_product`).
