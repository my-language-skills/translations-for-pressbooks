jQuery(document).ready(function () {
	jQuery("input.is-original").on("change",function(){
		var id=jQuery(this).parent("td").siblings("th").children("input").val();
		var val=jQuery(this).prop("checked");
		jQuery.ajax(
			{url:ajaxurl,
			 type:"POST",
			 data:{
			 	action:"efp_mark_as_original",
			 	book_id:id,
			 	is_original:val,
			 	_ajax_nonce:PB_Aldine_Admin.aldineAdminNonce
			 },
			 success:function(){
			 	if (val == 1) {
			 		alert('Book marked as original content!');
			    } else {
			    	alert('Book unmarked as original content!');
			    }
			 }
		    }
		);
	});
});
