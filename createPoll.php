<?php if ( ! defined( 'ABSPATH' ) ) exit; ?> 
<div class="createpoll">
	<div class="updated notice notice-success is-dismissible below-h2" style="display:none" id="message"><p></p></div>
<input type='hidden' value='<?php echo admin_url('admin-ajax.php') ?>' id="ajaxurl" name="ajaxurl"/>		
<?php
		 function ip(){
		    $ipaddress = '';
		     if (getenv('HTTP_CLIENT_IP'))
		         $ipaddress = getenv('HTTP_CLIENT_IP');
		     else if(getenv('HTTP_X_FORWARDED_FOR'))
		         $ipaddress = getenv('HTTP_X_FORWARDED_FOR');
		     else if(getenv('HTTP_X_FORWARDED'))
		         $ipaddress = getenv('HTTP_X_FORWARDED');
		     else if(getenv('HTTP_FORWARDED_FOR'))
		         $ipaddress = getenv('HTTP_FORWARDED_FOR');
		     else if(getenv('HTTP_FORWARDED'))
		        $ipaddress = getenv('HTTP_FORWARDED');
		     else if(getenv('REMOTE_ADDR'))
		         $ipaddress = getenv('REMOTE_ADDR');
		     else
		         $ipaddress = 'UNKNOWN';

		     return "$ipaddress"; 
		  }

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
			    'body' => array('merchantID'=>$merchantID,'secretKey'=>$secretKey,'id'=>intval($_REQUEST['id'])),
			   );    
		$result = wp_remote_post(  $polldeepServer.'/user/createAjax', $args );
		
		$res = json_decode($result['body'], true);  

		if($res['status']=='incorrect')
		{
			echo '<div class="updated notice notice-success is-dismissible 
			below-h2" id="message"><p>'.$res['msg'].'</p></div>';
			return;
		}
	
		$userMembership	= $res['userMembership'];
		$deeper_fields		= $res['deeper_fields'];
		$user			= $res['user'];
		$admin			= $res['admin'];
		$max_count		= $res['max_count'];
		$max_free		= $res['max_free'];
		$userMembership	= (object)$userMembership;
		$user			= (object)$user;
		$deeper_fields		= (object)$deeper_fields;
		$themeDtails		= array_reverse($res['theme']);
		$themeDtails		= (object)$themeDtails;
		$settingData 		=  $res['setting']; 
	?>
	<style>
	.form-group .set_background {
	  margin-top: 10px;
	}
	.set_background .icheckbox_flat {
	  display: inline-block;
	}
	.set_background label {
	  display: inline-block;
	  width: auto !important;
	}
	</style>
	<script>
	<?php  if($userMembership && !empty($userMembership) && !$admin): ?>
			var max_count = <?php echo (isset($userMembership) && $userMembership->no_of_ans) ? $userMembership->no_of_ans : $max_count; ?>;
			var max_polls = <?php echo (isset($userMembership) && $userMembership->no_of_polls) ? $userMembership->no_of_polls : $max_free; ?>;
		<?php elseif(isset($admin) && $admin): ?>
			var max_count = <?php echo ($max_count) ? $max_count: ""; ?>;
			var max_polls = <?php echo ($max_free) ? $max_free : ""; ?>;
		<?php else: ?>
			var max_count = <?php echo ($max_count) ? $max_count : ""; ?>;
			var max_polls = <?php echo ($max_free) ? $max_free : ""; ?>;
		<?php endif; ?>
		
		
		jQuery(document).ready(function(){
			if(max_count && max_count <=3 ) {
				jQuery("#add-field").addClass("disabled");
				return false;
			}
		});
  </script>
