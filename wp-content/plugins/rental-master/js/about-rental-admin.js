/**
 * File Name: about-rental-admin.js
 * *
 * Description: Holds the functions related to plugin is used for both Administrator as well Frontend
 */
var $=jQuery;
/**
*	Community Map help
*/
var map_help=function(){
	$(document).on('click','#help_map',function(){
		swal("Please click on following text  ", "<br><a href='https://support.google.com/maps/answer/144361' target='_blank'>https://support.google.com/maps/answer/144361</a>");
	});
}
map_help();

/**
*	community_APPARTMENTS_POPUP_AJAX
*	Description: Sends ajax request on click View button
*/
var community_APPARTMENTS_POPUP_AJAX=function(){
	$(document).on('click','.cfwpTHICK',function(){
		var cid	=	$(this).attr('cid');
		var cid= $(this).attr('cid');
		swal({ title:'Please Wait...', text:'Processing..',type:'info',showCancelButton: false, showConfirmButton: false});
		swal.enableLoading();
		$.ajax({
			type:'POST',url:ajaxurl,
			data:{'action':'get_appartments_by_post_meta_ajax','cid':cid},
			success:function(data){
				if(data){
					swal({  html:true,title:'<h2>Apartments</h2>',html:data});
				}
				else{swal('Oops!','','error');}
			}
		})
	});
}
community_APPARTMENTS_POPUP_AJAX();

/**----------------------	FRONT END ----------------------**/
/**
*	APPARTMENT_cf_add_to_fav
*
*/
APPARTMENT_cf_add_to_fav = function(pid){
	$(document).on('click','.apFVT',function(){
		var pid	=	$(this).attr('id');
		var st	=	$(this).attr('status');
		add_to_FAVOURITE_AJAX(pid,st);
	});
}
APPARTMENT_cf_add_to_fav();

/**
*	COMMUNITY_cf_add_to_fav
*
*/
COMMUNITY_cf_add_to_fav = function(pid){
	$(document).on('click','.cmFVT',function(){
		var pid	=	$(this).attr('id');
		var st	=	$(this).attr('status');
		add_to_FAVOURITE_AJAX(pid,st);
	});
}
COMMUNITY_cf_add_to_fav();

/**
*	add_to_FAVOURITE_AJAX
*
*/
var add_to_FAVOURITE_AJAX=function(pid,st){
	$.ajax({
			type:"POST",
			url:wp_ajax_url(),
			data:{"pid":pid,"action":"add_to_fav_ajax","status":st},
			success:function(data){
				var res	=	$.parseJSON(data);
				if(res.response == 1){
					$("#"+pid).text("Remove From Favorites").attr("status",0);
				}
				else if(res.response == 2){
					$("#"+pid).text("Remove From Favorites").attr("status",0);
				}
				else{
					$("#"+pid).text("Add To Favorites").attr("status",1);
				}
			}
		});
}

/**
*	cf_print_page
*	Description: Print whole page
*/
function cf_print_page() {
    window.print();
}
/**
*	view_MAP 
*
*/
var view_MAP=function(){
	$(document).on('click','.viewMap',function(){
		var title	=	$('#community_name').val();
		var map	=	$('#community_map').val();
		var mapHTML='<h3>'+title+'</h3><br><div style="max-width: 475px;overflow: hidden;" class="mapPopup">'+map+'</div>';
		swal({
		html:true,
		title:'',
		text:'<style>.sweet-alert input{display:none !important;}</style>'+mapHTML
		});
	});
}
view_MAP();

var abr_update_application_form_AJAX=function(){
	$(document).on('click','.SaveUrl',function(){
		var value	=	$('#applicationFormInput').val();
		if(value==''){
			$('#applicationFormInput').focus();
			return false;
		}
		else{
			swal.enableLoading();
			$.ajax({
				type:"POST",
				url:wp_ajax_url(),
				data:{"value":value,"action":"abr_update_application_form_url"},
				success:function(data){
					console.log(data);
					var res	=	$.parseJSON(data);
					if(res.response == 1){
						$('#applicationFormInput').val(res.value);
						swal({  html:true,title:'<h2>Success</h2>',html:''});
					}
				}
			});
		}
	});
}

abr_update_application_form_AJAX();


var abr_update_search_settings_AJAX=function(){
	$(document).on('click','#paginationSettings',function(){
		var value	=	$('#paginationNumber').val();
		if(value==''){
			$('#paginationNumber').focus();
			return false;
		}
		else{
			swal.enableLoading();
			$.ajax({
				type:"POST",
				url:wp_ajax_url(),
				data:{"value":value,"action":"abr_update_search_settings"},
				success:function(data){
					console.log(data);
					var res	=	$.parseJSON(data);
					if(res.response == 1){
						$('#paginationNumber').val(res.value);
						swal({  html:true,title:'<h2>Success</h2>',html:''});
					}
				}
			});
		}
	});
}

abr_update_search_settings_AJAX();


