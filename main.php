<?php   

/*
Plugin Name: WP-ADD-Video
Plugin URI: http://sabirul-mostofa.blogspot.com
Description: Add video from Youtube At your admin panel
Version: 1.0
Author: Sabirul Mostofa
Author URI: http://sabirul-mostofa.blogspot.com
*/


$wpAddVideo = new wpAddVideo();
if(isset($wpAddVideo)) {
	//add_action('init', array($wpAddVideo,'redirect'), 1);
	add_action('admin_menu', array($wpAddVideo,'CreateMenu'),50);
	if (isset($_POST['addVideoUnique'])) {
	}
		
	
}   
class wpAddVideo{
	
	function __construct(){
		add_action('admin_enqueue_scripts' , array($this,'add_scripts'));	
		add_action( 'wp_ajax_myajax-submit', array($this,'ajax_handle' ));
		add_action( 'wp_ajax_ajax_toggle', array($this,'ajax_toggle' ));
		add_action( 'wp_ajax_ajax_remove', array($this,'ajax_remove' ));
		add_action( 'wp_ajax_show_next', array($this,'ajax_next_page_show'));
		add_action( 'wp_ajax_ajax_getId', array($this,'ajax_process_insert'));
		register_activation_hook(__FILE__, array($this, 'create_table'));
		
		}
		
		function add_scripts(){
		if(preg_match('/wpAddVideo/',$_SERVER['REQUEST_URI']) || preg_match('/wpManageVideo/',$_SERVER['REQUEST_URI'])){
					
			wp_enqueue_script('jquery');
            wp_enqueue_script('add_video_script',plugins_url('/' , __FILE__).'js/script.js');	
            wp_localize_script('add_video_script', 'addVideoSettings',
array(
'ajaxurl'=>admin_url('admin-ajax.php'),
'pluginurl' => plugins_url('/' , __FILE__)

)
);	

  wp_register_style('add_video_css', plugins_url('/' , __FILE__).'css/style.css', false, '1.0.0');
    wp_enqueue_style('add_video_css');
    
 }
	
		
			}
			
		

	function CreateMenu(){
		add_submenu_page('theme-options.php','Add From YouTube','Add From YouTube','activate_plugins','wpAddVideo',array($this,'OptionsPage'));
	    add_submenu_page('theme-options.php','Manage Video','Manage Playlist','activate_plugins','wpManageVideo',array($this,'videoManage'));


	}
	
	function ajax_handle(){
		$id = trim($_REQUEST['id']);
		$title = trim($_REQUEST['title']);
		global $wpdb;
		
		if(!preg_match('/[a-zA-Z0-9]/', $id))exit;
		if($this->exists_in_table($id))exit;
		 $wpdb->insert( 'wp_video_list', 
					 array( 'video_id' => $id,
					        'video_title' => $title							
						),
							 array( '%s', '%s') 
					);
		
		print $id;
		
		exit;
		
		}
		
		
		function ajax_toggle(){
			global $wpdb;	
			
		$id = $_REQUEST['id'];
			
			
			$result = $wpdb -> get_results("SELECT video_stat FROM wp_video_list where video_id='$id'",'ARRAY_N' );
			
			if ($result[0][0] == 1){		
			$wpdb -> update('wp_video_list',
			array(
			'video_stat' => 2
			),
			array(
			'video_id' => $id
			),
			array('%d'),
			array('%s')		
			);
		}
			
			elseif($result[0][0] == '2'){
			$wpdb -> update('wp_video_list',
			array(
			'video_stat' => 1
			),
			array(
			'video_id' => $id
			),
			array('%d'),
			array('%s')		
			);
		}
			//$result = $wpdb -> get_results("SELECT video_stat FROM wp_video_list where  video_id='$id'",'ARRAY_N' );
			
			exit;
			
			}
			
			function ajax_remove(){
				global $wpdb;
				$id = $_REQUEST['id'];
								
				echo $test = $wpdb -> query("delete from wp_video_list where video_id='$id'");
				
				
				exit;
				
				}
				
				
				
				function ajax_next_page_show(){
					
					$num = $_REQUEST['pagenum'];
					if($num <1)$num = 1;					
					
					$this -> OptionsPage($num,1);
					
					exit;
					}
					
					
					
