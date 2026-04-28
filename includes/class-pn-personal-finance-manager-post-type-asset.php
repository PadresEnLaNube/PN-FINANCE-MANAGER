<?php
/**
 * Asset creator.
 *
 * This class defines Asset options, menus and templates.
 *
 * @link       padresenlanube.com/
 * @since      1.0.0
 * @package    PN_PERSONAL_FINANCE_MANAGER
 * @subpackage PN_PERSONAL_FINANCE_MANAGER/includes
 * @author     Padres en la Nube
 */

if ( ! defined( 'ABSPATH' ) ) exit;

class PN_PERSONAL_FINANCE_MANAGER_Post_Type_Asset {
  public function pn_personal_finance_manager_asset_get_fields($asset_id = 0) {
    $pn_personal_finance_manager_fields = [];
      $pn_personal_finance_manager_fields['pn_personal_finance_manager_asset_title'] = [
        'id' => 'pn_personal_finance_manager_asset_title',
        'class' => 'pn-personal-finance-manager-input pn-personal-finance-manager-width-100-percent',
        'input' => 'input',
        'type' => 'text',
        'required' => false,
        'value' => !empty($asset_id) ? esc_html(get_the_title($asset_id)) : '',
        'label' => __('Asset title', 'pn-personal-finance-manager'),
        'placeholder' => __('Asset title', 'pn-personal-finance-manager'),
      ];
      $pn_personal_finance_manager_fields['pn_personal_finance_manager_asset_description'] = [
        'id' => 'pn_personal_finance_manager_asset_description',
        'class' => 'pn-personal-finance-manager-input pn-personal-finance-manager-width-100-percent',
        'input' => 'textarea',
        'required' => false,
        'value' => !empty($asset_id) ? (str_replace(']]>', ']]&gt;', apply_filters('the_content', get_post($asset_id)->post_content))) : '',
        'label' => __('Comments', 'pn-personal-finance-manager'),
        'placeholder' => __('Optional comments', 'pn-personal-finance-manager'),
      ];
      $pn_personal_finance_manager_fields_meta['pn_personal_finance_manager_ajax_nonce'] = [
        'id' => 'pn_personal_finance_manager_ajax_nonce',
        'input' => 'input',
        'type' => 'nonce',
      ];
    return $pn_personal_finance_manager_fields;
  }

