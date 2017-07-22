(function() {
    tinymce.create('tinymce.plugins.code', {
        init : function(ed, url) {

            ed.addButton('codebutton', {
                title : 'Code',
                cmd : 'codebutton',
                image :  url + '/acf.png'
            });

            ed.addCommand('codebutton', function() {
                var return_text = '';
                return_text = '[ACF]';
                ed.execCommand('mceInsertContent', 0, return_text);
            });
        },
        // ... Hidden code
    });
    // Register plugin
    tinymce.PluginManager.add( 'mycodebutton', tinymce.plugins.code );
})(jQuery);

function change_content(id){
	var data = {
			action: 'my_action',
			id: id
		};
		jQuery.post(ajaxurl, data, function(response){
			jQuery("#loadcontent_here").html(response);
		});
}
