jQuery(function(){
	jQuery('#feedback-name').val('');
	jQuery('#feedback-phone').val('');
	jQuery('#feedback-email').val('');
	if (beautiful_feedback_captcha_required) jQuery('#feedback-captcha').val('');
	jQuery('#feedback-message').val('');
	jQuery('#feedback input').removeAttr('disabled', 'disabled').css('opacity', 1);
	jQuery('#feedback textarea').removeAttr('disabled', 'disabled').css('opacity', 1);
	jQuery('#feedback-send').css('background', '#b01').css('color', '#fff').css('opacity', 1);

	jQuery('#feedback-open').click(function(){
		if (jQuery('#feedback').hasClass('feedback-right'))
		{
			jQuery('#feedback').animate({
				'margin-left':-400,
				'width':400
			}, 500, 'swing');
		}
		else
		{
			jQuery('#feedback').animate({
				'margin-left':0
			}, 500, 'swing');
		}
		jQuery('#feedback-open').hide();
		jQuery('#feedback-close').show();
	});

	jQuery('#feedback-close').click(function(){
		if (jQuery('#feedback').hasClass('feedback-right'))
		{
			jQuery('#feedback').animate({
				'margin-left':-40,
				'width':40
			}, 500, 'swing');
		}
		else
		{
			jQuery('#feedback').animate({
				'margin-left':-360
			}, 500, 'swing');
		}
		jQuery('#feedback-open').show();
		jQuery('#feedback-close').hide();
	});
	
	jQuery('#feedback-send').click(function(){
		jQuery('#feedback input').attr('disabled', 'disabled').css('opacity', 0.7);
		jQuery('#feedback textarea').attr('disabled', 'disabled').css('opacity', 0.7);
		jQuery('#feedback-send').css('background', '#999').css('color', '#ccc').css('opacity', 0.7);
		var fdata = {
			name:jQuery('#feedback-name').val(),
			phone:jQuery('#feedback-phone').val(),
			email:jQuery('#feedback-email').val(),
			message:jQuery('#feedback-message').val()
		}
		if (beautiful_feedback_captcha_required) fdata['captcha'] = jQuery('#feedback-captcha').val();
		fdata[jQuery("#feedback-send").attr('name')] = jQuery('#feedback-send').val();
		jQuery.post(jQuery('#feedback-form').attr('action'), fdata, function(data){
			if (data == beautiful_feedback_success_text)
			{
				jQuery('#feedback-form').remove();
				jQuery('#feedback').append('<div id="feedback-result">'+data+'</div>')
				
				jQuery('#feedback-name').val('');
				jQuery('#feedback-phone').val('');
				jQuery('#feedback-email').val('');
				if (beautiful_feedback_captcha_required) jQuery('#feedback-captcha').val('');
				jQuery('#feedback-message').val('');
			}
			else alert(data);
			
			jQuery('#feedback input').removeAttr('disabled', 'disabled').css('opacity', 1);
			jQuery('#feedback textarea').removeAttr('disabled', 'disabled').css('opacity', 1);
			jQuery('#feedback-send').css('background', '#b01').css('color', '#fff').css('opacity', 1);
		});
	});
});