  public function pn_personal_finance_manager_asset_get_fields_meta() {
    $pn_personal_finance_manager_fields_meta = [];
      $pn_personal_finance_manager_fields_meta['pn_personal_finance_manager_asset_date'] = [
        'id' => 'pn_personal_finance_manager_asset_date',
        'class' => 'pn-personal-finance-manager-input pn-personal-finance-manager-width-100-percent',
        'input' => 'input',
        'type' => 'date',
        'label' => __('Asset purchase date', 'pn-personal-finance-manager'),
        'placeholder' => __('Asset purchase date', 'pn-personal-finance-manager'),
      ];
      $pn_personal_finance_manager_fields_meta['pn_personal_finance_manager_asset_time'] = [
        'id' => 'pn_personal_finance_manager_asset_time',
        'class' => 'pn-personal-finance-manager-input pn-personal-finance-manager-width-100-percent',
        'input' => 'input',
        'type' => 'time',
        'label' => __('Asset purchase time', 'pn-personal-finance-manager'),
        'placeholder' => __('Asset purchase time', 'pn-personal-finance-manager'),
      ];

      $pn_personal_finance_manager_fields_meta['pn_personal_finance_manager_asset_sold'] = [
        'id' => 'pn_personal_finance_manager_asset_sold',
        'input' => 'input',
        'type' => 'checkbox',
        'label' => __('Asset Sold', 'pn-personal-finance-manager'),
        'parent' => 'this',
      ];
      $pn_personal_finance_manager_fields_meta['pn_personal_finance_manager_asset_sold_date'] = [
        'id' => 'pn_personal_finance_manager_asset_sold_date',
        'class' => 'pn-personal-finance-manager-input pn-personal-finance-manager-width-100-percent',
        'input' => 'input',
        'type' => 'date',
        'label' => __('Sale Date', 'pn-personal-finance-manager'),
        'placeholder' => __('Sale date', 'pn-personal-finance-manager'),
        'parent' => 'pn_personal_finance_manager_asset_sold',
        'parent_option' => 'on',
      ];
      $pn_personal_finance_manager_fields_meta['pn_personal_finance_manager_asset_sold_price'] = [
        'id' => 'pn_personal_finance_manager_asset_sold_price',
        'class' => 'pn-personal-finance-manager-input pn-personal-finance-manager-width-100-percent',
        'input' => 'input',
        'type' => 'number',
        'step' => '0.01',
        'min' => '0',
        'label' => __('Sale Price', 'pn-personal-finance-manager'),
        'placeholder' => __('Enter sale price', 'pn-personal-finance-manager'),
        'description' => __('Per unit for stocks/crypto, total for other asset types.', 'pn-personal-finance-manager'),
        'parent' => 'pn_personal_finance_manager_asset_sold',
        'parent_option' => 'on',
      ];

      $pn_personal_finance_manager_fields_meta['pn_personal_finance_manager_asset_type'] = [
        'id' => 'pn_personal_finance_manager_asset_type',
        'class' => 'pn-personal-finance-manager-select pn-personal-finance-manager-width-100-percent',
        'input' => 'select',
        'parent' => 'this',
        'options' => PN_PERSONAL_FINANCE_MANAGER_Data::pn_personal_finance_manager_get_asset_types(),
        'label' => __('Asset type', 'pn-personal-finance-manager'),
        'placeholder' => __('Select asset type', 'pn-personal-finance-manager'),
      ];

      // Stock selection field - only shows when stocks is selected
      $pn_personal_finance_manager_fields_meta['pn_personal_finance_manager_stock_symbol'] = [
        'id' => 'pn_personal_finance_manager_stock_symbol',
        'class' => 'pn-personal-finance-manager-select pn-personal-finance-manager-width-100-percent',
        'input' => 'select',
        'options' => ['' => __('Loading stock symbols...', 'pn-personal-finance-manager')],
        'label' => __('Stock Symbol', 'pn-personal-finance-manager'),
        'placeholder' => __('Select stock symbol', 'pn-personal-finance-manager'),
        'parent' => 'pn_personal_finance_manager_asset_type',
        'parent_option' => 'stocks',
        'description' => __('Select the stock symbol for this asset. The list is populated from the configured stock API.', 'pn-personal-finance-manager'),
      ];

      // Total amount field - only shows when stocks is selected
      $pn_personal_finance_manager_fields_meta['pn_personal_finance_manager_stock_total_amount'] = [
        'id' => 'pn_personal_finance_manager_stock_total_amount',
        'class' => 'pn-personal-finance-manager-input pn-personal-finance-manager-width-100-percent',
        'input' => 'input',
        'type' => 'number',
        'step' => '0.0001',
        'min' => '0',
        'label' => __('Total Amount', 'pn-personal-finance-manager'),
        'placeholder' => __('Enter number of shares', 'pn-personal-finance-manager'),
        'parent' => 'pn_personal_finance_manager_asset_type',
        'parent_option' => 'stocks',
        'description' => __('Enter the total number of shares purchased for this stock asset.', 'pn-personal-finance-manager'),
      ];

      $pn_personal_finance_manager_fields_meta['pn_personal_finance_manager_stock_sector'] = [
        'id' => 'pn_personal_finance_manager_stock_sector',
        'class' => 'pn-personal-finance-manager-select pn-personal-finance-manager-width-100-percent',
        'input' => 'select',
        'options' => [
          '' => __('Select sector', 'pn-personal-finance-manager'),
          'technology' => __('Technology', 'pn-personal-finance-manager'),
          'healthcare' => __('Healthcare', 'pn-personal-finance-manager'),
          'financials' => __('Financials', 'pn-personal-finance-manager'),
          'consumer_discretionary' => __('Consumer Discretionary', 'pn-personal-finance-manager'),
          'consumer_staples' => __('Consumer Staples', 'pn-personal-finance-manager'),
          'industrials' => __('Industrials', 'pn-personal-finance-manager'),
          'energy' => __('Energy', 'pn-personal-finance-manager'),
          'utilities' => __('Utilities', 'pn-personal-finance-manager'),
          'real_estate' => __('Real Estate', 'pn-personal-finance-manager'),
          'materials' => __('Materials', 'pn-personal-finance-manager'),
          'communication' => __('Communication Services', 'pn-personal-finance-manager'),
          'other' => __('Other', 'pn-personal-finance-manager'),
        ],
        'label' => __('Sector', 'pn-personal-finance-manager'),
        'placeholder' => __('Select sector', 'pn-personal-finance-manager'),
        'parent' => 'pn_personal_finance_manager_asset_type',
        'parent_option' => 'stocks',
      ];
      $pn_personal_finance_manager_fields_meta['pn_personal_finance_manager_stock_country'] = [
        'id' => 'pn_personal_finance_manager_stock_country',
        'class' => 'pn-personal-finance-manager-select pn-personal-finance-manager-width-100-percent',
        'input' => 'select',
        'options' => [
          '' => __('Select country', 'pn-personal-finance-manager'),
          'us' => __('United States', 'pn-personal-finance-manager'),
          'cn' => __('China', 'pn-personal-finance-manager'),
          'jp' => __('Japan', 'pn-personal-finance-manager'),
          'gb' => __('United Kingdom', 'pn-personal-finance-manager'),
          'de' => __('Germany', 'pn-personal-finance-manager'),
          'fr' => __('France', 'pn-personal-finance-manager'),
          'ca' => __('Canada', 'pn-personal-finance-manager'),
          'ch' => __('Switzerland', 'pn-personal-finance-manager'),
          'au' => __('Australia', 'pn-personal-finance-manager'),
          'kr' => __('South Korea', 'pn-personal-finance-manager'),
          'tw' => __('Taiwan', 'pn-personal-finance-manager'),
          'in' => __('India', 'pn-personal-finance-manager'),
          'br' => __('Brazil', 'pn-personal-finance-manager'),
          'nl' => __('Netherlands', 'pn-personal-finance-manager'),
          'es' => __('Spain', 'pn-personal-finance-manager'),
          'it' => __('Italy', 'pn-personal-finance-manager'),
          'se' => __('Sweden', 'pn-personal-finance-manager'),
          'ie' => __('Ireland', 'pn-personal-finance-manager'),
          'dk' => __('Denmark', 'pn-personal-finance-manager'),
          'no' => __('Norway', 'pn-personal-finance-manager'),
          'fi' => __('Finland', 'pn-personal-finance-manager'),
          'il' => __('Israel', 'pn-personal-finance-manager'),
          'sg' => __('Singapore', 'pn-personal-finance-manager'),
          'hk' => __('Hong Kong', 'pn-personal-finance-manager'),
          'mx' => __('Mexico', 'pn-personal-finance-manager'),
          'ar' => __('Argentina', 'pn-personal-finance-manager'),
          'other' => __('Other', 'pn-personal-finance-manager'),
        ],
        'label' => __('Country', 'pn-personal-finance-manager'),
        'placeholder' => __('Select country', 'pn-personal-finance-manager'),
        'parent' => 'pn_personal_finance_manager_asset_type',
        'parent_option' => 'stocks',
      ];

      // Real Estate fields - only show when real_estate is selected
      $pn_personal_finance_manager_fields_meta['real_estate_purchase_price'] = [
        'id' => 'real_estate_purchase_price',
        'class' => 'pn-personal-finance-manager-input pn-personal-finance-manager-width-100-percent',
        'input' => 'input',
        'type' => 'number',
        'step' => '0.01',
        'min' => '0',
        'label' => __('Purchase Price', 'pn-personal-finance-manager'),
        'placeholder' => __('Enter purchase price', 'pn-personal-finance-manager'),
        'parent' => 'pn_personal_finance_manager_asset_type',
        'parent_option' => 'real_estate',
        'description' => __('Enter the purchase price of the real estate property.', 'pn-personal-finance-manager'),
      ];

      $pn_personal_finance_manager_fields_meta['real_estate_current_value'] = [
        'id' => 'real_estate_current_value',
        'class' => 'pn-personal-finance-manager-input pn-personal-finance-manager-width-100-percent',
        'input' => 'input',
        'type' => 'number',
        'step' => '0.01',
        'min' => '0',
        'label' => __('Current Value', 'pn-personal-finance-manager'),
        'placeholder' => __('Enter current market value', 'pn-personal-finance-manager'),
        'parent' => 'pn_personal_finance_manager_asset_type',
        'parent_option' => 'real_estate',
        'description' => __('Enter the current market value of the real estate property.', 'pn-personal-finance-manager'),
      ];

      $pn_personal_finance_manager_fields_meta['real_estate_ownership_percent'] = [
        'id' => 'real_estate_ownership_percent',
        'class' => 'pn-personal-finance-manager-field pn-personal-finance-manager-width-100-percent',
        'input' => 'range',
        'type' => 'range',
        'step' => '1',
        'pn_personal_finance_manager_min' => '0',
        'pn_personal_finance_manager_max' => '100',
        'pn_personal_finance_manager_label_min' => '0%',
        'pn_personal_finance_manager_label_max' => '100%',
        'button_text' => '100',
        'label' => __('Ownership Percentage', 'pn-personal-finance-manager'),
        'parent' => 'pn_personal_finance_manager_asset_type',
        'parent_option' => 'real_estate',
        'description' => __('Select your ownership percentage of this property.', 'pn-personal-finance-manager'),
      ];

      // Cryptocurrencies sub-fields
      $pn_personal_finance_manager_fields_meta['pn_personal_finance_manager_crypto_symbol'] = [
        'id' => 'pn_personal_finance_manager_crypto_symbol',
        'class' => 'pn-personal-finance-manager-select pn-personal-finance-manager-width-100-percent',
        'input' => 'select',
        'options' => ['' => __('Loading cryptocurrencies...', 'pn-personal-finance-manager')],
        'label' => __('Cryptocurrency', 'pn-personal-finance-manager'),
        'placeholder' => __('Select cryptocurrency', 'pn-personal-finance-manager'),
        'parent' => 'pn_personal_finance_manager_asset_type',
        'parent_option' => 'cryptocurrencies',
        'description' => __('Select the cryptocurrency for this asset. The list is populated from CoinGecko sorted by market cap.', 'pn-personal-finance-manager'),
      ];
      $pn_personal_finance_manager_fields_meta['pn_personal_finance_manager_crypto_amount'] = [
        'id' => 'pn_personal_finance_manager_crypto_amount',
        'class' => 'pn-personal-finance-manager-input pn-personal-finance-manager-width-100-percent',
        'input' => 'input',
        'type' => 'number',
        'step' => '0.00000001',
        'min' => '0',
        'label' => __('Amount', 'pn-personal-finance-manager'),
        'placeholder' => __('Enter amount', 'pn-personal-finance-manager'),
        'parent' => 'pn_personal_finance_manager_asset_type',
        'parent_option' => 'cryptocurrencies',
      ];
      $pn_personal_finance_manager_fields_meta['pn_personal_finance_manager_crypto_purchase_price'] = [
        'id' => 'pn_personal_finance_manager_crypto_purchase_price',
        'class' => 'pn-personal-finance-manager-input pn-personal-finance-manager-width-100-percent',
        'input' => 'input',
        'type' => 'number',
        'step' => '0.01',
        'min' => '0',
        'label' => __('Purchase Price (USD)', 'pn-personal-finance-manager'),
        'placeholder' => __('Enter purchase price', 'pn-personal-finance-manager'),
        'parent' => 'pn_personal_finance_manager_asset_type',
        'parent_option' => 'cryptocurrencies',
      ];
      // Commodities sub-fields
      $pn_personal_finance_manager_fields_meta['pn_personal_finance_manager_commodity_name'] = [
        'id' => 'pn_personal_finance_manager_commodity_name',
        'class' => 'pn-personal-finance-manager-input pn-personal-finance-manager-width-100-percent',
        'input' => 'input',
        'type' => 'text',
        'label' => __('Commodity Name (e.g. Oil, Wheat)', 'pn-personal-finance-manager'),
        'placeholder' => __('e.g. Oil, Wheat', 'pn-personal-finance-manager'),
        'parent' => 'pn_personal_finance_manager_asset_type',
        'parent_option' => 'commodities',
      ];
      $pn_personal_finance_manager_fields_meta['pn_personal_finance_manager_commodity_amount'] = [
        'id' => 'pn_personal_finance_manager_commodity_amount',
        'class' => 'pn-personal-finance-manager-input pn-personal-finance-manager-width-100-percent',
        'input' => 'input',
        'type' => 'number',
        'step' => '0.0001',
        'min' => '0',
        'label' => __('Amount', 'pn-personal-finance-manager'),
        'placeholder' => __('Enter amount', 'pn-personal-finance-manager'),
        'parent' => 'pn_personal_finance_manager_asset_type',
        'parent_option' => 'commodities',
      ];
      $pn_personal_finance_manager_fields_meta['pn_personal_finance_manager_commodity_unit'] = [
        'id' => 'pn_personal_finance_manager_commodity_unit',
        'class' => 'pn-personal-finance-manager-select pn-personal-finance-manager-width-100-percent',
        'input' => 'select',
        'options' => [
          'barrel' => __('Barrel', 'pn-personal-finance-manager'),
          'ounce' => __('Ounce', 'pn-personal-finance-manager'),
          'kg' => __('Kilogram', 'pn-personal-finance-manager'),
          'ton' => __('Ton', 'pn-personal-finance-manager'),
          'bushel' => __('Bushel', 'pn-personal-finance-manager'),
          'gallon' => __('Gallon', 'pn-personal-finance-manager'),
        ],
        'label' => __('Unit', 'pn-personal-finance-manager'),
        'placeholder' => __('Select unit', 'pn-personal-finance-manager'),
        'parent' => 'pn_personal_finance_manager_asset_type',
        'parent_option' => 'commodities',
      ];
      $pn_personal_finance_manager_fields_meta['pn_personal_finance_manager_commodity_purchase_price'] = [
        'id' => 'pn_personal_finance_manager_commodity_purchase_price',
        'class' => 'pn-personal-finance-manager-input pn-personal-finance-manager-width-100-percent',
        'input' => 'input',
        'type' => 'number',
        'step' => '0.01',
        'min' => '0',
        'label' => __('Purchase Price (USD)', 'pn-personal-finance-manager'),
        'placeholder' => __('Enter purchase price', 'pn-personal-finance-manager'),
        'parent' => 'pn_personal_finance_manager_asset_type',
        'parent_option' => 'commodities',
      ];
      $pn_personal_finance_manager_fields_meta['pn_personal_finance_manager_commodity_current_value'] = [
        'id' => 'pn_personal_finance_manager_commodity_current_value',
        'class' => 'pn-personal-finance-manager-input pn-personal-finance-manager-width-100-percent',
        'input' => 'input',
        'type' => 'number',
        'step' => '0.01',
        'min' => '0',
        'label' => __('Current Value (USD)', 'pn-personal-finance-manager'),
        'placeholder' => __('Enter current value', 'pn-personal-finance-manager'),
        'parent' => 'pn_personal_finance_manager_asset_type',
        'parent_option' => 'commodities',
      ];

      // Art & Collectibles sub-fields
      $pn_personal_finance_manager_fields_meta['pn_personal_finance_manager_art_category'] = [
        'id' => 'pn_personal_finance_manager_art_category',
        'class' => 'pn-personal-finance-manager-select pn-personal-finance-manager-width-100-percent',
        'input' => 'select',
        'options' => [
          'painting' => __('Painting', 'pn-personal-finance-manager'),
          'sculpture' => __('Sculpture', 'pn-personal-finance-manager'),
          'photograph' => __('Photograph', 'pn-personal-finance-manager'),
          'antique' => __('Antique', 'pn-personal-finance-manager'),
          'coins' => __('Coins', 'pn-personal-finance-manager'),
          'stamps' => __('Stamps', 'pn-personal-finance-manager'),
          'wine' => __('Wine', 'pn-personal-finance-manager'),
          'memorabilia' => __('Memorabilia', 'pn-personal-finance-manager'),
          'other' => __('Other', 'pn-personal-finance-manager'),
        ],
        'label' => __('Category', 'pn-personal-finance-manager'),
        'placeholder' => __('Select category', 'pn-personal-finance-manager'),
        'parent' => 'pn_personal_finance_manager_asset_type',
        'parent_option' => 'art_collectibles',
      ];
      $pn_personal_finance_manager_fields_meta['pn_personal_finance_manager_art_purchase_price'] = [
        'id' => 'pn_personal_finance_manager_art_purchase_price',
        'class' => 'pn-personal-finance-manager-input pn-personal-finance-manager-width-100-percent',
        'input' => 'input',
        'type' => 'number',
        'step' => '0.01',
        'min' => '0',
        'label' => __('Purchase Price', 'pn-personal-finance-manager'),
        'placeholder' => __('Enter purchase price', 'pn-personal-finance-manager'),
        'parent' => 'pn_personal_finance_manager_asset_type',
        'parent_option' => 'art_collectibles',
      ];
      $pn_personal_finance_manager_fields_meta['pn_personal_finance_manager_art_estimated_value'] = [
        'id' => 'pn_personal_finance_manager_art_estimated_value',
        'class' => 'pn-personal-finance-manager-input pn-personal-finance-manager-width-100-percent',
        'input' => 'input',
        'type' => 'number',
        'step' => '0.01',
        'min' => '0',
        'label' => __('Estimated Current Value', 'pn-personal-finance-manager'),
        'placeholder' => __('Enter estimated value', 'pn-personal-finance-manager'),
        'parent' => 'pn_personal_finance_manager_asset_type',
        'parent_option' => 'art_collectibles',
      ];

      // Precious Metals sub-fields
      $pn_personal_finance_manager_fields_meta['pn_personal_finance_manager_metal_type'] = [
        'id' => 'pn_personal_finance_manager_metal_type',
        'class' => 'pn-personal-finance-manager-select pn-personal-finance-manager-width-100-percent',
        'input' => 'select',
        'options' => [
          'gold' => __('Gold', 'pn-personal-finance-manager'),
          'silver' => __('Silver', 'pn-personal-finance-manager'),
          'platinum' => __('Platinum', 'pn-personal-finance-manager'),
          'palladium' => __('Palladium', 'pn-personal-finance-manager'),
        ],
        'label' => __('Metal Type', 'pn-personal-finance-manager'),
        'placeholder' => __('Select metal type', 'pn-personal-finance-manager'),
        'parent' => 'pn_personal_finance_manager_asset_type',
        'parent_option' => 'precious_metals',
      ];
      $pn_personal_finance_manager_fields_meta['pn_personal_finance_manager_metal_weight'] = [
        'id' => 'pn_personal_finance_manager_metal_weight',
        'class' => 'pn-personal-finance-manager-input pn-personal-finance-manager-width-100-percent',
        'input' => 'input',
        'type' => 'number',
        'step' => '0.001',
        'min' => '0',
        'label' => __('Weight', 'pn-personal-finance-manager'),
        'placeholder' => __('Enter weight', 'pn-personal-finance-manager'),
        'parent' => 'pn_personal_finance_manager_asset_type',
        'parent_option' => 'precious_metals',
      ];
      $pn_personal_finance_manager_fields_meta['pn_personal_finance_manager_metal_unit'] = [
        'id' => 'pn_personal_finance_manager_metal_unit',
        'class' => 'pn-personal-finance-manager-select pn-personal-finance-manager-width-100-percent',
        'input' => 'select',
        'options' => [
          'oz' => __('Troy Ounce (oz)', 'pn-personal-finance-manager'),
          'g' => __('Gram (g)', 'pn-personal-finance-manager'),
          'kg' => __('Kilogram (kg)', 'pn-personal-finance-manager'),
        ],
        'label' => __('Unit', 'pn-personal-finance-manager'),
        'placeholder' => __('Select unit', 'pn-personal-finance-manager'),
        'parent' => 'pn_personal_finance_manager_asset_type',
        'parent_option' => 'precious_metals',
      ];
      $pn_personal_finance_manager_fields_meta['pn_personal_finance_manager_metal_purchase_price'] = [
        'id' => 'pn_personal_finance_manager_metal_purchase_price',
        'class' => 'pn-personal-finance-manager-input pn-personal-finance-manager-width-100-percent',
        'input' => 'input',
        'type' => 'number',
        'step' => '0.01',
        'min' => '0',
        'label' => __('Purchase Price (USD)', 'pn-personal-finance-manager'),
        'placeholder' => __('Enter purchase price', 'pn-personal-finance-manager'),
        'parent' => 'pn_personal_finance_manager_asset_type',
        'parent_option' => 'precious_metals',
      ];
      $pn_personal_finance_manager_fields_meta['pn_personal_finance_manager_metal_current_value'] = [
        'id' => 'pn_personal_finance_manager_metal_current_value',
        'class' => 'pn-personal-finance-manager-input pn-personal-finance-manager-width-100-percent',
        'input' => 'input',
        'type' => 'number',
        'step' => '0.01',
        'min' => '0',
        'label' => __('Current Value (USD)', 'pn-personal-finance-manager'),
        'placeholder' => __('Enter current value', 'pn-personal-finance-manager'),
        'parent' => 'pn_personal_finance_manager_asset_type',
        'parent_option' => 'precious_metals',
      ];

      // Bonds sub-fields
      $pn_personal_finance_manager_fields_meta['pn_personal_finance_manager_bond_issuer'] = [
        'id' => 'pn_personal_finance_manager_bond_issuer',
        'class' => 'pn-personal-finance-manager-input pn-personal-finance-manager-width-100-percent',
        'input' => 'input',
        'type' => 'text',
        'label' => __('Issuer', 'pn-personal-finance-manager'),
        'placeholder' => __('Enter bond issuer', 'pn-personal-finance-manager'),
        'parent' => 'pn_personal_finance_manager_asset_type',
        'parent_option' => 'bonds',
      ];
      $pn_personal_finance_manager_fields_meta['pn_personal_finance_manager_bond_face_value'] = [
        'id' => 'pn_personal_finance_manager_bond_face_value',
        'class' => 'pn-personal-finance-manager-input pn-personal-finance-manager-width-100-percent',
        'input' => 'input',
        'type' => 'number',
        'step' => '0.01',
        'min' => '0',
        'label' => __('Face Value', 'pn-personal-finance-manager'),
        'placeholder' => __('Enter face value', 'pn-personal-finance-manager'),
        'parent' => 'pn_personal_finance_manager_asset_type',
        'parent_option' => 'bonds',
      ];
      $pn_personal_finance_manager_fields_meta['pn_personal_finance_manager_bond_interest_rate'] = [
        'id' => 'pn_personal_finance_manager_bond_interest_rate',
        'class' => 'pn-personal-finance-manager-input pn-personal-finance-manager-width-100-percent',
        'input' => 'input',
        'type' => 'number',
        'step' => '0.01',
        'min' => '0',
        'max' => '100',
        'label' => __('Interest Rate (%)', 'pn-personal-finance-manager'),
        'placeholder' => __('Enter interest rate', 'pn-personal-finance-manager'),
        'parent' => 'pn_personal_finance_manager_asset_type',
        'parent_option' => 'bonds',
      ];
      $pn_personal_finance_manager_fields_meta['pn_personal_finance_manager_bond_maturity_date'] = [
        'id' => 'pn_personal_finance_manager_bond_maturity_date',
        'class' => 'pn-personal-finance-manager-input pn-personal-finance-manager-width-100-percent',
        'input' => 'input',
        'type' => 'date',
        'label' => __('Maturity Date', 'pn-personal-finance-manager'),
        'placeholder' => __('Select maturity date', 'pn-personal-finance-manager'),
        'parent' => 'pn_personal_finance_manager_asset_type',
        'parent_option' => 'bonds',
      ];
      $pn_personal_finance_manager_fields_meta['pn_personal_finance_manager_bond_current_value'] = [
        'id' => 'pn_personal_finance_manager_bond_current_value',
        'class' => 'pn-personal-finance-manager-input pn-personal-finance-manager-width-100-percent',
        'input' => 'input',
        'type' => 'number',
        'step' => '0.01',
        'min' => '0',
        'label' => __('Current Value', 'pn-personal-finance-manager'),
        'placeholder' => __('Enter current value', 'pn-personal-finance-manager'),
        'parent' => 'pn_personal_finance_manager_asset_type',
        'parent_option' => 'bonds',
      ];

      // Vehicles sub-fields
      $pn_personal_finance_manager_fields_meta['pn_personal_finance_manager_vehicle_make_model'] = [
        'id' => 'pn_personal_finance_manager_vehicle_make_model',
        'class' => 'pn-personal-finance-manager-input pn-personal-finance-manager-width-100-percent',
        'input' => 'input',
        'type' => 'text',
        'label' => __('Make & Model', 'pn-personal-finance-manager'),
        'placeholder' => __('e.g. Toyota Camry', 'pn-personal-finance-manager'),
        'parent' => 'pn_personal_finance_manager_asset_type',
        'parent_option' => 'vehicles',
      ];
      $pn_personal_finance_manager_fields_meta['pn_personal_finance_manager_vehicle_year'] = [
        'id' => 'pn_personal_finance_manager_vehicle_year',
        'class' => 'pn-personal-finance-manager-input pn-personal-finance-manager-width-100-percent',
        'input' => 'input',
        'type' => 'number',
        'min' => '1900',
        'max' => '2100',
        'label' => __('Year', 'pn-personal-finance-manager'),
        'placeholder' => __('Enter year', 'pn-personal-finance-manager'),
        'parent' => 'pn_personal_finance_manager_asset_type',
        'parent_option' => 'vehicles',
      ];
      $pn_personal_finance_manager_fields_meta['pn_personal_finance_manager_vehicle_purchase_price'] = [
        'id' => 'pn_personal_finance_manager_vehicle_purchase_price',
        'class' => 'pn-personal-finance-manager-input pn-personal-finance-manager-width-100-percent',
        'input' => 'input',
        'type' => 'number',
        'step' => '0.01',
        'min' => '0',
        'label' => __('Purchase Price', 'pn-personal-finance-manager'),
        'placeholder' => __('Enter purchase price', 'pn-personal-finance-manager'),
        'parent' => 'pn_personal_finance_manager_asset_type',
        'parent_option' => 'vehicles',
      ];
      $pn_personal_finance_manager_fields_meta['pn_personal_finance_manager_vehicle_current_value'] = [
        'id' => 'pn_personal_finance_manager_vehicle_current_value',
        'class' => 'pn-personal-finance-manager-input pn-personal-finance-manager-width-100-percent',
        'input' => 'input',
        'type' => 'number',
        'step' => '0.01',
        'min' => '0',
        'label' => __('Current Value', 'pn-personal-finance-manager'),
        'placeholder' => __('Enter current value', 'pn-personal-finance-manager'),
        'parent' => 'pn_personal_finance_manager_asset_type',
        'parent_option' => 'vehicles',
      ];

      // Business Equity sub-fields
      $pn_personal_finance_manager_fields_meta['pn_personal_finance_manager_business_name'] = [
        'id' => 'pn_personal_finance_manager_business_name',
        'class' => 'pn-personal-finance-manager-input pn-personal-finance-manager-width-100-percent',
        'input' => 'input',
        'type' => 'text',
        'label' => __('Business Name', 'pn-personal-finance-manager'),
        'placeholder' => __('Enter business name', 'pn-personal-finance-manager'),
        'parent' => 'pn_personal_finance_manager_asset_type',
        'parent_option' => 'business_equity',
      ];
      $pn_personal_finance_manager_fields_meta['pn_personal_finance_manager_business_ownership_percent'] = [
        'id' => 'pn_personal_finance_manager_business_ownership_percent',
        'class' => 'pn-personal-finance-manager-input pn-personal-finance-manager-width-100-percent',
        'input' => 'input',
        'type' => 'number',
        'step' => '0.01',
        'min' => '0',
        'max' => '100',
        'label' => __('Ownership (%)', 'pn-personal-finance-manager'),
        'placeholder' => __('Enter ownership percentage', 'pn-personal-finance-manager'),
        'parent' => 'pn_personal_finance_manager_asset_type',
        'parent_option' => 'business_equity',
      ];
      $pn_personal_finance_manager_fields_meta['pn_personal_finance_manager_business_valuation'] = [
        'id' => 'pn_personal_finance_manager_business_valuation',
        'class' => 'pn-personal-finance-manager-input pn-personal-finance-manager-width-100-percent',
        'input' => 'input',
        'type' => 'number',
        'step' => '0.01',
        'min' => '0',
        'label' => __('Business Valuation', 'pn-personal-finance-manager'),
        'placeholder' => __('Enter business valuation', 'pn-personal-finance-manager'),
        'parent' => 'pn_personal_finance_manager_asset_type',
        'parent_option' => 'business_equity',
      ];

      // Retirement Accounts sub-fields
      $pn_personal_finance_manager_fields_meta['pn_personal_finance_manager_retirement_type'] = [
        'id' => 'pn_personal_finance_manager_retirement_type',
        'class' => 'pn-personal-finance-manager-select pn-personal-finance-manager-width-100-percent',
        'input' => 'select',
        'options' => [
          '401k' => __('401(k)', 'pn-personal-finance-manager'),
          'ira' => __('IRA', 'pn-personal-finance-manager'),
          'roth_ira' => __('Roth IRA', 'pn-personal-finance-manager'),
          'pension' => __('Pension', 'pn-personal-finance-manager'),
          'other' => __('Other', 'pn-personal-finance-manager'),
        ],
        'label' => __('Account Type', 'pn-personal-finance-manager'),
        'placeholder' => __('Select account type', 'pn-personal-finance-manager'),
        'parent' => 'pn_personal_finance_manager_asset_type',
        'parent_option' => 'retirement_accounts',
      ];
      $pn_personal_finance_manager_fields_meta['pn_personal_finance_manager_retirement_provider'] = [
        'id' => 'pn_personal_finance_manager_retirement_provider',
        'class' => 'pn-personal-finance-manager-input pn-personal-finance-manager-width-100-percent',
        'input' => 'input',
        'type' => 'text',
        'label' => __('Provider', 'pn-personal-finance-manager'),
        'placeholder' => __('Enter provider name', 'pn-personal-finance-manager'),
        'parent' => 'pn_personal_finance_manager_asset_type',
        'parent_option' => 'retirement_accounts',
      ];
      $pn_personal_finance_manager_fields_meta['pn_personal_finance_manager_retirement_balance'] = [
        'id' => 'pn_personal_finance_manager_retirement_balance',
        'class' => 'pn-personal-finance-manager-input pn-personal-finance-manager-width-100-percent',
        'input' => 'input',
        'type' => 'number',
        'step' => '0.01',
        'min' => '0',
        'label' => __('Current Balance', 'pn-personal-finance-manager'),
        'placeholder' => __('Enter current balance', 'pn-personal-finance-manager'),
        'parent' => 'pn_personal_finance_manager_asset_type',
        'parent_option' => 'retirement_accounts',
      ];

      // Insurance Policies sub-fields
      $pn_personal_finance_manager_fields_meta['pn_personal_finance_manager_insurance_type'] = [
        'id' => 'pn_personal_finance_manager_insurance_type',
        'class' => 'pn-personal-finance-manager-select pn-personal-finance-manager-width-100-percent',
        'input' => 'select',
        'options' => [
          'life' => __('Life', 'pn-personal-finance-manager'),
          'health' => __('Health', 'pn-personal-finance-manager'),
          'property' => __('Property', 'pn-personal-finance-manager'),
          'auto' => __('Auto', 'pn-personal-finance-manager'),
          'other' => __('Other', 'pn-personal-finance-manager'),
        ],
        'label' => __('Policy Type', 'pn-personal-finance-manager'),
        'placeholder' => __('Select policy type', 'pn-personal-finance-manager'),
        'parent' => 'pn_personal_finance_manager_asset_type',
        'parent_option' => 'insurance_policies',
      ];
      $pn_personal_finance_manager_fields_meta['pn_personal_finance_manager_insurance_provider'] = [
        'id' => 'pn_personal_finance_manager_insurance_provider',
        'class' => 'pn-personal-finance-manager-input pn-personal-finance-manager-width-100-percent',
        'input' => 'input',
        'type' => 'text',
        'label' => __('Provider', 'pn-personal-finance-manager'),
        'placeholder' => __('Enter provider name', 'pn-personal-finance-manager'),
        'parent' => 'pn_personal_finance_manager_asset_type',
        'parent_option' => 'insurance_policies',
      ];
      $pn_personal_finance_manager_fields_meta['pn_personal_finance_manager_insurance_value'] = [
        'id' => 'pn_personal_finance_manager_insurance_value',
        'class' => 'pn-personal-finance-manager-input pn-personal-finance-manager-width-100-percent',
        'input' => 'input',
        'type' => 'number',
        'step' => '0.01',
        'min' => '0',
        'label' => __('Policy Value', 'pn-personal-finance-manager'),
        'placeholder' => __('Enter policy value', 'pn-personal-finance-manager'),
        'parent' => 'pn_personal_finance_manager_asset_type',
        'parent_option' => 'insurance_policies',
      ];
      $pn_personal_finance_manager_fields_meta['pn_personal_finance_manager_insurance_premium'] = [
        'id' => 'pn_personal_finance_manager_insurance_premium',
        'class' => 'pn-personal-finance-manager-input pn-personal-finance-manager-width-100-percent',
        'input' => 'input',
        'type' => 'number',
        'step' => '0.01',
        'min' => '0',
        'label' => __('Annual Premium', 'pn-personal-finance-manager'),
        'placeholder' => __('Enter annual premium', 'pn-personal-finance-manager'),
        'parent' => 'pn_personal_finance_manager_asset_type',
        'parent_option' => 'insurance_policies',
      ];

      // Intellectual Property sub-fields
      $pn_personal_finance_manager_fields_meta['pn_personal_finance_manager_ip_type'] = [
        'id' => 'pn_personal_finance_manager_ip_type',
        'class' => 'pn-personal-finance-manager-select pn-personal-finance-manager-width-100-percent',
        'input' => 'select',
        'options' => [
          'patent' => __('Patent', 'pn-personal-finance-manager'),
          'copyright' => __('Copyright', 'pn-personal-finance-manager'),
          'trademark' => __('Trademark', 'pn-personal-finance-manager'),
          'trade_secret' => __('Trade Secret', 'pn-personal-finance-manager'),
          'other' => __('Other', 'pn-personal-finance-manager'),
        ],
        'label' => __('IP Type', 'pn-personal-finance-manager'),
        'placeholder' => __('Select IP type', 'pn-personal-finance-manager'),
        'parent' => 'pn_personal_finance_manager_asset_type',
        'parent_option' => 'intellectual_property',
      ];
      $pn_personal_finance_manager_fields_meta['pn_personal_finance_manager_ip_estimated_value'] = [
        'id' => 'pn_personal_finance_manager_ip_estimated_value',
        'class' => 'pn-personal-finance-manager-input pn-personal-finance-manager-width-100-percent',
        'input' => 'input',
        'type' => 'number',
        'step' => '0.01',
        'min' => '0',
        'label' => __('Estimated Value', 'pn-personal-finance-manager'),
        'placeholder' => __('Enter estimated value', 'pn-personal-finance-manager'),
        'parent' => 'pn_personal_finance_manager_asset_type',
        'parent_option' => 'intellectual_property',
      ];

      // Other sub-fields
      $pn_personal_finance_manager_fields_meta['pn_personal_finance_manager_other_estimated_value'] = [
        'id' => 'pn_personal_finance_manager_other_estimated_value',
        'class' => 'pn-personal-finance-manager-input pn-personal-finance-manager-width-100-percent',
        'input' => 'input',
        'type' => 'number',
        'step' => '0.01',
        'min' => '0',
        'label' => __('Estimated Value', 'pn-personal-finance-manager'),
        'placeholder' => __('Enter estimated value', 'pn-personal-finance-manager'),
        'parent' => 'pn_personal_finance_manager_asset_type',
        'parent_option' => 'other',
      ];

      $pn_personal_finance_manager_fields_meta['pn_personal_finance_manager_asset_form'] = [
        'id' => 'pn_personal_finance_manager_asset_form',
        'input' => 'input',
        'type' => 'hidden',
      ];
      $pn_personal_finance_manager_fields_meta['pn_personal_finance_manager_ajax_nonce'] = [
        'id' => 'pn_personal_finance_manager_ajax_nonce',
        'input' => 'input',
        'type' => 'nonce',
      ];
    return $pn_personal_finance_manager_fields_meta;
  }

