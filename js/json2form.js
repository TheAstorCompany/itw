/**
 * @author Desislav Michev	d [dot] michev [at] gmail.com
 * @website http://www.dotmedia.bg
 * @version 1.0
 */

(function($) {
	var methods = {
		init : function(options) { 
			if (!this.length) {
				alert('Attached form does not seems to be a real one :)');
			}
			
			var formObj = this;
			
			$.each(options, function(key, value) {
				var element = $(formObj).find('*[name="'+key+'"]');
				
				if (!element.length) {
					var element = $(formObj).find('*[name="'+key+'[]"]');
					
					if (!element.length) {
						return;
					}
				}
				
				if (element.attr('type') == 'checkbox') {
					$.each(value, function(vk, vv) {
						element.each(function (i, cb) {
							if ($(cb).val() == vk) {
								if (vv == true) {
									$(cb).attr('checked', 'checked');
								} else {
									$(cb).removeAttr('checked');
								}
							}
						});
					});
				} else if (element.attr('type') == 'radio') {
					element.attr('checked', 'checked');
				} else {
					element.val(value);
				}
			});
		}
	};

	$.fn.json2form = function(method) {
		if ( methods[method] ) {
			return methods[ method ].apply( this, Array.prototype.slice.call( arguments, 1 ));
		} else if ( typeof method === 'object' || ! method ) {
			return methods.init.apply( this, arguments );
		} else {
			$.error( 'Method ' +  method + ' does not exist on jQuery.json2form' );
		}
	};

})(jQuery);