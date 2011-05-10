
jQuery(document).ready(function($){
	
	$.fn.wpAddVideo = function(){	
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
	};
	
$('button.primary').bind('click',$.fn.wpAddVideo);
	
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

$('input#show-next').bind('click', function(){
	
	
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
			 'pagenum': ++pagenum
			  
			},
			
		success: function(data){
			
			var limita=(pagenum-1)*10+1;
			var message = 'Current Page: '+pagenum+'(Showing videos '+limita+'-' +(limita+9)+')';
		    $('#videoNumMessage h4').html(message);
			
			$('#show-prev').css('display','inline');
			
			$('#videoContents').fadeOut('slow').html(data).fadeIn('slow');			
				self.attr('class',pagenum);
								
				$('#show-prev').attr('class',--pagenum);
				
				var max = Number($('#max-page').val());
				if(self.attr('class')==max)
				self.hide();
				
				//rebind
				$('button.primary').bind('click',$.fn.wpAddVideo);				
		       //end of rebind
			}
	   }
       
      )
	
	
	});
	
	
//show-prev button action

$('input#show-prev').bind('click', function(){
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
			var limita=(pagenum-1)*10+1;
			var message = 'Current Page: '+pagenum+'(Showing videos '+limita+'-' +(limita+9)+')';
		    $('#videoNumMessage h4').html(message);
		
			
			$('#show-prev').css('display','inline');
			
			$('#videoContents').fadeOut('slow').html(data).fadeIn('slow');			
				self.attr('class', --pagenum);
								
				$('#show-next').attr('class',++pagenum);
				
				if(Number(self.attr('class')) == 0)self.hide();
				
				//rebind
               $('button.primary').bind('click',$.fn.wpAddVideo);				
		        //end of rebind
			}
	   }
       
      )
	
	
	});	
	//end of show-prev
	
	
	//
	$('input#areaSubmit').bind('click',function(){
	var title= $('#directTitle').val();
	if(title == 'Enter Title Here' || title == '') {
		alert('Add a title for the video');
		return false;
		}
	var url = $('#areaId').val();
	
	if(url =='') {
		alert('Add value in the url textarea');
		return false;
		
		}
	
	var self= $(this);
	
	
	

			
		$.ajax(
		{
			type:"post",
			url:addVideoSettings.ajaxurl,
		    timeout:5000,
		    data:{
			 'action':'ajax_getId',
			 'url':url,
			 'title':title			  
			},
			
		    success: function(data){
							
				$('#areaMessage').css('display','inline').html('Video has been added successfully').fadeIn('slow');
		
			}
	   }
       
       )
	});
	
	//
	
	});