  /**
   * Gets the asset meta fields with current values for editing.
   *
   * @since    1.0.5
   * @param    int    $asset_id    The asset ID.
   * @return   array  Array of asset meta fields with current values.
   */
  public function pn_personal_finance_manager_asset_get_fields_meta_with_values($asset_id = 0) {
    $pn_personal_finance_manager_fields_meta = self::pn_personal_finance_manager_asset_get_fields_meta();

    if (!empty($asset_id)) {
      // Auto-populate values for all fields that have an id starting with pn_personal_finance_manager_ or known prefixes
      $value_fields = [
        'pn_personal_finance_manager_asset_date',
        'pn_personal_finance_manager_asset_time',
        'pn_personal_finance_manager_asset_type',
        'pn_personal_finance_manager_stock_symbol',
        'pn_personal_finance_manager_stock_total_amount',
        // Real Estate
        'real_estate_purchase_price',
        'real_estate_current_value',
        'real_estate_ownership_percent',
        // Cryptocurrencies
        'pn_personal_finance_manager_crypto_symbol',
        'pn_personal_finance_manager_crypto_amount',
        'pn_personal_finance_manager_crypto_purchase_price',
        // Commodities
        'pn_personal_finance_manager_commodity_name',
        'pn_personal_finance_manager_commodity_amount',
        'pn_personal_finance_manager_commodity_unit',
        'pn_personal_finance_manager_commodity_purchase_price',
        'pn_personal_finance_manager_commodity_current_value',
        // Art & Collectibles
        'pn_personal_finance_manager_art_category',
        'pn_personal_finance_manager_art_purchase_price',
        'pn_personal_finance_manager_art_estimated_value',
        // Precious Metals
        'pn_personal_finance_manager_metal_type',
        'pn_personal_finance_manager_metal_weight',
        'pn_personal_finance_manager_metal_unit',
        'pn_personal_finance_manager_metal_purchase_price',
        'pn_personal_finance_manager_metal_current_value',
        // Bonds
        'pn_personal_finance_manager_bond_issuer',
        'pn_personal_finance_manager_bond_face_value',
        'pn_personal_finance_manager_bond_interest_rate',
        'pn_personal_finance_manager_bond_maturity_date',
        'pn_personal_finance_manager_bond_current_value',
        // Vehicles
        'pn_personal_finance_manager_vehicle_make_model',
        'pn_personal_finance_manager_vehicle_year',
        'pn_personal_finance_manager_vehicle_purchase_price',
        'pn_personal_finance_manager_vehicle_current_value',
        // Business Equity
        'pn_personal_finance_manager_business_name',
        'pn_personal_finance_manager_business_ownership_percent',
        'pn_personal_finance_manager_business_valuation',
        // Retirement Accounts
        'pn_personal_finance_manager_retirement_type',
        'pn_personal_finance_manager_retirement_provider',
        'pn_personal_finance_manager_retirement_balance',
        // Insurance Policies
        'pn_personal_finance_manager_insurance_type',
        'pn_personal_finance_manager_insurance_provider',
        'pn_personal_finance_manager_insurance_value',
        'pn_personal_finance_manager_insurance_premium',
        // Intellectual Property
        'pn_personal_finance_manager_ip_type',
        'pn_personal_finance_manager_ip_estimated_value',
        // Other
        'pn_personal_finance_manager_other_estimated_value',
      ];

      foreach ($value_fields as $field_key) {
        if (isset($pn_personal_finance_manager_fields_meta[$field_key])) {
          $pn_personal_finance_manager_fields_meta[$field_key]['value'] = get_post_meta($asset_id, $field_key, true);
        }
      }

      // Add saved stock symbol to options so the form can render it as selected
      $saved_symbol = get_post_meta($asset_id, 'pn_personal_finance_manager_stock_symbol', true);
      if (!empty($saved_symbol) && isset($pn_personal_finance_manager_fields_meta['pn_personal_finance_manager_stock_symbol'])) {
        $pn_personal_finance_manager_fields_meta['pn_personal_finance_manager_stock_symbol']['options'] = [
          '' => __('Loading stock symbols...', 'pn-personal-finance-manager'),
          $saved_symbol => $saved_symbol,
        ];
      }

      // Add saved crypto symbol to options so the form can render it as selected
      $saved_crypto = get_post_meta($asset_id, 'pn_personal_finance_manager_crypto_symbol', true);
      if (!empty($saved_crypto) && isset($pn_personal_finance_manager_fields_meta['pn_personal_finance_manager_crypto_symbol'])) {
        // Try to get display name from id→ticker mapping
        $id_to_ticker = get_option('pn_personal_finance_manager_crypto_id_to_ticker', []);
        $display_name = isset($id_to_ticker[$saved_crypto]) ? strtoupper($id_to_ticker[$saved_crypto]) . ' - ' . ucfirst($saved_crypto) : ucfirst($saved_crypto);
        $pn_personal_finance_manager_fields_meta['pn_personal_finance_manager_crypto_symbol']['options'] = [
          '' => __('Loading cryptocurrencies...', 'pn-personal-finance-manager'),
          $saved_crypto => $display_name,
        ];
      }

    }

    return $pn_personal_finance_manager_fields_meta;
  }

