jQuery(document).ready(function($){
	if ( $('#essential-seo-document-title').length > 0 ) {
		jQuery("#essential-seo-document-title").after( essential_seo_vars.title );
		jQuery("#counter-title").val(jQuery("#essential-seo-document-title").val().length);
		jQuery("#essential-seo-document-title").keyup( function() {
			jQuery("#counter-title").val(jQuery("#essential-seo-document-title").val().length);
		});
	}
	if ( $('#essential-seo-meta-description').length > 0 ) {
		jQuery("#essential-seo-meta-description").after( essential_seo_vars.description );
		jQuery("#counter-desc").val(jQuery("#essential-seo-meta-description").val().length);
		jQuery("#essential-seo-meta-description").keyup( function() {
			jQuery("#counter-desc").val(jQuery("#essential-seo-meta-description").val().length);
		});
	}
});