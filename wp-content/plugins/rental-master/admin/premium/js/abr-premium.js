/**
 * File Name: abr-premium.js
 * *
 * Description: Holds the functions related to plugin activation and deactivation
 */
var $=jQuery;

/**
 ** Function: Activates Premium Version
 * 
 ** Description: Get the ajax function response 0 or 1
 **
 */
var activate_PREMIUM_version=function(){
	$(document).on('click','#get-premium',function(){
		var key		=	$('#licence-key').val();
		var domain	=	document.location.host;
		console.log('Domain: ',domain);
		if(key==''){	
			swal('','Please Enter Your Licence Key','info');
			return false;
		}
		swal('Connecting...','Verifying Credentials..','info');
		swal.enableLoading();
		$.ajax({
			type:'POST',url:ajaxurl ,
			data:{'action':'abr_version_activation_ajax','act':'premium-version','key':key,'domain':domain},
			success:function(data){
				console.log(data);
				var res	=	$.parseJSON(data);
				if(res.response == 1){ // if key matches
					console.log(res.message);
					window.location.reload();
				}
				else{
					swal('',res.message,'error');
					}
			}
		})
	});
}
activate_PREMIUM_version();

/**
 ** Function: Activates Trail Version
 *
 ** Description: Get the ajax function response 0 or 1
 **
 */
var activate_TRIAL_version=function(){
	$(document).on('click','#trial-version',function(){
		$.ajax({
			type:'POST',url:ajaxurl,
			data:{'action':'abr_version_activation_ajax','act':'trial-version'},
			success:function(data){
				console.log(data);
				var res	=	$.parseJSON(data);
				if(res.response == 1){
					console.log(res.message);
					swal.enableLoading();
					window.location.reload();
				}
				else{
					swal('',res.message,'error');
				}
			}
		})
	});
}
activate_TRIAL_version();

/**
 ** Function: abr_updated_FEATURED
 *
 ** Description: (Checkbox) on change ajax request
 **
 */
var abr_update_FEATURED=function(){
	$(document).on('change','#featurediS',function(){
		var id	=	$(this).attr('pid');
		if ($(this).prop("checked")) {
			var is_featured	=	'on';
		}
		else{
			var is_featured	=	'';
		}
		swal('Updating...','Please Wait..','info');
		swal.enableLoading();
		$.ajax({
			type:'POST',url:ajaxurl,
			data:{'action':'abr_update_apartment_featured','id':id,'is_featured':is_featured},
			success:function(data){
				console.log(data);
				var res	=	$.parseJSON(data);
				if(res.response == 1){
					swal('',res.message,'success');
				}
				else{
					swal('',res.message,'error');
				}
			}
		});
	});
}
abr_update_FEATURED();

/*%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%		Availability Manager		%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%*/


/**
 ** Function: abr_update_availability_MANAGER
 *
 ** Description: Send the ajax request
 **
 */
 var abr_update_availability_MANAGER=function(){
	 $(document).on('click','.updateApartment',function(){
		 var id		=	$(this).parent().parent('li').attr('pid');
		 var Rent	=	$(this).parent().parent('li').find('#Rent').val();
		 var Units		=	$(this).parent().parent('li').find('#Units').val();
		 var rentRange	=	$(this).parent().parent('li').find('#rentRange').val();
		 var unitAvail		=	$(this).parent().parent('li').find('#unitAvail').val();
		 var availDate	=	$(this).parent().parent('li').find('#availDate').val();
		 if($(this).parent().parent('li').find('#isfeatured').is(":checked")){
			var  isFeatured	='on';
		 } else{
			 var  isFeatured	='';
		 }
		 swal('Updating...','Please Wait..','info');
		 swal.enableLoading();
		 $.ajax({
			 type:'POST',url:ajaxurl,
			 data:{'action':'update_availability_manager_ajax','id':id,'Rent':Rent,'Units':Units,'rentRange':rentRange,'unitAvail':unitAvail,'availDate':availDate,'isFeatured':isFeatured},
			 success:function(data){
				var res	=	$.parseJSON(data);
				if(res.response == 1){
					console.log(res.message);
					swal('',res.message,'success');
				}
				else{
					swal('',res.message,'error');
				}
			}
		 });
	 });
 }
 abr_update_availability_MANAGER();