  /**
   * Register Asset.
   *
   * @since    1.0.0
   */
  public function pn_personal_finance_manager_asset_register_post_type() {
    $labels = [
      'name'                => _x('Assets', 'Post Type general name', 'pn-personal-finance-manager'),
      'singular_name'       => _x('Asset', 'Post Type singular name', 'pn-personal-finance-manager'),
      'menu_name'           => esc_html(__('Assets', 'pn-personal-finance-manager')),
      'parent_item_colon'   => esc_html(__('Parent Asset', 'pn-personal-finance-manager')),
      'all_items'           => esc_html(__('All Assets', 'pn-personal-finance-manager')),
      'view_item'           => esc_html(__('View Asset', 'pn-personal-finance-manager')),
      'add_new_item'        => esc_html(__('Add new Asset', 'pn-personal-finance-manager')),
      'add_new'             => esc_html(__('Add new Asset', 'pn-personal-finance-manager')),
      'edit_item'           => esc_html(__('Edit Asset', 'pn-personal-finance-manager')),
      'update_item'         => esc_html(__('Update Asset', 'pn-personal-finance-manager')),
      'search_items'        => esc_html(__('Search Assets', 'pn-personal-finance-manager')),
      'not_found'           => esc_html(__('Not Asset found', 'pn-personal-finance-manager')),
      'not_found_in_trash'  => esc_html(__('Not Asset found in Trash', 'pn-personal-finance-manager')),
    ];

    $args = [
      'labels'              => $labels,
      'rewrite'             => ['slug' => (!empty(get_option('pn_personal_finance_manager_asset_slug')) ? get_option('pn_personal_finance_manager_asset_slug') : 'pn-personal-finance-manager-asset'), 'with_front' => false],
      'label'               => esc_html(__('Assets', 'pn-personal-finance-manager')),
      'description'         => esc_html(__('Asset description', 'pn-personal-finance-manager')),
      'supports'            => ['title', 'editor', 'excerpt', 'author', 'thumbnail', 'comments', 'revisions', 'page-attributes', ],
      'hierarchical'        => true,
      'public'              => false,
      'show_ui'             => true,
      'show_in_menu'        => 'pn_personal_finance_manager_options',
      'show_in_nav_menus'   => true,
      'show_in_admin_bar'   => true,
      'can_export'          => true,
      'has_archive'         => false,
      'exclude_from_search' => true,
      'publicly_queryable'  => false,
      'capability_type'     => 'page',
      'taxonomies'          => ['pnpfm_asset_category'],
      'show_in_rest'        => true, /* REST API enabled for Gutenberg */
    ];

    register_post_type('pnpfm_asset', $args);
    add_theme_support('post-thumbnails', ['page', 'pnpfm_asset']);
    
    // Block REST API access for pn_personal_finance_manager_asset post type
    add_filter('rest_pre_dispatch', [$this, 'pn_personal_finance_manager_block_asset_rest_api'], 10, 3);
  }

  /**
   * Block REST API access for pn_personal_finance_manager_asset post type.
   *
   * @since    1.0.0
   */
  public function pn_personal_finance_manager_block_asset_rest_api($result, $server, $request) {
    $route = $request->get_route();
    
    // Solo bloquear acceso público a la REST API, no el acceso del admin
    if (strpos($route, '/wp/v2/pn_personal_finance_manager_asset') !== false) {
      // Permitir acceso si el usuario está autenticado y tiene permisos de administrador
      if (is_user_logged_in() && current_user_can('edit_posts')) {
        return $result;
      }
      
      // Bloquear acceso público
      return new WP_Error(
        'rest_forbidden',
        __('Sorry, you are not allowed to access this resource.', 'pn-personal-finance-manager'),
        ['status' => 403]
      );
    }
    
    return $result;
  }

  /**
   * Add Asset dashboard metabox.
   *
   * @since    1.0.0
   */
  public function pn_personal_finance_manager_asset_add_meta_box() {
    add_meta_box('pn_personal_finance_manager_meta_box', esc_html(__('Asset details', 'pn-personal-finance-manager')), [$this, 'pn_personal_finance_manager_asset_meta_box_function'], 'pnpfm_asset', 'normal', 'high', ['__block_editor_compatible_meta_box' => true,]);
  }

  /**
   * Defines Asset dashboard contents.
   *
   * @since    1.0.0
   */
  public function pn_personal_finance_manager_asset_meta_box_function($post) {
    foreach (self::pn_personal_finance_manager_asset_get_fields_meta() as $pn_personal_finance_manager_field) {
      if (!is_null(PN_PERSONAL_FINANCE_MANAGER_Forms::pn_personal_finance_manager_input_wrapper_builder($pn_personal_finance_manager_field, 'post', $post->ID))) {
        echo wp_kses(PN_PERSONAL_FINANCE_MANAGER_Forms::pn_personal_finance_manager_input_wrapper_builder($pn_personal_finance_manager_field, 'post', $post->ID), PN_PERSONAL_FINANCE_MANAGER_KSES);
      }
    }
  }

  /**
   * Defines single template for Asset.
   *
   * @since    1.0.0
   */
  public function pn_personal_finance_manager_asset_single_template($single) {
    if (get_post_type() == 'pnpfm_asset') {
      if (file_exists(PN_PERSONAL_FINANCE_MANAGER_DIR . 'templates/public/single-pnpfm_asset.php')) {
        return PN_PERSONAL_FINANCE_MANAGER_DIR . 'templates/public/single-pnpfm_asset.php';
      }
    }

    return $single;
  }

  /**
   * Defines archive template for Asset.
   *
   * @since    1.0.0
   */
  public function pn_personal_finance_manager_asset_archive_template($archive) {
    if (get_post_type() == 'pnpfm_asset') {
      if (file_exists(PN_PERSONAL_FINANCE_MANAGER_DIR . 'templates/public/archive-pnpfm_asset.php')) {
        return PN_PERSONAL_FINANCE_MANAGER_DIR . 'templates/public/archive-pnpfm_asset.php';
      }
    }

    return $archive;
  }

