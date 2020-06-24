/**	
 *	Developed by: Lisa DeBona
 */
jQuery(document).ready(function ($) {
	
	var entries = [];
	
	// $("#extractData").click(function(e){
	// 	e.preventDefault();
	// 	var button = $(this);
	// 	var post_type = $(this).attr("data-posttype");
	// 	if( $(entries).length > 0 ) {
	// 		$.ajax({
	// 			url : myAjax.ajaxurl,
	// 			type : 'post',
	// 			dataType : "json",
	// 			data : {
	// 				action : 'extract_data_from_website',
	// 				posttype : post_type,
	// 				objects : entries
	// 			},
	// 			beforeSend:function(){
	// 				$("#wait").show();
	// 				button.hide();
	// 			},
	// 			success : function( obj ) {
	// 				if(obj.message) {
	// 					$("#response").html(obj.message);
	// 					$("#wait").hide();
	// 				}
	// 			},
	// 			error: function (xhr, status, error) {
	// 				$("#wait").hide();
	// 				var error = '<div>'+xhr+'</div>';
	// 				error += '<div>'+status+'</div>';
	// 				error += '<div>'+error+'</div>';
	// 				$("#errors").html(error);
	// 		    }
	// 		});
	// 	}
	// });

	

	// $("#downloadNotes").click(function(e){
	// 	e.preventDefault();
	// 	var id = $(this).attr("data-id");
	// 	var notesArrs = [];
	// 	$("textarea.notes").each(function(){
	// 		var str = $(this).val();
	// 		var str_clean = str.replace(/\s/g,'');
	// 		var text = (str_clean) ? str : '';
	// 		notesArrs.push(text);
	// 	});
	// 	$.ajax({
	// 		url : myAjax.ajaxurl,
	// 		type : 'post',
	// 		dataType : "json",
	// 		data : {
	// 			action : 'extract_page_content',
	// 			postid : id,
	// 			notes : notesArrs
	// 		},
	// 		beforeSend:function(){
	// 			// $("#wait").show();
	// 			// button.hide();
	// 		},
	// 		success : function( obj ) {
	// 			console.log(obj);
	// 			// if(obj.message) {
	// 			// 	$("#response").html(obj.message);
	// 			// 	$("#wait").hide();
	// 			// }
	// 		},
	// 		error: function (xhr, status, error) {
	// 			// $("#wait").hide();
	// 			// var error = '<div>'+xhr+'</div>';
	// 			// error += '<div>'+status+'</div>';
	// 			// error += '<div>'+error+'</div>';
	// 			// $("#errors").html(error);
	// 	    }
	// 	});
	//  });
	

});