<section>
  <div class="create">
    <div class="row">
      <div class="col-md-5 content">
		 <div class="alltabs">
			

			<button type="button" data-id="container_questions" class="btn btn-success mytabs">Questions</button>
			<button type="button" data-id="settings" class="btn btn-primary mytabs">Settings</button>
			<?php if(($userMembership && !empty($userMembership) && !$userMembership->multiple_themes) || $admin): ?>
				<button type="button" data-id="theme" class="btn btn-success mytabs">Theme</button>
			<?php endif; ?>
			
			<?php if(($userMembership && !empty($userMembership) && !$userMembership->customize_themes) || $admin): ?>
				<button type="button" data-id="customize" class="btn btn-primary mytabs">Customize</button>
			<?php endif; ?>

				<button type="button" data-id="embed_holder" class="btn btn-success mytabs last">Your Code </button>
		 </div>
        <div class="box-holder">
          <form role="form" class="live_form createPollForm" id="create-poll" method="post">
           <input type="hidden" name="users_ip_address" value = "<?= ip(); ?>" >
            <div id="container_questions" class="tabbed">
              <div class="form-group">
                <label for="questions">Your Question</label>
                <span class="help-block">No HTML allowed. Invalid question will be ignored.</span>
                <input type="text" class="form-control" id="questions" name="question" value="<?php echo (isset($_POST['question']) ?$_POST['question']:""); ?>">
              </div>
              <div class="form-group" id="widget_answers">
                <label>Answers</label>
                <span class="help-block">Leave fields empty to ignore options. No HTML allowed. Invalid answers will be ignored.</span>
                <ul id="sortable">
				<?php if( "1" <= $userMembership->no_of_ans || $userMembership->no_of_ans=="0")
				{
				?>
                  <li id="poll_sort_1">
                    <div class="input-group">
                      <span class="input-group-addon"><i class="glyphicon glyphicon-move"></i></span>
                      <input type="text" class="form-control" name="option[]">
                    </div>
                  </li>
                <?php }
                if( "2" <= $userMembership->no_of_ans || $userMembership->no_of_ans=="0")
				{
                ?>
                  <li id="poll_sort_2">
                    <div class="input-group">
                      <span class="input-group-addon"><i class="glyphicon glyphicon-move"></i></span>
                      <input type="text" class="form-control" name="option[]">
                    </div>
                  </li>
                <?php }
                if( "3" <= $userMembership->no_of_ans || $userMembership->no_of_ans=="0")
				{
                ?>  
                  <li id="poll_sort_3">
                    <div class="input-group">
                      <span class="input-group-addon"><i class="glyphicon glyphicon-move"></i></span>
                      <input type="text" class="form-control" name="option[]">
                    </div>
                  </li>
                <?php }?>
                </ul>
              </div>
          <?php if(isset($userMembership) && !empty($userMembership) && $userMembership->no_of_ans == "0" || $userMembership->no_of_ans > "3"): ?>
					<a href="#" id="add-field" class="btn btn-transparent">
						<small>Add Field</small>
					</a>
			
		  <?php endif; ?>
            </div>
            
            <div id="settings" class="tabbed">
				<?php if($user->membership_id == 6 || $user->membership_id == 5 ||  $admin ) {?>
				  <ul class="form_opt" data-id="share" data-callback="update_share">
					<li class="label">Sharing
					<small>Allows users to share and embed poll.</small>
					</li>
					<li><a href="#" class="last" data-value="0">No</a></li>
					<li><a href="#" class="first current" data-value="1">Yes</a></li>
				  </ul>
				  <input type="hidden" name="share" id="share" value="1">
				<?php } ?>
				<?php if(isset($userMembership) && $userMembership->allow_results=='0'){ ?>
				  <ul class="form_opt" data-id="results" data-callback="update_results_button">
					<li class="label">Show Results
					<small>Allows users to view results.</small>
					</li>
					<li><a href="#" class="last" data-value="0">No</a></li>
					<li><a href="#" class="first current" data-value="1">Yes</a></li>
				  </ul>
				  <input type="hidden" name="results" id="results" value="1">
				<?php } ?>
				<!--For Comments -->
				<?php if(isset($userMembership) && $userMembership->add_comments=='5'){ ?>
				 <ul class="form_opt" data-id="comments">
					<li class="label">Comments
					<small>Allows users to add comments.</small>
					</li>
					<li><a href="#" class="last" data-value="0">No</a></li>
					<li><a href="#" class="first current" data-value="1">Yes</a></li>
				  </ul>
				  <input type="hidden" name="comments" id="comments" value="1">
				<?php } ?>
				<input type="hidden" name="comments" id="comments" value="1">
				<?php if(isset($userMembership) && $userMembership->more_than_one_ans=='0' || $admin){  ?>
				  <ul class="form_opt" data-id="choice" data-callback="update_choice_type">
					<li class="label">Multiple Choices
					<small>Allows users to choose more than one option.</small>
					</li>
					<li><a href="#" class="last current" data-value="0">No</a></li>
					<li><a href="#" class="first" data-value="1">Yes</a></li>
				  </ul>
				  <input type="hidden" name="choice" id="choice" value="0">
				<?php } ?>
              <?php if(isset($userMembership) && $userMembership->multiple_votes=='0' || $admin): ?>
				  <ul class="form_opt" data-id="vote">
					<li class="label">Multiple Votes
					<small>Allows users to vote more than once</small>
					</li>
					<li><a href="#" class="last current" data-value="off">Off</a></li>
					<li><a href="#" data-value="day">Daily</a></li>
					<li><a href="#" class="first" data-value="month">Monthly</a></li>
				  </ul>
				  <input type="hidden" name="vote" id="vote" value="off">
              <?php endif; ?>

              <?php if(isset($userMembership) && $res['ispro']=='pro' && $userMembership->custom_logo==0): ?>
              
              <script>	

              /*Code for custom logo*/
              jQuery(document).ready(function(){
				 jQuery(".custom_logo_set_no").click(function(){ 	
					 jQuery('#poll_top_of_bar_logos').removeClass('theme-without-logo');		
					jQuery("#custom_logo_set_visiable").val("<?php echo $res['url'] ?>/static/member_logo/<?php echo $user->custom_logo ?>");
					 jQuery("#poll_top_of_bar_logos a img").attr('src',"<?php echo $res['url'] ?>/static/member_logo/<?php echo $user->custom_logo ?>");
				 });
				 
				 jQuery(".custom_logo_set_yes").click(function(){

					// jQuery("#custom_logo_set_visiable").val("<?php echo $res['url'] ?>/static/<?php echo $res['logo'] ?>");
					// jQuery("#poll_top_of_bar_logos a img").attr('src','<?php echo $res["url"] ?>/static/<?php echo $res["logo"] ?>');
					jQuery('#poll_top_of_bar_logos').addClass('theme-without-logo');
					jQuery("#custom_logo_set_visiable").val("");
					jQuery("#poll_top_of_bar_logos a img").attr('src','');
					
				 });
				  
			  });
              </script>
				<ul class="form_opt" data-id="custom_logo">
					<li class="label">Use Custom Logo
					<small>Use your own custom logo</small>
					</li>
					<li><a href="#" class="last current custom_logo_set_yes" data-value="0">No</a></li>
					<li><a href="#" class="first custom_logo_set_no" data-value="1">Yes</a></li>
					<input type="hidden" name="custom_logo_set_visiable" id="custom_logo_set_visiable" value="<?php echo $res['url'] ?>/static/<?php echo $res['logo'] ?>" />
				</ul>
				<input type="hidden" name="custom_logo" id="custom_logo" value="0">
              <?php endif; ?> 
              
              <?php if(((isset($userMembership->save_theme) && $res['ispro']=='pro' && $userMembership->save_theme=="0" ) && ($userMembership && !empty($userMembership) && !$userMembership->customize_themes)) || $admin): ?>
				<ul class="form_opt" data-id="save_theme">
					<li class="label">Save this theme for future use
					<small>Save this theme for future use</small>
					</li>
					<li><a href="#" class="last current" data-value="0">No</a></li>
					<li><a href="#" class="first" data-value="1">Yes</a></li>
				</ul>
				<input type="hidden" name="save_theme" id="save_theme" value="0">
              <?php endif; ?>
             
              <?php if($res['ispro']=='pro' && isset($userMembership) && $userMembership->restrict_by_password=='0'): ?>
                <div class="form-group">
                  <label for="pass">Password</label>
                  <input type="text" class="form-control" id="pass" name="pass">
                </div>
              <?php else: ?> 
                <div class="form-group">
                  <label for="pass">Password <a href="<?php echo $res['upgrade'] ?>" class='pull-right'><small>(Upgrade)</small></a></label>
                  <input type="text" class="form-control" id="pass" placeholder="Please upgrade to a premium package to unlock this feature." disabled>
                </div>
              <?php endif; ?>
              <?php if(isset($userMembership) && !empty($userMembership) && !$userMembership->decide_poll_period): ?>
              <div class="form-group">
                <label for="expires">Expires in</label>
				<table class="admin_fields"  border=1>
					<tr>
						<td width="1%">
							<input type="checkbox" class="form-control" id="expires_never" value="0" name="expires_never"> 
						</td>
						<td> 
							Never  
						</td>
						</tr>
						<tr>
						<td colspan="2">
							Select Date :   
							<input type="text" name="expires" id="expires" class="form-control" style="border: 1px solid rgb(204, 204, 204) ! important; box-shadow: 0px 1px 1px rgba(0, 0, 0, 0.075) inset !important;">  
						</td>
					</tr>
				</table>
          	<script>
			 jQuery(document).ready(function(){
				jQuery( "#expires" ).datepicker({
					dateFormat:'yy-mm-dd',
					minDate: 0
				});
			 });
             </script>
              </div>
              <?php endif; ?>
              <?php 
              	$special_condition = ($user->membership_id == 6 || $admin) ? true : false;
               ?>

           	<div class="heading_wrap">
		<b><?php echo "Poll Size"; ?></b>
		<span class="deeper_field_notice"><i><?php echo "Select The Size Of The Poll (px)"?></i></span>
	</div>   
	<table class="admin_fields"  border=1> 
		<tr>
			<td width="1%">
				<input type="radio" name="custom_poll_size" class="default_poll_size" id="default_poll_size" value="0" checked='checked'>
			</td>
			<td>Default (600 px)</td> 	
		</tr> 
		<tr>
		  	 <td width="1%" id="custom_hover" class = "custom_hover">
			   	<input type="radio" name="custom_poll_size" class="custom_poll_size <?= ($special_condition) ? 'popup-enabled' : 'popup-disabled' ?>" id="custom_poll_size" value="1">
			   		<?php if($user->membership_id != 6) { 
				   			if(!$admin) { ?>
				   				<div class="custom_tooltip">
				   					<?php echo 'The Poll Size option is available for Platinum plan only.'; ?>		
			   					</div>
		   				<?php } 
		   			} ?>
			</td>
			<td>Customized (600px - 400px)</td>
		</tr>
		
		<tr>
			<td width="1%" id="custom_hover_300" class = "custom_hover">
				<input type="radio" name="custom_poll_size" class="custom_poll_size_300 <?= ($special_condition) ? 'popup-enabled' : 'popup-disabled' ?>" id="custom_poll_size_300" value="2" >
					<?php if($user->membership_id != 6) { 
				   			if(!$admin) { ?>
				   				<div class="custom_tooltip">
				   					<?php echo 'The Poll Size option is available for Platinum plan only.'; ?>		
			   					</div>
		   				<?php } 
		   			} ?>
			</td>
			<td>Customized (300px)</td> 	
		</tr> 

		<tr class="custom_poll_field" style="display:none;" >
			<td colspan="2">
				<div class='custom_fields'>
					<span class="remove-field"></span>
					<div class='form-group'>
						<label for='theme_name' class='col-sm-3 control-label'><?php echo "Custom poll width"; ?></label>
						<div class='col-sm-9'>
						
						<?php   
						$max_poll_width = $settingData[33]['var']; 
						$min_poll_width = $settingData[34]['var'];  
						 ?>
							<input type='number' class='form-control custom_field_name custom_poll_text' name='custom_poll_text' id='custom_poll_text' value='' min='<?php echo $min_poll_width; ?>' max='<?php echo $max_poll_width; ?>'>
						</div>
						<div class="error"></div>
					</div>
				</div>
			</td>
		</tr> 

	</table> 

	<!-- Deep Analysis Start -->
	<?php if(!empty($deeper_fields) && !empty($userMembership) && !$userMembership->deeper_analysis) { ?> 

		<div class="heading_wrap">
			<b>
				<?php echo "Deep Analysis"; ?>
			</b>
			<span class="deeper_field_notice">
				<i><?php echo "Select The Deep Analysis You Want To Include In Your Poll"; ?></i>
			</span>
		</div>

		<table class="admin_fields"  border=1>
			<?php foreach($deeper_fields as $deeper_field): ?>
				<tr>
					<td width="1%"><input type="checkbox" name="deeper_fields[]" value="<?=$deeper_field['id']?>"></td>
					<td> <?php echo $deeper_field['field_name'];?> </td>
				</tr>
			<?php endforeach; ?> 

		<?php if($user->membership_id == 6 ||  $admin ) { ?>
		<tr class="deep_analyse">
			<td width="1%">
				<input type="checkbox" name="custom_check" class="custom_check" id="custom_check" value="true">
			</td>
			<td>Custom</td>
		</tr>

		<tr class='custom_fields_extra' style="display:none;" id="custom_fields_extra">
			<td colspan="2"> 
				<div class="smart-class" style="display:none"> 
					<div class='custom_field_data'> 

						<span class="remove-field"></span>
						<div class='form-group'>
						  <label for='theme_name' class='col-sm-3 control-label'><?php echo "Custom Chart Name"; ?></label> 
						  <div class='col-sm-9'>
							<input type='text' class='form-control custom_field_name' name='custom_field[0][field_name]' id='field_name' value=''>
						  </div> 
					  	</div>

					  	<div class='form-group field_type' style="display:none;">
						  <label for='question_font_type' class='col-sm-3 control-label'><?php echo "Field type"; ?></label>
						  <div class='col-sm-9'>
							  <select name='custom_field[0][field_type]' class='custom_field_type' id='field_type' onchange='change_field(this.id);'>
								<option value='select'><?php echo "Drop Down Menu"; ?></option>
								<option value='text'><?php echo "Text Field"; ?></option>
								<option value='checkbox'><?php echo "Checkbox"; ?></option>
								<option value='radio'><?php echo "Radio Button"; ?></option>
							  </select>
						  </div>
						</div>

						<div class='form-group field-options options'>
						  <label for='Options' class='col-sm-3 control-label'>Options</label>
						  <div class='col-sm-9'>
							<input type='text' class='form-control custom_field_options' id= 'field_options' name='custom_field[0][options][]' class='field-opt-text' value=''><span class='add_opt' ><i class='glyphicon glyphicon-plus'></i></span>
						  </div>
						</div>

						<div class='form-group status'>
						  <label for='status' class='col-sm-3 control-label'><?php echo "Status"; ?></label>
						  <div class='col-sm-9'>
							  <select name='custom_field[0][status]' class='custom_field_status' id='status'>
								<option value='0'><?php echo "Active"; ?></option>
								<option value='1'><?php echo "Inactive"; ?></option>
							  </select>
						  </div>
						</div> 

					</div> 
				</div>  

				<div class='custom_fields all-custom-fields-wrap' >
					<div class='custom_field_0'>
						<span class="remove-field"></span>
						
						<div class='form-group'>
						  <label for='theme_name' class='col-sm-3 control-label'><?php echo "Custom Chart Name"; ?></label>
						  <div class='col-sm-9'>
							<input type='text' class='form-control custom_field_name' name='custom_field[0][field_name]' id='field_name' value=''>
						  </div>
						</div> 

						<div class='form-group field_type' style="display:none;">
						  <label for='question_font_type' class='col-sm-3 control-label'><?php echo "Field type"; ?></label>
						  <div class='col-sm-9'>
							  <select name='custom_field[0][field_type]' class='custom_field_type' id='field_type' onchange='change_field(this.id);'>
								<option value='select'><?php echo "Drop Down Menu"; ?></option>
								<option value='text'><?php echo "Text Field"; ?></option>
								<option value='checkbox'><?php echo "Checkbox"; ?></option>
								<option value='radio'><?php echo "Radio Button"; ?></option>
							  </select>
						  </div>
						</div>

						<div class='form-group field-options options'>
						  <label for='Options' class='col-sm-3 control-label'>Options</label>
						  <div class='col-sm-9'>
							<input type='text' class='form-control custom_field_options' id= 'field_options' name='custom_field[0][options][]' class='field-opt-text' value=''><span class='add_opt' ><i class='glyphicon glyphicon-plus'></i></span>
						  </div>
						</div>

						<div class='form-group status'>
						  <label for='status' class='col-sm-3 control-label'><?php echo "Status"; ?></label>
						  <div class='col-sm-9'>
							  <select name='custom_field[0][status]' class='custom_field_status' id='status'>
								<option value='0'><?php echo "Active"; ?></option>
								<option value='1'><?php echo "Inactive"; ?></option>
							  </select>
						  </div>
						</div> 
					</div> 
				</div>
				<div class='form-group add-custom-field-button'>
					<div class='col-sm-6'> 
					<a href='javascript:void(0);' id="add_custom_field" class='btn btn-transparent'><?php echo "Add Field"; ?></a>
					</div>
				</div> 
			</td>
		</tr>

		<?php } ?>
		<tr class="map_analyses">
			<td width="1%"><input type="checkbox" name="local_map" class="local_map" id="local_map" value="1"></td>
			<td>Map</td>
			
		</tr> 
	</table> 
<?php } ?> 
<!-- Deep analysis End -->

       <?php       if(isset($userMembership) && !empty($userMembership) && !$userMembership->SHD_analysis): ?>
					
			<script type='text/javascript'>
				
				function change_field(elemid){
					if (jQuery("#"+elemid).val() != 'text')
					{
							jQuery("#"+elemid).parent().parent().next(".form-group.field-options").show();
					}
					else
						jQuery("#"+elemid).parent().parent().next(".form-group.field-options").hide();
				} 
		            </script>
					
              <?php endif; ?>
            </div>
			<?php if(($userMembership && !empty($userMembership) && !$userMembership->multiple_themes) || $admin){   ?>
            <div id="theme" class="tabbed">
			
				<?php    

				foreach($themeDtails as $theme) {
						$theme=(array) $theme;
						$option_data=unserialize($theme['serialized_data']) ; 

						foreach ($option_data as $area_key => $area_value) {
							foreach ($area_value as $property_key => $property_value) {
								$option_data[$area_key][$property_key] = str_replace('!important','',$option_data[$area_key][$property_key]);
							}
						}   
					?>
					<div class="qp-layout">
						<div class="qp-img">
							<div id='qp<?=$theme['id']?>_b1' class="theme_custom<?=$theme['id']?>_b1">
								<div id='qp<?=$theme['id']?>_q1' style='border-radius: 6px; font-family: Arial; font-size: 12px;  color: rgb(255, 255, 255); margin-bottom: 10px;  '>
								   <div id='qp<?=$theme['id']?>_qi1' class="question_common" style='padding: 10px;'>Do you want a Poll</div>
								</div>

								<div class="answer_wrap" id='qp<?=$theme['id']?>_ao1' style='border-radius: 6px; background-image: none; filter: none; background-color: transparent;'>
									<div  ='this.childNodes[0].childNodes[0].checked=true' id='qp<?=$theme['id']?>_a1' style='display: block; font-family: Arial; font-size: 12px; color: rgb(0, 0, 0); padding-top: 5px; padding-bottom: 5px; clear: both; cursor: pointer;' class="answer_backg">
										<span id='qp<?=$theme['id']?>_t1' class='qp<?=$theme['id']?>_t theme_answers' style='display: block; padding-left: 30px;'>
										<input type='radio' value='1' name='qp<?=$theme['id']?>_v' id='qp<?=$theme['id']?>_i1' style='float: left; width: 25px; margin-left: -25px; margin-top: -1px; padding: 0px; height: 18px;'><span>Yes</span><div class="answer_label"></div></span>
									</div>

									<div onclick='this.childNodes[0].childNodes[0].checked=true' id='qp<?=$theme['id']?>_a2' style='display: block; font-family: Arial; font-size: 12px; color: rgb(0, 0, 0); padding-top: 5px; padding-bottom: 5px; clear: both; cursor: pointer;' class="answer_backg">
										<span id='qp<?=$theme['id']?>_t2' class='qp<?=$theme['id']?>_t theme_answers' style='display: block; padding-left: 30px;'>
										<input type='radio' value='2' name='qp<?=$theme['id']?>_v' id='qp<?=$theme['id']?>_i2' style='float: left; width: 25px; margin-left: -25px; margin-top: -1px; padding: 0px; height: 18px;'><span>No</span><div class="answer_label"></div></span>
									</div>
									<div onclick='this.childNodes[0].childNodes[0].checked=true' id='qp<?=$theme['id']?>_a3' style='display: block; font-family: Arial; font-size: 12px; color: rgb(0, 0, 0); padding-top: 5px; padding-bottom: 5px; clear: both; cursor: pointer;' class="answer_backg">
										<span id='qp<?=$theme['id']?>_t3' class='qp<?=$theme['id']?>_t theme_answers' style='display: block; padding-left: 30px;'>
										<input type='radio' value='3' name='qp<?=$theme['id']?>_v' id='qp<?=$theme['id']?>_i3' style='float: left; width: 25px; margin-left: -25px; margin-top: -1px; padding: 0px; height: 18px;'><span>Maybe</span><div class="answer_label"></div></span>
									</div>
								</div>

								<div id='qp<?=$theme['id']?>_bo1' style='padding-top: 10px; clear: both;' class="button_wrap">
									<a href='#' class=' qp<?=$theme['id']?>_btnh'>
										<input type='button' class='qp<?=$theme['id']?>_btn' value='Vote' id='qp<?=$theme['id']?>_btn1' name='qp<?=$theme['id']?>_b' style='width: 80px;  margin-right: 5px; border-radius: 10px; border: 1px solid rgb(150, 163, 170); font-family: Arial; font-size: 12px; font-weight: bold; color: rgb(0, 0, 0); cursor: pointer;'>
									</a>
									<a href='#' class='qp<?=$theme['id']?>_btnh'>
										<input type='button' value='View Results' class='qp<?=$theme['id']?>_btn' id='qp<?=$theme['id']?>_btn2' name='qp<?=$theme['id']?>_b' style='width: 110px; margin-right: 5px; border-radius: 10px; border: 1px solid rgb(150, 163, 170); font-family: Arial; font-size: 12px; font-weight: bold; color: rgb(0, 0, 0); cursor: pointer;'>
									</a>
								</div>
								<!--/form-->
							 </div>
							<?php echo "<script>
									jQuery(function() {
										".(isset($option_data['poll']['bottom-bar-color']) && !empty($option_data['poll'][
										'bottom-bar-color'])?'jQuery("#qp'.$theme['id'].'_bo1").css("background-color","#'.$option_data['poll']['bottom-bar-color'].'");':'')."

										".(isset($option_data['poll']['bottom-bar-color']) && !empty($option_data['poll'][
										'bottom-bar-color'])?'jQuery("#qp'.$theme['id'].'_ao1     .answer_label").css("background-color","#'.$option_data['poll']['bottom-bar-color'].'");':'')."


										".(isset($option_data['poll']['background-color']) && !empty($option_data['poll'][
										'background-color'])?'jQuery("#qp'.$theme['id'].'_b1").css("cssText", "border-top-color: #'.$option_data['poll']['bottom-bar-color'].' ;background-color: #'.$option_data['poll']['background-color'].'" );':'')."

										".(isset($option_data['poll']['background-image']) && !empty($option_data['poll']['background-image'])?'jQuery("#qp'.$theme['id'].'_b1").css("background","url('.$res["url"]."/admin/images/themebg/".$option_data['poll']['background-image'].') repeat scroll left top / 100% auto transparent");':'')."

										".(isset($option_data['poll']['border-width']) && !empty($option_data['poll']['border-width'])?'jQuery("#qp'.$theme['id'].'_b1").css("border-width","'.$option_data['poll']['border-width'].'");':'')."
										".(isset($option_data['question']['font-type']) && !empty($option_data['question']['font-type'])?'jQuery("#qp'.$theme['id'].'_qi1").css("font-family","'.$option_data['question']['font-type'].'");':'')."
										".(isset($option_data['question']['font-size']) && !empty($option_data['question']['font-size'])?'jQuery("#qp'.$theme['id'].'_qi1").css("font-size","'.$option_data['question']['font-size'].'");':'')."
										".(isset($option_data['question']['font-color']) && !empty($option_data['question']['font-color'])?'jQuery("#qp'.$theme['id'].'_qi1").css("color","#'.$option_data['question']['font-color'].'");':'')."
										".(isset($option_data['question']['border-color']) && !empty($option_data['question']['border-color'])?'jQuery("#qp'.$theme['id'].'_qi1").css("border-width","'.$option_data['question']['border-width'].'");':'')."
										".(isset($option_data['question']['border-color']) && !empty($option_data['question']['border-color'])?'jQuery("#qp'.$theme['id'].'_qi1").css("border-color","#'.$option_data['question']['border-color'].'");':'')."
										".(isset($option_data['question']['border-color']) && !empty($option_data['question']['border-color'])?'jQuery("#qp'.$theme['id'].'_qi1").css("border-top-color","#'.$option_data['question']['border-color'].'");':'')."
										".(isset($option_data['question']['border-color']) && !empty($option_data['question']['border-color'])?'jQuery("#qp'.$theme['id'].'_qi1").css("border-right-color","#'.$option_data['question']['border-color'].'");':'')."
										".(isset($option_data['question']['border-color']) && !empty($option_data['question']['border-color'])?'jQuery("#qp'.$theme['id'].'_qi1").css("border-bottom-color","#'.$option_data['question']['border-color'].'");':'')."
										".(isset($option_data['question']['border-color']) && !empty($option_data['question']['border-color'])?'jQuery("#qp'.$theme['id'].'_qi1").css("border-left-color","#'.$option_data['question']['border-color'].'");':'')."
										".(isset($option_data['button']['border-color']) && !empty($option_data['button']['border-color'])?'jQuery(".qp'.$theme['id'].'_btn").css("border-style","solid");':'')."
										".(isset($option_data['button']['border-color']) && !empty($option_data['button']['border-color'])?'jQuery(".qp'.$theme['id'].'_btn").css("border-color","#'.$option_data['button']['border-color'].'");':'')."
										".(isset($option_data['button']['border-color']) && !empty($option_data['button']['border-color'])?'jQuery(".qp'.$theme['id'].'_btn").css("borderTopColor","#'.$option_data['button']['border-color'].'");':'')."
										".(isset($option_data['button']['border-color']) && !empty($option_data['button']['border-color'])?'jQuery(".qp'.$theme['id'].'_btn").css("borderBottomColor","#'.$option_data['button']['border-color'].'");':'')."
										".(isset($option_data['button']['border-color']) && !empty($option_data['button']['border-color'])?'jQuery(".qp'.$theme['id'].'_btn").css("borderLeftColor","#'.$option_data['button']['border-color'].'");':'')."
										".(isset($option_data['question']['border-color']) && !empty($option_data['question']['border-color'])?'jQuery("#qp'.$theme['id'].'_qi1").css("border-style","solid");':'')."
										".(isset($option_data['question']['border-radius']) && !empty($option_data['question']['border-radius'])?'jQuery("#qp'.$theme['id'].'_q1").css("border-radius","'.$option_data['question']['border-radius'].'");':'')."
										".(isset($option_data['question']['border-radius']) && !empty($option_data['question']['border-radius'])?'jQuery("#qp'.$theme['id'].'_qi1").css("borderRadius",parseInt("'.$option_data['question']['border-radius'].'"));':'')."
										".(isset($option_data['question']['background-color']) && !empty($option_data['question']['background-color'])?'jQuery("#qp'.$theme['id'].'_qi1").css("background-color","#'.$option_data['question']['background-color'].'");':'')."
										".(isset($option_data['answer']['font-type']) && !empty($option_data['answer']['font-type'])?'jQuery(".qp'.$theme['id'].'_t").css("font-family","'.$option_data['answer']['font-type'].'");':'')."
										".(isset($option_data['answer']['font-size']) && !empty($option_data['answer']['font-size'])?'jQuery(".qp'.$theme['id'].'_t").css("font-size","'.$option_data['answer']['font-size'].'");':'')."
										".(isset($option_data['answer']['font-color']) && !empty($option_data['answer']['font-color'])?'jQuery(".qp'.$theme['id'].'_t").css("color","#'.$option_data['answer']['font-color'].'");':'')."
										".(isset($option_data['answer']['border-color']) && !empty($option_data['answer']['border-color'])?'jQuery("#qp'.$theme['id'].'_ao1").css("border","#'.$option_data['answer']['border-color'].' solid '.$option_data['answer']['border-width'].'");':'')."
										".(isset($option_data['answer']['border-radius']) && !empty($option_data['answer']['border-radius'])?'jQuery("#qp'.$theme['id'].'_ao1").css("borderRadius",parseInt("'.$option_data['answer']['border-radius'].'"));':'')."
										".(isset($option_data['answer']['background-color']) && !empty($option_data['answer']['background-color'])?'jQuery("#qp'.$theme['id'].'_ao1").css("background-color","#'.$option_data['answer']['background-color'].'");':'')."
										".(isset($option_data['button']['font-type']) && !empty($option_data['button']['font-type'])?'jQuery(".qp'.$theme['id'].'_btn").css("font-family","'.$option_data['button']['font-type'].'");':'')."
										".(isset($option_data['button']['font-size']) && !empty($option_data['button']['font-size'])?'jQuery(".qp'.$theme['id'].'_btn").css("font-size","'.$option_data['button']['font-size'].'");':'')."
										".(isset($option_data['button']['font-color']) && !empty($option_data['button']['font-color'])?'jQuery(".qp'.$theme['id'].'_btn").css("color","#'.$option_data['button']['font-color'].'");':'')."
										".(isset($option_data['button']['border-color']) && !empty($option_data['button']['border-color'])?'jQuery(".qp'.$theme['id'].'_btn").css("border-width","'.$option_data['button']['border-width'].'");':'')."
										".(isset($option_data['button']['border-color']) && !empty($option_data['button']['border-color'])?'jQuery(".qp'.$theme['id'].'_btn").css("border-style","solid");':'')."
										".(isset($option_data['button']['border-color']) && !empty($option_data['button']['border-color'])?'jQuery(".qp'.$theme['id'].'_btn").css("border-color","#'.$option_data['button']['border-color'].'");':'')."
										".(isset($option_data['button']['border-color']) && !empty($option_data['button']['border-color'])?'jQuery(".qp'.$theme['id'].'_btn").css("borderTopColor","#'.$option_data['button']['border-color'].'");':'')."
										".(isset($option_data['button']['border-color']) && !empty($option_data['button']['border-color'])?'jQuery(".qp'.$theme['id'].'_btn").css("borderBottomColor","#'.$option_data['button']['border-color'].'");':'')."
										".(isset($option_data['button']['border-color']) && !empty($option_data['button']['border-color'])?'jQuery(".qp'.$theme['id'].'_btn").css("borderLeftColor","#'.$option_data['button']['border-color'].'");':'')."
										".(isset($option_data['button']['border-color']) && !empty($option_data['button']['border-color'])?'jQuery(".qp'.$theme['id'].'_btn").css("borderRightColor","#'.$option_data['button']['border-color'].'");':'')."
										".(isset($option_data['button']['border-radius']) && !empty($option_data['button']['border-radius'])?'jQuery(".qp'.$theme['id'].'_btn").css("borderRadius",parseInt("'.$option_data['button']['border-radius'].'"));':'')."
										
										".(isset($option_data['button']['background-color']) && !empty($option_data['button']['background-color'])?'jQuery(".qp'.$theme['id'].'_btn").css("background-color","#'.$option_data['button']['background-color'].'");':'')."
									});
								</script>"; ?>
								<?php if(isset($option_data['button']['border-color']) && isset($option_data['button']['border-width'])) : ?>
								<style>
								<?php echo "body a .qp".$theme['id']."_btn"; ?> {
									border:<?php echo str_replace('!important','',$option_data['button']['border-width']); ?> solid <?php echo "#".str_replace('!important','',$option_data['button']['border-color']); ?> !important;
								}
								</style>
								<?php endif; ?>
								<?php if(isset($option_data['poll']['border-color']) && isset($option_data['poll']['border-width'])) : ?>
								<style>
								<?php echo "body #qp".$theme['id']."_b1"; ?> {
									border:<?php echo str_replace('!important','',$option_data['poll']['border-width']); ?> solid <?php echo "#".str_replace('!important','',$option_data['poll']['border-color']); ?>;
									border-radius: <?php echo str_replace('!important','',$option_data['poll']['border-radius']); ?>;
								}
								</style>
								<?php endif; ?>
						</div>
						<a class="id-select" data-id="<?php echo $theme['id'];?>" data-options='<?php echo $theme["serialized_data"] ?>' href="javascript:void(0)">Select</a>
					<?php if(($userMembership && !empty($userMembership) &&  !$userMembership->customize_themes) || $admin){ ?>
						<a class="id-customize" data-id="<?php echo $theme['id'];?>" data-options='<?php echo $theme["serialized_data"] ?>' href="javascript:void(0)">Customize</a>
						<?php } ?>
					</div>
				<?php } ?> 
				<div class="clear"></div>
				<script>
					jQuery(document).ready(function(){
						jQuery('.id-select').click(function(){ 
							jQuery('#poll_question h3').removeAttr('style');
									jQuery('#poll_answer').removeAttr('style');
									jQuery('#poll_button .btn').removeAttr('style');
									jQuery('#poll_widget').removeAttr('style');
									
									jQuery('input[name="theme"]').val(jQuery(this).data("id"));
									var options=unserialize(jQuery(this).data( "options" ));
									var myJsonString = options.poll['background-color'];

									if(jQuery('#poll_top_of_bar_logos a img').attr('src') == false) {
										jQuery('#poll_top_of_bar_logos').addClass('theme-without-logo');
									} 

									/*JS for top bar css on selecting theme*/
									var borderStyleText 		= '' ;
									var borderRadius 		= '' ;
									var backgroundColorText	= ''  ;

									if(typeof options.top_bar != "undefined") {   
										borderStyleText 	= "border:" + options.top_bar['border-width'] + ' solid' + ' #'+options.top_bar['border-color'] + ';' ;
										borderRadius 		= 'border-radius: '+ parseInt(options.top_bar['border-radius']) + 'px !important' + ';' ;
										backgroundColorText	= 'background-color: #'+options.top_bar['background-color'] + ';'  ;
									} else {
										borderStyleText 	= "border: 0 !important;" ;
										borderRadius 		= "border-radius: 0 !important;" ;
										backgroundColorText	= "background-color: transparent !important;" ;
									}
									var allCss = borderStyleText +  borderRadius  + backgroundColorText ; 
									jQuery('#poll_top_of_bar_logos').css("cssText", allCss); 
									/*JS for top bar css on selecting theme*/
									
									/***** change question font type *****/
									jQuery('#question_font_type').val(options.question['font-type']);
									jQuery('#question_font_type').trigger('chosen:updated');
									jQuery('#question_font_type').trigger('change');
									/***** change question font size *****/
									var size=options.question['font-size'].replace ( /[^\d.]/g, '' ).trim();
									jQuery('#poll_question h3').attr('style', function(i,s) { return s + 'font-size:'+size+'px !important;'});
									hs=jQuery('#slider-range-min');
									hs.slider( "option", "value", size);
									/***** change question font color *****/
									jQuery('#question_font_color_picker').colpickSetColor("#"+options.question['font-color'],true);
									/***** change question background color *****/
									jQuery('#question_background_color_picker').colpickSetColor("#"+options.question['background-color'],true);
									/***** change question border color *****/
									jQuery('#question_border_color_picker').colpickSetColor("#"+options.question['border-color'],true);
									/***** change question border width *****/
									var size=options.question['border-width'].replace ( /[^\d.]/g, '' ).trim();
									jQuery('#poll_question h3').attr('style', function(i,s) { return s + ';border-width:'+size+'px !important; '});
									hs=jQuery('#slider-range-min4');
									hs.slider('option', 'value',size);
									/***** change question border radius *****/
									var size=options.question['border-radius'].replace ( /[^\d.]/g, '' ).trim();
									hs=jQuery('#slider-range-min5');
									jQuery('#poll_question h3').attr('style', function(i,s) { return s + ';border-radius:'+size+'px !important;' });
									hs.slider('option', 'value',size);
									/***** change answer font type *****/
									jQuery('#answer_font_type').val(options.answer['font-type']);
									jQuery('#answer_font_type').trigger('chosen:updated');
									jQuery('#answer_font_type').trigger('change');
									/***** change answer font size *****/
									var size=options.answer['font-size'].replace ( /[^\d.]/g, '' ).trim();
									jQuery('#poll_answers li label span').attr('style', function(i,s) { return s + 'font-size:'+size+'px !important; '});
									hs=jQuery('#slider-range-min8');
									hs.slider('option', 'value',size);
									/***** change answer font color *****/
									jQuery('#answer_font_color_picker').colpickSetColor("#"+options.answer['font-color'],true);
									/***** change answer background color *****/
									jQuery('#answer_background_color_picker').colpickSetColor("#"+options.answer['background-color'],true);
									/***** change answer border color *****/
									jQuery('#answer_border_color_picker').colpickSetColor("#"+options.answer['border-color'],true);
									var color = options.answer['border-color'].replace('!important','').trim();
									jQuery('#poll_answers').attr('style', function(i,s) { return s + 'border-color:'+color+' !important; '});
									/***** change answer border width *****/
									var size=options.answer['border-width'].replace ( /[^\d.]/g, '' ).trim();
									jQuery('#poll_answers').attr('style', function(i,s) { return s + 'border-width:'+size+'px !important; '});
									hs=jQuery('#slider-range-min9');
									hs.slider('option', 'value',size);
									/***** change answer border radius *****/
									var size=options.answer['border-radius'].replace ( /[^\d.]/g, '' ).trim();
									jQuery('#poll_answers').attr('style', function(i,s) { return s + 'border-radius:'+size+'px !important; '});
									hs=jQuery('#slider-range-min10');
									hs.slider('option', 'value',size);
									/***** change button font type *****/
									jQuery('#button_font_type').val(options.button['font-type']);
									jQuery('#button_font_type').trigger('chosen:updated');
									jQuery('#button_font_type').trigger('change');
									/***** change button font size *****/
									var size=options.button['font-size'].replace ( /[^\d.]/g, '' ).trim();
									jQuery('#poll_button .btn').attr('style', function(i,s) { return s + ';font-size:'+size+'px !important; '});
									hs=jQuery('#slider-range-min1');
									hs.slider('option', 'value',size);
									/***** change button font color *****/
									jQuery('#button_font_color_picker').colpickSetColor("#"+options.button['font-color'],true);
									/***** change button background color *****/
									jQuery('#button_background_color_picker').colpickSetColor("#"+options.button['background-color'],true);
									/***** change button border color *****/
									jQuery('#button_border_color_picker').colpickSetColor("#"+options.button['border-color'],true);
									/***** change button border width *****/
									var size=options.button['border-width'].replace ( /[^\d.]/g, '' ).trim();
									jQuery('#poll_button .btn').attr('style', function(i,s) { return s + 'border-width:'+size+'px !important; '});
									hs=jQuery('#slider-range-min6');
									hs.slider('option', 'value',size);
									/***** change button border radius *****/
									var size=options.button['border-radius'].replace ( /[^\d.]/g, '' ).trim();
									jQuery('#poll_button .btn').attr('style', function(i,s) { return s + 'border-radius:'+size+'px !important; '});
									hs=jQuery('#slider-range-min7');
									hs.slider('option', 'value',size);
									/***** change poll background color *****/
									if(options.poll['background-color'] == "") {
										options.poll['background-color'] = "ffffff";
									}
									jQuery('#poll_background_color_picker').colpickSetColor("#"+options.poll['background-color'],true);
									/***** change poll border color *****/
									jQuery('#poll_border_color_picker').colpickSetColor("#"+options.poll['border-color'],true);
									/***** change poll border width *****/
									var size=options.poll['border-width'].replace ( /[^\d.]/g, '' ).trim();
									// jQuery('#poll_widget').attr('style', function(i,s) { return s + 'border-width:'+size+'px !important; '});
									jQuery('#poll_widget').attr('style', function(i,s) { return s + 'border-style:solid !important; '});
									hs=jQuery('#slider-range-min2');
									hs.slider('option', 'value',size);
									/***** change poll border radius *****/
									var size=options.poll['border-radius'].replace ( /[^\d.]/g, '' ).trim();
									jQuery('#poll_widget').attr('style', function(i,s) { return s + 'border-radius:'+size+'px !important; '});
									hs=jQuery('#slider-range-min3');
									hs.slider('option', 'value',size);
									/**** change poll background image *****/
									jQuery('.preview-img').remove();
									jQuery('#poll_background_image').remove();
									if('background-image' in options.poll){
										jQuery("<div class='preview-wrapper'><span>&#10799;</span><img alt='"+options.poll['background-image']+"' class='preview-img' width='150' src='<?php echo $res['url']?>/admin/images/themebg/"+options.poll['background-image']+"'/><input id='poll_background_image' type='hidden' name='poll_background_image' value='"+options.poll['background-image']+"'></div>" ).insertAfter( '#poll_background_image_file' );
										jQuery('#poll_widget').css('background','url("<?php echo $res['url']?>/admin/images/themebg/'+options.poll['background-image']+'") repeat scroll left top / 100% auto transparent');
									}
									/**** change poll background image *****/
									if('background-image' in options.poll){
										jQuery('#poll_widget').attr('style', function(i,s) { return s 
											+ "background:url(<?php echo 
											$res['url']?>/admin/images/themebg/"+options.poll['background-image']+") repeat scroll left top / 100% auto transparent !important;"});
									}
								
									if(options.answer['border-color'] ){
										jQuery('#poll_answers').attr('style', function(i,s) { return s + 'border:'+ options.answer['border-width'] +' solid #'+ options.answer['border-color'] +' !important;' });
									}
									if(options.button['border-color'] || options.button['width'] ){
										jQuery('#poll_button .btn-widget').attr('style', function(i,s) { return s + 'border-color:#'+ options.button['border-color']+' !important;' });
										
										jQuery('#poll_button .btn-widget').attr('style', function(i,s) { return s + 'background-color:#'+ options.button['background-color']+' !important;' });
									}
								if(options.poll['bottom-bar-color']) {
									var botom_color = options.poll['bottom-bar-color'].replace(' !important','');
									var trim_botom_color = botom_color.trim(); 
									jQuery('#poll_bottom_bar_color_picker').colpickSetColor(trim_botom_color,true);
								}

						});
						/*Select button click event end*/

						jQuery('#question_font_type').on('change', function(evt, params) { 
							if(jQuery(this).val() === '' || jQuery(this).val() === null) {
								jQuery("#poll_question h3").css("cssText", "font-family:arial ");
								jQuery("#question_font_type_change").val('arial !important');
								
							} else {
								var questionFont = jQuery(this).val();
								questionFont = questionFont.replace(' !important','');
								jQuery("#poll_question h3").css("font-family",questionFont);
								jQuery("#question_font_type_change").val(jQuery(this).val()+' !important');
							}
						});


						jQuery('#answer_font_type').on('change', function(evt, params) { 
							if(jQuery(this).val() === '' || jQuery(this).val() === null) {
								jQuery("#poll_answers li label span").css("cssText", "font-family:arial ");
								jQuery("#button_font_type_change").val('arial !important');
							} else {
								var answerFont = jQuery(this).val();
								answerFont = answerFont.replace(' !important', ''); 
								jQuery("#poll_answers li label span").css("font-family",answerFont);
								jQuery("#button_font_type_change").val(jQuery(this).val()+' !important');
							}

						});


						jQuery('#button_font_type').on('change', function(evt, params) {
							if(jQuery(this).val() === '' || jQuery(this).val() === null) {
								jQuery("#poll_button .btn.btn-widget").css("cssText", "font-family:arial ");
							} else {
								var buttonFont = jQuery(this).val();
								buttonFont = buttonFont.replace(' !important', '');
								jQuery("#poll_button .btn.btn-widget").css("font-family",buttonFont);
							}
						});




						jQuery('.id-customize').click(function(){
							jQuery(this).siblings(".id-select").trigger('click');
							jQuery('button[data-id="customize"].mytabs').trigger('click');
						});
						jQuery(document).on('click', 'div.preview-wrapper span',function() {
							jQuery('#upload_image').val('');
							jQuery('.preview-wrapper').remove();
							jQuery('#poll_widget').css('background',jQuery("#poll_background_color").val());
						});
					});
				</script>
            </div>
				<script>
					(function(jQuery){
						jQuery(document).ready(function(){
							jQuery('#theme').mCustomScrollbar({
								theme:'dark',
							});
						})
					})(jQuery);
				</script>
            <?php } ?>
            <div class="clear"></div>
            <div id="customize" class="tabbed">
              <input type="hidden" name="theme" value="" id="poll_theme_value">
              <br>
              <div class="form-group">
                <label for="question_background_color">Top Of Bar background Color</label>
                <div id="top_bar_background_color_picker">
					<div class="set_background">
						<input type="checkbox"  title="Transparent background" id="top_bar_background_color_transparent_checkbox" value="yes" name="top_bar_background_color_transparent_checkbox"  />
						<label>Transparent</label>
					</div>
                </div>
				<input type='hidden' class='color form-control' name='top_bar_background_color' id='top_bar_background_color' value='#333333 !important'>
				<script>
					jQuery(function() {
						jQuery('#top_bar_background_color_picker').colpick({
							flat:true,
							layout:'hex',
							submit:0,
							color:'2094d9',
							onChange:function(hsb,hex,rgb,el,bySetColor){
								jQuery("#top_bar_background_color").val('#' + hex+" !important");
								jQuery("#poll_top_of_bar_logos").css("background","#"+ hex);
								
							}
						});
						
						
					});
				</script>
              </div>
              <div class="form-group">
					<label for="question_border_color">Top Of Bar border Color</label>
					<div id="top_bar_border_color_picker"></div>
					<input type='hidden' class='color form-control' name='top_bar_border_color' id='top_bar_border_color' value='#ffffff !important'>
					<script>
						jQuery(function() {
							jQuery('#top_bar_border_color_picker').colpick({
								flat:true,
								layout:'hex',
								submit:0,
								onChange:function(hsb,hex,rgb,el,bySetColor){
								jQuery("#top_bar_border_color").val('#' + hex+" !important");
								jQuery('#poll_top_of_bar_logos').attr('style', function(i,s) { return s + 'border-color:#'+hex+' !important;' });
								jQuery('#poll_top_of_bar_logos').attr('style', function(i,s) { return s + 'border-style:solid !important;' });
								}
							});
						});
					</script>
			</div>
            
            <div class='form-group'>
				  <label for='question_border_width' >Top Of Bar border width</label>
					<div id='slider-range-min20'></div>
					<input type='text' name='top_bar_border_width' id='top_bar_border_width' class='slider-amount' readonly style='border:0; color:#f6931f; font-weight:bold;'>
					<script>
						jQuery(function() {
							jQuery( '#slider-range-min20' ).slider({
								range: 'min',
								value: 0,
								min: 0,
								max: 10,
								change: function( event, ui ) {
									jQuery( '#top_bar_border_width' ).val( ui.value + 'px' );
								},
								slide: function( event, ui ) {
									jQuery( '#top_bar_border_width' ).val( ui.value + 'px' );
									jQuery('#poll_top_of_bar_logos').css('border-width', ui.value + 'px' );
								}
							});
							jQuery( '#top_bar_border_width' ).val( jQuery( '#slider-range-min20' ).slider( 'value' ) + 'px'  );
						});
					</script>

			</div>
			<div class='form-group'>
				  <label for='question_border_radius'>Top Of Bar border radius</label>
					<div id='slider-range-min21'></div>
					<input type='text' name='top_var_border_radius' id='top_var_border_radius' class='slider-amount' readonly style='border:0; color:#f6931f; font-weight:bold;'>
					<script>
						jQuery(function() {
							jQuery( '#slider-range-min21' ).slider({
								range: 'min',
								value: 0,
								min: 0,
								max: 50,
								change: function( event, ui ) {
									jQuery( '#top_var_border_radius' ).val( ui.value + 'px !important' );
								},
								slide: function( event, ui ) {
									jQuery( '#top_var_border_radius' ).val( ui.value + 'px !important' );
									jQuery('#poll_top_of_bar_logos').css('border-radius',ui.value + 'px');
								}
							});
							jQuery( '#top_var_border_radius' ).val( jQuery( '#slider-range-min21' ).slider( 'value' ) + 'px'  );
						});
					</script>
			</div>
              <div class='form-group'>
				  <label for='question_font_type'>Question font type</label>
					<select name='question_font_type' id='question_font_type'>
						<option value='arial !important'>Arial</option>
						<option value='courier new !important'>Courier New</option>
						<option value='georgia !important'>Georgia</option>
						<option value='sans-serif !important'>Sans-Serif</option>
						<option value='tohama !important'>Tohama</option>
						<option value='times new !important'>Times New</option>
						<option value='terbutchet !important'>Terbutchet</option>
						<option value='verdana !important'>Verdana</option>
					</select>
				</div>
			  <div class='form-group'>
				  <label for='question_font_size' class='col-sm-3 control-label'>Question font size</label>
					<div id='slider-range-min'></div>
					<input type='text' name='question_font_size' id='question_font_size'  class='slider-amount' readonly style='border:0; color:#f6931f; font-weight:bold;'>
					<script>
						jQuery(function() {
							if(jQuery('#slider-range-min').size()>0) {
							jQuery( '#slider-range-min' ).slider({
								range: 'min',
								value: 18,
								min: 6,
								max: 30,
								change: function( event, ui ) {
									jQuery( '#question_font_size' ).val( ui.value + 'px' );
								},
								slide: function( event, ui ) {
									jQuery( '#question_font_size' ).val( ui.value + 'px' );
									jQuery("#poll_question h3").css("font-size", ui.value + 'px' );
								}
							});
							}
							jQuery( '#question_font_size' ).val( jQuery( '#slider-range-min' ).slider( 'value' ) + 'px'  );
						});
					</script>
			  </div>
			 
			  <div class="form-group font-color">
                <label for="font">Question Font Color</label>
                <div id="question_font_color_picker"></div>
				<input type='hidden' class='color form-control' name='question_font_color' id='question_font_color' value='#000000 !important'>
				<script>
					jQuery(function() {
						jQuery('#question_font_color_picker').colpick({
							flat:true,
							layout:'hex',
							submit:0,
							color:'ffffff',
							onChange:function(hsb,hex,rgb,el,bySetColor){
								jQuery("#question_font_color").val('#' + hex+" !important");
								jQuery("#poll_question h3").css("color","#" + hex);
							}
						});
					});
				</script>
              </div>

              <div class="form-group">
                <label for="question_background_color">Question background Color</label>
                <div id="question_background_color_picker">
					<div class="set_background">
						<input type="checkbox" title="Transparent background" id="question_background_color_transparent_checkbox" value="yes" name="question_background_color_transparent_checkbox"  />
						<label>Transparent</label>
					</div>
				
                </div>
                
				<input type='hidden' class='color form-control' name='question_background_color' id='question_background_color' value='#ffffff !important'>
				<script>
					jQuery(function() {
						jQuery('#question_background_color_picker').colpick({
							flat:true,
							layout:'hex',
							submit:0,
							color:'2094d9',
							onChange:function(hsb,hex,rgb,el,bySetColor){
								jQuery("#question_background_color").val('#' + hex+" !important");
								jQuery("#poll_question h3").css("background","#"+ hex);
								jQuery("#poll_question h3").css("padding",'10px');
							}
						});
					});
				</script>
				
				
              </div>
              <div class="form-group">
                <label for="question_border_color">Question border Color</label>
                <div id="question_border_color_picker"></div>
				<input type='hidden' class='color form-control' name='question_border_color' id='question_border_color' value='#ffffff !important'>
				<script>
					jQuery(function() {
						jQuery('#question_border_color_picker').colpick({
							flat:true,
							layout:'hex',
							submit:0,
							onChange:function(hsb,hex,rgb,el,bySetColor){
								jQuery("#question_border_color").val('#' + hex+" !important");
								jQuery("#poll_question h3").css("padding",'10px');
							    jQuery("#poll_question h3").css("border","#"+hex+' solid '+jQuery("#question_border_width").val());
							}
						});
					});
				</script>
              </div>
              <div class='form-group'>
				  <label for='question_border_width' >Question border width</label>
					<div id='slider-range-min4'></div>
					<input type='text' name='question_border_width' id='question_border_width' class='slider-amount' readonly style='border:0; color:#f6931f; font-weight:bold;'>
					<script>
						jQuery(function() {
							jQuery( '#slider-range-min4' ).slider({
								range: 'min',
								value: 0,
								min: 0,
								max: 10,
								change: function( event, ui ) {
									jQuery( '#question_border_width' ).val( ui.value + 'px' );
								},
								slide: function( event, ui ) {
									jQuery( '#question_border_width' ).val( ui.value + 'px' );
									jQuery('#poll_question h3').css('border-width', ui.value + 'px' );
								}
							});
							jQuery( '#question_border_width' ).val( jQuery( '#slider-range-min4' ).slider( 'value' ) + 'px'  );
						});
					</script>

			  </div>
			  <div class='form-group'>
				  <label for='question_border_radius'>Question border radius</label>
					<div id='slider-range-min5'></div>
					<input type='text' name='question_border_radius' id='question_border_radius' class='slider-amount' readonly style='border:0; color:#f6931f; font-weight:bold;'>
					<script>
						jQuery(function() {
							jQuery( '#slider-range-min5' ).slider({
								range: 'min',
								value: 6,
								min: 0,
								max: 50,
								change: function( event, ui ) {
									jQuery( '#question_border_radius' ).val( ui.value + 'px !important' );
								},
								slide: function( event, ui ) {
									jQuery( '#question_border_radius' ).val( ui.value + 'px !important' );
									jQuery('#poll_question h3').css('border-radius',ui.value + 'px');
								}
							});
							jQuery( '#question_border_radius' ).val( jQuery( '#slider-range-min5' ).slider( 'value' ) + 'px'  );
						});
					</script>

			  </div>
			  <div class='form-group'>
				  <label for='answer_font_type'>Answer font type</label>

					  <select name='answer_font_type' id='answer_font_type'>
						<option value='arial !important'>Arial</option>
						<option value='courier new !important'>Courier New</option>
						<option value='georgia !important'>Georgia</option>
						<option value='sans-serif !important'>Sans-Serif</option>
						<option value='tohama !important'>Tohama</option>
						<option value='times new !important'>Times New</option>
						<option value='terbutchet !important'>Terbutchet</option>
						<option value='verdana !important'>Verdana</option>
					  </select>
			  </div>
			  <div class='form-group'>
				  <label for='answer_font_size'>Answer font size</label>
					<div id='slider-range-min8'></div>
					<input type='text' name='answer_font_size' id='answer_font_size' class='slider-amount' readonly style='border:0; color:#f6931f; font-weight:bold;'>
					<script>
						jQuery(function() {
							jQuery( '#slider-range-min8' ).slider({
								range: 'min',
								value: 13,
								min: 6,
								max: 30,
								change: function( event, ui ) {
									jQuery( '#answer_font_size' ).val( ui.value + 'px !important' );
								},
								slide: function( event, ui ) {
									jQuery( '#answer_font_size' ).val( ui.value + 'px !important' );
									jQuery("#poll_answers li label span").css("font-size",ui.value + 'px' );
								}
							});
							jQuery( '#answer_font_size' ).val( jQuery( '#slider-range-min8' ).slider( 'value' ) + 'px !important'  );
						});
					</script>
  			  </div>
  			  <div class="form-group">
                <label for="answer_font_color">Answer font color</label>
                <div id="answer_font_color_picker"></div>
				<input type='hidden' class='color form-control' name='answer_font_color' id='answer_font_color' value='#666666 !important'>
				<script>
					jQuery(function() {
						jQuery('#answer_font_color_picker').colpick({
							flat:true,
							layout:'hex',
							submit:0,
							color:'ffffff',
							onChange:function(hsb,hex,rgb,el,bySetColor){
								jQuery("#answer_font_color").val('#' + hex+" !important");
								jQuery("#poll_answers li label span").css("color",'#' + hex);
							}
						});
					});
				</script>
              </div>
              <script>
              jQuery(function(){
					   function execute_script(){
						  
						  jQuery('#top_bar_background_color_transparent_checkbox').on('ifChecked', function(event){
							    jQuery("#poll_top_of_bar_logos").css("background","transparent");
								jQuery("#top_bar_background_color").val('transparent !important');
							});
							jQuery('#top_bar_background_color_transparent_checkbox').on('ifUnchecked', function(event){
								jQuery("#poll_top_of_bar_logos").css("background","#333");
								jQuery("#top_bar_background_color").val('#333 !important');
									
							});

						  jQuery('#question_background_color_transparent_checkbox').on('ifChecked', function(event){
							    jQuery("#poll_question h3").css("background","transparent");
								jQuery("#question_background_color").val('transparent !important');
							});
							jQuery('#question_background_color_transparent_checkbox').on('ifUnchecked', function(event){
								jQuery("#poll_question h3").css("background","none");
								jQuery("#question_background_color").val('none !important');
									
							});
							
							
						  jQuery('#answer_background_color_transparent_checkbox').on('ifChecked', function(event){
							    jQuery("#poll_answers").css("background","transparent");
								jQuery("#answer_background_color").val('transparent !important');
							});
							jQuery('#answer_background_color_transparent_checkbox').on('ifUnchecked', function(event){
								jQuery("#poll_answers").css("background","none");
								jQuery("#answer_background_color").val('none !important');
									
							});
							
						  jQuery('#poll_background_color_transparent_checkbox').on('ifChecked', function(event){
							    jQuery("#poll_widget").css("background","transparent");
								jQuery("#poll_background_color").val('transparent !important');
							});
							jQuery('#poll_background_color_transparent_checkbox').on('ifUnchecked', function(event){
								jQuery("#poll_widget").css("background","none");
								jQuery("#poll_background_color").val('none !important');
									
							});
							
							
						  jQuery('#button_background_color_transparent_checkbox').on('ifChecked', function(event){
							    jQuery("#poll_button .btn.btn-widget").css("background","transparent");
								jQuery("#button_background_color").val('transparent !important');
							});
							jQuery('#button_background_color_transparent_checkbox').on('ifUnchecked', function(event){
								jQuery("#poll_widget").css("background","none");
								jQuery("#poll_button .btn.btn-widget").val('none !important');
									
							});
						
					   };
					   window.setTimeout( execute_script, 5000 ); // 5 seconds
					});
					
              </script>
              <div class="form-group">
                <label for="answer_background_color">Answer background color</label>
                <div id="answer_background_color_picker">
					<div class="set_background">
						<input type="checkbox" title="Transparent background" id="answer_background_color_transparent_checkbox" value="yes" name="answer_background_color_transparent_checkbox"  />
						<label>Transparent</label>
					</div>
                </div>
				<input type='hidden' class='color form-control' name='answer_background_color' id='answer_background_color' value=''>
				<script>
					jQuery(function() {
						jQuery('#answer_background_color_picker').colpick({
							flat:true,
							layout:'hex',
							submit:0,
							color:'2094d9',
							onChange:function(hsb,hex,rgb,el,bySetColor){
								jQuery("#answer_background_color").val('#' + hex);
								jQuery("#poll_answers").css("background",'#' + hex);
								jQuery("#poll_answers").css("margin",'auto 20px');
							}
						});
					});
				</script>
				
				
              </div>
              <div class="form-group">
                <label for="answer_border_color">Answer border color</label>
                <div id="answer_border_color_picker"></div>
				<input type='hidden' class='color form-control' name='answer_border_color' id='answer_border_color' value=''>
				<script>
					jQuery(function() {
						jQuery('#answer_border_color_picker').colpick({
							flat:true,
							layout:'hex',
							submit:0,
							onChange:function(hsb,hex,rgb,el,bySetColor){
								jQuery("#answer_border_color").val('#' + hex);
								jQuery("#poll_answers").css("border","#"+hex+' solid '+jQuery("#answer_border_width").val());
								jQuery("#poll_answers").css("margin",'auto 20px');
							}
						});
					});
				</script>
              </div>
              <div class='form-group'>	
				  <label for='answer_border_width' >Answer border width</label>
					<div id='slider-range-min9'></div>
					<input type='text' name='answer_border_width' id='answer_border_width' class='slider-amount' readonly style='border:0; color:#f6931f; font-weight:bold;'>
					<script>
						jQuery(function() {
							jQuery( '#slider-range-min9' ).slider({
								range: 'min',
								value: 0,
								min: 0,
								max: 10,
								change: function( event, ui ) {
									jQuery( '#answer_border_width' ).val( ui.value + 'px' );
								},
								slide: function( event, ui ) {
									jQuery( '#answer_border_width' ).val( ui.value + 'px' );
									jQuery("#poll_answers").css("border",jQuery("#answer_border_color").val()+' solid '+ui.value + 'px');
									jQuery("#poll_answers").css("margin",'auto 20px');
								
									
								}
							});
							jQuery( '#answer_border_width' ).val( jQuery( '#slider-range-min9' ).slider( 'value' ) + 'px'  );
						});
					</script>

			  </div>
			  <div class='form-group'>
				  <label for='answer_border_radius'>Answer border radius</label>
					<div id='slider-range-min10'></div>
					<input type='text' name='answer_border_radius' id='answer_border_radius' class='slider-amount' readonly style='border:0; color:#f6931f; font-weight:bold;'>
					<script>
						jQuery(function() {
							jQuery( '#slider-range-min10' ).slider({
								range: 'min',
								value: 6,
								min: 0,
								max: 50,
								change: function( event, ui ) {
									jQuery( '#answer_border_radius' ).val( ui.value + 'px !important' );
								},
								slide: function( event, ui ) {
									jQuery( '#answer_border_radius' ).val( ui.value + 'px !important' );
									jQuery('#poll_answers').css('border-radius',ui.value + 'px');
								}
							});
							jQuery( '#answer_border_radius' ).val( jQuery( '#slider-range-min10' ).slider( 'value' ) + 'px !important'  );
						});
					</script>
			  </div>
			  <div class='form-group'>
				  <label for='button_font_type'>Button font type</label>
				  <select name='button_font_type' id='button_font_type'>
					<option value='arial !important'>Arial</option>
						<option value='courier new !important'>Courier New</option>
						<option value='georgia !important'>Georgia</option>
						<option value='sans-serif !important'>Sans-Serif</option>
						<option value='tohama !important'>Tohama</option>
						<option value='times new !important'>Times New</option>
						<option value='terbutchet !important'>Terbutchet</option>
						<option value='verdana !important'>Verdana</option>
				  </select>
			  </div>
			  <div class='form-group'>
				  <label for='button_font_size' >Button font size</label>
					<div id='slider-range-min1'></div>
					<input type='text' name='button_font_size' id='button_font_size' class='slider-amount' readonly style='border:0; color:#f6931f; font-weight:bold;'>
					<script>
						jQuery(function() {
							jQuery( '#slider-range-min1' ).slider({
								range: 'min',
								value: 14,
								min: 6,
								max: 30,
								change: function( event, ui ) {
									jQuery( '#button_font_size' ).val( ui.value + 'px !important' );
								},
								slide: function( event, ui ) {
									jQuery( '#button_font_size' ).val( ui.value + 'px !important' );
									jQuery("#poll_button .btn.btn-widget").css('font-size',ui.value + 'px');
								}
							});
							jQuery( '#button_font_size' ).val( jQuery( '#slider-range-min1' ).slider( 'value' ) + 'px !important'  );
						});
					</script>

			  </div>
			  <div class="form-group">
                <label for="button_font_color">Button font color</label>
                <div id="button_font_color_picker"></div>
				<input type='hidden' class='color form-control' name='button_font_color' id='button_font_color' value=''>
				<script>
					jQuery(function() {
						jQuery('#button_font_color_picker').colpick({
							flat:true,
							layout:'hex',
							submit:0,
							color:'ffffff',
							onChange:function(hsb,hex,rgb,el,bySetColor){
								jQuery("#button_font_color").val('#' + hex+" !important");
								jQuery("#poll_button .btn.btn-widget").css('color','#' + hex);
							}
						});
					});
				</script>
              </div>
              <div class="form-group">
                <label for="button_background_color">
					Button background color
				</label>
                <div id="button_background_color_picker">
					<div class="set_background">
						<input type="checkbox" title="Transparent background" id="button_background_color_transparent_checkbox" value="yes" name="button_background_color_transparent_checkbox"  />
						<label>Transparent</label>
					</div>
                </div>
				<input type='hidden' class='color form-control' name='button_background_color' id='button_background_color' value=''>
				<script>
					jQuery(function() {
						jQuery('#button_background_color_picker').colpick({
							flat:true,
							layout:'hex',
							submit:0,
							color: '104A6C',
							onChange:function(hsb,hex,rgb,el,bySetColor){
								jQuery("#button_background_color").val('#' + hex+ " !important");
								jQuery("#poll_button .btn.btn-widget").css('background','#' + hex);
							}
						});
					});
				</script>
				
              </div>
              <div class="form-group">
                <label for="button_border_color">Button border color</label>
                <div id="button_border_color_picker"></div>
				<input type='hidden' class='color form-control' name='button_border_color' id='button_border_color' value=''>
				<script>
					jQuery(function() {
						jQuery('#button_border_color_picker').colpick({
							flat:true,
							layout:'hex',
							submit:0,
							onChange:function(hsb,hex,rgb,el,bySetColor){
								jQuery("#button_border_color").val('#' + hex);
								jQuery("#poll_button .btn-widget").css("border","#"+hex+' solid '+jQuery("#button_border_width").val());
							}
						});
					});
				</script>
              </div>
              <div class='form-group'>
				  <label for='button_border_width'>Button border width</label>
					<div id='slider-range-min6'></div>
					<input type='text' name='button_border_width' id='button_border_width' class='slider-amount' readonly style='border:0; color:#f6931f; font-weight:bold;'>
					<script>
						jQuery(function() {
							jQuery( '#slider-range-min6' ).slider({
								range: 'min',
								value: 0,
								min: 0,
								max: 10,
								change: function( event, ui ) {
									jQuery('#button_border_width').val( ui.value + 'px' );
								},
								slide: function( event, ui ) {
									jQuery('#button_border_width').val( ui.value + 'px' );
									jQuery("#poll_button .btn-widget").css("border",jQuery("#button_border_color").val()+' solid '+ui.value + 'px');
								}
							});
							jQuery( '#button_border_width' ).val( jQuery( '#slider-range-min6' ).slider( 'value' ) + 'px'  );
						});
					</script>

			  </div>
			  <div class='form-group'>
				<label for='button_border_radius'>Button border radius</label>
					<div id='slider-range-min7'></div>
					<input type='text' name='button_border_radius' id='button_border_radius' class='slider-amount' readonly style='border:0; color:#f6931f; font-weight:bold;'>
					<script>
						jQuery(function() {
							jQuery( '#slider-range-min7' ).slider({
								range: 'min',
								value: 2,
								min: 0,
								max: 50,
								change: function( event, ui ) {
									jQuery( '#button_border_radius' ).val( ui.value + 'px !important' );
								},
								slide: function( event, ui ) {
									jQuery( '#button_border_radius' ).val( ui.value + 'px !important' );
									jQuery("#poll_button .btn-widget").css('border-radius',ui.value + 'px');
								}
							});
							jQuery( '#button_border_radius' ).val( jQuery( '#slider-range-min7' ).slider( 'value' ) + 'px !important'  );
						});
					</script>
					
				
			  </div>
			  <div class="form-group">
                			<label for="poll_background_image_file">Poll background image</label> 
				<input id="upload_image" type="text" size="36" name="poll_background_image_file" value="<?php echo $gearimage; ?>" />
				<input type="file" class="color " name="poll_background_image_file" id="upload_image_button" value="Upload Image"> 
              </div>
			  <div class="form-group">
                <label for="poll_background_color">Poll background color</label>
                <div id="poll_background_color_picker">
					<div class="set_background">
							<input title="Transparent background" type="checkbox" id="poll_background_color_transparent_checkbox" value="yes" name="poll_background_color_transparent_checkbox"  />
							<label>Transparent</label>
					</div>
                </div>
				<input type='hidden' class='color form-control' name='poll_background_color' id='poll_background_color' value=''>
				<script>
					jQuery(function() {
						jQuery('#poll_background_color_picker').colpick({
							flat:true,
							layout:'hex',
							submit:0,
							onChange:function(hsb,hex,rgb,el,bySetColor){
								jQuery("#poll_background_color").val('#' + hex+" !important");
								jQuery("#poll_widget").css('background','#' + hex);
							}
						});
					});
				</script>
				
              </div>
              <div class="form-group">
                <label for="poll_border_color">Poll border color</label>
                <div id="poll_border_color_picker"></div>
				<input type='hidden' class='color form-control' name='poll_border_color' id='poll_border_color' value=''>
				<script>
					jQuery(function() {
						jQuery('#poll_border_color_picker').colpick({
							flat:true,
							layout:'hex',
							submit:0,
							onChange:function(hsb,hex,rgb,el,bySetColor){
								jQuery("#poll_border_color").val('#' + hex+" !important");
								jQuery("#poll_widget").css('border-color','#' + hex);
								jQuery("#poll_widget").css("border","#"+hex+' solid '+jQuery("#poll_border_width").val());
							}
						});
					});
				</script>
              </div>

              <div class="form-group"> 
                <label for="poll_bottom_bar">Poll bottom bar color</label>
                <div id="poll_bottom_bar_color_picker">
					<div class="set_background">
						<input title="Transparent background" type="checkbox" id="poll_bottom_bar_color_transparent_checkbox" value="yes" name="poll_bottom_bar_color_transparent_checkbox"  />
						<label>Transparent</label>
					</div>
				</div>
				<input type='hidden' class='color form-control' name='poll_bottom_bar' id='poll_bottom_bar' value=''>
				<script>
					jQuery(function() {
						jQuery('#poll_bottom_bar_color_picker').colpick({
							flat:true,
							layout:'hex',
							submit:0,
							color:'2094d9',
							onChange:function(hsb,hex,rgb,el,bySetColor){
								jQuery("#poll_bottom_bar").val('#' + hex + " !important");
								
								jQuery("#poll_button").css('background-color','#' + hex);
								jQuery("#poll_widget").css('border-top-color','#' + hex);
							}
						});
					});
				</script>
				
              </div>

              <div class='form-group'>
				<label for='poll_border_width' class='col-sm-3 control-label'>Poll border width</label>
						<div id='slider-range-min2'></div>
						<input type='text' name='poll_border_width' id='poll_border_width' class='slider-amount' readonly style='border:0; color:#f6931f; font-weight:bold;'>
						<script>
							jQuery(function() {
								jQuery( '#slider-range-min2' ).slider({
									range: 'min',
									value: 1,
									min: 0,
									max: 50,
									change: function( event, ui ) {
										jQuery( '#poll_border_width' ).val( ui.value + 'px' );
									},
									slide: function( event, ui ) {
										jQuery( '#poll_border_width' ).val( ui.value + 'px' );
										jQuery( '#qp_b1' ).css('border-width', ui.value + 'px' );
										jQuery("#poll_widget").css("border",jQuery("#poll_border_color").val()+' solid '+ui.value + 'px');
									}
								});
								jQuery( '#poll_border_width' ).val( jQuery( '#slider-range-min2' ).slider( 'value' ) + 'px'  );
							});
						</script>

			  </div>
			  <div class='form-group'>
				<label for='poll_border_radius' class='col-sm-3 control-label'>Poll border radius</label>
					<div id='slider-range-min3'></div>
					<input type='text' name='poll_border_radius' id='poll_border_radius' class='slider-amount' readonly style='border:0; color:#f6931f; font-weight:bold;'>
					<script>
						jQuery(function() {
							jQuery( '#slider-range-min3' ).slider({
								range: 'min',
								value: 0,
								min: 0,
								max: 50,
								change: function( event, ui ) {
									jQuery( '#poll_border_width' ).val( ui.value + 'px' );
								},
								slide: function( event, ui ) {
									jQuery( '#poll_border_radius' ).val( ui.value + 'px !important' );
									jQuery("#poll_widget").css('border-radius',ui.value + 'px');
								}
							});
							jQuery( '#poll_border_radius' ).val( jQuery( '#slider-range-min3' ).slider( 'value' ) + 'px !important'  );
						});
					</script>
			  </div>

            </div>
			<!-- div for embed -->
			<script>
				(function($){
					jQuery(window).load(function(){
						jQuery('#customize').mCustomScrollbar({
							theme:'dark',
						});
						
					});
				})(jQuery);
			</script>
			<div id="embed_holder" class="live_form tabbed">
            <div class="input-group">
              <span class="input-group-addon">Share</span>
              <input type="text" class="form-control" value="Your permalink will show up here" >
            </div>
            <div class="input-group">
              <span class="input-group-addon">Embed</span>
              <input type="text" class="form-control" value="Your embed code will show up here" >
            </div>
            <div class="input-group">
              <a href="#" class="btn btn-transparent">Share on Twitter</a>
              <a href="#" class="btn btn-transparent">Share on Facebook</a>
            </div>
          </div>
          <br>
          <input type="hidden" class="form-control" id="createForm" name="createForm" value="1">
		  <input type="hidden" name="merchantID" value="<?php echo $merchantID ?>" id="merchantID">
		  <input type="hidden" name="secretKey" value="<?php echo $secretKey ?>" id="secretKey">
          <button type="submit" class="create-btn createSubmit btn btn-primary">Create</button>
        
		  <a id='view-btn' target="_blank" class='btn btn-success btn-lg pull-right'>Preview</a>		  
         </form>
        </div>
      </div>
      <!--Preview Widget -->
      <div class="col-md-7 previewWD content">
      	<div class="outer-poll-wrap"> 
		<div class="poll_wrap" id="poll_wrap"> 
      			<!-- Custom Logo start -->  
			<div class="poll-display site_logo <?php if(empty($user->custom_logo)) { echo 'logo_not_hidden'; } ?>" id="poll_top_of_bar_logos"> 
				<?php if ( $user->custom_logo != '' ) {
					if( $user->membership_id == 6 || $user->membership_id == 5 || $admin  ) { ?> 
						<a href="<?php echo $res['url']; ?>/static/member_logo/<?php echo $user->custom_logo; ?>"><img src="" alt=""></a>
					<?php }
				} else { 
					if($user->membership_id == 6 || $user->membership_id == 5  ) { ?>
						<h3>
							<a href="<?php echo $res['url']; ?>">
								<?php echo $res["title"]; ?>
							</a>
						</h3>
				<?php }  
			 }  ?>
			</div>
			<!-- Custom Logo End  -->
		        <div id="poll_widget">
		        <?php  if($user->membership_id == 6 || $user->membership_id == 5 ||  $admin) { ?> 
			          <a href="#embed" id="poll_embed">Embed</a>
			          <div id="poll_embed_holder" class="live_form">
			            <div class="input-group">
			              <span class="input-group-addon">Share</span>
			              <input type="text" class="form-control" value="Your permalink will show up here">
			            </div>
			            <div class="input-group">
			              <span class="input-group-addon">Embed</span>
			              <input type="text" class="form-control" value="Your embed code will show up here">
			            </div>
			            <div class="input-group">
			              <a href="#" class="btn btn-transparent">Share on Twitter</a>
			              <a href="#" class="btn btn-transparent">Share on Facebook</a>
			            </div>
			          </div><!-- /#poll_embed_holder -->
	        		<?php } ?>
		          <!-- logo bar -->
		          <style>
		          .poll-display.site_logo {
					  background: #333 none repeat scroll 0 0;
					  margin: 0;
					  padding: 10px;
					  text-align: left;
					}
		          </style>  
		          <!-- End logo bar -->
		          <div id="poll_question">
		            <h3>Question</h3>
		          </div> 
		          <ul id="poll_answers" class="answers_create"></ul>
		          <div id="poll_button">
		            <button class="btn btn-widget">Vote</button>
		            <button class="btn btn-widget" id="view_results_button">View Results</button>
		           
		          </div>
		        </div><!--Poll Widget -->
		              <?php  if($user->membership_id == 3 || $user->membership_id == 4 || $admin )  {  ?>
				<span class="branding pull-right"> 
					Powered by 
					<a href="<?php echo $res["url"] ?>"> 
						<img src="<?= polldeep_plugin_url().'images/auto_site_logo.png'; ?>" alt="<?= $res['from_title'] ?>">
					</a> 
				</span>


			<?php  } ?>
		      </div> 
	</div> 
      </div> 
    </div>
  </div>
</section>
</div>