var abr_Select_Deselect=function(){
	$(document).on('click', '.checkAll', function() {
		$(this).parent('li').find('select').addClass('atr');
		
		if ($(this).val() == 'Select All') {			
			$(this).parent('li').find('.atr option').attr("selected","selected");
			$(this).val('Deselect All');
		}
		else {
			$(this).parent('li').find('.atr option').removeAttr("selected");
			$(this).val('Select All');
		}
	  });
	}
abr_Select_Deselect();

jQuery(document).ready(function(e){(function($){
	
	
	function validateEmail(email) {
		var emailReg = /^([\w-\.]+@([\w-]+\.)+[\w-]{2,4})?$/;
		return emailReg.test(email );
	}
	
	function submitappform(){
		var fname   = $("#First_Name").val();
		var lname   = $("#Last_Name").val();
		var dob     = $("#Dob").val();
		var caddres  = $("#Current_Address").val();
		var chomphon	= $("#Current_Home_Phone").val();
		var cworpho	= $("#Current_Work_Phone").val();
		var email	= $("#Email").val();
		var rname	= $("#Roommates_Names").val();
		var rphone   = $("#Roommates_Phone").val();
		var remail	= $("#Roommate_Email").val();
		var laplifor	= $("#Location_Applying_For").val();
		var comm	= $("#communities").val();
		var montbegn	= $("#Month_To_Begin").val();
		var bedromned= $("#Bedrooms_Needed").val();
		var paddres	= $("#Permanent_Address").val();
		var pphone	= $("#Permanent_Phone").val();
		if(fname==''){$("#First_Name").focus();return false;}
		if(lname==''){$("#Last_Name").focus();return false;}
		if(dob==''){$("#Dob").focus();return false;}
		if(caddres==''){$("#Current_Address").focus();return false;}
		if(chomphon==''){$("#Current_Home_Phone").focus();return false;}
		if((email=='')||(!validateEmail(email))){$("#Email").focus();return false;}
		if(laplifor==''){$("#Location_Applying_For").focus();return false;}
		if(montbegn==''){$("#Month_To_Begin").focus();return false;}
		if(bedromned==''){$("#Bedrooms_Needed").focus();return false;}
		if(paddres==''){$("#Permanent_Address").focus();return false;}
		if(pphone==''){$("#Permanent_Phone").focus();return false;}
		swal({ title:'Please Wait...', text:'Processing..',type:'info',showCancelButton: false, showConfirmButton: false});
		$.ajax({
			type:"POST",url:wp_ajax_url(),
			data:{"fname":fname,"lname":lname,"dob":dob,"caddres":caddres,"chomphon":chomphon,"cworpho":cworpho,"email":email,"rname":rname,"rphone":rphone,"remail":remail,"laplifor":laplifor,"comm":comm,"montbegn":montbegn,"bedromned":bedromned,"paddres":paddres,"pphone":pphone,action:"application_form_ajax","act":"applynow"},
			success:function(data){
				var res	=	$.parseJSON(data);
				if(res.response == 1){
					$(".frmINPT").val("");
					swal(res.message,'','success');
				}
				else{
					swal('Mail not sent!','','error');
				}
			}
		});
	}

	$(document).on('click','#SubmitApplication',function(){
		submitappform();
		return false;
	});

/**	Favourite APARTMENT Email to friends	**/
	function submitfavemailform(){
		var remail1	=	$("#remail").val();
		var remail2	=	$("#remail2").val();
		var remail3	=	$("#remail3").val();
		var remail4	=	$("#remail4").val();
		var remail5	=	$("#remail5").val();
		var semail	=	$("#semail").val();
		var sname   =	$("#sname").val();
		var email_subject = $("#email_subject").val();
		var message  = $("#message").val();
		if((remail1=='')||(!validateEmail(remail1))){$("#remail").focus();return false;}
		if(!validateEmail(remail2)){$("#remail2").focus();return false;}
		if(!validateEmail(remail3)){$("#remail3").focus();return false;}
		if(!validateEmail(remail4)){$("#remail4").focus();return false;}
		if((semail=='')||(!validateEmail(semail))){$("#semail").focus();return false;	}
		if(sname==''){$("#sname").focus();return false;}
		if(email_subject==''){$("#email_subject").focus();return false;}
		if(message==''){	$("#message").focus();return false;}
		else{
			swal({ title:'Please Wait...', text:'Processing..',type:'info',showCancelButton: false, showConfirmButton: false});
			$.ajax({
				type:"POST",url:wp_ajax_url(),
				data:{"remail1":remail1,"remail2":remail2,"remail3":remail3,"remail4":remail4,"remail5":remail5,"semail":semail,"sname":sname,"email_subject":email_subject,"message":message,action:"application_form_ajax","act":"favemail"},
				success:function(data){
					console.log('data:',data);
					var res	=	$.parseJSON(data);
					if(res.response == 1){
						$(".contacttxt").val("");
						swal(res.message,'','success');
					}
					else{
						swal('Mail not sent!','','error');
					}
				}
			});
		}
	}
	
	$(document).on('click','.emailFriends',function(){
		submitfavemailform();
		return false;
	});

}(jQuery))});