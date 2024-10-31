<?php if ( ! defined( 'ABSPATH' ) ) exit; ?>
<?php
global $current_user;
$user_id=$current_user->ID;
$merchantID=get_user_meta($user_id,'merchantID',true);
$secretKey=get_user_meta($user_id,'secretKey',true);
?> 
<div class="poll-container">
	<div class="createpoll user-dash">
	 <?php if($merchantID=='' || $secretKey=='') 
		 { 
			  echo polldeep_poll_function();
		 } 
		else if(isset($_GET['action']) && sanitize_text_field($_GET['action'])=='preview')
		{  
			echo polldeep_preview();
		} 
		else if(isset($_GET['action']) && sanitize_text_field($_GET['action'])=='edit')
		{   
			echo '<div class="updated notice notice-success is-dismissible below-h2" style="display:none" id="message"><p></p></div>';
			echo polldeep_editPoll();
		} 
		else if(isset($_GET['action']) &&  sanitize_text_field($_GET['action'])=='analyzeAjax')
		{
		   echo polldeep_analyze();
		} 
		else if(isset($_GET['action']) && sanitize_text_field($_GET['action'])=='pollshare')
		 {
			 echo polldeep_pollShare();
		 }
		else
		{
			global $polldeepServer;
			$actual_link = (isset($_SERVER['HTTPS']) ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
			$sort='expired';
			 if(isset($_GET['pagi']))
			 { 
				$offset=sanitize_text_field($_GET['pagi']);
			 } 
			 else 
			 { 			   
				$offset=1;
			 } 
		 $args=array(
			'method' => 'POST',
			'timeout' => 45,
			'redirection' => 5, 
			'httpversion' => '1.0',  
			'blocking' => true,
			'headers' => array(),
			'cookies' => array(),
			'body' => array( 'merchantID' => $merchantID, 'secretKey' => $secretKey ,'offset'=>$offset,'sort'=>$sort),
		   );

		 $result = wp_remote_post( $polldeepServer.'/user/get_polls_ajax', $args );
	 
		 $res = json_decode($result['body'], true);
		  if($res['status']=='incorrect')
			{
				echo '<div class="updated notice notice-success is-dismissible 
				below-h2" id="message"><p>'.$res['msg'].'</p></div>';
				return;
			}
			else if(count($res)==0)
			{  
				
				echo '<div class="updated notice notice-success is-dismissible 
				below-h2" id="message"><p>Data Not 
				Found...!!!</p></div>';
				return; 
			}   
		if($res['status']!="nopoll")
		{
		  //-----------------pagination------------------------------//
			 if(isset($_GET['pagi'])) 
			 { 
				$pagi= sanitize_text_field($_GET['pagi']);
			 }
			 else
			 {
				 $pagi= 1;
			 }
			 $items_per_page=10;
			 $total_records=$res['totalPoll'];
			 $total_pages=ceil($total_records/$items_per_page);
			 $pagLink='';
			
			?>
		  <section>
		 <div class="row">
		 <div class="dashboard">
		 <div class="col-md-9 content">
		     <div class="updated notice notice-success is-dismissible below-h2" style="display:none" id="message"><p></p></div>
			  <div class='btn-group'>
					<button type='button' class='btn btn-danger' id='delete_all'>Delete All</button>
					<button type='button' class='btn btn-primary' id='select_all'>Select All</button>
			 </div>
			 <br><br>	
			 <form id='delete_all_form' method='post'>
			   <div class='row'>
				   <ul class='poll-list'>
					<?php
					 $i=0;
					 $str ='';  
					 foreach($res['polls'] as $poll)
					 {
						 $i++;
						 
						 if($poll['open']==0)
						 {
							 $openL='Open';
							 $datRq='open';
						 }
						 else
						 {
							$openL='Close'; 
							$datRq='close';
						 }
						  if($poll['votes']==0)
							 {
								$edit='<a class="btn btn-xs btn-success button-large" href="'.$actual_link.'&action=edit&id='.$poll['id'].'">Edit</a>';
							 }
							 else
							 {
								 $edit='';
							 }
							 if($poll['intemail']==0)
							 {  
							   $intemail='<a class="btn btn-xs btn-success button-large intemail" href="'.$actual_link.'&action=pollshare&uniqueid='.$poll['uniqueid'].'">Email</a>'; 
							 }
							 else
							 {
							   $intemail='';
							 }
						  $str .="<li class='col-sm-6 left'><div 
							class='option-holder".($poll['open']==1 ?"":" alt")."'><div 
							class='checkbox'><input type='checkbox' name='delete-id[]' 
							value='".$poll['id']."' data-class='blue' class='input-check-delete' 
							/></div><div 
							class='head-box'><h4>".$poll["question"]."</h4></div><div 
							class='data'><span>".$poll["votes"]." Votes</span></div><div 
							class='button-group'><div class='btn-group 
							btn-group-xs'><a href='".$poll["viewUrl"]."' class='btn btn-xs 
							btn-success' target='_blank'>View</a><a 
							href='".$actual_link.'&action=analyzeAjax&id='.$poll['id']."' 
							class='analyze btn btn-xs 
							btn-success'>Analyze</a>".$intemail."</div><div class='btn-group 
							btn-group-xs pull-right'><a href='javascript:void(0)' 
							data-request='".$datRq."' data-id='".$poll['id']."' 
							data-target='this' class='openclose  btn btn-xs 
							btn-success'>".$openL."</a>".$edit."<a 
							data-id='".$poll['id']."' 
							 href='javascript:void(0)' class='deleteitem btn btn-xs btn-primary'>Delete</a></div></div></li>";
							
							if($i%2==0)
							 {  
								 $str .='<div class="clear"></div>'; 
								
							 }
						 
					 }
					 echo $str;
					?>
				</ul>
			   </div>
			</form>	        
				   <?php
					$pagLink='';
					 for($jj=1;$jj<=$total_pages;$jj++)
					 {
						if($pagi!=0 && $pagi==$jj)
						{
							$pagLink .="<li class='active'><a  href='javascript:void(0)'>".$jj."</a></li>";
						}
						else
						{
							$pagLink .="<li><a href='".$actual_link."&pagi=".$jj."'>".$jj."</a></li>";
						}
					 }
				   ?>
				<ul class="pagination">
					<?php
					  echo $pagLink; 
					?>
					<li>
					<?php
						$next=$pagi+1;
						echo "<li><a href='".$actual_link."&pagi=".$next."'>Next</a></li>";
					?>
					</li> 
				</ul>
			<input type='hidden' value='<?php echo admin_url('admin-ajax.php') ?>' id="ajaxurl" name="ajaxurl"/>
			<input type="hidden" value="<?php echo $merchantID; ?>" name="merchantID" id="merchantID">
			<input type="hidden" value="<?php echo $secretKey; ?>" name="secretKey" id="secretKey">
			</div>	
		   <?php 
		   }
		   else
		   {
			   echo '<div class="update-nag notice notice-success is-dismissible below-h2"  id="message"><p>'.$res['msg'].'</p></div>';	   
		   } 
		   ?>

		<?php
		} ?>
</div>
<?php
