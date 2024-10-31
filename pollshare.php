<?php if ( ! defined( 'ABSPATH' ) ) exit; ?>
<div class="poll-container">
	<div class="createpoll user-dash dsas">
		<div class="dashboardpoll">
			<div class="updated notice notice-success is-dismissible below-h2" style="display:none" id="message"><p></p></div>
			<input type='hidden' value='<?php echo admin_url('admin-ajax.php') ?>' id="ajaxurl" name="ajaxurl"/>	
			<?php
			global $current_user,$polldeepServer;
			$user_id=$current_user->ID;
			$merchantID=get_user_meta($user_id,'merchantID',true);
			$secretKey=get_user_meta($user_id,'secretKey',true);
			$args=array(
				'method' => 'POST',
				'timeout' => 45,
				'redirection' => 5,
				'httpversion' => '1.0',
				'blocking' => true,
				'headers' => array(),
				'cookies' => array(),
			    'body' => array('merchantID'=>$merchantID,'secretKey'=>$secretKey,'id'=>intval($_REQUEST['uniqueid'])),
			   );
	
			$result = wp_remote_post(  $polldeepServer.'/user/pollshareAjax', $args );
			
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
		?>
			<section>
				<script>
					jQuery(document).ready(function(){
						jQuery(document).on('click','.add_opt' ,function(){
							var n = jQuery('field-opt-text').length + 1;
							
							var box_html = jQuery('<label class=\'col-sm-3\'></label><div class=\'col-sm-9 space-bot\'><input type=\'text\' id=\'recipients\' class=\'form-control\' name=\'recipients[]\' class=\'field-opt-text\' value=\'\'><span class=\'rem_opt\' ><i class=\'glyphicon glyphicon-minus\'></i></span> </div>');
							var mainelem = jQuery(this).parent();
							box_html.hide();
							
							mainelem.after(box_html);
							box_html.fadeIn('slow');
							return false;
						});
						
						jQuery(document).on('click','.rem_opt' ,function(){
							jQuery(this).parent().css( 'background-color', '#FF6C6C' );
							jQuery(this).parent().fadeOut('slow', function() {
								
								 jQuery(this).prev().remove();
								 jQuery(this).remove();
								jQuery('.box-number').each(function(index){
									jQuery(this).text( index + 1 );
								});
							});
							return false;
						});
					});
				</script>
				<div class="pollshare">
					<div class="row">
						<div class="col-md-12 content">
							<h2 class="promo-heading">Share Poll</h2>
							<form role="form" class="live_form" method="post">
								<input type="hidden" id="unique_id" value="<?php echo intval($_REQUEST['uniqueid']); ?>" name="unique_id">
								<div class="col-sm-9 space-bot">
									<label for="questions">Email Address</label>
									<input type="text" class="form-control emailAdd" id="recipients" name="recipients[]">
									<span class='add_opt' ><i class='glyphicon glyphicon-plus'></i></span>
								</div>
								
								<div class="form-group col-sm-9">
									<label for="questions">Comment</label>
									<?php 
									if(isset($_REQUEST['uniqueid']) && intval($_REQUEST['uniqueid'])!='')
									{
										$uniqueid=intval($_REQUEST['uniqueid']);
									}
									else
									{
										$uniqueid='';
									}
									if ($polldeepServer[strlen($polldeepServer) - 1] == '/') 
									{
										$link=$polldeepServer.$uniqueid;
									}
									else
									{
										$link=$polldeepServer."/".$uniqueid;
									}
									 ?>
									<textarea type="text" rows=7 class="form-control" id="comment" name="comment"><?php echo "Hello\n\nYou are invited to vote on the following poll.\n<a href='{$link}'>{$link}</a>\n\n".$res['name'];?></textarea>
								</div>
									
								<div class="form-group col-sm-9">
									<button type="submit" class="pollshareBtn create-btn btn btn-primary pull-right">Email</button>
									  <input type="hidden" class="form-control" id="createForm" name="createForm" value="1">
									  <input type="hidden" name="merchantID" value="<?php echo $merchantID ?>" id="merchantID">
									  <input type="hidden" name="secretKey" value="<?php echo $secretKey ?>" id="secretKey">
								</div>
							</form>
						</div>
					</div>
				</div>
			</section>
		</div>
	</div>
</div>
