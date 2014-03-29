/**
 * @file
 * JavaScript file for the Coffee module.
 */

(function($, Drupal, drupalSettings, DrupalCoffee) {
  // Remap the filter functions for autocomplete to recognise the
  // extra value "command".
  var proto = $.ui.autocomplete.prototype,
  	initSource = proto._initSource;
  var DrupalCoffee;
  function filter(array, term) {
  	var matcher = new RegExp( $.ui.autocomplete.escapeRegex(term), 'i');
  	return $.grep(array, function(value) {
                return matcher.test(value.command) || matcher.test(value.label) || matcher.test(value.value);
  	});
  }

  $.extend(proto, {
  	_initSource: function() {
  		if ($.isArray(this.options.source)) {
  			this.source = function(request, response) {
  				response(filter(this.options.source, request.term));
  			};
  		}
  		else {
  			initSource.call(this);
  		}
  	}
  });

  DrupalCoffee = DrupalCoffee || {};

  Drupal.behaviors.coffee = {
    attach: function() {
      $('body').once('coffee', function() {
        var body = $(this);
        DrupalCoffee.bg.appendTo(body).hide();
        DrupalCoffee.wrapper.appendTo('body').addClass('hide-form');
        DrupalCoffee.form
        .append(DrupalCoffee.label)
        .append(DrupalCoffee.field)
        .append(DrupalCoffee.results)
        .wrapInner('<div id="coffee-form-inner" />')
        .appendTo(DrupalCoffee.wrapper);

        // Load autocomplete data set, consider implementing
        // caching with local storage.
        DrupalCoffee.dataset = [];

        var autocomplete_data_element = 'ui-autocomplete';

        $.ajax({
          url: drupalSettings.path.basePath + 'admin/coffee/get-data',
          dataType: 'json',
          success: function(data) {
            DrupalCoffee.dataset = data;

            // Apply autocomplete plugin on show
            var $autocomplete = $(DrupalCoffee.field).autocomplete({
              source: DrupalCoffee.dataset,
              select: function(event, ui) {
                DrupalCoffee.redirect(ui.item.value, event.metaKey);
                event.preventDefault();

                return false;
              },
              delay: 0,
              appendTo: DrupalCoffee.results
           });
            
           $autocomplete.data(autocomplete_data_element)._renderItem = function(ul, item) {
              return  $('<li></li>')
                      .data('item.autocomplete', item)
                      .append('<a>' + item.label + '<small class="description">' + item.value + '</small></a>')
                      .appendTo(ul);
            };

            // We want to limit the number of results.
            $(DrupalCoffee.field).data(autocomplete_data_element)._renderMenu = function(ul, items) {
          		var self = this;
          		items = items.slice(0, 7); // @todo: max should be in Drupal.settings var.
          		$.each( items, function(index, item) {
          			self._renderItemData(ul, item);
          		});
          	};

          	// On submit of the form select the first result if available.
          	DrupalCoffee.form.submit(function() {
          	  var firstItem = jQuery(DrupalCoffee.results).find('li:first').data('item.autocomplete');
          	  if (typeof firstItem == 'object') {
          	    DrupalCoffee.redirect(firstItem.value, false);
          	  }

          	  return false;
          	});
          },
          error: function() {
            DrupalCoffee.field.val('Could not load data, please refresh the page');
          }
        });

        // Key events
        $(document).keydown(function(event) {
          var activeElement = $(document.activeElement);

          // Show the form with alt + D. Use 2 keycodes as 'D' can be uppercase or lowercase.
          if (DrupalCoffee.wrapper.hasClass('hide-form') &&
        		  event.altKey === true && 
        		  // 68/206 = d/D, 75 = k. 
        		  (event.keyCode === 68 || event.keyCode === 206  || event.keyCode === 75)) {
            DrupalCoffee.coffee_show();
            event.preventDefault();
          }
          // Close the form with esc or alt + D.
          else if (!DrupalCoffee.wrapper.hasClass('hide-form') && (event.keyCode === 27 || (event.altKey === true && (event.keyCode === 68 || event.keyCode === 206)))) {
            DrupalCoffee.coffee_close();
            event.preventDefault();
          }
        });
      });
    }
  };

  // Prefix the open and close functions to avoid
  // conflicts with autocomplete plugin.

  /**
   * Open the form and focus on the search field.
   */
  DrupalCoffee.coffee_show = function() {
    DrupalCoffee.wrapper.removeClass('hide-form');
    DrupalCoffee.bg.show();
    DrupalCoffee.field.focus();
    $(DrupalCoffee.field).autocomplete({enable: true});
  };

  /**
   * Close the form and destroy all data.
   */
  DrupalCoffee.coffee_close = function() {
    DrupalCoffee.field.val('');
    //DrupalCoffee.results.empty();
    DrupalCoffee.wrapper.addClass('hide-form');
    DrupalCoffee.bg.hide();
    $(DrupalCoffee.field).autocomplete({enable: false});
  };

  /**
   * Close the Coffee form and redirect.
   */
  DrupalCoffee.redirect = function(path, openInNewWindow) {
    DrupalCoffee.coffee_close();

    if (openInNewWindow) {
      window.open(drupalSettings.path.basePath + path);
    }
    else {
      document.location = drupalSettings.path.basePath + path;
    }
  };

  /**
   * The HTML elements.
   */
  DrupalCoffee.label = $('<label for="coffee-q" class="hidden" />').text(Drupal.t('Query'));
  DrupalCoffee.results = $('<div id="coffee-results" />');
  DrupalCoffee.wrapper = $('<div class="coffee-form-wrapper" />');
  DrupalCoffee.form = $('<form id="coffee-form" action="#" />');

  DrupalCoffee.bg = $('<div id="coffee-bg" />').click(function() {
    DrupalCoffee.coffee_close();
  });

  DrupalCoffee.field = $('<input id="coffee-q" type="text" autocomplete="off" />');

}(jQuery, Drupal, drupalSettings));
