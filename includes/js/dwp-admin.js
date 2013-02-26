jQuery(document).ready(function() {  
    
	// Logo upload for login box
	jQuery('#upload_logo_button').click(function() {  
        tb_show('Upload a logo', 'media-upload.php?referer=docebo-login&type=image&TB_iframe=true&post_id=0', false);  
        return false;  
    });  
	
	window.send_to_editor = function(html) {  
		var image_url = jQuery('img',html).attr('src');  
		jQuery('#logo_url').val(image_url);  
		tb_remove();  
	};
	
});