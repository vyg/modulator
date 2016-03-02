(function($) {

	$.entwine('ss.preview', function($) {

		$('.cms-preview').entwine({

			_loadCurrentPage: function() {

				// If we're on a page module data object, prevent the preview from redirecting to the parent page's edit screen
				if($('.cms-pagemodule').length)
					return;

				this._super();
			},

		});

	});
}(jQuery));
