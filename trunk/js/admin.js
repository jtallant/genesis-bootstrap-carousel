/*global jQuery, document */
(function($) {

var postType           = $('.post-type-select'),
	showTitle          = $('.show-title'),
	showExcerpt        = $('.show-excerpt'),
	captionOptions     = $('.carousel-caption-options'),
	excerptOptions     = $('.excerpt-options'),
	taxonomyOptions    = $('#genesis-bootstrap-carousel-taxonomy');

function toggle_taxonomy_exclude_options() {
	var type = postType.find('option:selected').val();

	if ( 'page' == type ) {
		taxonomyOptions.hide('slow');
	} else {
		taxonomyOptions.show('slow');
	}
}

function toggle_excerpt_options() {

	if ( showExcerpt.is(':checked') ) {
		excerptOptions.show('slow');
	} else {
		excerptOptions.hide('slow');
	}
}

function toggle_caption_options() {

	if ( showExcerpt.is(':checked') || showTitle.is(':checked') ) {
		captionOptions.show('slow');
	} else {
		captionOptions.hide('slow');
	}
}

$(document).ready(function($) {

	toggle_taxonomy_exclude_options();
	toggle_caption_options();
	toggle_excerpt_options();

	showExcerpt.change(function () {
		toggle_excerpt_options();
		toggle_caption_options();
	});
	
	showTitle.change(function () {
		toggle_caption_options();
	});

	postType.change(function () {
		toggle_taxonomy_exclude_options();
	});
});


})(jQuery);