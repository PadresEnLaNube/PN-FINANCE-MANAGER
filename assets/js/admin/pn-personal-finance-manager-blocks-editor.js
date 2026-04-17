/**
 * Personal Finance Manager – Gutenberg block registrations.
 *
 * No build step required – uses WP global packages:
 *   wp.blocks, wp.element, wp.components, wp.blockEditor, wp.i18n
 *
 * @since 1.2.0
 */
(function () {
  var registerBlockType = wp.blocks.registerBlockType;
  var el = wp.element.createElement;
  var __ = wp.i18n.__;
  var InspectorControls = wp.blockEditor.InspectorControls;
  var PanelBody = wp.components.PanelBody;
  var SelectControl = wp.components.SelectControl;

  var CATEGORY = 'pn-personal-finance-manager';

  /* ------------------------------------------------------------------
   * Dynamic brand color from plugin settings (default: teal #008080)
   * ----------------------------------------------------------------*/
  var BRAND = (typeof pn_personal_finance_manager_blocks_editor !== 'undefined' && pn_personal_finance_manager_blocks_editor.primaryColor) || '#008080';

  function hexToRgb(hex) {
    var result = /^#?([a-f\d]{2})([a-f\d]{2})([a-f\d]{2})$/i.exec(hex);
    return result
      ? { r: parseInt(result[1], 16), g: parseInt(result[2], 16), b: parseInt(result[3], 16) }
      : { r: 0, g: 128, b: 128 };
  }

  var brandRgb = hexToRgb(BRAND);
  var BRAND_LIGHT = 'rgba(' + brandRgb.r + ', ' + brandRgb.g + ', ' + brandRgb.b + ', 0.08)';
  var BRAND_GRADIENT = 'linear-gradient(135deg, ' + BRAND + ' 0%, rgba(' + brandRgb.r + ', ' + brandRgb.g + ', ' + brandRgb.b + ', 0.6) 100%)';
  var BRAND_SHADOW = '0 2px 8px rgba(' + brandRgb.r + ', ' + brandRgb.g + ', ' + brandRgb.b + ', 0.15)';

  /* ------------------------------------------------------------------
   * Custom SVG icons (used for both block registration & placeholders)
   * ----------------------------------------------------------------*/
  var icons = {
    assets: el('svg', { xmlns: 'http://www.w3.org/2000/svg', viewBox: '0 0 24 24', width: 24, height: 24 },
      el('path', { fill: BRAND, d: 'M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1.41 16.09V20h-2.67v-1.93c-1.71-.36-3.16-1.46-3.27-3.4h1.96c.1 1.05.82 1.87 2.65 1.87 1.96 0 2.4-.98 2.4-1.59 0-.83-.44-1.61-2.67-2.14-2.48-.6-4.18-1.62-4.18-3.67 0-1.72 1.39-2.84 3.11-3.21V4h2.67v1.95c1.86.45 2.79 1.86 2.85 3.39H14.3c-.05-1.11-.64-1.87-2.22-1.87-1.5 0-2.4.68-2.4 1.64 0 .84.65 1.39 2.67 1.94s4.18 1.36 4.18 3.85c0 1.89-1.44 2.98-3.12 3.19z' })
    ),
    liabilities: el('svg', { xmlns: 'http://www.w3.org/2000/svg', viewBox: '0 0 24 24', width: 24, height: 24 },
      el('path', { fill: BRAND, d: 'M19 14V6c0-1.1-.9-2-2-2H3c-1.1 0-2 .9-2 2v8c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2zm-2 0H3V6h14v8zm-7-7c-1.66 0-3 1.34-3 3s1.34 3 3 3 3-1.34 3-3-1.34-3-3-3zm13 0v11c0 1.1-.9 2-2 2H4v-2h17V7h2z' })
    ),
    portfolio: el('svg', { xmlns: 'http://www.w3.org/2000/svg', viewBox: '0 0 24 24', width: 24, height: 24 },
      el('path', { fill: BRAND, d: 'M3.5 18.49l6-6.01 4 4L22 6.92l-1.41-1.41-7.09 7.97-4-4L2 16.99z' })
    ),
    watchlist: el('svg', { xmlns: 'http://www.w3.org/2000/svg', viewBox: '0 0 24 24', width: 24, height: 24 },
      el('path', { fill: BRAND, d: 'M12 4.5C7 4.5 2.73 7.61 1 12c1.73 4.39 6 7.5 11 7.5s9.27-3.11 11-7.5c-1.73-4.39-6-7.5-11-7.5zM12 17c-2.76 0-5-2.24-5-5s2.24-5 5-5 5 2.24 5 5-2.24 5-5 5zm0-8c-1.66 0-3 1.34-3 3s1.34 3 3 3 3-1.34 3-3-1.34-3-3-3z' })
    ),
  };

  /* ------------------------------------------------------------------
   * Large SVG icons for the placeholder center (48px)
   * ----------------------------------------------------------------*/
  var largeIcons = {
    assets: el('svg', { xmlns: 'http://www.w3.org/2000/svg', viewBox: '0 0 24 24', width: 48, height: 48 },
      el('path', { fill: BRAND, d: 'M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1.41 16.09V20h-2.67v-1.93c-1.71-.36-3.16-1.46-3.27-3.4h1.96c.1 1.05.82 1.87 2.65 1.87 1.96 0 2.4-.98 2.4-1.59 0-.83-.44-1.61-2.67-2.14-2.48-.6-4.18-1.62-4.18-3.67 0-1.72 1.39-2.84 3.11-3.21V4h2.67v1.95c1.86.45 2.79 1.86 2.85 3.39H14.3c-.05-1.11-.64-1.87-2.22-1.87-1.5 0-2.4.68-2.4 1.64 0 .84.65 1.39 2.67 1.94s4.18 1.36 4.18 3.85c0 1.89-1.44 2.98-3.12 3.19z' })
    ),
    liabilities: el('svg', { xmlns: 'http://www.w3.org/2000/svg', viewBox: '0 0 24 24', width: 48, height: 48 },
      el('path', { fill: BRAND, d: 'M19 14V6c0-1.1-.9-2-2-2H3c-1.1 0-2 .9-2 2v8c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2zm-2 0H3V6h14v8zm-7-7c-1.66 0-3 1.34-3 3s1.34 3 3 3 3-1.34 3-3-1.34-3-3-3zm13 0v11c0 1.1-.9 2-2 2H4v-2h17V7h2z' })
    ),
    portfolio: el('svg', { xmlns: 'http://www.w3.org/2000/svg', viewBox: '0 0 24 24', width: 48, height: 48 },
      el('path', { fill: BRAND, d: 'M3.5 18.49l6-6.01 4 4L22 6.92l-1.41-1.41-7.09 7.97-4-4L2 16.99z' })
    ),
    watchlist: el('svg', { xmlns: 'http://www.w3.org/2000/svg', viewBox: '0 0 24 24', width: 48, height: 48 },
      el('path', { fill: BRAND, d: 'M12 4.5C7 4.5 2.73 7.61 1 12c1.73 4.39 6 7.5 11 7.5s9.27-3.11 11-7.5c-1.73-4.39-6-7.5-11-7.5zM12 17c-2.76 0-5-2.24-5-5s2.24-5 5-5 5 2.24 5 5-2.24 5-5 5zm0-8c-1.66 0-3 1.34-3 3s1.34 3 3 3 3-1.34 3-3-1.34-3-3-3z' })
    ),
  };

  /* ------------------------------------------------------------------
   * Helper: styled editor placeholder
   * ----------------------------------------------------------------*/
  function blockPreview(iconKey, label, description, badge) {
    return el('div', {
        style: {
          background: BRAND_LIGHT,
          border: '1px solid ' + BRAND,
          borderRadius: '8px',
          padding: '32px 24px',
          textAlign: 'center',
          position: 'relative',
          overflow: 'hidden',
        },
      },
      // Top gradient bar
      el('div', {
        style: {
          position: 'absolute',
          top: 0,
          left: 0,
          right: 0,
          height: '4px',
          background: BRAND_GRADIENT,
        },
      }),
      // Icon circle
      el('div', {
        style: {
          width: '80px',
          height: '80px',
          borderRadius: '50%',
          background: '#fff',
          boxShadow: BRAND_SHADOW,
          display: 'flex',
          alignItems: 'center',
          justifyContent: 'center',
          margin: '0 auto 16px',
        },
      }, largeIcons[iconKey]),
      // Title
      el('h3', {
        style: {
          margin: '0 0 4px',
          fontSize: '16px',
          fontWeight: '600',
          color: '#1e1e1e',
          letterSpacing: '-0.01em',
        },
      }, label),
      // Badge (optional, e.g. display type)
      badge
        ? el('span', {
            style: {
              display: 'inline-block',
              background: BRAND,
              color: '#fff',
              fontSize: '11px',
              fontWeight: '500',
              padding: '2px 10px',
              borderRadius: '10px',
              marginBottom: '8px',
              textTransform: 'capitalize',
            },
          }, badge)
        : null,
      // Description
      el('p', {
        style: {
          margin: badge ? '8px 0 0' : '8px 0 0',
          fontSize: '13px',
          color: '#757575',
          lineHeight: '1.5',
        },
      }, description),
      // "Personal Finance Manager" watermark
      el('div', {
        style: {
          marginTop: '16px',
          fontSize: '11px',
          color: BRAND,
          fontWeight: '500',
          opacity: '0.7',
          letterSpacing: '0.05em',
          textTransform: 'uppercase',
        },
      }, 'Personal Finance Manager')
    );
  }

  /* ------------------------------------------------------------------
   * 1. Assets List
   * ----------------------------------------------------------------*/
  registerBlockType('pn-personal-finance-manager/assets-list', {
    title: __('Assets List', 'pn-personal-finance-manager'),
    description: __('Displays the list of financial assets for the current user.', 'pn-personal-finance-manager'),
    icon: icons.assets,
    category: CATEGORY,
    supports: { multiple: false, html: false },
    edit: function () {
      return blockPreview(
        'assets',
        __('Assets List', 'pn-personal-finance-manager'),
        __('Displays the user\'s financial assets including stocks, crypto, real estate, and more.', 'pn-personal-finance-manager')
      );
    },
    save: function () {
      return null;
    },
  });

  /* ------------------------------------------------------------------
   * 2. Liabilities List
   * ----------------------------------------------------------------*/
  registerBlockType('pn-personal-finance-manager/liabilities-list', {
    title: __('Liabilities List', 'pn-personal-finance-manager'),
    description: __('Displays the list of financial liabilities for the current user.', 'pn-personal-finance-manager'),
    icon: icons.liabilities,
    category: CATEGORY,
    supports: { multiple: false, html: false },
    edit: function () {
      return blockPreview(
        'liabilities',
        __('Liabilities List', 'pn-personal-finance-manager'),
        __('Displays the user\'s financial liabilities including mortgages, loans, and debts.', 'pn-personal-finance-manager')
      );
    },
    save: function () {
      return null;
    },
  });

  /* ------------------------------------------------------------------
   * 3. Portfolio
   * ----------------------------------------------------------------*/
  registerBlockType('pn-personal-finance-manager/portfolio', {
    title: __('Portfolio', 'pn-personal-finance-manager'),
    description: __('Displays the user\'s financial portfolio (stocks, crypto, etc.).', 'pn-personal-finance-manager'),
    icon: icons.portfolio,
    category: CATEGORY,
    supports: { multiple: false, html: false },
    attributes: {
      display_type: { type: 'string', default: 'portfolio' },
    },
    edit: function (props) {
      var display_type = props.attributes.display_type;
      var setAttributes = props.setAttributes;

      var typeLabels = {
        portfolio: __('Full Portfolio', 'pn-personal-finance-manager'),
        summary: __('Summary', 'pn-personal-finance-manager'),
        stocks_only: __('Stocks Only', 'pn-personal-finance-manager'),
      };

      return el(
        wp.element.Fragment,
        null,
        el(
          InspectorControls,
          null,
          el(
            PanelBody,
            { title: __('Portfolio Settings', 'pn-personal-finance-manager'), initialOpen: true },
            el(SelectControl, {
              label: __('Display Type', 'pn-personal-finance-manager'),
              value: display_type,
              options: [
                { label: __('Full Portfolio', 'pn-personal-finance-manager'), value: 'portfolio' },
                { label: __('Summary', 'pn-personal-finance-manager'), value: 'summary' },
                { label: __('Stocks Only', 'pn-personal-finance-manager'), value: 'stocks_only' },
              ],
              onChange: function (val) {
                setAttributes({ display_type: val });
              },
            })
          )
        ),
        blockPreview(
          'portfolio',
          __('Portfolio', 'pn-personal-finance-manager'),
          __('Displays the user\'s investment portfolio with performance charts and P&L tracking.', 'pn-personal-finance-manager'),
          typeLabels[display_type] || display_type
        )
      );
    },
    save: function () {
      return null;
    },
  });

  /* ------------------------------------------------------------------
   * 4. Watchlist
   * ----------------------------------------------------------------*/
  registerBlockType('pn-personal-finance-manager/watchlist', {
    title: __('Watchlist', 'pn-personal-finance-manager'),
    description: __('Displays the user\'s stock and crypto watchlist.', 'pn-personal-finance-manager'),
    icon: icons.watchlist,
    category: CATEGORY,
    supports: { multiple: false, html: false },
    edit: function () {
      return blockPreview(
        'watchlist',
        __('Watchlist', 'pn-personal-finance-manager'),
        __('Displays the user\'s stock and crypto watchlist with price alerts and real-time data.', 'pn-personal-finance-manager')
      );
    },
    save: function () {
      return null;
    },
  });
})();
