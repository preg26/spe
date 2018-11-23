$(document).ready(function(){
	$('div.sous-compte').click(function(){
		var id = $(this).attr('reel');
		$('.payment').slideUp();
		$('.compte'+id).slideDown();
	});
});