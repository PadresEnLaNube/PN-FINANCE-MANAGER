(function($) {
	'use strict';

  window.pn_finance_manager_pad = function (str, max) {
	  var str = str.toString();
	  return str.length < max ? pad('0' + str, max) : str;
	}

	window.pn_finance_manager_empty = function (element) {
    return !(typeof element !== 'undefined');
	}

  window.pn_finance_manager_in_array = function (needle, haystack) {
    var length = haystack.length;
    for(var i = 0; i < length; i++) {
        if(haystack[i] == needle) return true;
    }

    return false;
  }

  window.pn_finance_manager_sanitize_title = function (title, separator = '-') {
    var diacritics_map;

    return remove_single_trailing_dash(replace_spaces_with_dash(remove_html_entities(remove_accents(title.replace(/<[^>]+>/ig, '')).toLowerCase().replace(/&(?:(?:nbsp)|(?:ndash)|(?:mdash));/g, separator)).replace(/[\/\.]/g, separator).replace(/[^\w\s-]+/g, '')));

    function remove_html_entities(str) {
      return str.replace(/&.*?;/g, '');
    }

    function replace_spaces_with_dash(str) {
      return str.replace(/ +/g, separator).replace(/-{2,}/g, separator);
    }

    function remove_single_trailing_dash(str) {
      if ('-' === str.substr(str.length - 1)) {
        return str.substr(0, str.length - 1);
      }
      return str;
    }

    function get_diacritics_removal_map() {
      if (diacritics_map) {
        return diacritics_map;
      }

      var default_diacritics_removal_map = [
        {'base':'-', 'letters':'\u2013\u2014\u00A0'},
        {'base':'A', 'letters':'\u0041\u24B6\uFF21\u00C0\u00C1\u00C2\u1EA6\u1EA4\u1EAA\u1EA8\u00C3\u0100\u0102\u1EB0\u1EAE\u1EB4\u1EB2\u0226\u01E0\u00C4\u01DE\u1EA2\u00C5\u01FA\u01CD\u0200\u0202\u1EA0\u1EAC\u1EB6\u1E00\u0104\u023A\u2C6F'},
        {'base':'AA','letters':'\uA732'},
        {'base':'AE','letters':'\u00C6\u01FC\u01E2'},
        {'base':'AO','letters':'\uA734'},
        {'base':'AU','letters':'\uA736'},
        {'base':'AV','letters':'\uA738\uA73A'},
        {'base':'AY','letters':'\uA73C'},
        {'base':'B', 'letters':'\u0042\u24B7\uFF22\u1E02\u1E04\u1E06\u0243\u0182\u0181'},
        {'base':'C', 'letters':'\u0043\u24B8\uFF23\u0106\u0108\u010A\u010C\u00C7\u1E08\u0187\u023B\uA73E'},
        {'base':'D', 'letters':'\u0044\u24B9\uFF24\u1E0A\u010E\u1E0C\u1E10\u1E12\u1E0E\u0110\u018B\u018A\u0189\uA779\u00D0'},
        {'base':'DZ','letters':'\u01F1\u01C4'},
        {'base':'Dz','letters':'\u01F2\u01C5'},
        {'base':'E', 'letters':'\u0045\u24BA\uFF25\u00C8\u00C9\u00CA\u1EC0\u1EBE\u1EC4\u1EC2\u1EBC\u0112\u1E14\u1E16\u0114\u0116\u00CB\u1EBA\u011A\u0204\u0206\u1EB8\u1EC6\u0228\u1E1C\u0118\u1E18\u1E1A\u0190\u018E'},
        {'base':'F', 'letters':'\u0046\u24BB\uFF26\u1E1E\u0191\uA77B'},
        {'base':'G', 'letters':'\u0047\u24BC\uFF27\u01F4\u011C\u1E20\u011E\u0120\u01E6\u0122\u01E4\u0193\uA7A0\uA77D\uA77E'},
        {'base':'H', 'letters':'\u0048\u24BD\uFF28\u0124\u1E22\u1E26\u021E\u1E24\u1E28\u1E2A\u0126\u2C67\u2C75\uA78D'},
        {'base':'I', 'letters':'\u0049\u24BE\uFF29\u00CC\u00CD\u00CE\u0128\u012A\u012C\u0130\u00CF\u1E2E\u1EC8\u01CF\u0208\u020A\u1ECA\u012E\u1E2C\u0197'},
        {'base':'J', 'letters':'\u004A\u24BF\uFF2A\u0134\u0248'},
        {'base':'K', 'letters':'\u004B\u24C0\uFF2B\u1E30\u01E8\u1E32\u0136\u1E34\u0198\u2C69\uA740\uA742\uA744\uA7A2'},
        {'base':'L', 'letters':'\u004C\u24C1\uFF2C\u013F\u0139\u013D\u1E36\u1E38\u013B\u1E3C\u1E3A\u0141\u023D\u2C62\u2C60\uA748\uA746\uA780'},
        {'base':'LJ','letters':'\u01C7'},
        {'base':'Lj','letters':'\u01C8'},
        {'base':'M', 'letters':'\u004D\u24C2\uFF2D\u1E3E\u1E40\u1E42\u2C6E\u019C'},
        {'base':'N', 'letters':'\u004E\u24C3\uFF2E\u01F8\u0143\u00D1\u1E44\u0147\u1E46\u0145\u1E4A\u1E48\u0220\u019D\uA790\uA7A4'},
        {'base':'NJ','letters':'\u01CA'},
        {'base':'Nj','letters':'\u01CB'},
        {'base':'O', 'letters':'\u004F\u24C4\uFF2F\u00D2\u00D3\u00D4\u1ED2\u1ED0\u1ED6\u1ED4\u00D5\u1E4C\u022C\u1E4E\u014C\u1E50\u1E52\u014E\u022E\u0230\u00D6\u022A\u1ECE\u0150\u01D1\u020C\u020E\u01A0\u1EDC\u1EDA\u1EE0\u1EDE\u1EE2\u1ECC\u1ED8\u01EA\u01EC\u00D8\u01FE\u0186\u019F\uA74A\uA74C'},
          {'base':'OI','letters':'\u01A2'},
        {'base':'OO','letters':'\uA74E'},
        {'base':'OU','letters':'\u0222'},
        {'base':'OE','letters':'\u008C\u0152'},
        {'base':'oe','letters':'\u009C\u0153'},
        {'base':'P', 'letters':'\u0050\u24C5\uFF30\u1E54\u1E56\u01A4\u2C63\uA750\uA752\uA754'},
        {'base':'Q', 'letters':'\u0051\u24C6\uFF31\uA756\uA758\u024A'},
        {'base':'R', 'letters':'\u0052\u24C7\uFF32\u0154\u1E58\u0158\u0210\u0212\u1E5A\u1E5C\u0156\u1E5E\u024C\u2C64\uA75A\uA7A6\uA782'},
        {'base':'S', 'letters':'\u0053\u24C8\uFF33\u1E9E\u015A\u1E64\u015C\u1E60\u0160\u1E66\u1E62\u1E68\u0218\u015E\u2C7E\uA7A8\uA784'},
        {'base':'T', 'letters':'\u0054\u24C9\uFF34\u1E6A\u0164\u1E6C\u021A\u0162\u1E70\u1E6E\u0166\u01AC\u01AE\u023E\uA786'},
        {'base':'TZ','letters':'\uA728'},
        {'base':'U', 'letters':'\u0055\u24CA\uFF35\u00D9\u00DA\u00DB\u0168\u1E78\u016A\u1E7A\u016C\u00DC\u01DB\u01D7\u01D5\u01D9\u1EE6\u016E\u0170\u01D3\u0214\u0216\u01AF\u1EEA\u1EE8\u1EEE\u1EEC\u1EF0\u1EE4\u1E72\u0172\u1E76\u1E74\u0244'},
        {'base':'V', 'letters':'\u0056\u24CB\uFF36\u1E7C\u1E7E\u01B2\uA75E\u0245'},
        {'base':'VY','letters':'\uA760'},
        {'base':'W', 'letters':'\u0057\u24CC\uFF37\u1E80\u1E82\u0174\u1E86\u1E84\u1E88\u2C72'},
        {'base':'X', 'letters':'\u0058\u24CD\uFF38\u1E8A\u1E8C'},
        {'base':'Y', 'letters':'\u0059\u24CE\uFF39\u1EF2\u00DD\u0176\u1EF8\u0232\u1E8E\u0178\u1EF6\u1EF4\u01B3\u024E\u1EFE'},
        {'base':'Z', 'letters':'\u005A\u24CF\uFF3A\u0179\u1E90\u017B\u017D\u1E92\u1E94\u01B5\u0224\u2C7F\u2C6B\uA762'},
        {'base':'a', 'letters':'\u0061\u24D0\uFF41\u1E9A\u00E0\u00E1\u00E2\u1EA7\u1EA5\u1EAB\u1EA9\u00E3\u0101\u0103\u1EB1\u1EAF\u1EB5\u1EB3\u0227\u01E1\u00E4\u01DF\u1EA3\u00E5\u01FB\u01CE\u0201\u0203\u1EA1\u1EAD\u1EB7\u1E01\u0105\u2C65\u0250'},
        {'base':'aa','letters':'\uA733'},
        {'base':'ae','letters':'\u00E6\u01FD\u01E3'},
        {'base':'ao','letters':'\uA735'},
        {'base':'au','letters':'\uA737'},
        {'base':'av','letters':'\uA739\uA73B'},
        {'base':'ay','letters':'\uA73D'},
        {'base':'b', 'letters':'\u0062\u24D1\uFF42\u1E03\u1E05\u1E07\u0180\u0183\u0253'},
        {'base':'c', 'letters':'\u0063\u24D2\uFF43\u0107\u0109\u010B\u010D\u00E7\u1E09\u0188\u023C\uA73F\u2184'},
        {'base':'d', 'letters':'\u0064\u24D3\uFF44\u1E0B\u010F\u1E0D\u1E11\u1E13\u1E0F\u0111\u018C\u0256\u0257\uA77A'},
        {'base':'dz','letters':'\u01F3\u01C6'},
        {'base':'e', 'letters':'\u0065\u24D4\uFF45\u00E8\u00E9\u00EA\u1EC1\u1EBF\u1EC5\u1EC3\u1EBD\u0113\u1E15\u1E17\u0115\u0117\u00EB\u1EBB\u011B\u0205\u0207\u1EB9\u1EC7\u0229\u1E1D\u0119\u1E19\u1E1B\u0247\u025B\u01DD'},
        {'base':'f', 'letters':'\u0066\u24D5\uFF46\u1E1F\u0192\uA77C'},
        {'base':'g', 'letters':'\u0067\u24D6\uFF47\u01F5\u011D\u1E21\u011F\u0121\u01E7\u0123\u01E5\u0260\uA7A1\u1D79\uA77F'},
        {'base':'h', 'letters':'\u0068\u24D7\uFF48\u0125\u1E23\u1E27\u021F\u1E25\u1E29\u1E2B\u1E96\u0127\u2C68\u2C76\u0265'},
        {'base':'hv','letters':'\u0195'},
        {'base':'i', 'letters':'\u0069\u24D8\uFF49\u00EC\u00ED\u00EE\u0129\u012B\u012D\u00EF\u1E2F\u1EC9\u01D0\u0209\u020B\u1ECB\u012F\u1E2D\u0268\u0131'},
        {'base':'j', 'letters':'\u006A\u24D9\uFF4A\u0135\u01F0\u0249'},
        {'base':'k', 'letters':'\u006B\u24DA\uFF4B\u1E31\u01E9\u1E33\u0137\u1E35\u0199\u2C6A\uA741\uA743\uA745\uA7A3'},
        {'base':'l', 'letters':'\u006C\u24DB\uFF4C\u0140\u013A\u013E\u1E37\u1E39\u013C\u1E3D\u1E3B\u017F\u0142\u019A\u026B\u2C61\uA749\uA781\uA747'},
        {'base':'lj','letters':'\u01C9'},
        {'base':'m', 'letters':'\u006D\u24DC\uFF4D\u1E3F\u1E41\u1E43\u0271\u026F'},
        {'base':'n', 'letters':'\u006E\u24DD\uFF4E\u01F9\u0144\u00F1\u1E45\u0148\u1E47\u0146\u1E4B\u1E49\u019E\u0272\u0149\uA791\uA7A5'},
        {'base':'nj','letters':'\u01CC'},
        {'base':'o', 'letters':'\u006F\u24DE\uFF4F\u00F2\u00F3\u00F4\u1ED3\u1ED1\u1ED7\u1ED5\u00F5\u1E4D\u022D\u1E4F\u014D\u1E51\u1E53\u014F\u022F\u0231\u00F6\u022B\u1ECF\u0151\u01D2\u020D\u020F\u01A1\u1EDD\u1EDB\u1EE1\u1EDF\u1EE3\u1ECD\u1ED9\u01EB\u01ED\u00F8\u01FF\u0254\uA74B\uA74D\u0275'},
          {'base':'oi','letters':'\u01A3'},
        {'base':'ou','letters':'\u0223'},
        {'base':'oo','letters':'\uA74F'},
        {'base':'p','letters':'\u0070\u24DF\uFF50\u1E55\u1E57\u01A5\u1D7D\uA751\uA753\uA755'},
        {'base':'q','letters':'\u0071\u24E0\uFF51\u024B\uA757\uA759'},
        {'base':'r','letters':'\u0072\u24E1\uFF52\u0155\u1E59\u0159\u0211\u0213\u1E5B\u1E5D\u0157\u1E5F\u024D\u027D\uA75B\uA7A7\uA783'},
        {'base':'s','letters':'\u0073\u24E2\uFF53\u00DF\u015B\u1E65\u015D\u1E61\u0161\u1E67\u1E63\u1E69\u0219\u015F\u023F\uA7A9\uA785\u1E9B'},
        {'base':'t','letters':'\u0074\u24E3\uFF54\u1E6B\u1E97\u0165\u1E6D\u021B\u0163\u1E71\u1E6F\u0167\u01AD\u0288\u2C66\uA787'},
        {'base':'tz','letters':'\uA729'},
        {'base':'u','letters': '\u0075\u24E4\uFF55\u00F9\u00FA\u00FB\u0169\u1E79\u016B\u1E7B\u016D\u00FC\u01DC\u01D8\u01D6\u01DA\u1EE7\u016F\u0171\u01D4\u0215\u0217\u01B0\u1EEB\u1EE9\u1EEF\u1EED\u1EF1\u1EE5\u1E73\u0173\u1E77\u1E75\u0289'},
        {'base':'v','letters':'\u0076\u24E5\uFF56\u1E7D\u1E7F\u028B\uA75F\u028C'},
        {'base':'vy','letters':'\uA761'},
        {'base':'w','letters':'\u0077\u24E6\uFF57\u1E81\u1E83\u0175\u1E87\u1E85\u1E98\u1E89\u2C73'},
        {'base':'x','letters':'\u0078\u24E7\uFF58\u1E8B\u1E8D'},
        {'base':'y','letters':'\u0079\u24E8\uFF59\u1EF3\u00FD\u0177\u1EF9\u0233\u1E8F\u00FF\u1EF7\u1E99\u1EF5\u01B4\u024F\u1EFF'},
        {'base':'z','letters':'\u007A\u24E9\uFF5A\u017A\u1E91\u017C\u017E\u1E93\u1E95\u01B6\u0225\u0240\u2C6C\uA763'}
      ];

      var diacritics_map = {};
      for (var i=0; i < default_diacritics_removal_map.length; i++){
        var letters = default_diacritics_removal_map [i].letters;
        for (var j = 0; j < letters.length; j++){
          diacritics_map[letters[j]] = default_diacritics_removal_map [i].base;
        }
      }
      return diacritics_map;
    }

    function remove_accents (str) {
      var diacritics_map = get_diacritics_removal_map();
      return str.replace(/[^\u0000-\u007E]/g, function(a) {
        return diacritics_map[a] || a;
      });
    }
  }

	window.pn_finance_manager_main_message_animate = function (){
		$('#pn-finance-manager-bar').css('width', '1%');
		$('#pn-finance-manager-bar').animate({
      'width': '100%', 
    }, 6000);
  }

	window.pn_finance_manager_get_main_message = function (pn_finance_manager_main_message_new_content){
		var pn_finance_manager_main_message_timeout;

		pn_finance_manager_main_message_animate();
		clearTimeout(pn_finance_manager_main_message_timeout);
    var pn_finance_manager_main_message = $('#pn-finance-manager-main-message');
    pn_finance_manager_main_message.find('#pn-finance-manager-main-message-span').html(pn_finance_manager_main_message_new_content);
    pn_finance_manager_main_message.css('right', '-500px');
    pn_finance_manager_main_message.css('display', 'block');

    $(pn_finance_manager_main_message).animate({
      'right': 0,
    }, 1000);

    pn_finance_manager_main_message_timeout = setTimeout(function(){
    	if (pn_finance_manager_main_message.is(":visible")) {
      	pn_finance_manager_main_message.fadeOut('slow');
    	}
    }, 5000);
  }

  window.pn_finance_manager_form_update = function (){
    $('.pn-finance-manager-field[data-pn-finance-manager-parent-option]').closest('.pn-finance-manager-input-wrapper').addClass('pn-finance-manager-display-none');

    $('.pn-finance-manager-field[data-pn-finance-manager-parent~="this"]').each(function(index_parent, element_parent) {
      var parent_this = $(this);

      $('.pn-finance-manager-field[data-pn-finance-manager-parent~=' + parent_this.attr('id') + ']').each(function(index, element) {
        if (parent_this.hasClass('pn-finance-manager-checkbox')) {
          if (parent_this.is(':checked') && $(this).attr('data-pn-finance-manager-parent-option') == 'on') {
            $(this).closest('.pn-finance-manager-input-wrapper').removeClass('pn-finance-manager-display-none');
          }
        } else {
          if (parent_this.val() == $(this).attr('data-pn-finance-manager-parent-option')) {
            $(this).closest('.pn-finance-manager-input-wrapper').removeClass('pn-finance-manager-display-none');
          }
        }
      });
    });
  }

  $(document).ready(function() {
    if ($('.pn-finance-manager-countdown').length) {
      $('.pn-finance-manager-countdown').each(function() {
        var pn_finance_manager_countdown = $(this);
        var pn_finance_manager_counter = pn_finance_manager_countdown.find('.pn-finance-manager-countdown-counter');
        var days = pn_finance_manager_countdown.find('.pn-finance-manager-countdown-days .pn-finance-manager-counter');
        var hours = pn_finance_manager_countdown.find('.pn-finance-manager-countdown-hours .pn-finance-manager-counter');
        var minutes = pn_finance_manager_countdown.find('.pn-finance-manager-countdown-minutes .pn-finance-manager-counter');
        var seconds = pn_finance_manager_countdown.find('.pn-finance-manager-countdown-seconds .pn-finance-manager-counter');
        var until = parseInt(pn_finance_manager_counter.attr('data-pn-finance-manager-until'), 10);

        var updateTime = function() {
          var now = Math.round((+new Date()) / 1000);

          /*const dt = new Date();
          var now = Math.round((+new Date(dt.toLocaleString('en-US', { timeZone: 'America/Los_Angeles' }))) / 1000);*/

          if(until <= now) {
            pn_finance_manager_countdown.find('.pn-finance-manager-countdown-over').fadeIn('slow');
            pn_finance_manager_countdown.find('.pn-finance-manager-countdown-counters, .pn-finance-manager-countdown-title').fadeOut('fast');
            clearInterval(interval);
            setTimeout(function() {document.location.reload(true);}, 10000);
          }

          var left = until - now;
          seconds_new = left % 60;
          if (seconds.text() != seconds_new) {
            seconds.animate({
              opacity: 0,
              fontSize: '2em'
            }, 500, function() {
              seconds.css('opacity', 1);
              seconds.css('font-size', '1em');
            })
          }
          seconds.text(seconds_new);

          left = Math.floor(left / 60);
          minutes_new = left % 60;
          if (minutes.text() != minutes_new) {
            minutes.animate({
              opacity: 0,
              fontSize: '2em'
            }, 500, function() {
              minutes.css('opacity', 1);
              minutes.css('font-size', '1em');
            })
          }
          minutes.text(minutes_new);

          left = Math.floor(left / 60);
          hours_new = left % 24;
          if (hours.text() != hours_new) {
            hours.animate({
              opacity: 0,
              fontSize: '2em'
            }, 500, function() {
              hours.css('opacity', 1);
              hours.css('font-size', '1em');
            })
          }
          hours.text(hours_new);

          left = Math.floor(left / 24);
          days_new = left;
          if (days.text() != days_new) {
            days.animate({
              opacity: 0,
              fontSize: '2em'
            }, 500, function() {
              days.css('opacity', 1);
              days.css('font-size', '1em');
            })
          }
          days.text(days_new);
        };

        var interval = setInterval(updateTime, 1000);

        $(window).load(function() {
          pn_finance_manager_countdown.find('.pn-finance-manager-loader').fadeOut('fast');
          pn_finance_manager_countdown.find('#pn-finance-manager-countdown').fadeIn('slow');
        });
      });
    }
    
    if($('.pn-finance-manager-reload-page').length) {
      $(document).on('click','.pn-finance-manager-reload-page',function(e){
        e.preventDefault();
        location.reload();
      });
    }

    if($('.pn-finance-manager-close-icon').length){
      $(document).on('click','.pn-finance-manager-close-icon',function(e){
        e.preventDefault();
        $(this).closest('div').fadeOut('slow');
      });
    }

    if($('.pn-finance-manager-menu-more-btn').length){
      $(document).on('click', '.pn-finance-manager-menu-more-btn', function(e){
        $(this).siblings('.pn-finance-manager-menu-more').fadeToggle('fast').toggleClass('pn-finance-manager-active');
        $('.pn-finance-manager-menu-more-overlay').fadeToggle();
      });

      $(document).on('click', '.pn-finance-manager-menu-more-overlay', function(e){
        $('.pn-finance-manager-menu-more.pn-finance-manager-active').fadeOut('slow').removeClass('pn-finance-manager-active');
        $('.pn-finance-manager-menu-more-overlay').fadeOut('fast');
      });

      $(document).on('keyup', function(e){
        if (e.keyCode == 27) {
          $('.pn-finance-manager-menu-more.pn-finance-manager-active').fadeOut('slow').removeClass('pn-finance-manager-active');
          $('.pn-finance-manager-menu-more-overlay').fadeOut('fast');
        }
      });
    }

    if($('.pn-finance-manager-list-more-btn').length){
      $(document).on('click', '.pn-finance-manager-list-more-btn', function(e){
        $(this).siblings('.pn-finance-manager-list-more').fadeToggle('fast').toggleClass('pn-finance-manager-active');
        $('.pn-finance-manager-menu-more-overlay').fadeToggle();
      });

      $(document).on('click', '.pn-finance-manager-menu-more-overlay', function(e){
        $('.pn-finance-manager-list-more.pn-finance-manager-active').fadeOut('slow').removeClass('pn-finance-manager-active');
        $('.pn-finance-manager-menu-more-overlay').fadeOut('fast');
      });

      $(document).on('keyup', function(e){
        if (e.keyCode == 27) {
          $('.pn-finance-manager-list-more.pn-finance-manager-active').fadeOut('slow').removeClass('pn-finance-manager-active');
          $('.pn-finance-manager-menu-more-overlay').fadeOut('fast');
        }
      });
    }

    if (pn_finance_manager_action.action != '') {
      if (pn_finance_manager_action.btn_id != '') {
        $(window).on('load', function(e) {
          $('#' + pn_finance_manager_action.btn_id).click();
        });
      }

      if (pn_finance_manager_action.popup != '') {
        $(window).on('load', function(e) {
          // Use custom popup system instead of Fancybox
          if (typeof PN_FINANCE_MANAGER_Popups !== 'undefined') {
            PN_FINANCE_MANAGER_Popups.open($('#' + pn_finance_manager_action.popup));
          }

          if (typeof pn_finance_manager_action.tab != '') {
            $('.userspn-tab-links[data-userspn-id="userspn-tab-' + pn_finance_manager_action.tab + '"]').click();
            $('#userspn-' + pn_finance_manager_action.tab + ' input#userspn_email').focus();
          } else {
            $('.userspn-tab-links[data-userspn-id="userspn-tab-login"]').click();
            $('#userspn-login input#user_login').focus();
          }
        });
      }
    }

    // --- Search toggle (show/hide search input) ---
    $(document).on('click', '.pn-finance-manager-cpt-search-toggle', function(e) {
      e.preventDefault();
      var searchToggle = $(this);
      var searchWrapper = searchToggle.closest('.pn-finance-manager-cpt-search-wrapper');
      var searchInput = searchWrapper.find('.pn-finance-manager-cpt-search-input');
      var listContainer = searchToggle.closest('.pn-finance-manager-cpt-search-container').siblings('[class*="-list-wrapper"]').first();

      if (searchInput.hasClass('pn-finance-manager-display-none')) {
        searchInput.removeClass('pn-finance-manager-display-none').focus();
        searchToggle.text('close');
        searchWrapper.addClass('pn-finance-manager-search-active');
      } else {
        searchInput.addClass('pn-finance-manager-display-none').val('');
        searchToggle.text('search');
        searchWrapper.removeClass('pn-finance-manager-search-active');
        listContainer.find('li[class*="-list-item"]').show();
      }
    });

    // --- Real-time text search on keyup ---
    $(document).on('keyup', '.pn-finance-manager-cpt-search-input', function(e) {
      var searchInput = $(this);
      var searchTerm = searchInput.val().toLowerCase().trim();
      var container = searchInput.closest('.pn-finance-manager-cpt-search-container');
      var listContainer = container.siblings('[class*="-list-wrapper"]').first();
      var items = listContainer.find('li[class*="-list-item"]');
      var filterSelect = container.find('.pn-finance-manager-cpt-filter-select');
      var activeFilter = filterSelect.length ? filterSelect.val() : '';

      if (searchTerm === '' && activeFilter === '') {
        items.show();
      } else {
        items.each(function() {
          var itemTitle = $(this).find('.pn-finance-manager-display-inline-table a span').first().text().toLowerCase();
          var matchesSearch = searchTerm === '' || itemTitle.indexOf(searchTerm) !== -1;
          var matchesFilter = true;

          if (activeFilter !== '') {
            var itemType = $(this).attr('data-pn-finance-manager-asset-type') || $(this).attr('data-pn-finance-manager-liability-type') || '';
            matchesFilter = (itemType === activeFilter);
          }

          if (matchesSearch && matchesFilter) {
            $(this).show();
          } else {
            $(this).hide();
          }
        });
      }
    });

    // --- Close search on Escape ---
    $(document).on('keydown', '.pn-finance-manager-cpt-search-input', function(e) {
      if (e.keyCode === 27) {
        var searchInput = $(this);
        var searchWrapper = searchInput.closest('.pn-finance-manager-cpt-search-wrapper');
        var searchToggle = searchWrapper.find('.pn-finance-manager-cpt-search-toggle');
        var listContainer = searchInput.closest('.pn-finance-manager-cpt-search-container').siblings('[class*="-list-wrapper"]').first();

        searchInput.addClass('pn-finance-manager-display-none').val('');
        searchToggle.text('search');
        searchWrapper.removeClass('pn-finance-manager-search-active');
        listContainer.find('li[class*="-list-item"]').show();
      }
    });

    // --- Filter toggle (show/hide filter dropdown) ---
    $(document).on('click', '.pn-finance-manager-cpt-filter-toggle', function(e) {
      e.preventDefault();
      var filterToggle = $(this);
      var container = filterToggle.closest('.pn-finance-manager-cpt-search-container');
      var filterDropdown = container.find('.pn-finance-manager-cpt-filter-dropdown');

      if (filterDropdown.hasClass('pn-finance-manager-display-none')) {
        filterDropdown.removeClass('pn-finance-manager-display-none');
        filterToggle.addClass('pn-finance-manager-filter-active');
      } else {
        filterDropdown.addClass('pn-finance-manager-display-none');
        filterToggle.removeClass('pn-finance-manager-filter-active');
        // Reset filter
        filterDropdown.find('.pn-finance-manager-cpt-filter-select').val('').trigger('change');
      }
    });

    // --- Filter by type on select change ---
    $(document).on('change', '.pn-finance-manager-cpt-filter-select', function() {
      var filterSelect = $(this);
      var filterValue = filterSelect.val();
      var container = filterSelect.closest('.pn-finance-manager-cpt-search-container');
      var listContainer = container.siblings('[class*="-list-wrapper"]').first();
      var items = listContainer.find('li[class*="-list-item"]');
      var searchInput = container.find('.pn-finance-manager-cpt-search-input');
      var searchTerm = searchInput.hasClass('pn-finance-manager-display-none') ? '' : searchInput.val().toLowerCase().trim();

      items.each(function() {
        var itemType = $(this).attr('data-pn-finance-manager-asset-type') || $(this).attr('data-pn-finance-manager-liability-type') || '';
        var matchesFilter = filterValue === '' || itemType === filterValue;
        var matchesSearch = true;

        if (searchTerm !== '') {
          var itemTitle = $(this).find('.pn-finance-manager-display-inline-table a span').first().text().toLowerCase();
          matchesSearch = itemTitle.indexOf(searchTerm) !== -1;
        }

        if (matchesFilter && matchesSearch) {
          $(this).show();
        } else {
          $(this).hide();
        }
      });
    });

    // --- Sort toggle (show/hide sort dropdown) ---
    $(document).on('click', '.pn-finance-manager-cpt-sort-toggle', function(e) {
      e.preventDefault();
      e.stopPropagation();
      var sortToggle = $(this);
      var container = sortToggle.closest('.pn-finance-manager-cpt-search-container');
      var sortDropdown = container.find('.pn-finance-manager-cpt-sort-dropdown');

      if (sortDropdown.hasClass('pn-finance-manager-display-none')) {
        sortDropdown.removeClass('pn-finance-manager-display-none');
        sortToggle.addClass('pn-finance-manager-sort-active');
      } else {
        sortDropdown.addClass('pn-finance-manager-display-none');
        sortToggle.removeClass('pn-finance-manager-sort-active');
      }
    });

    // --- Sort on select change ---
    $(document).on('change', '.pn-finance-manager-cpt-sort-select', function() {
      var sortValue = $(this).val();
      if (!sortValue) return;

      var container = $(this).closest('.pn-finance-manager-cpt-search-container');
      var parentWrapper = container.parent();
      var listWrapper = parentWrapper.find('[class*="-list-wrapper"]').first();
      var list = listWrapper.find('ul').first();

      if (!list.length) return;

      var items = [];
      list.children('li').each(function() {
        var cls = $(this).attr('class') || '';
        if (cls.indexOf('-list-item') !== -1) {
          items.push(this);
        }
      });

      if (items.length === 0) return;

      items.sort(function(a, b) {
        var $a = $(a);
        var $b = $(b);

        if (sortValue === 'value-desc' || sortValue === 'value-asc') {
          var valA = parseFloat($a.attr('data-pn-finance-manager-asset-value')) || 0;
          var valB = parseFloat($b.attr('data-pn-finance-manager-asset-value')) || 0;
          return sortValue === 'value-desc' ? valB - valA : valA - valB;
        }

        if (sortValue === 'alpha-asc' || sortValue === 'alpha-desc') {
          var nameA = $a.find('a span').first().text().toLowerCase().trim();
          var nameB = $b.find('a span').first().text().toLowerCase().trim();
          var cmp = nameA.localeCompare(nameB);
          return sortValue === 'alpha-asc' ? cmp : -cmp;
        }

        if (sortValue === 'percent-desc') {
          var pctA = parseFloat($a.attr('data-pn-finance-manager-asset-portfolio-percent')) || 0;
          var pctB = parseFloat($b.attr('data-pn-finance-manager-asset-portfolio-percent')) || 0;
          return pctB - pctA;
        }

        return 0;
      });

      // Detach sorted items and re-insert at the top of the list
      for (var i = 0; i < items.length; i++) {
        list.prepend(items[items.length - 1 - i]);
      }
    });

    if($('.pn-finance-manager-text-swapper').length){
      $('.pn-finance-manager-text-swapper').each(function(index, element) {
        var text = $(this).attr('data-pn-finance-manager-text').split('|');
        var color = $(this).attr('data-pn-finance-manager-color').split('|');
        var index = 0;

        if (text.length) {
          setInterval(function(){
            index++;

            if (index == text.length) {
              index = 0;
            }

            $('.pn-finance-manager-text-swapper').css('opacity', '0%');

            if (color.length) {
              $('.pn-finance-manager-text-swapper').css('color', color[index]);
            }

            $('.pn-finance-manager-text-swapper').text(text[index]);
            $('.pn-finance-manager-text-swapper').animate({
              'opacity': '100%', 
            }, 1000);
          }, 3000);
        }
      });
    }

    if($('.pn-finance-manager-tabs-wrapper').length) {
      if ($('.pn-finance-manager-tab-links').length) {
        function pn_finance_manager_tab_navigation(tab_wrapper, clicked = false) {
          var tab_link_active = tab_wrapper.find('button.pn-finance-manager-tab-links.active');

          tab_wrapper.find('.pn-finance-manager-navigator-previous, .pn-finance-manager-navigator-next').html('');

          var tab_previous = tab_link_active.prev();
          if (tab_previous.length) {
            tab_wrapper.find('.pn-finance-manager-navigator-previous').html('<a href="#" class="pn-finance-manager-tab-btn pn-finance-manager-text-decoration-none" data-pn-finance-manager-id="' + tab_previous.attr('data-pn-finance-manager-id') + '"><i class="material-icons-outlined pn-finance-manager-vertical-align-middle pn-finance-manager-mr-10">chevron_left</i>' + tab_previous.text() + '</a>');
          }

          var tab_next = tab_link_active.next();
          if (tab_next.length) {
            tab_wrapper.find('.pn-finance-manager-navigator-next').html('<a href="#" class="pn-finance-manager-tab-btn pn-finance-manager-text-decoration-none" data-pn-finance-manager-id="' + tab_next.attr('data-pn-finance-manager-id') + '">' + tab_next.text() + '<i class="material-icons-outlined pn-finance-manager-vertical-align-middle pn-finance-manager-ml-10">chevron_right</i></a>');
          }

          if (clicked) {            
            $('html, body').animate({
              scrollTop: tab_wrapper.find('.pn-finance-manager-tabs').offset().top - 150
            }, 500);
          }
        }

        $('.pn-finance-manager-tabs-wrapper').each(function(index, element) {
          pn_finance_manager_tab_navigation($(this));
        });

        $(document).on('click', '.pn-finance-manager-tab-links', function(e){
          e.preventDefault();
          e.stopPropagation();
          var tab_link = $(this);
          var tab_wrapper = $(this).closest('.pn-finance-manager-tabs-wrapper');
          
          tab_wrapper.find('.pn-finance-manager-tab-links').each(function(index, element) {
            $(this).removeClass('active');
            $($(this).attr('data-pn-finance-manager-id')).addClass('pn-finance-manager-display-none');
          });

          tab_wrapper.find('.pn-finance-manager-tab-content').each(function(index, element) {
            $(this).addClass('pn-finance-manager-display-none');
          });
          
          tab_link.addClass('active');
          tab_wrapper.find('#' + tab_link.attr('data-pn-finance-manager-id')).removeClass('pn-finance-manager-display-none');

          pn_finance_manager_tab_navigation(tab_wrapper, true);
        });

        $(document).on('click', '.pn-finance-manager-tab-btn', function(e){
          e.preventDefault();
          $(this).closest('.pn-finance-manager-tabs-wrapper').find('.pn-finance-manager-tab-links[data-pn-finance-manager-id="' + $(this).attr('data-pn-finance-manager-id') + '"]').click();
        });
      }
    }

    $(document).on('click', '.pn-finance-manager-btn-copy', function(e) {
      e.preventDefault();
      var textToCopy = $($(this).attr('data-pn-finance-manager-copy-content')).text();
      
      // Try using the modern Clipboard API first
      if (navigator.clipboard && window.isSecureContext) {
        navigator.clipboard.writeText(textToCopy)
          .then(() => {
            pn_finance_manager_get_main_message(pn_finance_manager_i18n.copied);
          })
          .catch(() => {
            // Fallback for when clipboard API fails
            fallback_copy_text_to_clipboard(textToCopy);
          });
      } else {
        // Fallback for older browsers
        fallback_copy_text_to_clipboard(textToCopy);
      }
    });

    // Fallback function for older browsers
    function fallback_copy_text_to_clipboard(text) {
      var pn_finance_manager_temp = $('<textarea>');
      $('body').append(pn_finance_manager_temp);
      pn_finance_manager_temp.val(text).select();
      try {
        document.execCommand('copy');
        pn_finance_manager_get_main_message(pn_finance_manager_i18n.copied);
      } catch (err) {
        console.error('Fallback: Unable to copy text', err);
      }
      pn_finance_manager_temp.remove();
    }
  });
})(jQuery);
