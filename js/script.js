
jQuery(document).ready(function($){
	
$('button.primary').bind('click',function(){
	var id= $(this).attr('id');
	var parent=$(this).parent();
	var title= '';
	
	$('.'+id).each(function(){
		title=$(this).html();		
		});
			
		$.ajax(
		{
			type:"post",
			url:addVideoSettings.ajaxurl,
		timeout:5000,
		data:{
			 'action':'myajax-submit',
			 'id':id,
			 'title':title
			  
			},
			
		success: function(data){
			parent.html('<b><h3>Added in the playlist</h3></b>').hide().fadeIn('slow');
			}
	   }
       
       )
	});
	
$('button.action').bind('click',function(){
	var id= $(this).attr('id');
	var self= $(this);
	
	var title= '';
	

			
		$.ajax(
		{
			type:"post",
			url:addVideoSettings.ajaxurl,
		timeout:5000,
		data:{
			 'action':'ajax_toggle',
			 'id':id			  
			},
			
		success: function(data){
			if(self.text()=="Suspend"){
			self.text("Add").hide().fadeIn('slow');		
			self.parent().prev().text("Suspended").hide().fadeIn('slow');
		    }
		    
		    else if(self.text()=="Add"){
				self.text("Suspend").hide().fadeIn('slow');
				self.parent().prev().text("Active").hide().fadeIn('slow');
				
				}
			//parent.html('<b><h3>Added in the playlist</h3></b>').hide().fadeIn('slow');
			}
	   }
       
       )
	});
	
$('button.remove').bind('click',function(){
	var id= $(this).parent().prev().children('button').attr('id');
	var self= $(this);
	
	var title= '';
	$.ajax(
		{
			type:"post",
			url:addVideoSettings.ajaxurl,
		    timeout:5000,
		    data:{
			 'action':'ajax_remove',
			 'id':id
			  
			},
			
		success: function(data){
			if(data==1){
				self.parent().parent().fadeOut('slow');
				
				
				
				}
			}
	   }
       
      )//end of ajax
	
		
});

$('button#show-next').bind('click', function(){
	var pagenum=$(this).attr('class');
	var self=$(this);
	pagenum= Number(pagenum);
		$.ajax(
		{
			type:"post",
			url:addVideoSettings.ajaxurl,
		    timeout:10000,
		    data:{
			 'action':'show_next',
			 'pagenum': pagenum
			  
			},
			
		success: function(data){
			
			var img = addVideoSettings.pluginurl+'/images/left.png';
			img = '<button id="show-prev"><img src="'+img+'"/>';
			
			if($('#show-prev')== false)
			self.parent().prepend(img);
		
			$('#videoContents').html(data).fadeOut('slow').fadeIn('slow');			
				self.attr('class',++pagenum);
				
				//rebind
	$('button.primary').bind('click',function(){	
	var id= $(this).attr('id');
	var parent=$(this).parent();
	var title= '';
	
	$('.'+id).each(function(){
		title=$(this).html();		
		});
			
		$.ajax(
		{
			type:"post",
			url:addVideoSettings.ajaxurl,
		timeout:5000,
		data:{
			 'action':'myajax-submit',
			 'id':id,
			 'title':title
			  
			},
			
		success: function(data){
			parent.html('<b><h3>Added in the playlist</h3></b>').hide().fadeIn('slow');
			}
	   }
       
       )
	});
				
				
		//end of rebind
			}
	   }
       
      )
	
	
	});
	
	
	});
