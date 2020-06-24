/**
 *	Custom jQuery Scripts
 *	
 *	Developed by: Lisa DeBona
 */

jQuery(document).ready(function ($) {
	
	var swiper = new Swiper('#slideshow', {
		effect: 'fade', /* "fade", "cube", "coverflow" or "flip" */
		loop: true,
		noSwiping: false,
		simulateTouch : false,
		speed: 1000,
		autoplay: {
			delay: 4000,
		}
    });

    /* Smooth Scroll */
    $('a[href*="#"]')
	  .not('[href="#"]')
	  .not('[href="#0"]')
	  .click(function(event) {
	    // On-page links
	    if (
	      location.pathname.replace(/^\//, '') == this.pathname.replace(/^\//, '') 
	      && 
	      location.hostname == this.hostname
	    ) {
	      // Figure out element to scroll to
	      var target = $(this.hash);
	      target = target.length ? target : $('[name=' + this.hash.slice(1) + ']');
	      // Does a scroll target exist?
	      if (target.length) {
	        // Only prevent default if animation is actually gonna happen
	        event.preventDefault();
	        $('html, body').animate({
	          scrollTop: target.offset().top
	        }, 1000, function() {
	          // Callback after animation
	          // Must change focus!
	          var $target = $(target);
	          $target.focus();
	          if ($target.is(":focus")) { // Checking if the target was focused
	            return false;
	          } else {
	            $target.attr('tabindex','-1'); // Adding tabindex for elements not focusable
	            $target.focus(); // Set focus again
	          };
	        });
	      }
	    }
	});
	

	/*
	*
	*	Wow Animation
	*
	------------------------------------*/
	new WOW().init();


	$(document).on("click","#toggleMenu",function(){
		$(this).toggleClass('open');
		$('body').toggleClass('open-mobile-menu');
	});

	$(document).on("click",".sermonBtn",function(e){
		e.preventDefault();
		var txt = $(this).text();
		if(txt=='Add notes') {
			$(this).text('Hide notes');
		} else {
			$(this).html('<i class="fas fa-edit"></i>Add notes');
		}
		$(this).toggleClass('hide-note');
		$(this).parents(".sermon-note-wrap").toggleClass("open");
		$(this).parents(".sermon-note-wrap").find("textarea").focus();
	});


	var noteVal = $("#notefield").val();
	// var count = (noteVal.match(/{%AddNoteButton%}/g) || []).length;
	// var addNoteButtons = $(noteVal.match(/{%AddNoteButton%}/g);


	$(".notes-input").each(function(k){
		var x = k+1;
		var idName = 'sermonAnswerInput' + x;
		$(this).attr("id",idName);
		//Cookies.set(idName,'');
		var field = $(this).clone();
		var i = k+1;
		$(this).attr("data-index",k);
		$(field).appendTo(".notesContainer");
	});

	var dateStr = get_current_date();
	var n=1; $(".notes-input").on('keyup focusout',function(e){
		var id = $(this).attr("id");
		var index = $(this).attr("data-index");
		var str = $(this).val().trim();
		var str_clean = str.replace(/\s/g,'').trim();
		var txtVal = (str_clean) ? str : '';
		$(".notesContainer .notes-input").eq(index).val(txtVal);
		var cookieField = id + "_" + dateStr;
		Cookies.set(cookieField,str);
		n++;
	});

	// var allNotes = [];
	$("#downloadNotes").click(function(e){
		e.preventDefault();
		$("input#action_type").val("download");
		$("input#userEmail").val("");
		setTimeout(function(){
			$("#notesForm").submit();
		},100);
	});

	/* Email Action */
	$("input#emailTo").on('keyup focusout',function(e){
		var index = $(this).attr("data-index");
		var str = $(this).val();
		var str_clean = str.replace(/\s/g,'').trim();
		var txtVal = (str_clean) ? str : '';
		$("input#userEmail").val(str_clean);
	});

	// $("#emailBtn").click(function(e){
	// 	e.preventDefault();
	// 	$("input#action_type").val("email");
	// 	$("input#emailTo").focus();
	// });


	$( "#emailNotesFrm" ).on('shown.bs.modal', function(){
	    $("input#action_type").val("email");
	    $("input#emailTo").focus();
	});

	$(document).on("click","#emailNotes",function(e){
		e.preventDefault();
		var email1 = $("input#emailTo").val();
		var email2 = $("input#userEmail").val();
		var message = '';
		if(email1 && email2) {
			if( isValidEmailAddress(email1) ) {
				$("#notesForm").submit();
			} else {
				message = '<div class="msg">Please enter a valid email address.</div>';
				
			}
		} else {
			message = '<div class="msg">Please enter a valid email address.</div>';
		}
		$("#respond").html(message);
	});

	$(document).on("click",".closeModal",function(e){
		e.preventDefault();
		// $("#messageModal").removeClass('show');
		// $("#messageBg").removeClass('show');
		$("#messageModal").remove();
		$("#messageBg").remove();
	});

	$(document).on("click","#resetBtn",function(e){
		e.preventDefault();
		$(".notes-input").each(function(k){
			var x = k+1;
			var fn = 'sermonAnswerInput'+x+"_"+dateStr;
			var x = k+1;
			Cookies.remove(fn,'');
			$(this).val("");
			if( $(this).next(".mobileField").length > 0 ) {
				$(this).next(".mobileField").text("");
				$(this).next(".mobileField").removeClass("auto-width");
			}
		});
	});

	/* Set Cookie reference: 
	https://medium.com/@manivannan_data/create-update-and-delete-cookies-using-jquery-5235b110d384 */
	
	/* Read cookie */
	//Cookies.get('closehomepopup');

	/* Delete Cookies 
	Uncomment these function to display Homepage pop-up
	*/
	//Cookies.remove('closehomepopup');
	//Cookies.remove('lastviewed');

	
	/* Get cookies */
	var notesInputCount = $(".sermon-posts .notes-input").length;
	Cookies.set('sermonInputsCount',notesInputCount);
	var dateTodayInput = $("input#dateToday").val();
	var is_same_day = false;
	if( Cookies.get('sermonsaveddate')!='undefined' ) {
		Cookies.set('sermonsaveddate',dateStr);
	} 
	if( Cookies.get('sermonInputsCount') > 0 ) {
		var saved_date = Cookies.get('sermonsaveddate');
		var notesCount = Cookies.get('sermonInputsCount');
		var i;
		for (i = 1; i <= notesCount; i++) {
			var fn = 'sermonAnswerInput'+i+"_"+dateStr;
		  	var inputVal = ( typeof Cookies.get(fn) !='undefined' ) ? Cookies.get(fn) : '';
		  	$("#sermonAnswerInput"+i).val(inputVal);
		  	$("#notesForm #sermonAnswerInput"+i).val(inputVal);
		}

	}


	/* Input fields on mobile version */
	input_fields_mobile_version();

	function input_fields_mobile_version() {
		var screenWidth = $(window).width();
		if( $("input.notes-input").length > 0 ) {
			$("input.notes-input").each(function(){
				var spanMobile;
				var target = $(this);
				var id = $(this).attr("id");
				var cookieField = id + "_" + dateStr;
				var cookiesVal = ( typeof Cookies.get(cookieField)!='undefined' ) ? Cookies.get(cookieField):'';
				if(cookiesVal) {
					spanMobile = '<span data-rel="#'+id+'" class="mobileField auto-width" style="display:inline-block">'+cookiesVal+'</span>';
				} else {
					spanMobile = '<span data-rel="#'+id+'" class="mobileField" style="display:none">&nbsp;</span>';
				}
				$(spanMobile).insertAfter(target);
			});
			$(document).on("click","span.mobileField",function(){
				var inputField = $(this).attr('data-rel');
				var inputVal = $(this).text();
				$(this).addClass('keying');
				$("input.ansTxtbox").attr("data-mapinput",inputField);
				$("#modalInputField").addClass("open");
				if(inputVal) {
					$("#modalInputField input.ansTxtbox").val(inputVal);
				}
				$("#modalInputField input.ansTxtbox").focus();
				// var inputField = $(this).before();
				// console.log(inputField).addClass("test");
				
			});

			$(document).on("click","#cancelInputBtn",function(e){
				e.preventDefault();
				$("#modalInputField").removeClass("open");
				$("#modalInputField input.ansTxtbox").val("");
				$("#modalInputField input.ansTxtbox").attr("data-mapinput","");
				$("span.mobileField.keying").removeClass("auto-width");
				$("span.mobileField.keying").text("");
			});

			$(document).on("click","#saveInputBtn",function(e){
				e.preventDefault();
				var inputField = $("input.ansTxtbox").attr("data-mapinput");
				var inputVal = $("input.ansTxtbox").val();
				$("input"+inputField).val(inputVal);
				var id = inputField.replace("#","");
				var cookieField = id + "_" + dateStr;
				Cookies.set(cookieField,inputVal);

				$("#modalInputField").removeClass("open");
				$("#modalInputField input.ansTxtbox").val("");
				$("#modalInputField input.ansTxtbox").attr("data-mapinput","");
			});

			$(document).on("keyup focusout","#modalInputField input.ansTxtbox",function(){
				var inputFieldSelector = $(this).attr("data-mapinput");
				var str = $(this).val().replace(/\s/g,' ').trim();
				var str_clean = str.replace(/\s/g,'').trim();
				var inputVal = (str_clean) ? str : '';
				if( $('span.mobileField[data-rel="'+inputFieldSelector+'"]').length > 0 ) {
					$('span.mobileField[data-rel="'+inputFieldSelector+'"]').text(inputVal);
					$('span.mobileField[data-rel="'+inputFieldSelector+'"]').addClass("auto-width");
				}
				$("input#sermonAnswerInput1").val(inputVal);
			});

		}
	}


	function get_current_date() {
		var d = new Date();
		var mo = d.getMonth() + 1;
		var month = (mo.toString().length < 2 ? "0"+mo.toString() : mo);
		var day = (d.getDate().toString().length < 2 ? "0"+d.getDate().toString() :d.getDate());
		var year = d.getFullYear();
		//var dateNow = year+"/"+month+"/"+day;
		var dateNow = month+day+year;
		return dateNow;
	}

	function isValidEmailAddress(emailAddress) {
	    var pattern = new RegExp(/^(("[\w-\s]+")|([\w-]+(?:\.[\w-]+)*)|("[\w-\s]+")([\w-]+(?:\.[\w-]+)*))(@((?:[\w-]+\.)*\w[\w-]{0,66})\.([a-z]{2,6}(?:\.[a-z]{2})?)$)|(@\[?((25[0-5]\.|2[0-4][0-9]\.|1[0-9]{2}\.|[0-9]{1,2}\.))((25[0-5]|2[0-4][0-9]|1[0-9]{2}|[0-9]{1,2})\.){2}(25[0-5]|2[0-4][0-9]|1[0-9]{2}|[0-9]{1,2})\]?$)/i);
	    return pattern.test(emailAddress);
	}



});// END #####################################    END