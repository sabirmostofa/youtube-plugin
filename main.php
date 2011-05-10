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
		register_activation_hook(__FILE__, array($this, 'create_table'));
		
		}
		
		function add_scripts(){			
			wp_enqueue_script('jquery');
            wp_enqueue_script('add_video_script',plugins_url('/' , __FILE__).'js/script.js');	
            wp_localize_script('add_video_script', 'addVideoSettings',
array(
'ajaxurl'=>admin_url('admin-ajax.php')

));		
			
			}

	function CreateMenu(){
		add_submenu_page('theme-options.php','Add Video','WP Add Video','activate_plugins','wpAddVideo',array($this,'OptionsPage'));
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
	
	
	
	
	
	
	
	
	function OptionsPage(){
		?>
		
		<div class="wrap">
					<table class="widefat">
						<thead>
							<tr>
								<th>Video Title</th>
								<th>Thumbnail</th>
								<th>Action<th>
							</tr>
						</thead>
						<tfoot>
							<tr>
								<th>Video Title</th>
								<th>Thumbnail</th>
								<th>Action<th>
							</tr>
						</tfoot>
						<tbody>
							
						<?php if (isset($_POST['addVideoUnique']))						
							update_option('videoUser', trim($_POST['videoUser'][0]));
							$this -> renderVideos();
							
							?>
						
							
						</tbody>
					</table>
				</div>
		
		
		
		<div class="wrap"><h2>Insert your YouTube UserName</h2>
		<form method="post" action="admin.php?page=wpAddVideo">
		<table cellpadding=3>
		<tr><td>
		<input type="text" name="videoUser[]" value="<?php echo get_option('videoUser')?>" style="width:10em" />&nbsp;</td>
		<td>
		<p class="submit"><input type="submit" name="addVideoUnique" class="button-primary" value="<?php _e('GET Video') ?>" /></p>
		</td>
		</tr>
		</table>
		
		
		
		</form>
		
	<?php
	}//endof options page
	
	
	/*
 * SUBMENU PAGE Manage Video
 * 
 * */	
	
	function videoManage(){
	?>
		
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
						//echo '<td>','<img src="'.$image.'"/>','</td>';
						echo '<td></td>';
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
		
		
		
		

			
	
	function renderVideos(){
		$user = get_option('videoUser');
		if(!$user)return;	
		
		//sample author  wataahISg00d
		
		// http://i.ytimg.com/vi/{$user}/1.jpg sample image
		
		$url = "http://gdata.youtube.com/feeds/api/videos?author={$user}&max-results=10";
		
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
		
		 
		
		
		 foreach($dataA as $yo){
		$image = 'http://i.ytimg.com/vi/'.$yo['id'].'/1.jpg';
		
	      echo '<tr><td class="', $yo['id'], '" style="width:400px">',$yo['title'],'</td>';
	      echo '<td>','<img src="' . $image. '"/>','</td>';
	      
	     if ($this -> exists_in_table($yo['id']))
	       echo '<td><b><h3>Added in the playlist</h3></b></td></tr>';
	       
	      else	
	       echo '<td><button id="',$yo['id'],'" class="primary" name="add">Add</button></td></tr>';	
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