  public function pn_personal_finance_manager_asset_save_post($post_id, $cpt, $update) {
    if($cpt->post_type == 'pnpfm_asset' && array_key_exists('pn_personal_finance_manager_asset_form', $_POST)){
      // Always require nonce verification
      if (!array_key_exists('pn_personal_finance_manager_ajax_nonce', $_POST)) {
        echo wp_json_encode([
          'error_key' => 'pn_personal_finance_manager_nonce_error_required',
          'error_content' => esc_html(__('Security check failed: Nonce is required.', 'pn-personal-finance-manager')),
        ]);

        exit;
      }

      if (!wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['pn_personal_finance_manager_ajax_nonce'])), 'pn-personal-finance-manager-nonce')) {
        echo wp_json_encode([
          'error_key' => 'pn_personal_finance_manager_nonce_error_invalid',
          'error_content' => esc_html(__('Security check failed: Invalid nonce.', 'pn-personal-finance-manager')),
        ]);

        exit;
      }

      if (!array_key_exists('pn_personal_finance_manager_duplicate', $_POST)) {
        foreach (array_merge(self::pn_personal_finance_manager_asset_get_fields(), self::pn_personal_finance_manager_asset_get_fields_meta()) as $pn_personal_finance_manager_field) {
          $pn_personal_finance_manager_input = array_key_exists('input', $pn_personal_finance_manager_field) ? $pn_personal_finance_manager_field['input'] : '';

          if (array_key_exists($pn_personal_finance_manager_field['id'], $_POST) || $pn_personal_finance_manager_input == 'html_multi') {
            $pn_personal_finance_manager_value = array_key_exists($pn_personal_finance_manager_field['id'], $_POST) ? 
              PN_PERSONAL_FINANCE_MANAGER_Forms::pn_personal_finance_manager_sanitizer(
                wp_unslash($_POST[$pn_personal_finance_manager_field['id']]),
                $pn_personal_finance_manager_field['input'], 
                !empty($pn_personal_finance_manager_field['type']) ? $pn_personal_finance_manager_field['type'] : '',
                $pn_personal_finance_manager_field // Pass the entire field config
              ) : '';

            if (!empty($pn_personal_finance_manager_input)) {
              switch ($pn_personal_finance_manager_input) {
                case 'input':
                  if (array_key_exists('type', $pn_personal_finance_manager_field) && $pn_personal_finance_manager_field['type'] == 'checkbox') {
                    if (isset($_POST[$pn_personal_finance_manager_field['id']])) {
                      update_post_meta($post_id, $pn_personal_finance_manager_field['id'], $pn_personal_finance_manager_value);
                    } else {
                      update_post_meta($post_id, $pn_personal_finance_manager_field['id'], '');
                    }
                  } else {
                    update_post_meta($post_id, $pn_personal_finance_manager_field['id'], $pn_personal_finance_manager_value);
                  }

                  break;
                case 'select':
                  if (array_key_exists('multiple', $pn_personal_finance_manager_field) && $pn_personal_finance_manager_field['multiple']) {
                    $multi_array = [];
                    $empty = true;
                    $unslashed_multi = isset($_POST[$pn_personal_finance_manager_field['id']]) ? wp_unslash($_POST[$pn_personal_finance_manager_field['id']]) : [];

                    foreach ((array) $unslashed_multi as $multi_value) {
                      $multi_array[] = PN_PERSONAL_FINANCE_MANAGER_Forms::pn_personal_finance_manager_sanitizer(
                        sanitize_text_field($multi_value),
                        $pn_personal_finance_manager_field['input'],
                        !empty($pn_personal_finance_manager_field['type']) ? $pn_personal_finance_manager_field['type'] : '',
                        $pn_personal_finance_manager_field // Pass the entire field config
                      );
                    }

                    update_post_meta($post_id, $pn_personal_finance_manager_field['id'], $multi_array);
                  } else {
                    update_post_meta($post_id, $pn_personal_finance_manager_field['id'], $pn_personal_finance_manager_value);
                  }
                  
                  break;
                case 'html_multi':
                  foreach ($pn_personal_finance_manager_field['html_multi_fields'] as $pn_personal_finance_manager_multi_field) {
                    if (array_key_exists($pn_personal_finance_manager_multi_field['id'], $_POST)) {
                      $multi_array = [];
                      $empty = true;

                      // Sanitize the POST data before using it
                      $sanitized_post_data = isset($_POST[$pn_personal_finance_manager_multi_field['id']]) ?
                        array_map('sanitize_text_field', wp_unslash((array) $_POST[$pn_personal_finance_manager_multi_field['id']])) : [];
                      
                      foreach ($sanitized_post_data as $multi_value) {
                        if (!empty($multi_value)) {
                          $empty = false;
                        }

                        $multi_array[] = PN_PERSONAL_FINANCE_MANAGER_Forms::pn_personal_finance_manager_sanitizer(
                          $multi_value, 
                          $pn_personal_finance_manager_multi_field['input'], 
                          !empty($pn_personal_finance_manager_multi_field['type']) ? $pn_personal_finance_manager_multi_field['type'] : '',
                          $pn_personal_finance_manager_multi_field // Pass the entire field config
                        );
                      }

                      if (!$empty) {
                        update_post_meta($post_id, $pn_personal_finance_manager_multi_field['id'], $multi_array);
                      } else {
                        update_post_meta($post_id, $pn_personal_finance_manager_multi_field['id'], '');
                      }
                    }
                  }

                  break;
                default:
                  update_post_meta($post_id, $pn_personal_finance_manager_field['id'], $pn_personal_finance_manager_value);
                  break;
              }
            }
          } else {
            update_post_meta($post_id, $pn_personal_finance_manager_field['id'], '');
          }
        }
      }
    }
  }

  public function pn_personal_finance_manager_asset_form_save($element_id, $key_value, $pn_personal_finance_manager_form_type, $pn_personal_finance_manager_form_subtype) {
    $post_type = !empty(get_post_type($element_id)) ? get_post_type($element_id) : 'pnpfm_asset';

    if ($post_type == 'pnpfm_asset') {
      switch ($pn_personal_finance_manager_form_type) {
        case 'post':
          switch ($pn_personal_finance_manager_form_subtype) {
            case 'post_new':
              if (!empty($key_value)) {
                foreach ($key_value as $key => $value) {
                  if (strpos($key, 'pn_personal_finance_manager_') !== false) {
                    ${$key} = $value;
                    delete_post_meta($element_id, $key);
                  }
                }
              }

              if (empty($pn_personal_finance_manager_asset_title)) {
                $asset_types = PN_PERSONAL_FINANCE_MANAGER_Data::pn_personal_finance_manager_get_asset_types();
                $type_key = isset($pn_personal_finance_manager_key_value['pn_personal_finance_manager_asset_type']) ? $pn_personal_finance_manager_key_value['pn_personal_finance_manager_asset_type'] : 'other';
                $type_label = isset($asset_types[$type_key]) ? $asset_types[$type_key] : __('Asset', 'pn-personal-finance-manager');
                $pn_personal_finance_manager_asset_title = $type_label . ' - ' . gmdate('Y-m-d H:i');
              }

              $post_functions = new PN_PERSONAL_FINANCE_MANAGER_Functions_Post();
              $asset_id = $post_functions->pn_personal_finance_manager_insert_post(esc_html($pn_personal_finance_manager_asset_title), $pn_personal_finance_manager_asset_description, '', sanitize_title(esc_html($pn_personal_finance_manager_asset_title)), 'pnpfm_asset', 'publish', get_current_user_id());

              if (!empty($key_value)) {
                foreach ($key_value as $key => $value) {
                  update_post_meta($asset_id, $key, $value);
                }
              }

              break;
            case 'post_edit':
              if (!empty($key_value)) {
                foreach ($key_value as $key => $value) {
                  if (strpos($key, 'pn_personal_finance_manager_') !== false) {
                    ${$key} = $value;
                    delete_post_meta($element_id, $key);
                  }
                }
              }

              $asset_id = $element_id;
              wp_update_post(['ID' => $asset_id, 'post_title' => $pn_personal_finance_manager_asset_title, 'post_content' => $pn_personal_finance_manager_asset_description,]);

              if (!empty($key_value)) {
                foreach ($key_value as $key => $value) {
                  update_post_meta($asset_id, $key, $value);
                }
              }

              break;
}
      }
    }
  }

  public function pn_personal_finance_manager_asset_register_scripts() {
    if (!wp_script_is('pn-personal-finance-manager-aux', 'registered')) {
      wp_register_script('pn-personal-finance-manager-aux', PN_PERSONAL_FINANCE_MANAGER_URL . 'assets/js/pn-personal-finance-manager-aux.js', [], PN_PERSONAL_FINANCE_MANAGER_VERSION, true);
    }

    if (!wp_script_is('pn-personal-finance-manager-forms', 'registered')) {
      wp_register_script('pn-personal-finance-manager-forms', PN_PERSONAL_FINANCE_MANAGER_URL . 'assets/js/pn-personal-finance-manager-forms.js', [], PN_PERSONAL_FINANCE_MANAGER_VERSION, true);
    }
    
    if (!wp_script_is('pn-personal-finance-manager-selector', 'registered')) {
      wp_register_script('pn-personal-finance-manager-selector', PN_PERSONAL_FINANCE_MANAGER_URL . 'assets/js/pn-personal-finance-manager-selector.js', [], PN_PERSONAL_FINANCE_MANAGER_VERSION, true);
    }
  }

  public function pn_personal_finance_manager_asset_print_scripts() {
    wp_print_scripts(['pn-personal-finance-manager-aux', 'pn-personal-finance-manager-forms', 'pn-personal-finance-manager-selector']);
  }

  public function pn_personal_finance_manager_asset_list_wrapper() {
    $asset_types = PN_PERSONAL_FINANCE_MANAGER_Data::pn_personal_finance_manager_get_asset_types();

    // Get existing asset types to limit filter options
    $asset_atts = [
      'fields' => 'ids',
      'numberposts' => -1,
      'post_type' => 'pnpfm_asset',
      'post_status' => 'any',
    ];
    if (class_exists('Polylang')) {
      $asset_atts['lang'] = pll_current_language('slug');
    }
    $all_asset_ids = get_posts($asset_atts);
    $all_asset_ids = PN_PERSONAL_FINANCE_MANAGER_Functions_User::pn_personal_finance_manager_filter_user_posts($all_asset_ids, 'pnpfm_asset');
    $existing_types = [];
    foreach ($all_asset_ids as $aid) {
      $t = get_post_meta($aid, 'pn_personal_finance_manager_asset_type', true);
      if (!empty($t)) {
        $existing_types[$t] = true;
      }
    }

    ob_start();
    ?>
      <div class="pn-personal-finance-manager-pn_personal_finance_manager_asset-list pn-personal-finance-manager-mb-50">
        <?php echo wp_kses_post(PN_PERSONAL_FINANCE_MANAGER_Common::pn_personal_finance_manager_page_nav('assets')); ?>
        <div class="pn-personal-finance-manager-cpt-search-container pn-personal-finance-manager-mb-20 pn-personal-finance-manager-text-align-right">
          <div class="pn-personal-finance-manager-cpt-search-wrapper pn-personal-finance-manager-pnpfm_asset-search-wrapper">
            <?php PN_PERSONAL_FINANCE_MANAGER_Forms::pn_personal_finance_manager_input_builder([
							'input'       => 'input',
							'type'        => 'text',
							'id'          => 'pn_personal_finance_manager_asset_search',
							'class'       => 'pn-personal-finance-manager-cpt-search-input pn-personal-finance-manager-pnpfm_asset-search-input pn-personal-finance-manager-input pn-personal-finance-manager-display-none',
							'placeholder' => __('Filter...', 'pn-personal-finance-manager'),
						], 'none'); ?>
            <i class="material-icons-outlined pn-personal-finance-manager-cpt-search-toggle pn-personal-finance-manager-pnpfm_asset-search-toggle pn-personal-finance-manager-cursor-pointer pn-personal-finance-manager-font-size-30 pn-personal-finance-manager-vertical-align-middle pn-personal-finance-manager-tooltip" title="<?php esc_attr_e('Search Assets', 'pn-personal-finance-manager'); ?>">search</i>
            <i class="material-icons-outlined pn-personal-finance-manager-cpt-filter-toggle pn-personal-finance-manager-pnpfm_asset-filter-toggle pn-personal-finance-manager-cursor-pointer pn-personal-finance-manager-font-size-30 pn-personal-finance-manager-vertical-align-middle pn-personal-finance-manager-tooltip" title="<?php esc_attr_e('Filter by type', 'pn-personal-finance-manager'); ?>">filter_list</i>
            <i class="material-icons-outlined pn-personal-finance-manager-cpt-sort-toggle pn-personal-finance-manager-cursor-pointer pn-personal-finance-manager-font-size-30 pn-personal-finance-manager-vertical-align-middle pn-personal-finance-manager-tooltip" title="<?php esc_attr_e('Sort', 'pn-personal-finance-manager'); ?>">sort</i>
            <?php if (is_user_logged_in()) : ?>
            <a href="#" class="pn-personal-finance-manager-popup-open-ajax pn-personal-finance-manager-text-decoration-none" data-pn-personal-finance-manager-popup-id="pn-personal-finance-manager-popup-pn_personal_finance_manager_asset-add" data-pn-personal-finance-manager-ajax-type="pn_personal_finance_manager_asset_new">
              <i class="material-icons-outlined pn-personal-finance-manager-cursor-pointer pn-personal-finance-manager-font-size-30 pn-personal-finance-manager-vertical-align-middle pn-personal-finance-manager-tooltip" title="<?php esc_attr_e('Add new Asset', 'pn-personal-finance-manager'); ?>">add</i>
            </a>
            <?php endif; ?>
          </div>
          <div class="pn-personal-finance-manager-cpt-filter-dropdown pn-personal-finance-manager-pnpfm_asset-filter-dropdown pn-personal-finance-manager-display-none">
            <?php
						$filter_options = [];
						foreach ($asset_types as $type_key => $type_label) {
							if (isset($existing_types[$type_key])) {
								$filter_options[$type_key] = $type_label;
							}
						}
						PN_PERSONAL_FINANCE_MANAGER_Forms::pn_personal_finance_manager_input_builder([
							'input'       => 'select',
							'id'          => 'pn_personal_finance_manager_asset_filter',
							'class'       => 'pn-personal-finance-manager-cpt-filter-select pn-personal-finance-manager-pnpfm_asset-filter-select pn-personal-finance-manager-select',
							'placeholder' => __('All types', 'pn-personal-finance-manager'),
							'options'     => $filter_options,
						], 'none');
						?>
          </div>
          <div class="pn-personal-finance-manager-cpt-sort-dropdown pn-personal-finance-manager-display-none">
            <?php PN_PERSONAL_FINANCE_MANAGER_Forms::pn_personal_finance_manager_input_builder([
							'input'       => 'select',
							'id'          => 'pn_personal_finance_manager_asset_sort',
							'class'       => 'pn-personal-finance-manager-cpt-sort-select pn-personal-finance-manager-select',
							'placeholder' => __('Default order', 'pn-personal-finance-manager'),
							'options'     => [
								'value-desc'   => __('Value (highest first)', 'pn-personal-finance-manager'),
								'value-asc'    => __('Value (lowest first)', 'pn-personal-finance-manager'),
								'alpha-asc'    => __('Alphabetical (A-Z)', 'pn-personal-finance-manager'),
								'alpha-desc'   => __('Alphabetical (Z-A)', 'pn-personal-finance-manager'),
								'percent-desc' => __('Portfolio % (highest first)', 'pn-personal-finance-manager'),
							],
						], 'none'); ?>
          </div>
        </div>
        <div class="pn-personal-finance-manager-pn_personal_finance_manager_asset-list-wrapper pn-personal-finance-manager-pnpfm_asset-list">
          <?php echo wp_kses(self::pn_personal_finance_manager_asset_list(), PN_PERSONAL_FINANCE_MANAGER_KSES); ?>
        </div>
      </div>

      <div class="pn-personal-finance-manager-pnpfm_asset-statistics">
        <?php echo wp_kses_post(self::pn_personal_finance_manager_asset_statistics()); ?>
      </div>
    <?php
    $pn_personal_finance_manager_return_string = ob_get_contents();
    ob_end_clean();
    return $pn_personal_finance_manager_return_string;
  }

  public static function pn_personal_finance_manager_asset_statistics() {
    $asset_atts = [
      'fields' => 'ids',
      'numberposts' => -1,
      'post_type' => 'pnpfm_asset',
      'post_status' => 'any',
      'orderby' => 'menu_order',
      'order' => 'ASC',
    ];

    if (class_exists('Polylang')) {
      $asset_atts['lang'] = pll_current_language('slug');
    }

    $assets = get_posts($asset_atts);
    $assets = PN_PERSONAL_FINANCE_MANAGER_Functions_User::pn_personal_finance_manager_filter_user_posts($assets, 'pnpfm_asset');

    $total_count = count($assets);

    if ($total_count === 0) {
      return '<div class="pn-personal-finance-manager-statistics-panel"><p class="pn-personal-finance-manager-statistics-empty">' . esc_html__('No data to display', 'pn-personal-finance-manager') . '</p></div>';
    }

    $asset_types = PN_PERSONAL_FINANCE_MANAGER_Data::pn_personal_finance_manager_get_asset_types();
    $currency = get_option('pn_personal_finance_manager_currency', 'eur');
    $currency_symbol = PN_PERSONAL_FINANCE_MANAGER_Data::pn_personal_finance_manager_get_currency_symbol($currency);
    $counts = [];
    $type_values = [];
    $stocks_instance = null;

    foreach ($assets as $asset_id) {
      $type = get_post_meta($asset_id, 'pn_personal_finance_manager_asset_type', true);
      if (empty($type)) {
        $type = 'other';
      }
      if (!isset($counts[$type])) {
        $counts[$type] = 0;
        $type_values[$type] = 0;
      }
      $counts[$type]++;

      $asset_value = 0;

      if ($type === 'stocks') {
        $symbol = get_post_meta($asset_id, 'pn_personal_finance_manager_stock_symbol', true);
        $total_amount = get_post_meta($asset_id, 'pn_personal_finance_manager_stock_total_amount', true);
        if (!empty($symbol) && !empty($total_amount)) {
          if (!$stocks_instance) {
            $stocks_instance = new PN_PERSONAL_FINANCE_MANAGER_Stocks();
          }
          $stock_data = $stocks_instance->pn_personal_finance_manager_get_stock_data($symbol);
          if ($stock_data && !empty($stock_data['price'])) {
            $asset_value = floatval($total_amount) * floatval($stock_data['price']);
            $asset_value = PN_PERSONAL_FINANCE_MANAGER_Data::pn_personal_finance_manager_convert_from_usd($asset_value, $currency);
          }
        }
      } elseif ($type === 'cryptocurrencies') {
        $coin_id = get_post_meta($asset_id, 'pn_personal_finance_manager_crypto_symbol', true);
        $amount = get_post_meta($asset_id, 'pn_personal_finance_manager_crypto_amount', true);
        if (!empty($coin_id) && !empty($amount)) {
          if (!$stocks_instance) {
            $stocks_instance = new PN_PERSONAL_FINANCE_MANAGER_Stocks();
          }
          $crypto_data = $stocks_instance->pn_personal_finance_manager_get_crypto_data($coin_id);
          if ($crypto_data && !empty($crypto_data['price'])) {
            $asset_value = floatval($amount) * floatval($crypto_data['price']);
            $asset_value = PN_PERSONAL_FINANCE_MANAGER_Data::pn_personal_finance_manager_convert_from_usd($asset_value, $currency);
          }
        }
      } elseif ($type === 'real_estate') {
        $current_value = get_post_meta($asset_id, 'real_estate_current_value', true);
        $purchase_price = get_post_meta($asset_id, 'real_estate_purchase_price', true);
        $ownership_percent = get_post_meta($asset_id, 'real_estate_ownership_percent', true);
        $ownership_percent = is_numeric($ownership_percent) ? floatval($ownership_percent) : 100;
        if (!empty($current_value)) {
          $asset_value = floatval($current_value) * ($ownership_percent / 100);
        } elseif (!empty($purchase_price)) {
          $asset_value = floatval($purchase_price) * ($ownership_percent / 100);
        }
      }

      $type_values[$type] += $asset_value;
    }

    $global_total = array_sum($type_values);

    $colors = ['#007bff', '#28a745', '#ffc107', '#dc3545', '#17a2b8', '#6f42c1', '#fd7e14', '#20c997', '#e83e8c', '#6c757d', '#0056b3', '#155724', '#856404'];
    $labels = [];
    $values = [];
    $chart_colors = [];
    $legend_items = [];
    $color_index = 0;

    foreach ($type_values as $type_key => $value) {
      $label = isset($asset_types[$type_key]) ? $asset_types[$type_key] : ucfirst(str_replace('_', ' ', $type_key));
      $color = $colors[$color_index % count($colors)];
      $labels[] = $label;
      $values[] = round($value, 2);
      $chart_colors[] = $color;
      $legend_items[] = [
        'label' => $label,
        'value' => $value,
        'count' => $counts[$type_key],
        'color' => $color,
      ];
      $color_index++;
    }

    $chart_data = wp_json_encode([
      'labels' => $labels,
      'values' => $values,
      'colors' => $chart_colors,
    ]);

    $formatted_total = PN_PERSONAL_FINANCE_MANAGER_Data::pn_personal_finance_manager_format_currency($global_total, $currency);

    ob_start();
    ?>
    <div class="pn-personal-finance-manager-statistics-panel">
      <div class="pn-personal-finance-manager-statistics-metrics">
        <div class="pn-personal-finance-manager-metric-row">
          <div class="pn-personal-finance-manager-metric-item">
            <span class="pn-personal-finance-manager-metric-label"><?php esc_html_e('Total Assets', 'pn-personal-finance-manager'); ?></span>
            <span class="pn-personal-finance-manager-metric-value"><?php echo esc_html($total_count); ?></span>
          </div>
          <div class="pn-personal-finance-manager-metric-item">
            <span class="pn-personal-finance-manager-metric-label"><?php esc_html_e('Portfolio Value', 'pn-personal-finance-manager'); ?></span>
            <span class="pn-personal-finance-manager-metric-value"><?php echo esc_html($formatted_total['full']); ?></span>
          </div>
        </div>
      </div>
      <div class="pn-personal-finance-manager-statistics-chart-wrapper">
        <canvas class="pn-personal-finance-manager-statistics-chart" data-chart="<?php echo esc_attr($chart_data); ?>" data-currency-symbol="<?php echo esc_attr($currency_symbol); ?>"></canvas>
      </div>
      <div class="pn-personal-finance-manager-statistics-legend">
        <?php foreach ($legend_items as $item): ?>
          <div class="pn-personal-finance-manager-legend-item">
            <span class="pn-personal-finance-manager-legend-color" style="background-color: <?php echo esc_attr($item['color']); ?>;"></span>
            <span class="pn-personal-finance-manager-legend-label"><?php echo esc_html($item['label']); ?> (<?php echo esc_html($item['count']); ?>)</span>
            <span class="pn-personal-finance-manager-legend-value">
              <?php
              $fmt = PN_PERSONAL_FINANCE_MANAGER_Data::pn_personal_finance_manager_format_currency($item['value'], $currency);
              echo esc_html($fmt['full']);
              ?>
            </span>
          </div>
        <?php endforeach; ?>
        <div class="pn-personal-finance-manager-legend-total">
          <span class="pn-personal-finance-manager-legend-label"><strong><?php esc_html_e('Total', 'pn-personal-finance-manager'); ?></strong></span>
          <span class="pn-personal-finance-manager-legend-value"><strong><?php echo esc_html($formatted_total['full']); ?></strong></span>
        </div>
      </div>
    </div>
    <?php
    return ob_get_clean();
  }

  public function pn_personal_finance_manager_asset_list() {
    $asset_atts = [
      'fields' => 'ids',
      'numberposts' => -1,
      'post_type' => 'pnpfm_asset',
      'post_status' => 'any',
      'orderby' => 'menu_order',
      'order' => 'ASC',
    ];

    if (class_exists('Polylang')) {
      $asset_atts['lang'] = pll_current_language('slug');
    }

    $asset = get_posts($asset_atts);

    // Filter assets based on user permissions
    $asset = PN_PERSONAL_FINANCE_MANAGER_Functions_User::pn_personal_finance_manager_filter_user_posts($asset, 'pnpfm_asset');

    // Pre-compute values for all assets (for portfolio percentage and sorting)
    $currency = get_option('pn_personal_finance_manager_currency', 'eur');
    $asset_values = [];
    $stocks_instance = null;
    foreach ($asset as $aid) {
      $type = get_post_meta($aid, 'pn_personal_finance_manager_asset_type', true);
      $val = 0;
      if ($type === 'stocks') {
        $sym = get_post_meta($aid, 'pn_personal_finance_manager_stock_symbol', true);
        $amt = get_post_meta($aid, 'pn_personal_finance_manager_stock_total_amount', true);
        if (!empty($sym) && !empty($amt)) {
          if (!$stocks_instance) { $stocks_instance = new PN_PERSONAL_FINANCE_MANAGER_Stocks(); }
          $sd = $stocks_instance->pn_personal_finance_manager_get_stock_data($sym);
          if ($sd && !empty($sd['price'])) {
            $val = PN_PERSONAL_FINANCE_MANAGER_Data::pn_personal_finance_manager_convert_from_usd(floatval($amt) * floatval($sd['price']), $currency);
          }
        }
      } elseif ($type === 'cryptocurrencies') {
        $cid = get_post_meta($aid, 'pn_personal_finance_manager_crypto_symbol', true);
        $camt = get_post_meta($aid, 'pn_personal_finance_manager_crypto_amount', true);
        if (!empty($cid) && !empty($camt)) {
          if (!$stocks_instance) { $stocks_instance = new PN_PERSONAL_FINANCE_MANAGER_Stocks(); }
          $cd = $stocks_instance->pn_personal_finance_manager_get_crypto_data($cid);
          if ($cd && !empty($cd['price'])) {
            $val = PN_PERSONAL_FINANCE_MANAGER_Data::pn_personal_finance_manager_convert_from_usd(floatval($camt) * floatval($cd['price']), $currency);
          }
        }
      } elseif ($type === 'real_estate') {
        $cv = get_post_meta($aid, 'real_estate_current_value', true);
        $pp = get_post_meta($aid, 'real_estate_purchase_price', true);
        $op = get_post_meta($aid, 'real_estate_ownership_percent', true);
        $op = is_numeric($op) ? floatval($op) : 100;
        if (!empty($cv)) {
          $val = floatval($cv) * ($op / 100);
        } elseif (!empty($pp)) {
          $val = floatval($pp) * ($op / 100);
        }
      }
      $asset_values[$aid] = $val;
    }
    $portfolio_total = array_sum($asset_values);

    ob_start();
    ?>
      <ul class="pn-personal-finance-manager-assets pn-personal-finance-manager-list-style-none pn-personal-finance-manager-p-0 pn-personal-finance-manager-margin-auto">
        <?php if (!empty($asset)): ?>
          <?php foreach ($asset as $asset_id): ?>
            <?php
              $pn_personal_finance_manager_asset_period = get_post_meta($asset_id, 'pn_personal_finance_manager_asset_period', true);
              $pn_personal_finance_manager_asset_type = get_post_meta($asset_id, 'pn_personal_finance_manager_asset_type', true);
              $asset_types = PN_PERSONAL_FINANCE_MANAGER_Data::pn_personal_finance_manager_get_asset_types();
              $asset_type_label = !empty($pn_personal_finance_manager_asset_type) && isset($asset_types[$pn_personal_finance_manager_asset_type]) ? $asset_types[$pn_personal_finance_manager_asset_type] : '';
              $pn_personal_finance_manager_stock_symbol = get_post_meta($asset_id, 'pn_personal_finance_manager_stock_symbol', true);
              $item_value = isset($asset_values[$asset_id]) ? $asset_values[$asset_id] : 0;
              $item_portfolio_pct = ($portfolio_total > 0 && $item_value > 0) ? round(($item_value / $portfolio_total) * 100, 2) : 0;
            ?>

            <li class="pn-personal-finance-manager-asset pn-personal-finance-manager-pn_personal_finance_manager_asset-list-item pn-personal-finance-manager-mb-10" data-pn_personal_finance_manager_asset-id="<?php echo esc_attr($asset_id); ?>" data-pn-personal-finance-manager-asset-type="<?php echo esc_attr($pn_personal_finance_manager_asset_type); ?>" data-pn-personal-finance-manager-asset-value="<?php echo esc_attr($item_value); ?>" data-pn-personal-finance-manager-asset-portfolio-percent="<?php echo esc_attr($item_portfolio_pct); ?>">
              <div class="pn-personal-finance-manager-display-table pn-personal-finance-manager-width-100-percent">
                <div class="pn-personal-finance-manager-display-inline-table pn-personal-finance-manager-width-80-percent">
                  <a href="#" class="pn-personal-finance-manager-popup-open-ajax pn-personal-finance-manager-text-decoration-none" data-pn-personal-finance-manager-popup-id="pn-personal-finance-manager-popup-pn_personal_finance_manager_asset-view" data-pn-personal-finance-manager-ajax-type="pn_personal_finance_manager_asset_view">
                    <span><?php echo esc_html(get_the_title($asset_id)); ?></span>

                    <?php if (!empty($asset_type_label)): ?>
                      <span class="pn-personal-finance-manager-asset-type-badge pn-personal-finance-manager-ml-10 pn-personal-finance-manager-p-5 pn-personal-finance-manager-font-size-12 pn-personal-finance-manager-border-radius-3" style="background-color: #f0f0f0; color: #666;">
                        <?php echo esc_html($asset_type_label); ?>
                      </span>
                    <?php endif ?>

                    <?php if ($item_portfolio_pct > 0): ?>
                      <span class="pn-personal-finance-manager-portfolio-pct-badge pn-personal-finance-manager-ml-10 pn-personal-finance-manager-p-5 pn-personal-finance-manager-font-size-12 pn-personal-finance-manager-border-radius-3" style="background-color: #6f42c1; color: white;">
                        <?php echo esc_html(number_format($item_portfolio_pct, 1)); ?>%
                      </span>
                    <?php endif; ?>

                    <?php if ($pn_personal_finance_manager_asset_type === 'real_estate'): ?>
                      <?php
                        $current_value = get_post_meta($asset_id, 'real_estate_current_value', true);
                        $purchase_price = get_post_meta($asset_id, 'real_estate_purchase_price', true);
                        $ownership_percent = get_post_meta($asset_id, 'real_estate_ownership_percent', true);
                        $ownership_percent = is_numeric($ownership_percent) ? floatval($ownership_percent) : 100;
                        // Real estate values are entered in user's currency, no USD conversion needed
                        $value = 0;
                        if (!empty($current_value)) {
                          $value = floatval($current_value) * ($ownership_percent / 100);
                        } elseif (!empty($purchase_price)) {
                          $value = floatval($purchase_price) * ($ownership_percent / 100);
                        }
                        $converted = PN_PERSONAL_FINANCE_MANAGER_Data::pn_personal_finance_manager_format_currency($value, $currency);
                      ?>
                      <span class="pn-personal-finance-manager-realstate-badge pn-personal-finance-manager-ml-10 pn-personal-finance-manager-p-5 pn-personal-finance-manager-font-size-12 pn-personal-finance-manager-border-radius-3" style="background-color: #ffc107; color: #333;">
                        <?php esc_html_e('Property Value:', 'pn-personal-finance-manager'); ?> <?php echo esc_html($converted['full']); ?>
                        <?php if ($ownership_percent < 100): ?>
                          <span class="pn-personal-finance-manager-ml-10">(<?php echo esc_html(number_format($ownership_percent, 2)); ?>%)</span>
                        <?php endif; ?>
                      </span>
                    <?php endif; ?>

                    <?php if ($pn_personal_finance_manager_asset_type === 'stocks' && !empty($pn_personal_finance_manager_stock_symbol)): ?>
                      <span class="pn-personal-finance-manager-stock-symbol-badge pn-personal-finance-manager-ml-10 pn-personal-finance-manager-p-5 pn-personal-finance-manager-font-size-12 pn-personal-finance-manager-border-radius-3" style="background-color: #007bff; color: white;">
                        <?php echo esc_html($pn_personal_finance_manager_stock_symbol); ?>
                      </span>
                      <?php
                         $total_amount = get_post_meta($asset_id, 'pn_personal_finance_manager_stock_total_amount', true);
                         if (!empty($total_amount)) {
                           $symbol = get_post_meta($asset_id, 'pn_personal_finance_manager_stock_symbol', true);
                           $current_value = 0;
                           if (!empty($symbol)) {
                             if (!$stocks_instance) { $stocks_instance = new PN_PERSONAL_FINANCE_MANAGER_Stocks(); }
                             $current_stock_data = $stocks_instance->pn_personal_finance_manager_get_stock_data($symbol);
                             if ($current_stock_data && !empty($current_stock_data['price'])) {
                               $current_value = floatval($total_amount) * floatval($current_stock_data['price']);
                             }
                           }
                           $converted = PN_PERSONAL_FINANCE_MANAGER_Data::pn_personal_finance_manager_format_currency(
                               PN_PERSONAL_FINANCE_MANAGER_Data::pn_personal_finance_manager_convert_from_usd($current_value, $currency),
                               $currency
                           );
                        ?>
                          <span class="pn-personal-finance-manager-shares-badge pn-personal-finance-manager-ml-10 pn-personal-finance-manager-p-5 pn-personal-finance-manager-font-size-12 pn-personal-finance-manager-border-radius-3" style="background-color: #28a745; color: white;">
                            <span class="pn-personal-finance-manager-shares-label"><?php esc_html_e('Shares:', 'pn-personal-finance-manager'); ?></span>
                            <span class="pn-personal-finance-manager-shares-amount"><?php echo esc_html(number_format($total_amount, 2)); ?></span>
                            <?php if ($current_value > 0): ?>
                              <span class="pn-personal-finance-manager-current-value-label"> | <?php esc_html_e('Value:', 'pn-personal-finance-manager'); ?></span>
                              <span class="pn-personal-finance-manager-current-value-amount"><?php echo esc_html($converted['full']); ?></span>
                            <?php endif; ?>
                          </span>
                        <?php } ?>
                    <?php endif ?>

                    <?php if ($pn_personal_finance_manager_asset_type === 'cryptocurrencies'):
                      $pn_personal_finance_manager_crypto_symbol = get_post_meta($asset_id, 'pn_personal_finance_manager_crypto_symbol', true);
                      if (!empty($pn_personal_finance_manager_crypto_symbol)):
                        $id_to_ticker = get_option('pn_personal_finance_manager_crypto_id_to_ticker', []);
                        $crypto_ticker = isset($id_to_ticker[$pn_personal_finance_manager_crypto_symbol]) ? strtoupper($id_to_ticker[$pn_personal_finance_manager_crypto_symbol]) : strtoupper($pn_personal_finance_manager_crypto_symbol);
                    ?>
                      <span class="pn-personal-finance-manager-stock-symbol-badge pn-personal-finance-manager-ml-10 pn-personal-finance-manager-p-5 pn-personal-finance-manager-font-size-12 pn-personal-finance-manager-border-radius-3" style="background-color: #f7931a; color: white;">
                        <?php echo esc_html($crypto_ticker); ?>
                      </span>
                      <?php
                        $crypto_amount = get_post_meta($asset_id, 'pn_personal_finance_manager_crypto_amount', true);
                        if (!empty($crypto_amount)) {
                          if (!$stocks_instance) { $stocks_instance = new PN_PERSONAL_FINANCE_MANAGER_Stocks(); }
                          $crypto_data = $stocks_instance->pn_personal_finance_manager_get_crypto_data($pn_personal_finance_manager_crypto_symbol);
                          $crypto_current_value = 0;
                          if ($crypto_data && !empty($crypto_data['price'])) {
                            $crypto_current_value = floatval($crypto_amount) * floatval($crypto_data['price']);
                          }
                          $converted = PN_PERSONAL_FINANCE_MANAGER_Data::pn_personal_finance_manager_format_currency(
                            PN_PERSONAL_FINANCE_MANAGER_Data::pn_personal_finance_manager_convert_from_usd($crypto_current_value, $currency),
                            $currency
                          );
                      ?>
                        <span class="pn-personal-finance-manager-shares-badge pn-personal-finance-manager-ml-10 pn-personal-finance-manager-p-5 pn-personal-finance-manager-font-size-12 pn-personal-finance-manager-border-radius-3" style="background-color: #28a745; color: white;">
                          <span class="pn-personal-finance-manager-shares-label"><?php esc_html_e('Amount:', 'pn-personal-finance-manager'); ?></span>
                          <span class="pn-personal-finance-manager-shares-amount"><?php echo esc_html(number_format($crypto_amount, 2)); ?></span>
                          <?php if ($crypto_current_value > 0): ?>
                            <span class="pn-personal-finance-manager-current-value-label"> | <?php esc_html_e('Value:', 'pn-personal-finance-manager'); ?></span>
                            <span class="pn-personal-finance-manager-current-value-amount"><?php echo esc_html($converted['full']); ?></span>
                          <?php endif; ?>
                        </span>
                      <?php } ?>
                    <?php endif; endif; ?>

                    <?php if ($pn_personal_finance_manager_asset_period == 'on'): ?>
                      <i class="material-icons-outlined pn-personal-finance-manager-timed pn-personal-finance-manager-cursor-pointer pn-personal-finance-manager-vertical-align-super pn-personal-finance-manager-p-5 pn-personal-finance-manager-font-size-15 pn-personal-finance-manager-tooltip" title="<?php esc_html_e('This Asset is periodic', 'pn-personal-finance-manager'); ?>">replay</i>
                    <?php endif ?>
                  </a>
                </div>

                <div class="pn-personal-finance-manager-display-inline-table pn-personal-finance-manager-width-20-percent pn-personal-finance-manager-text-align-right pn-personal-finance-manager-position-relative">
                  <i class="material-icons-outlined pn-personal-finance-manager-menu-more-btn pn-personal-finance-manager-cursor-pointer pn-personal-finance-manager-vertical-align-middle pn-personal-finance-manager-font-size-30">more_vert</i>

                  <div class="pn-personal-finance-manager-menu-more pn-personal-finance-manager-z-index-99 pn-personal-finance-manager-display-none-soft">
                    <ul class="pn-personal-finance-manager-list-style-none">
                      <li>
                        <a href="#" class="pn-personal-finance-manager-popup-open-ajax pn-personal-finance-manager-text-decoration-none" data-pn-personal-finance-manager-popup-id="pn-personal-finance-manager-popup-pn_personal_finance_manager_asset-view" data-pn-personal-finance-manager-ajax-type="pn_personal_finance_manager_asset_view">
                          <div class="pn-personal-finance-manager-display-table pn-personal-finance-manager-width-100-percent">
                            <div class="pn-personal-finance-manager-display-inline-table pn-personal-finance-manager-width-70-percent">
                              <p><?php esc_html_e('View Asset', 'pn-personal-finance-manager'); ?></p>
                            </div>
                            <div class="pn-personal-finance-manager-display-inline-table pn-personal-finance-manager-width-20-percent pn-personal-finance-manager-text-align-right">
                              <i class="material-icons-outlined pn-personal-finance-manager-vertical-align-middle pn-personal-finance-manager-font-size-30 pn-personal-finance-manager-ml-30">visibility</i>
                            </div>
                          </div>
                        </a>
                      </li>
                      <li>
                        <a href="#" class="pn-personal-finance-manager-popup-open-ajax pn-personal-finance-manager-text-decoration-none" data-pn-personal-finance-manager-popup-id="pn-personal-finance-manager-popup-pn_personal_finance_manager_asset-edit" data-pn-personal-finance-manager-ajax-type="pn_personal_finance_manager_asset_edit"> 
                          <div class="pn-personal-finance-manager-display-table pn-personal-finance-manager-width-100-percent">
                            <div class="pn-personal-finance-manager-display-inline-table pn-personal-finance-manager-width-70-percent">
                              <p><?php esc_html_e('Edit Asset', 'pn-personal-finance-manager'); ?></p>
                            </div>
                            <div class="pn-personal-finance-manager-display-inline-table pn-personal-finance-manager-width-20-percent pn-personal-finance-manager-text-align-right">
                              <i class="material-icons-outlined pn-personal-finance-manager-vertical-align-middle pn-personal-finance-manager-font-size-30 pn-personal-finance-manager-ml-30">edit</i>
                            </div>
                          </div>
                        </a>
                      </li>
                      <li>
                        <a href="#" class="pn-personal-finance-manager-pn_personal_finance_manager_asset-duplicate-post">
                          <div class="pn-personal-finance-manager-display-table pn-personal-finance-manager-width-100-percent">
                            <div class="pn-personal-finance-manager-display-inline-table pn-personal-finance-manager-width-70-percent">
                              <p><?php esc_html_e('Duplicate Asset', 'pn-personal-finance-manager'); ?></p>
                            </div>
                            <div class="pn-personal-finance-manager-display-inline-table pn-personal-finance-manager-width-20-percent pn-personal-finance-manager-text-align-right">
                              <i class="material-icons-outlined pn-personal-finance-manager-vertical-align-middle pn-personal-finance-manager-font-size-30 pn-personal-finance-manager-ml-30">copy</i>
                            </div>
                          </div>
                        </a>
                      </li>
                      <li>
                        <a href="#" class="pn-personal-finance-manager-popup-open" data-pn-personal-finance-manager-popup-id="pn-personal-finance-manager-popup-pn_personal_finance_manager_asset-remove">
                          <div class="pn-personal-finance-manager-display-table pn-personal-finance-manager-width-100-percent">
                            <div class="pn-personal-finance-manager-display-inline-table pn-personal-finance-manager-width-70-percent">
                              <p><?php esc_html_e('Remove Asset', 'pn-personal-finance-manager'); ?></p>
                            </div>
                            <div class="pn-personal-finance-manager-display-inline-table pn-personal-finance-manager-width-20-percent pn-personal-finance-manager-text-align-right">
                              <i class="material-icons-outlined pn-personal-finance-manager-vertical-align-middle pn-personal-finance-manager-font-size-30 pn-personal-finance-manager-ml-30">delete</i>
                            </div>
                          </div>
                        </a>
                      </li>
                    </ul>
                  </div>
                </div>
              </div>
            </li>
          <?php endforeach ?>
        <?php endif ?>

        <li class="pn-personal-finance-manager-mt-50 pn-personal-finance-manager-asset" data-pn_personal_finance_manager_asset-id="0">
        <?php if (is_user_logged_in()) : ?>
          <a href="#" class="pn-personal-finance-manager-popup-open-ajax pn-personal-finance-manager-text-decoration-none" data-pn-personal-finance-manager-popup-id="pn-personal-finance-manager-popup-pn_personal_finance_manager_asset-add" data-pn-personal-finance-manager-ajax-type="pn_personal_finance_manager_asset_new">
            <div class="pn-personal-finance-manager-display-table pn-personal-finance-manager-width-100-percent">
              <div class="pn-personal-finance-manager-display-inline-table pn-personal-finance-manager-width-20-percent pn-personal-finance-manager-tablet-display-block pn-personal-finance-manager-tablet-width-100-percent pn-personal-finance-manager-text-align-center">
                <i class="material-icons-outlined pn-personal-finance-manager-cursor-pointer pn-personal-finance-manager-vertical-align-middle pn-personal-finance-manager-font-size-30 pn-personal-finance-manager-width-25">add</i>
              </div>
              <div class="pn-personal-finance-manager-display-inline-table pn-personal-finance-manager-width-80-percent pn-personal-finance-manager-tablet-display-block pn-personal-finance-manager-tablet-width-100-percent">
                <?php esc_html_e('Add new Asset', 'pn-personal-finance-manager'); ?>
              </div>
            </div>
          </a>
        <?php endif; ?>
        </li>
      </ul>
    <?php
    $pn_personal_finance_manager_return_string = ob_get_contents(); 
    ob_end_clean(); 
    return $pn_personal_finance_manager_return_string;
  }

  public function pn_personal_finance_manager_asset_view($asset_id) {
    ob_start();
    self::pn_personal_finance_manager_asset_register_scripts();
    self::pn_personal_finance_manager_asset_print_scripts();
    $current_asset_type = get_post_meta($asset_id, 'pn_personal_finance_manager_asset_type', true);
    ?>
      <div class="pn_personal_finance_manager_asset-view pn-personal-finance-manager-p-30" data-pn_personal_finance_manager_asset-id="<?php echo esc_attr($asset_id); ?>">
        <h4 class="pn-personal-finance-manager-text-align-center"><?php echo esc_html(get_the_title($asset_id)); ?></h4>

        <div class="pn-personal-finance-manager-word-wrap-break-word">
          <p><?php echo wp_kses(str_replace(']]>', ']]&gt;', apply_filters('the_content', get_post($asset_id)->post_content)), PN_PERSONAL_FINANCE_MANAGER_KSES); ?></p>
        </div>

        <div class="pn_personal_finance_manager_asset-view">
          <?php foreach (array_merge(self::pn_personal_finance_manager_asset_get_fields(), self::pn_personal_finance_manager_asset_get_fields_meta_with_values($asset_id)) as $pn_personal_finance_manager_field): ?>
            <?php if (!empty($pn_personal_finance_manager_field['parent_option']) && $pn_personal_finance_manager_field['parent_option'] !== $current_asset_type) continue; ?>
            <?php echo wp_kses(PN_PERSONAL_FINANCE_MANAGER_Forms::pn_personal_finance_manager_input_display_wrapper($pn_personal_finance_manager_field, 'post', $asset_id), PN_PERSONAL_FINANCE_MANAGER_KSES); ?>
          <?php endforeach ?>

          <div class="pn-personal-finance-manager-text-align-right pn-personal-finance-manager-asset" data-pn_personal_finance_manager_asset-id="<?php echo esc_attr($asset_id); ?>">
            <a href="#" class="pn-personal-finance-manager-btn pn-personal-finance-manager-btn-mini pn-personal-finance-manager-popup-open-ajax" data-pn-personal-finance-manager-popup-id="pn-personal-finance-manager-popup-pn_personal_finance_manager_asset-edit" data-pn-personal-finance-manager-ajax-type="pn_personal_finance_manager_asset_edit"><?php esc_html_e('Edit Asset', 'pn-personal-finance-manager'); ?></a>
          </div>
        </div>
      </div>
    <?php
    $pn_personal_finance_manager_return_string = ob_get_contents(); 
    ob_end_clean(); 
    return $pn_personal_finance_manager_return_string;
  }

  public function pn_personal_finance_manager_asset_new() {
    if (!is_user_logged_in()) {
      wp_die(esc_html__('You must be logged in to create a new asset.', 'pn-personal-finance-manager'), esc_html__('Access Denied', 'pn-personal-finance-manager'), ['response' => 403]);
    }
    
    ob_start();
    self::pn_personal_finance_manager_asset_register_scripts();
    self::pn_personal_finance_manager_asset_print_scripts();
    ?>
      <div class="pn_personal_finance_manager_asset-new pn-personal-finance-manager-p-30">
        <h4 class="pn-personal-finance-manager-mb-30"><?php esc_html_e('Add new Asset', 'pn-personal-finance-manager'); ?></h4>

        <form action="" method="post" id="pn-personal-finance-manager-form-asset-new" class="pn-personal-finance-manager-form">
          <?php
            $asset_fields = self::pn_personal_finance_manager_asset_get_fields();
            // Render title first
            if (isset($asset_fields['pn_personal_finance_manager_asset_title'])) {
              echo wp_kses(PN_PERSONAL_FINANCE_MANAGER_Forms::pn_personal_finance_manager_input_wrapper_builder($asset_fields['pn_personal_finance_manager_asset_title'], 'post'), PN_PERSONAL_FINANCE_MANAGER_KSES);
            }
          ?>

          <?php foreach (self::pn_personal_finance_manager_asset_get_fields_meta() as $pn_personal_finance_manager_field_meta): ?>
            <?php echo wp_kses(PN_PERSONAL_FINANCE_MANAGER_Forms::pn_personal_finance_manager_input_wrapper_builder($pn_personal_finance_manager_field_meta, 'post'), PN_PERSONAL_FINANCE_MANAGER_KSES); ?>
          <?php endforeach ?>

          <?php
            // Render description/comments last
            if (isset($asset_fields['pn_personal_finance_manager_asset_description'])) {
              echo wp_kses(PN_PERSONAL_FINANCE_MANAGER_Forms::pn_personal_finance_manager_input_wrapper_builder($asset_fields['pn_personal_finance_manager_asset_description'], 'post'), PN_PERSONAL_FINANCE_MANAGER_KSES);
            }
          ?>

          <div class="pn-personal-finance-manager-text-align-right">
            <input class="pn-personal-finance-manager-btn" data-pn-personal-finance-manager-type="post" data-pn-personal-finance-manager-subtype="post_new" data-pn-personal-finance-manager-post-type="pnpfm_asset" type="submit" value="<?php esc_attr_e('Create Asset', 'pn-personal-finance-manager'); ?>"/>
          </div>
        </form> 
      </div>
    <?php
    $pn_personal_finance_manager_return_string = ob_get_contents(); 
    ob_end_clean(); 
    return $pn_personal_finance_manager_return_string;
  }

  public function pn_personal_finance_manager_asset_edit($asset_id) {
    ob_start();
    self::pn_personal_finance_manager_asset_register_scripts();
    self::pn_personal_finance_manager_asset_print_scripts();
    ?>
      <div class="pn_personal_finance_manager_asset-edit pn-personal-finance-manager-p-30">
        <p class="pn-personal-finance-manager-text-align-center pn-personal-finance-manager-mb-0"><?php esc_html_e('Editing', 'pn-personal-finance-manager'); ?></p>
        <h4 class="pn-personal-finance-manager-text-align-center pn-personal-finance-manager-mb-30"><?php echo esc_html(get_the_title($asset_id)); ?></h4>

        <form action="" method="post" id="pn-personal-finance-manager-form-asset-edit" class="pn-personal-finance-manager-form">
          <?php
            $asset_fields = self::pn_personal_finance_manager_asset_get_fields($asset_id);
            // Render title first
            if (isset($asset_fields['pn_personal_finance_manager_asset_title'])) {
              echo wp_kses(PN_PERSONAL_FINANCE_MANAGER_Forms::pn_personal_finance_manager_input_wrapper_builder($asset_fields['pn_personal_finance_manager_asset_title'], 'post', $asset_id), PN_PERSONAL_FINANCE_MANAGER_KSES);
            }
          ?>

          <?php foreach (self::pn_personal_finance_manager_asset_get_fields_meta_with_values($asset_id) as $pn_personal_finance_manager_field_meta): ?>
            <?php echo wp_kses(PN_PERSONAL_FINANCE_MANAGER_Forms::pn_personal_finance_manager_input_wrapper_builder($pn_personal_finance_manager_field_meta, 'post', $asset_id), PN_PERSONAL_FINANCE_MANAGER_KSES); ?>
          <?php endforeach ?>

          <?php
            // Render description/comments last
            if (isset($asset_fields['pn_personal_finance_manager_asset_description'])) {
              echo wp_kses(PN_PERSONAL_FINANCE_MANAGER_Forms::pn_personal_finance_manager_input_wrapper_builder($asset_fields['pn_personal_finance_manager_asset_description'], 'post', $asset_id), PN_PERSONAL_FINANCE_MANAGER_KSES);
            }
          ?>

          <div class="pn-personal-finance-manager-text-align-right">
            <input class="pn-personal-finance-manager-btn" type="submit" data-pn-personal-finance-manager-type="post" data-pn-personal-finance-manager-subtype="post_edit" data-pn-personal-finance-manager-post-type="pnpfm_asset" data-pn-personal-finance-manager-post-id="<?php echo esc_attr($asset_id); ?>" value="<?php esc_attr_e('Save Asset', 'pn-personal-finance-manager'); ?>"/>
          </div>
        </form> 
      </div>
    <?php
    $pn_personal_finance_manager_return_string = ob_get_contents();
    ob_end_clean();
    return $pn_personal_finance_manager_return_string;
  }

  /**
   * Define custom columns for the Assets admin list table.
   *
   * @since  1.2.0
   * @param  array $columns Default columns.
   * @return array
   */
  public function pn_personal_finance_manager_asset_admin_columns( $columns ) {
    $new = [];
    $new['cb']    = $columns['cb'];
    $new['title'] = $columns['title'];
    $new['pnpfm_asset_type']   = __( 'Type', 'pn-personal-finance-manager' );
    $new['pnpfm_asset_value']  = __( 'Value', 'pn-personal-finance-manager' );
    $new['pnpfm_asset_date']   = __( 'Purchase Date', 'pn-personal-finance-manager' );
    $new['pnpfm_asset_sold']   = __( 'Status', 'pn-personal-finance-manager' );
    $new['author']            = __( 'Owner', 'pn-personal-finance-manager' );
    $new['date']              = $columns['date'];
    return $new;
  }

  /**
   * Render content for custom asset columns.
   *
   * @since  1.2.0
   * @param  string $column  Column key.
   * @param  int    $post_id Post ID.
   */
  public function pn_personal_finance_manager_asset_admin_column_content( $column, $post_id ) {
    $currency = get_option( 'pn_personal_finance_manager_currency', 'eur' );

    switch ( $column ) {
      case 'pnpfm_asset_type':
        $type = get_post_meta( $post_id, 'pn_personal_finance_manager_asset_type', true );
        $types = PN_PERSONAL_FINANCE_MANAGER_Data::pn_personal_finance_manager_get_asset_types();
        echo esc_html( ! empty( $types[ $type ] ) ? $types[ $type ] : '—' );
        break;

      case 'pnpfm_asset_value':
        $type = get_post_meta( $post_id, 'pn_personal_finance_manager_asset_type', true );
        $value = $this->pn_personal_finance_manager_get_asset_display_value( $post_id, $type );
        if ( $value !== null ) {
          $formatted = PN_PERSONAL_FINANCE_MANAGER_Data::pn_personal_finance_manager_format_currency(
            PN_PERSONAL_FINANCE_MANAGER_Data::pn_personal_finance_manager_convert_from_usd( $value, $currency ),
            $currency
          );
          echo esc_html( $formatted['full'] );
        } else {
          echo '—';
        }
        break;

      case 'pnpfm_asset_date':
        $date = get_post_meta( $post_id, 'pn_personal_finance_manager_asset_date', true );
        echo esc_html( ! empty( $date ) ? date_i18n( get_option( 'date_format' ), strtotime( $date ) ) : '—' );
        break;

      case 'pnpfm_asset_sold':
        $sold = get_post_meta( $post_id, 'pn_personal_finance_manager_asset_sold', true );
        if ( $sold === 'on' ) {
          echo '<span style="color:#d63638;">' . esc_html__( 'Sold', 'pn-personal-finance-manager' ) . '</span>';
        } else {
          echo '<span style="color:#00a32a;">' . esc_html__( 'Active', 'pn-personal-finance-manager' ) . '</span>';
        }
        break;
    }
  }

  /**
   * Make custom asset columns sortable.
   *
   * @since  1.2.0
   * @param  array $columns Sortable columns.
   * @return array
   */
  public function pn_personal_finance_manager_asset_sortable_columns( $columns ) {
    $columns['pnpfm_asset_type'] = 'pnpfm_asset_type';
    $columns['pnpfm_asset_date'] = 'pnpfm_asset_date';
    $columns['pnpfm_asset_sold'] = 'pnpfm_asset_sold';
    return $columns;
  }

  /**
   * Handle ordering by custom asset meta fields.
   *
   * @since  1.2.0
   * @param  \WP_Query $query The current query.
   */
  public function pn_personal_finance_manager_asset_column_orderby( $query ) {
    if ( ! is_admin() || ! $query->is_main_query() ) return;
    if ( $query->get( 'post_type' ) !== 'pnpfm_asset' ) return;

    $orderby = $query->get( 'orderby' );
    $meta_map = [
      'pnpfm_asset_type' => 'pn_personal_finance_manager_asset_type',
      'pnpfm_asset_date' => 'pn_personal_finance_manager_asset_date',
      'pnpfm_asset_sold' => 'pn_personal_finance_manager_asset_sold',
    ];

    if ( isset( $meta_map[ $orderby ] ) ) {
      $query->set( 'meta_key', $meta_map[ $orderby ] );
      $query->set( 'orderby', 'meta_value' );
    }
  }

  /**
   * Get a representative display value (USD) for an asset depending on its type.
   *
   * @since  1.2.0
   * @param  int    $post_id Asset post ID.
   * @param  string $type    Asset type key.
   * @return float|null      Value in USD or null.
   */
  private function pn_personal_finance_manager_get_asset_display_value( $post_id, $type ) {
    switch ( $type ) {
      case 'stocks':
        $shares = floatval( get_post_meta( $post_id, 'pn_personal_finance_manager_stock_total_amount', true ) );
        $purchase_price = floatval( get_post_meta( $post_id, 'pn_personal_finance_manager_stock_purchase_price', true ) );
        if ( $shares > 0 && $purchase_price > 0 ) {
          return $shares * $purchase_price;
        }
        return null;

      case 'cryptocurrencies':
        $amount = floatval( get_post_meta( $post_id, 'pn_personal_finance_manager_crypto_amount', true ) );
        $price  = floatval( get_post_meta( $post_id, 'pn_personal_finance_manager_crypto_purchase_price', true ) );
        if ( $amount > 0 && $price > 0 ) {
          return $amount * $price;
        }
        return null;

      case 'real_estate':
        $val = floatval( get_post_meta( $post_id, 'real_estate_current_value', true ) );
        return $val > 0 ? $val : ( floatval( get_post_meta( $post_id, 'real_estate_purchase_price', true ) ) ?: null );

      case 'commodities':
        $val = floatval( get_post_meta( $post_id, 'pn_personal_finance_manager_commodity_current_value', true ) );
        return $val > 0 ? $val : ( floatval( get_post_meta( $post_id, 'pn_personal_finance_manager_commodity_purchase_price', true ) ) ?: null );

      case 'art_collectibles':
        $val = floatval( get_post_meta( $post_id, 'pn_personal_finance_manager_art_estimated_value', true ) );
        return $val > 0 ? $val : ( floatval( get_post_meta( $post_id, 'pn_personal_finance_manager_art_purchase_price', true ) ) ?: null );

      case 'precious_metals':
        $val = floatval( get_post_meta( $post_id, 'pn_personal_finance_manager_metal_current_value', true ) );
        return $val > 0 ? $val : ( floatval( get_post_meta( $post_id, 'pn_personal_finance_manager_metal_purchase_price', true ) ) ?: null );

      default:
        return null;
    }
  }
}