					function ajax_process_insert(){						
						$code = trim($_REQUEST['url']);
						if(!preg_match('/[a-zA-Z0-9]/',$code))exit;
						
						//id only
						if(!preg_match('?/?',$code)){
							echo $id = trim($code,'?');							
						}
						
						//if url
						if(preg_match('?http://www.youtube.com/watch?',$code)){
							
							if(preg_match('/v=+.[^&]+&/',$code,$test))
							echo $id = trim(str_replace('v=','',$test[0]),'&');
							
							}
						
						
						//if shortcode
					if(preg_match('/youtu\.be/',$code,$test)){
							  echo $id = str_replace('http://youtu.be/','',$code);							   
							
							}
							
							//if iframe
					if(preg_match('/<iframe/',$code,$test)){
			
									$dom = new DOMDocument();
									$dom->loadHTML($code);			
									$a= $dom->getElementsByTagName('iframe')->item(0)->getAttribute('src');
									preg_match('$/+.[^/]+\?$',$a,$test);
									echo $id = trim($test[0],'/?');
			       }
					
					//if old object
					
						if(preg_match('/<object/',$code)){								
			                    $dom = new DOMDocument();
			                    @$dom->loadHTML($code);			
			                      $a= $dom->getElementsByTagName('embed')->item(0)->getAttribute('src');
			                     	preg_match('$/+.[^/]+\?$',$a,$test);
			                         $id = trim($test[0],'/?');		
			                         echo $id;
								
							}
							
							$this -> add_single_video($id);
						
						
						//http://youtu.be/wagn8Wrmzuc
						//<iframe width="560" height="349" src="http://www.youtube.com/embed/wagn8Wrmzuc?rel=0" frameborder="0" allowfullscreen></iframe>
						//<object width="560" height="349"><param name="movie" value="http://www.youtube.com/v/wagn8Wrmzuc?fs=1&amp;hl=en_US&amp;rel=0"></param><param name="allowFullScreen" value="true"></param><param name="allowscriptaccess" value="always"></param><embed src="http://www.youtube.com/v/wagn8Wrmzuc?fs=1&amp;hl=en_US&amp;rel=0" type="application/x-shockwave-flash" width="560" height="349" allowscriptaccess="always" allowfullscreen="true"></embed></object>	
						//http://www.youtube.com/watch/v=?84343&d
					
						
						
						exit;					
						}
						
						
						function add_single_video($d){
							
							
							
							
							
							}
		
		
	function create_table(){
	
   $sql = "CREATE TABLE IF NOT EXISTS `wp_video_list` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT, 
  `video_id` varchar(30)  NOT NULL,
  `video_title` text not null,
  `video_stat` tinyint unsigned NOT NULL default 1,   
   PRIMARY KEY (`id`),
   key `video_id`(`video_id`),
   key `video_stat`(`video_stat`)
)";


global $wpdb;
require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
dbDelta($sql);

	}	
		
		
/*
 * SUBMENU PAGE Add video
 * 
 * */	
	
	
	
	
	
	
	
	
	function OptionsPage( $pagenum=1, $from_ajax=0 ){
		if (isset($_POST['addVideoUnique']))						
		update_option('videoUserYouTube', trim($_POST['videoUserYouTube'][0]));
		
		$dataA = $this ->getVideo($pagenum);
		
		//--------
		if(!$from_ajax):
		$this->direct_add();
		?>
			<div class="wrap"><h3>Insert YouTube UserName to List videos</h3>
		<form method="post" action="admin.php?page=wpAddVideo">
		<table cellpadding=3>
		<tr><td>
		<input type="text" name="videoUserYouTube[]" value="<?php echo get_option('videoUserYouTube')?>" style="width:10em" />&nbsp;</td>
		<td>
		<p class="submit"><input type="submit" name="addVideoUnique" class="button-primary" value="<?php _e('List  Videos') ?>" /></p>
		</td>
		</tr>
		</table>	
		
		</form>
		</div>
		
		<?php
		endif;
		//----
		
		if(is_array($dataA)):
		$total = $dataA['total'];
		$max_page = ceil($total/10);
		
		
		if(!$from_ajax):
		echo $message = ($total==0)?'<div class="error">No Video found for The user</div>':
		"<h3>Total {$total} Videos Found - {$max_page} Page(s)</h3>";	
		echo "<input type='hidden' id='max-page' value='$max_page'/>";

	     if($total != 0 && $total !=''){
		$toShow = ($total<10)? $total : 10;
			
		echo "<div id='videoNumMessage'><h4>Current Page: 1(Showing videos 1-{$toShow})</h4> </div>";
		 
		}
		?>
		<div class="paginate_video"><?php $this->paginate($total,$pagenum); ?></div>
		
		<div id="videoContents">
		<?php 
		endif;
		$this->common_ajax($dataA); 
		
		
		if(!$from_ajax):
		?>
		</div>
		<?php	
		 endif;
		 endif;
	
	}//endof options page
	
	
	/*
 * SUBMENU PAGE Manage Video
 * 
 * */	
	
	function videoManage(){
	?>
		<div style="text-align:center;margin 15px 0"> <h3>Manage Video Playlist</h3></div>
		<div class="wrap">
					<table class="widefat">
						<thead>
							<tr>
								<th>Video Title</th>
								<th>Thumbnail</th>
								<th>Status</th>
								<th>Action</th>
								<th>Remove</th>
							</tr>
						</thead>
						<tfoot>
							<tr>
								<th>Video Title</th>
								<th>Thumbnail</th>
								<th>Status</th>
								<th>Action</th>
								<th>Remove</th>
							</tr>
						</tfoot>
						<tbody>
							
						<?php
						global $wpdb;
						$result = $wpdb->get_results( "SELECT video_title,video_id,video_stat FROM wp_video_list",ARRAY_N );			 
						foreach($result as $single):
						$title = $single[0];
						$id= $single[1];
						$stat = $single[2];
						$image = 'http://i.ytimg.com/vi/'.$id.'/1.jpg';
						
						echo '<td>',$title,'</td>';
						echo '<td>','<img src="'.$image.'"/>','</td>';
						
						if($stat == 1)
						echo '<td>',Active,'</td>';
						else 
						echo '<td>',Suspended,'</td>'; 
						
						if($stat == 1)
						echo '<td><button id="',$id,'" class="action">Suspend</button></td>'; 
						else 
						echo '<td><button id="',$id,'" class="action">Add</button></td>';
						
						echo '<td><button class="remove">Remove</button></td></tr>';
						
						endforeach;
							
						?>
						
							
						</tbody>
					</table>
				</div>
		

<?php
       }// end of video manage submenu page
       
       
       
       function common_ajax($dataA){
		   ?>
		   	<div class="wrap">
					<table class="widefat">
						<thead>
							<tr>
								<th>Video Title</th>
								<th>Thumbnail</th>
								<th>Action</th>
							</tr>
						</thead>
						<tfoot>
							<tr>
								<th>Video Title</th>
								<th>Thumbnail</th>
								<th>Action</th>
							</tr>
						</tfoot>
						<tbody>
							
						<?php 
						
							$this -> renderVideos($dataA);
							
							?>
						
							
						</tbody>
					</table>
				</div>
	<?php	   
		   }
		   
		   
		   //direct add from code
		   function direct_add(){
			    
			   echo '<h3>Enter  Youtube Video  ID/Url to Add in the Playlist</h3>';
			   ?>
			   <table>
			   <tr>
			   <td>		  
			   <?php echo '<textarea id="areaId" rows="5" cols="50"></textarea>'; ?>
			   </td>
			   <td>
			   <input class='button-primary' type='submit' id='areaSubmit' value='Add To Playslist'/>
			   </td>
			   </tr>
			    </table>
			    <div class='updated' id='areaMessage'></div>
			   <?php
			   
			   }
       
       
       
       
       function getVideo($pagenum=1){
		   
		   $start=($pagenum==1)?1:($pagenum-1)*10+1;
		   
		   $user = get_option('videoUserYouTube');
		if(!$user)return;	
		
		//sample author  wataahISg00d
		
		// http://i.ytimg.com/vi/{$user}/1.jpg sample image
		
		
		$url = ($start>1)?
		"http://gdata.youtube.com/feeds/api/videos?author={$user}&start-index={$start}&max-results=10":
		"http://gdata.youtube.com/feeds/api/videos?author={$user}&max-results=10"
		;
		
		$content_url = 'http://www.youtube.com/v/EgHY53dOZ-U?f=videos&amp;app=youtube_gdata';

	$content_type ='video/3gpp' ;

	$code =  <<<EOF
	  <object width="425" height="350">
	  <param name="movie" value="$content_url"></param>
	  <embed src="$content_url" 
	   type="$content_type" width="425" height="350">
	  </embed>
	</object>
EOF;
		
		
		$parser=new DOMDocument();
		@$parser->load($url);
		if(preg_match('/<feed+[^>]+>/',$parser->saveXML(),$a))$feedString=$a[0];

		$parser->documentURI;
		$nURI=$parser->getElementsByTagName('feed')->item(0)->getAttribute('xmlns:media');
		
		//total video items
		$search=$parser->getElementsByTagName('feed')->item(0)->getAttribute('xmlns:openSearch');
		$total=0;
		$total = $parser -> getElementsByTagNameNS($search,'totalResults')->item(0)->nodeValue;


		$entryNumber=$parser->getElementsByTagName('entry')->length;




		//fetching data

		$dataA = array();
		$counter=0;
		foreach($parser->getElementsByTagName('entry') as $entry):
		$counter++;
		  $eString=$parser->saveXML($entry); 
		   
		  //creating sudo xml like the parent
		  $eString=$feedString.$eString.'</feed>';
		  
		  $eParser=new DOMDocument();
		  @$eParser->loadXML($eString);  


		 $dataA[$counter-1]['title'] = $eParser->getElementsByTagName('title')->item(0)->nodeValue; 
		 $dataA[$counter-1]['id'] = trim(preg_replace('?http://gdata.youtube.com/feeds/api/videos/?','',$eParser->getElementsByTagName('id')->item(0)->nodeValue),'/'); 
         
		$data=array('content','thumbnail');
		foreach($data as $single):
		 foreach($eParser->getElementsByTagNameNS($nURI,$single) as $tracker):
		 
		 if($single ==  'thumbnail') $dataA[$counter-1]['thumbnail'] = $tracker->getAttribute('url');
		 
		 if( $tracker->getAttribute('yt:format') == 5)
		 $dataA[$counter-1]['url'] = $tracker->getAttribute('url');
		 
		 
		 endforeach;
		 endforeach;
		 
		 endforeach;
		$dataA['total'] = $total;
		   
		   return($dataA);
		   
		   
		   
		   
		   
		   }//end of getVideo
		
		
		
     //pagination
     
     function paginate($total,$items_per_page=10){
		// if ($total<=10)return;		 
		//$pages = ceil($total/$items_per_page);
		//$current_page = 1;
		if($total>10){
			$img_right = plugins_url('/' , __FILE__).'images/right.png';
			$img_left = plugins_url('/' , __FILE__).'images/left.png';
	?>
			
		<input  type='image' src='<?php echo $img_left ?>' id="show-prev" style="display:none" value=''/>
		<input class='1' type='image' src='<?php echo $img_right ?>' id="show-next" value=''/>
		
		
<?php
	 }
		 
		}
		
		

			
	
	function renderVideos($dataA){		 
		
		 array_pop($dataA);
		
		 foreach($dataA as $yo){
		$image = 'http://i.ytimg.com/vi/'.$yo['id'].'/1.jpg';
		
	      echo '<tr><td class="', $yo['id'], '" style="width:400px">',$yo['title'],'</td>';
	      echo '<td>','<img src="' . $image. '"/>','</td>';
	      
	     if ($this -> exists_in_table($yo['id']))
	       echo '<td><b><h3>Added in the playlist</h3></b></td></tr>';
	       
	      else	
	       echo '<td><button id="',$yo['id'],'" class="primary" name="add">Add to Playlist</button></td></tr>';	
	 }
		
		
		
		
   }//endof renderVideos
   
   
 
   
   
   
   //Crude functions
        function exists_in_table($video_id){
			global $wpdb;
			//$wpdb = new wpdb( DB_USER, DB_PASSWORD, DB_NAME, DB_HOST );
			$result=$wpdb->get_results( "SELECT video_title FROM wp_video_list where  video_id='$video_id'" );
			if(empty($result))return false;
			else return true;			

			}
			
		function insert(){
			
			}
			
		function delete($vido_id){
				
				}
				
		function suspend(){			
			
		}
		
	
	  


}


?>
