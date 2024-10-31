<?php if ( ! defined( 'ABSPATH' ) ) exit; ?>
<div class="createpoll">
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
			    'body' => array( 'merchantID' => $merchantID, 'secretKey' => $secretKey ,'id'=>intval($_REQUEST['id'])),
			   );
	
		$result = wp_remote_post( $polldeepServer.'/user/editAjax', $args );
		
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
		$userMembership	= $res['userMembership'];
		$poll			= $res['poll'];
		$deeper_fields		= $res['deeper_fields'];
		$user			= $res['user'];
		$themeDtails		=  array_reverse($res['theme']);
		$cssStyle		= $res['cssStyle'];
		$admin			= $res['admin'];
		$max_count		= $res['max_count'];
		$max_free		= $res['max_free'];
		$userMembership	= (object)$userMembership;
		$poll			= (object)$poll;
		$user			= (object)$user;
		$deeper_fields		= (object)$deeper_fields;
		$themeDtails		= (object)$themeDtails;
		$settingData 		=  $res['setting'];
		
	?>
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
		  <div class="row">
			  <div class="col-md-5 content">
				   <div class="alltabs">
					<button type="button" data-id="container_questions" class="btn btn-success mytabs">Questions</button>
					<button type="button" data-id="settings" class="btn btn-primary mytabs">Settings</button>
					<?php if(($userMembership && !empty($userMembership) && !$userMembership->multiple_themes) || $admin): ?>
						<button type="button" data-id="theme" class="btn btn-success mytabs">Theme</button>
					<?php endif; ?>
					
					<?php if(($userMembership && !empty($userMembership) && !$userMembership->customize_themes) || $admin): ?>
						<button type="button" data-id="customize" class="btn btn-primary mytabs">Customize </button>
					<?php endif; ?>
                       <button type="button" data-id="embed_holder" class="btn btn-success mytabs last">Your Code</button>
				 </div>
			       <div class="box-holder"> 
				 <!--********** Start Form ********************* -->
				   <form role="form" class="live_form editPollForm" id="create-poll" method="post" action="">
						<!----------------container Questions---------------------->
						<div id="container_questions" class="tabbed">
						  <div class="form-group">
							<label for="questions">Your Question</label>
							<span class="help-block">No HTML allowed. Invalid question will be ignored</span>
							<input type="text" class="form-control" id="questions" 
							name="question" value="<?php echo $poll->question; ?>">  
						  </div>
						  <div class="form-group" id="widget_answers">
							<label>Answers</label>
							<span class="help-block">Leave fields empty to ignore options. No HTML allowed. Invalid answers will be ignored.</span>
							<ul id="sortable">
							 <?php  
							   $options=$res['options']; 
							   $key=0;
							   foreach($options as $option)
							   { $key++;
							   $option=(object)$option;
							  ?>
								<li id='poll_sort_<?php echo $key;?>'>
								  <div class='row'>
									<div class='col-md-12'>
									  <div class='input-group input-append input-prepend'>
										<span class='input-group-addon'><i class='glyphicon glyphicon-move'></i></span>
										<input type='text' class='form-control' 
										name='option[<?php echo $key;?>]' 
										value='<?php echo 
										$option->answer; ?>'>
										<span class='input-group-addon'><i class='glyphicon 
										glyphicon-user'></i>&nbsp;&nbsp;&nbsp;<?php 
										echo $option->count ?></span>
									  </div>
									</div>
								  </div>
								</li>
							 <?php } ?>
							</ul>
						  </div>
					  <?php if(isset($userMembership) && !empty($userMembership) && $userMembership->no_of_ans > 3): ?>
						<a href="#" id="add-field" class="btn btn-transparent">
							<small><?php echo "Add Field"; ?></small>
						</a>
					  <?php endif; ?>
						</div>
						<!--------------- Settings---------------------->
					    <div id="settings" class="tabbed">
								<?php if(isset($userMembership) && $userMembership->share_and_embed=='0')
								{?>
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
									<small>Allows users to add comments</small>
									</li>
									<li><a href="#" class="last <?php echo (!$poll->comments) ? "current":"" ?>" data-value="0">No</a></li>
									<li><a href="#" class="first <?php echo ($poll->comments) ? "current":"" ?>" data-value="1">Yes</a></li>
								</ul>
								<input type="hidden" name="comments" id="comments" value="<?=$poll->comments?>">
							<?php } ?>
							<input type="hidden" name="comments" id="comments" value="<?=$poll->comments?>">
								<?php if(isset($userMembership) && $userMembership->more_than_one_ans=='0' || $admin){ ?>
							  <ul class="form_opt" data-id="choice" data-callback="update_choice_type">
								<li class="label">Multiple Choices
								<small>Allows users to choose more than one option.</small>
								</li>
								<li><a href="#" class="last current" data-value="0">No</a></li>
								<li><a href="#" class="first" data-value="1">Yes</a></li>
							  </ul>
							  <input type="hidden" name="choice" id="choice" value="0">
								<?php } ?>
							  <?php if(isset($userMembership) && !empty($userMembership) && 
							  $userMembership->multiple_votes=='0' || $admin) 
							  {?>
							  <ul class="form_opt" data-id="vote">
								<li class="label">Multiple Votes
								<small>Allows users to vote more than once</small>
								</li>
								<li><a href="#" class="last current" data-value="off">Off</a></li>
								<li><a href="#" data-value="day">Daily</a></li>
								<li><a href="#" class="first" data-value="month">Monthly</a></li>
							  </ul>
							  <input type="hidden" name="vote" id="vote" value="off">
							  <?php } 
							  ?>
							  
							  <?php if($res['ispro']=='pro' && isset($userMembership) && 
							  !empty($userMembership) && $userMembership->custom_logo=="0"){?>
							<script>
							  jQuery(document).ready(function(){
								 jQuery(".custom_logo_set_no").click(function(){

					 				jQuery('#poll_top_of_bar_logos').removeClass('theme-without-logo');		
									jQuery("#custom_logo_set_visiable").val("<?php echo $res['url']; ?>/static/member_logo/<?php echo $user->custom_logo ?>");
									jQuery("#poll_top_of_bar_logos a img").attr('src',"<?php echo $res['url']; ?>/static/member_logo/<?php echo $user->custom_logo ?>");
								 });
								 
								 jQuery(".custom_logo_set_yes").click(function(){ 

									jQuery('#poll_top_of_bar_logos').addClass('theme-without-logo'); 
									jQuery("#custom_logo_set_visiable").val("");
									jQuery("#poll_top_of_bar_logos a img").attr('src','');
								
				 				});
								 
							 });
							
							  </script>
								<ul class="form_opt" data-id="custom_logo">
									<li class="label">Use Custom Logo
									<small>Use your custom logo</small>
									</li>
									<li><a href="#" class="last custom_logo_set_yes <?php echo (!$poll->custom_logo) ? "current":"" ?>" data-value="0">No</a></li>
									<li><a href="#" class="first  custom_logo_set_no <?php echo 
									($poll->custom_logo) ? "current":"" ?>" data-value="1">Yes</a></li>
									<input type="hidden" name="custom_logo_set_visiable" 
									id="custom_logo_set_visiable" value="<?php echo $res['url']; 
									?>/static/<?php echo $res['logo'] ?>" />
								</ul>
								<input type="hidden" name="custom_logo" id="custom_logo" value="<?=$poll->custom_logo?>">
							  <?php } ?>
							  
							  <?php/* if(isset($userMembership) && 
							  $res['ispro']=='pro'){ ?>
								<ul class="form_opt" data-id="smaller_version">
									<li class="label">Use Smaller Version of Poll
									<small>Use Smaller Version of Poll</small>
									</li>
									<li><a href="#" class="last <?php echo 
									(!$poll->smaller_version) ? "current":"" ?>" data-value="0">No</a></li>
									<li><a href="#" class="first <?php echo 
									($poll->smaller_version) ? "current":"" ?>" data-value="1">Yes</a></li>
								</ul>
								<input type="hidden" name="smaller_version" id="smaller_version" value="<?=$poll->smaller_version?>">
							  <?php }*/?>
							  
							  <?php if(((isset($userMembership->save_theme) && 
							  $res['ispro']=='pro' && $userMembership->save_theme=="0" ) && ($userMembership && !empty($userMembership) && !$userMembership->customize_themes)) || $admin){ ?>
								<ul class="form_opt" data-id="save_theme">
									<li class="label">Save this theme for future use
									<small>Previously saved theme will be lost</small>
									</li>
									<li><a href="#" class="last <?php echo (!$poll->save_theme) ? 
									"current":"" ?>" data-value="0">No</a></li>
									<li><a href="#" class="first <?php echo ($poll->save_theme) ? 
									"current":"" ?>" data-value="1">Yes</a></li>
								</ul>
								<input type="hidden" name="save_theme" id="save_theme" value="<?=$poll->save_theme?>">
							  <?php } ?>
							  
							  <?php if($res['ispro']=='pro' && isset($userMembership) && 
							  $userMembership->restrict_by_password=='0'){ ?>
								<div class="form-group">
								  <label for="pass">Password</label>
								  <input type="text" class="form-control" id="pass" name="pass">
								</div>
							  <?php } else { ?>
								<div class="form-group">
								  <label for="pass">Password<a href="<?php echo 
								  $res['upgrade'] ?>" class='pull-right'><small>(Upgrade)</small></a></label>
								  <input type="text" class="form-control" id="pass" 
								  placeholder="Please upgrade to a premium package to unlock 
								  this feature." disabled>
								</div>
							  <?php } ?>
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
							  	<?php  $custom_size_option = '';
							            		 if($poll->custom_poll_size == 600) {
							            		 	$custom_size_option = 0;
							            		 } else if($poll->custom_poll_size == 300) {
							            		 	$custom_size_option = 2;
							            		 } else {
							            		 	$custom_size_option = 1;
							            		 }   

							            		 ?>
							  <div class="heading_wrap">
								<b><?php echo "Poll Size"; ?></b>
								<span class="deeper_field_notice"><i><?php echo "Select The Size Of The Poll (px)"?></i></span>
							</div>   
							<table class="admin_fields"  border=1> 
								<tr>
									<td width="1%">
										<input type="radio" name="custom_poll_size" class="default_poll_size" id="default_poll_size" value="0" <?php if($custom_size_option == 0){ echo 'checked = checked'; }?>>
									</td>
									<td>Default (600 px)</td> 	
								</tr> 
								<tr>
								  	 <td width="1%" id="custom_hover" class = "custom_hover">
									   	<input type="radio" name="custom_poll_size" class="custom_poll_size" id="custom_poll_size" value="1" <?php if($custom_size_option == 1){ echo 'checked = checked'; }?>>
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
										<input type="radio" name="custom_poll_size" class="custom_poll_size_300" id="custom_poll_size_300" value="2" <?php if($custom_size_option == 2){ echo 'checked = checked'; }?>>
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
							  <?php if(!empty($deeper_fields) && isset($userMembership) && 
							  !empty($userMembership) && !$userMembership->SHD_analysis){ ?>
									<?php $prevFields = explode(',',$poll->deeper_fields); ?>
									<!-- <span class="deeper_field_notice"><i>(Please select the Deeper Analysis fields you want to include in your poll)</i></span> -->
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
												<td width="1%"><input type="checkbox" <?php echo (in_array($deeper_field['id'],$prevFields)) ? "checked" : "" ;  ?> name="deeper_fields[]" value="<?=$deeper_field['id']?>"></td>
												<td> <?php echo $deeper_field['field_name'];?> </td>
											</tr>
										<?php endforeach; ?>
										<?php if($user->membership_id == 6 ||  $admin ) { ?>
										<?php  $custom_fields = $res['poll']['custom_fields'];
											$custom_ption = json_decode($custom_fields);   
											// echo "<pre>";
											// print_r($res);
											// echo "<pre>";
											  ?>
		<tr class="deep_analyse">
			<td width="1%">
				<input  type="checkbox" 
					name="custom_check" 
					class="custom_check" 
					id="custom_check" 
					value="true" 
					<?= (!empty($custom_ption)) ? 'checked' : '' ?>
				/>
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
						 
					<?php foreach($custom_ption as $key => $value){ 
						$option_array = $value->options;?> 
						<div class='custom_field_<?= $key; ?>'>
						   <span class="remove-field"></span>
						   <div class='form-group'>
						      <label for='theme_name' class='col-sm-3 control-label'>Custom Field Name</label>
						      <div class='col-sm-9'>
						         <input type='text' class='form-control custom_field_name' name='custom_field[<?= $key; ?>][field_name]' id='field_name' value='<?php echo $value->field_name;?>'>
						      </div>
						   </div>
						   <div class='form-group field_type' style='display:none;'>
						      <label for='question_font_type' class='col-sm-3 control-label'>Field type</label>
						      <div class='col-sm-9'>
						         <select name='custom_field[<?= $key; ?>][field_type]' class='custom_field_type' id='field_type' onchange='change_field(this.id);'>
						            <option value='select'>Drop Down Menu</option>
						            <option value='text'>Text Field</option>
						            <option value='checkbox'>Checkbox</option>
						            <option value='radio'>Radio Button</option>
						         </select>
						      </div>
						   </div>
						   <?php 
						      $first_time_flag = true; ?> 
						   <div class='form-group field-options options'>
						      <label for='Options' class='col-sm-3 control-label'>Options</label>
						      <?php foreach($option_array as $o_key => $o_value) { ?>
						      <div>
						         <div class='col-sm-9'>
						            <label class="col-sm-3"></label>
						            <input type='text' class='form-control custom_field_options' id= 'field_options' name='custom_field[<?= $key; ?>][options][]' class='field-opt-text' value='<?php echo $o_value; ?>'><span class=<?= ($first_time_flag) ? 'add_opt' : 'rem_opt'?> ><i class='glyphicon glyphicon-<?= ($first_time_flag) ? "plus" : "minus" ?>'></i></span>
						         </div>
						      </div>
						      <?php if($first_time_flag) $first_time_flag = false; 
						         } ?>
						   </div>
						   <div class='form-group status'>
						      <label for='status' class='col-sm-3 control-label'>Status</label>
						      <div class='col-sm-9'>
						         <select name='custom_field[<?= $key; ?>][status]' class='custom_field_status' id='status'>
						            <option value='0'>Active</option>
						            <option value='1'>Inactive</option>
						         </select>
						      </div>
						   </div>
						</div>
					<?php }   ?>
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
			<td width="1%">
				<input  type="checkbox" 
					name="local_map" 
					class="local_map" 
					id="local_map" 
					value="1"
					<?= ($res['poll']['local_map']) ? 'checked' : '' ?>
				/>
			</td>
			<td>Map</td>
			
		</tr> 
	</table> 
<?php } ?> 
<?php /*echo "<pre>"; print_r($res); echo "</pre>";*/ ?>						  
							  <?php if(isset($userMembership) && !empty($userMembership) && 
							  !$userMembership->deeper_analysis)
							  { ?>
									
									<script type='text/javascript'>
										
										function change_field(elemid)
										{
											if (jQuery("#"+elemid).val() != 'text')
											{
													jQuery("#"+elemid).parent().parent().next(".form-group.field-options").show();
											}
											else
												jQuery("#"+elemid).parent().parent().next(".form-group.field-options").hide();
										    }	
							      jQuery(document).ready(function(){
							
									// jQuery('.form-group.field-options').hide();		
									//  jQuery(document).on('click','.add_opt' ,function(){
									// 	var parClass= jQuery(this).parent().parent().parent().attr("class");
									// 	var tmp=parClass.split("_").pop();
									// 	var curcount = parseInt(tmp, 10);
										
									//     var n =jQuery('field-opt-text').length + 1;
									// 	var box_html = jQuery('<label class=\'col-sm-3\'></label><div class=\'col-sm-9 space-bot\'><input type=\'text\' id=\'box\' + n + \'\'  class=\'form-control\' name=\'custom_field['+curcount+'][options][]\' class=\'field-opt-text\' value=\'\'><span class=\'rem_opt\' ><i class=\'glyphicon glyphicon-minus\'></i></span> </div>');
									// 	var mainelem = jQuery(this).parent();
									// 	box_html.hide();
									// 	mainelem.after(box_html);
									// 	box_html.fadeIn('slow');
									// 	return false;
									// });
									// jQuery(document).on('click','.rem_opt' ,function(){
									// 	jQuery(this).parent().css( 'background-color', '#FF6C6C' );
									// 	jQuery(this).parent().fadeOut('slow', function() {
									// 		 jQuery(this).prev().remove();
									// 		 jQuery(this).remove();
									// 		jQuery('.box-number').each(function(index){
									// 			jQuery(this).text( index + 1 );
									// 		});
									// 	});
									// 	return false;
									// });
									
									// jQuery(document).on("click",".custom_fields .remove-field",function(){
										
									// 	jQuery(this).parent().fadeOut("slow",function(){
									// 		jQuery(this).remove();
									// 	});
									// });
									
									// jQuery(document).on('click','#add_custom_field',function(){
							
									// 	var tmpclass=jQuery(".custom_fields > div:last-child").attr("class");
									// 	var tt=tmpclass.split("_").pop();
									// 	var count = parseInt(tt, 10);
									// 		count++;	
									// 	var clone = jQuery(".custom_field_0").clone()
									// 	clone.find('div.chosen-container').remove();
									// 	clone.appendTo("#settings .custom_fields");	
										
									// 	jQuery(".custom_fields > div:last-child").attr("class","custom_field_"+count);
									// 	jQuery(".custom_field_"+count+" .field_type select").attr("id","field_type_"+count);
									// 	jQuery(".custom_field_"+count+" .status select").attr("id","status_"+count);
									// 	jQuery(".custom_field_"+count+" select").chosen();
									// 	jQuery(".custom_field_"+count+" .remove-field").html("<i class='glyphicon glyphicon-remove'></i>");	
									// 	jQuery(".custom_field_"+count+" .options input").attr("name","custom_field["+count+"][options][]");
									// 	jQuery(".custom_field_"+count+" .custom_field_name").attr("name","custom_field["+count+"][field_name]");
									// 	jQuery(".custom_field_"+count+" .custom_field_type").attr("name","custom_field["+count+"][field_type]");
									// 	jQuery(".custom_field_"+count+" .custom_field_status").attr("name","custom_field["+count+"][status]");
									// });
								});
							</script>
									<!-- <div class='custom_fields'>
										<div class='custom_field_0'>
											<span class="remove-field"></span>
											<div class='form-group'>
											  <label for='theme_name' class='col-sm-3 control-label'>Custom Analysis Field Name</label>
											  <div class='col-sm-9'>
												<input type='text' class='form-control custom_field_name' name='custom_field[0][field_name]' id='field_name' value=''>
											  </div>
											</div>

											<div class='form-group field_type'>
											  <label for='question_font_type' class='col-sm-3 control-label'>Field type</label>
											  <div class='col-sm-9'>
												  <select name='custom_field[0][field_type]' class='custom_field_type' id='field_type' onchange='change_field(this.id);'>
													<option value='text'>Text Field</option>
													<option value='select'>Drop Down Menu</option>
													<option value='checkbox'>Checkbox</option>
													<option value='radio'>Radio Button</option>
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
											  <label for='status' class='col-sm-3 control-label'>Status</label>
											  <div class='col-sm-9'>
												  <select name='custom_field[0][status]' class='custom_field_status' id='status'>
													<option value='0'>Active</option>
													<option value='1'>Inactive</option>
												  </select>
											  </div>
											</div>
											
										</div>
									</div> -->
									
									<!-- <div class='form-group add-custom-field-button'>
									  <div class='col-sm-6'>
										<a href='javascript:void(0);' id="add_custom_field" class='btn btn-transparent'>Add Field</a>
									  </div>
									</div> -->
								  <!--/form-->
							  <?php } ?>
							</div> 
					<?php if(($userMembership && !empty($userMembership) && !$userMembership->multiple_themes) || $admin){  ?>
					 <div id="theme" class="tabbed">
						<?php 
						foreach($themeDtails as $theme) 
						{
							$theme=(array)$theme;
							$option_data=unserialize($theme['serialized_data']);
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
							 </div>
								<!--/form-->
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
											border:<?php echo str_replace('!important','',$option_data['poll']['border-width']); ?> solid <?php echo "#".str_replace('!important','',$option_data['poll']['border-color']); ?> !important;
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
							jQuery(function() {
								jQuery('.id-select').click(function(){
									jQuery('#poll_question h3').removeAttr('style');
									jQuery('#poll_answer').removeAttr('style');
									jQuery('#poll_button .btn').removeAttr('style');
									jQuery('#poll_widget, #poll_button').removeAttr('style');
									
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
									jQuery('#poll_widget').attr('style', function(i,s) { return s + 'border-width:'+size+'px !important; '});
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
								/*Event for changing question font type START*/
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
								/*Event for changing question font type END*/

								/*Event for changing answer font type START*/
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
								/*Event for changing answer font type END*/

								/*Event for changing button font type START*/
								jQuery('#button_font_type').on('change', function(evt, params) {
									if(jQuery(this).val() === '' || jQuery(this).val() === null) {
										jQuery("#poll_button .btn.btn-widget").css("cssText", "font-family:arial ");
									} else {
										var buttonFont = jQuery(this).val();
										buttonFont = buttonFont.replace(' !important', '');
										jQuery("#poll_button .btn.btn-widget").css("font-family",buttonFont);
									}
								});
								/*Event for changing button font type END*/

								jQuery('.id-customize').click(function(){
									jQuery(this).siblings(".id-select").trigger('click');
									jQuery('button[data-id="customize"].mytabs').trigger('click');
								});
								jQuery(document).on('click', 'div.preview-wrapper span',function() {
									jQuery('.preview-wrapper').remove();
									jQuery('#poll_widget').css('background',jQuery("#poll_background_color").val());
								});
							});
						</script>
					</div>
                    <!-----------------End Theme---------------------------->	
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
					 <!-----------------Start customize---------------------------->	
						<div id="customize" class="tabbed">
						  <input type="hidden" name="theme" value="" id="poll_theme_value">
						   <?php
						   $get_border_top_of_logo_width_ex =  explode(" solid ",$cssStyle['.poll-display.site_logo']['border']);
						   $get_border_top_of_logo_width = $get_border_top_of_logo_width_ex[0];
						   $get_border_top_of_logo_color = $get_border_top_of_logo_width_ex[1];
						   ?>
						  <div class="form-group">
							<label for="question_background_color">Top Of Bar background Color</label>
							<div id="top_bar_background_color_picker"></div>
							<input type='hidden' class='color form-control' name='top_bar_background_color' id='top_bar_background_color' value='#333333 !important'>	
							<script>
								jQuery(function() {
									jQuery('#top_bar_background_color_picker').colpick({
										flat:true,
										layout:'hex',
										submit:0,
										color:'<?php echo str_replace("# !important","",$cssStyle['.poll-display.site_logo']['background-color']); ?>',
										onChange:function(hsb,hex,rgb,el,bySetColor){
											jQuery("#top_bar_background_color").val('#' + hex+" !important");
											jQuery('#poll_top_of_bar_logos').attr('style', function(i,s) { return s + 'background:#'+hex +' !important;' });
											
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
											color:'<?php echo str_replace(" ","",$get_border_top_of_logo_color); ?>',
											onChange:function(hsb,hex,rgb,el,bySetColor){
												jQuery("#top_bar_border_color").val('#' + hex+" !important");
												jQuery('#poll_top_of_bar_logos').attr('style', function(i,s) { return s + 'border-color:#'+hex+' !important;' });
											}
										});
									});
								</script>
						</div>
						  <div class='form-group'>
							  <label for='question_border_width' >Top Of Bar border width</label>
								<div id='slider-range-min20'></div>
								<input type='text' name='top_bar_border_width' id='top_bar_border_width' class='slider-amount' readonly style='border:0; color:#f6931f; font-weight:bold;'>
								<?php
								;
								if (strpos($get_border_top_of_logo_width,'!important') !== false) {
									$top_bar_border_width = str_replace("px !important","",$get_border_top_of_logo_width);
								}
								else{
									$top_bar_border_width = str_replace("px","",$get_border_top_of_logo_width);
								}
								?>
								<script>
									jQuery(function() {
										jQuery( '#slider-range-min20' ).slider({
											range: 'min',
											value: '<?php echo $top_bar_border_width; ?>',
											min: 0,
											max: 10,
											change: function( event, ui ) {
												jQuery( '#top_bar_border_width' ).val( ui.value + 'px' );
											},
											slide: function( event, ui ) {
												jQuery( '#top_bar_border_width' ).val( ui.value + 'px' );
												jQuery('#poll_top_of_bar_logos').attr('style', function(i,s) { return s + 'border-width:'+ui.value +'px !important;' });
											}
										});
										jQuery( '#top_bar_border_width' ).val( jQuery( '#slider-range-min20' ).slider( 'value' ) + 'px'  );
									});
								</script>

						</div>
						  <div class='form-group'>
							  <label for='question_border_radius'>Top Of Bar border radius</label>
								<?php
								
								if (strpos($cssStyle['.poll-display.site_logo']['border-radius'],'!important') !== false) {
									$top_bar_border_radius = str_replace("px !important","",$cssStyle['.poll-display.site_logo']['border-radius']);
								}
								else{
									$top_bar_border_radius = str_replace("px","",$cssStyle['.poll-display.site_logo']['border-radius']);
								}
								?>
								<div id='slider-range-min21'></div>
								<input type='text' name='top_var_border_radius' id='top_var_border_radius' class='slider-amount' readonly style='border:0; color:#f6931f; font-weight:bold;'>
								<script>
									jQuery(function() {
										jQuery( '#slider-range-min21' ).slider({
											range: 'min',
											value: '<?php echo $top_bar_border_radius; ?>',
											min: 0,
											max: 50,
											change: function( event, ui ) {
												jQuery( '#top_var_border_radius' ).val( ui.value + 'px !important' );
											},
											slide: function( event, ui ) {
												jQuery( '#top_var_border_radius' ).val( ui.value + 'px !important' );
												jQuery('#poll_top_of_bar_logos').attr('style', function(i,s) { return s + 'border-radius:'+ui.value +'px !important;' });
											}
										});
										jQuery( '#top_var_border_radius' ).val( jQuery( '#slider-range-min21' ).slider( 'value' ) + 'px'  );
									});
								</script>

						</div>
						  <div class='form-group'>
							  <label for='question_font_type'>Question font type</label>
								<select name='question_font_type' id='question_font_type'>
									<option value='arial' <?php echo (($cssStyle['#poll_widget #poll_question h3']['font-family']=='arial !important')?'selected':'')?>>Arial</option>
									<option value='courier new' <?php echo (($cssStyle['#poll_widget #poll_question h3']['font-family']=='courier new !important')?'selected':'')?>>Courier New</option>
									<option value='georgia' <?php echo (($cssStyle['#poll_widget #poll_question h3']['font-family']=='georgia !important')?'selected':'')?>>Georgia</option>
									<option value='sans-serif' <?php echo (($cssStyle['#poll_widget #poll_question h3']['font-family']=='sans-serif !important')?'selected':'')?>>Sans-Serif</option>
									<option value='tohama' <?php echo (($cssStyle['#poll_widget #poll_question h3']['font-family']=='tohama !important')?'selected':'')?>>Tohama</option>
									<option value='times new' <?php echo (($cssStyle['#poll_widget #poll_question h3']['font-family']=='times new !important')?'selected':'')?>>Times New</option>
									<option value='terbutchet' <?php echo (($cssStyle['#poll_widget #poll_question h3']['font-family']=='terbutchet !important')?'selected':'')?>>Terbutchet</option>
									<option value='verdana' <?php echo (($cssStyle['#poll_widget #poll_question h3']['font-family']=='verdana !important')?'selected':'')?>>Verdana</option>
								</select>
								<input type='hidden' id='question_font_type_change' name='question_font_type_change' value=''>
							</div>
						  <div class='form-group'>
							  <label for='question_font_size' class='col-sm-3 control-label'>Question font size</label>
								<div id='slider-range-min'></div>
								<input type='text' name='question_font_size' id='question_font_size' class='slider-amount' readonly style='border:0; color:#f6931f; font-weight:bold;'>
								<input type='hidden' id='question_font_size_change' value='<?php echo $cssStyle['#poll_widget #poll_question h3']['font-size']; ?>' name='question_font_size_change' value=''>
								<?php
								if (strpos($cssStyle['#poll_widget #poll_question h3']['font-size'],'!important') !== false) {
									$question_font_size_value = str_replace("px !important","",$cssStyle['#poll_widget #poll_question h3']['font-size']);
								}
								else{
									$question_font_size_value = str_replace("px","",$cssStyle['#poll_widget #poll_question h3']['font-size']);
								}
								?>
								<script>
									jQuery(function() {
										if(jQuery('#slider-range-min').size()>0) {
										jQuery( '#slider-range-min' ).slider({
											range: 'min',
											value: <?php echo $question_font_size_value; ?>,
											min: 6,
											max: 30,
											change: function( event, ui ) {
												jQuery( '#question_font_size' ).val( ui.value + 'px' );
											},
											slide: function( event, ui ) {
												jQuery( '#question_font_size' ).val( ui.value + 'px' );
												jQuery('#poll_question h3').attr('style', function(i,s) { return s + 'font-size:'+ui.value +'px !important;' });
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
							<input type='hidden' class='color form-control' name='question_font_color' id='question_font_color' value="<?php echo $cssStyle['#poll_widget #poll_question h3']['color']?>">
							<input type='hidden' id='question_font_color_change' name='question_font_color_change' value=''>
							<script>
								jQuery(function() {
									jQuery('#question_font_color_picker').colpick({
										flat:true,
										layout:'hex',
										submit:0,
										color:'<?php echo str_replace("# !important","",$cssStyle['#poll_widget #poll_question h3']['color']); ?>',
										onChange:function(hsb,hex,rgb,el,bySetColor){
											jQuery("#question_font_color").val('#' + hex+"!important");
											jQuery('#poll_question h3').attr('style', function(i,s) { return s + 'color:#'+hex+' !important;' });
										}
									});
								});
							</script>
						  </div>
						  <div class="form-group">
							<label for="question_background_color">Question background Color</label>
							<div id="question_background_color_picker"></div>
							<input type='hidden' class='color form-control' name='question_background_color' id='question_background_color' value='<?php echo $cssStyle['#poll_widget #poll_question h3']['background'] ?>'>
							<input type='hidden' id='question_background_color_change' name='question_background_color_change' value=''>
							<script>
								jQuery(function() {
									jQuery('#question_background_color_picker').colpick({
										flat:true,
										layout:'hex',
										submit:0,
										color:'<?php echo str_replace("# !important","",$cssStyle['#poll_widget #poll_question h3']['background']); ?>',
										onChange:function(hsb,hex,rgb,el,bySetColor){
											jQuery("#question_background_color").val('#' + hex+"!important");
											jQuery('#poll_question h3').attr('style', function(i,s) { return s + 'background:#'+hex+' !important;' });
											jQuery("#poll_question h3").css("padding",'10px');
										}
									});
								});
							</script>
						  </div>
						  <?php 
							$temp=explode("#",$cssStyle['#poll_widget #poll_question h3']['border']);
							$temp1=explode("px",$cssStyle['#poll_widget #poll_question h3']['border']);
						  ?>
						  <div class="form-group">
							<label for="question_border_color">Question border Color</label>
							<div id="question_border_color_picker"></div>
							<input type='hidden' class='color form-control' name='question_border_color' id='question_border_color' value='#<?php echo (isset($temp[1]) ? $temp[1] : ''); ?>'>
							<input type='hidden' id='question_border_color_change' name='question_border_color_change' value=''>
							<script>
								jQuery(function() {
									jQuery('#question_border_color_picker').colpick({
										flat:true,
										layout:'hex',
										submit:0,
										color:'<?php echo (isset($temp[1]) ? $temp[1] : ''); ?>',
										onChange:function(hsb,hex,rgb,el,bySetColor){
											jQuery("#question_border_color").val('#' + hex+"!important");
											jQuery("#poll_question h3").css("padding",'10px');
											jQuery('#poll_question h3').attr('style', function(i,s) { return s + 'border:#'+hex+' solid '+ jQuery("#question_border_width").val()});
										}
									});
								});
							</script>
						  </div>
						  <div class='form-group'>
							  <label for='question_border_width' >Question border width</label>
								<div id='slider-range-min4'></div>
								<input type='text' name='question_border_width' id='question_border_width' class='slider-amount' readonly value="<?=$temp1[0]?>" style='border:0; color:#f6931f; font-weight:bold;'>
								<input type='hidden' id='question_border_width_change' name='question_border_width_change' value=''>
								<script>
									jQuery(function() {
										jQuery( '#slider-range-min4' ).slider({
											range: 'min',
											value: '<?php echo $temp1[0]; ?>',
											min: 0,
											max: 10,
											change: function( event, ui ) {
												jQuery( '#question_border_width' ).val( ui.value + 'px !important;' );
											},
											slide: function( event, ui ) {
												jQuery( '#question_border_width' ).val( ui.value + 'px !important;' );
												jQuery('#poll_question h3').attr('style', function(i,s) { return s + 'border-width:'+ui.value+'px !important; '});
												
												jQuery('#poll_question h3').attr('style', function(i,s) { return s + 'border-style:solid'+' !important;'});
											}
										});
										jQuery( '#question_border_width' ).val( jQuery( '#slider-range-min4' ).slider( 'value' ) + 'px !important'  );
									});
								</script>

						  </div>
						  <div class='form-group'>
							  <label for='question_border_radius'>Question border radius</label>
								<?php
								if (strpos($cssStyle['#poll_widget #poll_question h3']['border-radius'],'!important') !== false) {
									$question_border_radius_value = str_replace("px !important","",$cssStyle['#poll_widget #poll_question h3']['border-radius']);
								}
								else{
									$question_border_radius_value = str_replace("px","",$cssStyle['#poll_widget #poll_question h3']['border-radius']);
								}
								?>
								<div id='slider-range-min5'></div>
								<input type='text' name='question_border_radius' id='question_border_radius' class='slider-amount' readonly style='border:0; color:#f6931f; font-weight:bold;' value="<?php echo $cssStyle['#poll_widget #poll_question h3']['border-radius']; ?>">
								<input type='hidden' id='question_border_radius_change' name='question_border_radius_change' value=''>
								,
								<script>
									jQuery(function() {
										jQuery( '#slider-range-min5' ).slider({
											range: 'min',
											value: <?php echo $question_border_radius_value; ?>,
											min: 0,
											max: 50,
											change: function( event, ui ) {
												jQuery( '#question_border_radius' ).val( ui.value + 'px' );
											},
											slide: function( event, ui ) {
												jQuery( '#question_border_radius' ).val( ui.value + 'px' );
												jQuery('#poll_question h3').attr('style', function(i,s) { return s + 'border-radius:'+ui.value+'px !important;' });
											}
										});
										jQuery( '#question_border_radius' ).val( jQuery( '#slider-range-min5' ).slider( 'value' ) + 'px'  );
									});
								</script>

						  </div>
						  <div class='form-group'>
							<label for='answer_font_type'>Answer font type</label>
								  <select name='answer_font_type' id='answer_font_type'>
									<option value='arial' <?php echo (($cssStyle['#poll_widget ul#poll_answers li span']['font-family']=='arial !important')?'selected':''); ?>>Arial</option>
									<option value='courier new' <?php echo (($cssStyle['#poll_widget ul#poll_answers li span']['font-family']=='courier new !important')?'selected':''); ?>>Courier New</option>
									<option value='georgia' <?php echo (($cssStyle['#poll_widget ul#poll_answers li span']['font-family']=='georgia !important')?'selected':''); ?>>Georgia</option>
									<option value='sans-serif' <?php echo (($cssStyle['#poll_widget ul#poll_answers li span']['font-family']=='sans-serif !important')?'selected':''); ?>>Sans-Serif</option>
									<option value='tohama' <?php echo (($cssStyle['#poll_widget ul#poll_answers li span']['font-family']=='tohama !important')?'selected':''); ?>>Tohama</option>
									<option value='times new' <?php echo (($cssStyle['#poll_widget ul#poll_answers li span']['font-family']=='times new !important')?'selected':''); ?>>Times New</option>
									<option value='terbutchet' <?php echo (($cssStyle['#poll_widget ul#poll_answers li span']['font-family']=='terbutchet !important')?'selected':''); ?>>Terbutchet</option>
									<option value='verdana' <?php echo (($cssStyle['#poll_widget ul#poll_answers li span']['font-family']=='verdana !important')?'selected':''); ?>>Verdana</option>
								  </select>
							<input type='hidden' id='answer_font_type_change' name='answer_font_type_change' value=''>
						  </div>
						  <?php
							if (strpos($cssStyle['#poll_widget ul#poll_answers li span']['font-size'],'!important') !== false) {
								$slider_range_min8 = str_replace("px !important","",$cssStyle['#poll_widget ul#poll_answers li span']['font-size']);
							}
							else{
								$slider_range_min8 = str_replace("px","",$cssStyle['#poll_widget ul#poll_answers li span']['font-size']);
							}
							?>
						  <div class='form-group'>
							  <label for='answer_font_size'>Answer font size</label>
								<div id='slider-range-min8'></div>
								<input type='text' name='answer_font_size' id='answer_font_size' class='slider-amount' readonly style='border:0; color:#f6931f; font-weight:bold;' value="<?php echo $cssStyle['#poll_widget ul#poll_answers li span']['font-size']; ?>">
								<input type='hidden' id='answer_font_size_change' name='answer_font_size_change' value=''>
								<script>
									jQuery(function() {
										jQuery( '#slider-range-min8' ).slider({
											range: 'min',
											value: <?php echo $slider_range_min8; ?>,
											min: 6,
											max: 30,
											change: function( event, ui ) {
												jQuery( '#answer_font_size' ).val( ui.value + 'px' );
											},
											slide: function( event, ui ) {
												jQuery( '#answer_font_size' ).val( ui.value + 'px' );
												jQuery('#poll_answers li label span').attr('style', function(i,s) { return s + 'font-size:'+ui.value+'px !important; '});
											}
										});
										jQuery( '#answer_font_size' ).val( jQuery( '#slider-range-min8' ).slider( 'value' ) + 'px !important'  );
									});
								</script>
						  </div>
						  <div class="form-group">
							<label for="answer_font_color">Answer font color</label>
							<div id="answer_font_color_picker"></div>
							<input type='hidden' class='color form-control' name='answer_font_color' id='answer_font_color' value='<?php echo $cssStyle['#poll_widget ul#poll_answers li span']['color']; ?>'>
							<input type='hidden' id='answer_font_color_change' name='answer_font_color_change' value=''>
							<script>
								jQuery(function() {
									jQuery('#answer_font_color_picker').colpick({
										flat:true,
										layout:'hex',
										submit:0,
										color:'<?php echo str_replace("px !important","",$cssStyle['#poll_widget ul#poll_answers li span']['color']); ?>',
										onChange:function(hsb,hex,rgb,el,bySetColor){
											jQuery("#answer_font_color").val('#' + hex+" !important;");
											jQuery('#poll_answers li label span').attr('style', function(i,s) { return s + 'color:#'+hex+' !important;'});
											
										}
									});
								});
							</script>
						  </div>
						  <div class="form-group">
							<label for="answer_background_color">Answer background color</label>
							<div id="answer_background_color_picker"></div>
							<input type='hidden' class='color form-control' name='answer_background_color' id='answer_background_color' value='<?php echo $cssStyle['#poll_widget ul#poll_answers']['background']; ?>'>
							<input type='hidden' id='answer_background_color_change' name='answer_background_color_change' value=''>
							<script>
								jQuery(function() {
									jQuery('#answer_background_color_picker').colpick({
										flat:true,
										layout:'hex',
										submit:0,
										color:'<?php echo str_replace("# !important","",$cssStyle['#poll_widget ul#poll_answers']['background']); ?>',
										onChange:function(hsb,hex,rgb,el,bySetColor){
											jQuery("#answer_background_color").val('#' + hex+" !important");
											jQuery('#poll_answers').attr('style', function(i,s) { return s + 'background:#'+hex+' !important;' });
											jQuery("#poll_answers").css("margin",'auto 20px');
											jQuery('#poll_answers').attr('style', function(i,s) { return s + 'background:#'+hex+' !important;' });
										}
									});
								});
							</script>
						  </div>
						  <?php
								$temp=explode("#",$cssStyle['#poll_widget ul#poll_answers']['border']);
								$temp1=explode("px",$cssStyle['#poll_widget ul#poll_answers']['border']);
								
						  ?>
						  <div class="form-group">
							<label for="answer_border_color">Answer border color</label>
							<div id="answer_border_color_picker"></div>
							<input type='hidden' class='color form-control' name='answer_border_color' id='answer_border_color' value=''>
							<input type='hidden' id='answer_border_color_change' name='answer_border_color_change' value=''>
							<script>
								jQuery(function() {
									jQuery('#answer_border_color_picker').colpick({
										flat:true,
										layout:'hex',
										submit:0,
										color:'<?php echo (isset($temp[1]) ? $temp[1] : ''); ?>',
										onChange:function(hsb,hex,rgb,el,bySetColor){
										
											jQuery("#answer_border_color").val('#' + hex+" !important");
											jQuery('#poll_answers').attr('style', function(i,s) { return s + 'border-color:#'+hex+' !important;' });
											
											jQuery('#poll_answers').attr('style', function(i,s) { return s + 'border-style:solid'+' !important;'});
											
											jQuery("#poll_answers").css("margin",'auto 20px');
										}
									});
								});
							</script>
						  </div>
						  <div class='form-group'>
							  <label for='answer_border_width' >Answer border width</label>
								<div id='slider-range-min9'></div>
								<input type='text' name='answer_border_width' id='answer_border_width' value='<?=$temp1[0]?>' class='slider-amount' readonly style='border:0; color:#f6931f; font-weight:bold;'>
								<input type='hidden' id='answer_border_width_change' name='answer_border_width_change' value=''>
								<script>
									jQuery(function() {
										jQuery( '#slider-range-min9' ).slider({
											range: 'min',
											value: '<?php echo $temp1[0]; ?>',
											min: 0,
											max: 10,
											change: function( event, ui ) {
												jQuery( '#answer_border_width' ).val( ui.value + 'px !important' );
											},
											slide: function( event, ui ) {
												jQuery( '#answer_border_width' ).val( ui.value + 'px !important' );
												jQuery("#poll_answers").css("margin",'auto 20px');
												jQuery('#poll_answers').attr('style', function(i,s) { return s + 'border-width:'+ui.value+'px !important;' });
												
											}
										});
										jQuery( '#answer_border_width' ).val( jQuery( '#slider-range-min9' ).slider( 'value' ) + 'px !important'  );
									});
								</script>

						  </div>
						  <div class='form-group'>
						<?php
							if (strpos($cssStyle['#poll_widget ul#poll_answers']['border-radius'],'!important') !== false) {
								$answer_border_radius = str_replace("px !important","",$cssStyle['#poll_widget ul#poll_answers']['border-radius']);
							}
							else{
								$answer_border_radius = str_replace("px","",$cssStyle['#poll_widget ul#poll_answers']['border-radius']);
							}
						 ?>
							  <label for='answer_border_radius'>Answer border radius</label>
								<div id='slider-range-min10'></div>
								<input type='text' name='answer_border_radius' id='answer_border_radius' value='<?=$cssStyle['#poll_widget ul#poll_answers']['border-radius']?>' class='slider-amount' readonly style='border:0; color:#f6931f; font-weight:bold;'>
								<input type='hidden' id='answer_border_radius_change' name='answer_border_radius_change' value=''>
								<script>
									jQuery(function() {
										jQuery( '#slider-range-min10' ).slider({
											range: 'min',
											value: <?php echo $answer_border_radius; ?>,
											min: 0,
											max: 50,
											change: function( event, ui ) {
												jQuery( '#answer_border_radius' ).val( ui.value + 'px !important' );
											},
											slide: function( event, ui ) {
												jQuery( '#answer_border_radius' ).val( ui.value + 'px !important' );
												jQuery('#poll_answers').attr('style', function(i,s) { return s + 'border-radius:'+ui.value+'px !important;' });
											}
										});
										jQuery( '#answer_border_radius' ).val( jQuery( '#slider-range-min10' ).slider( 'value' ) + 'px !important'  );
									});
								</script>
						  </div>
						  <div class='form-group'>
							  <label for='button_font_type'>Button font type</label>
							  <select name='button_font_type' id='button_font_type'>
								<option value='arial' <?php echo (($cssStyle['#poll_widget #poll_button .btn-widget']['font-family']=='arial !important')?'selected':''); ?>>Arial</option>
								<option value='courier new' <?php echo (($cssStyle['#poll_widget #poll_button .btn-widget']['font-family']=='courier new !important')?'selected':''); ?>>Courier New</option>
								<option value='georgia' <?php echo (($cssStyle['#poll_widget #poll_button .btn-widget']['font-family']=='georgia !important')?'selected':''); ?>>Georgia</option>
								<option value='sans-serif' <?php echo (($cssStyle['#poll_widget #poll_button .btn-widget']['font-family']=='sans-serif !important')?'selected':''); ?>>Sans-Serif</option>
								<option value='tohama' <?php echo (($cssStyle['#poll_widget #poll_button .btn-widget']['font-family']=='tohama !important')?'selected':''); ?>>Tohama</option>
								<option value='times new' <?php echo (($cssStyle['#poll_widget #poll_button .btn-widget']['font-family']=='times new !important')?'selected':''); ?>>Times New</option>
								<option value='terbutchet' <?php echo (($cssStyle['#poll_widget #poll_button .btn-widget']['font-family']=='terbutchet !important')?'selected':''); ?>>Terbutchet</option>
								<option value='verdana' <?php echo (($cssStyle['#poll_widget #poll_button .btn-widget']['font-family']=='verdana !important')?'selected':''); ?>>Verdana</option>
							  </select>
							  <input type='hidden' id='button_font_type_change' name='button_font_type_change' value=''>
						  </div>
						  <div class='form-group'>
							  <label for='button_font_size' >Button font size</label>
								<div id='slider-range-min1'></div>
								<input type='text' name='button_font_size' id='button_font_size' class='slider-amount' readonly style='border:0; color:#f6931f; font-weight:bold;'>
								<input type='hidden' id='button_font_size_change' name='button_font_size_change' value=''>
								<script>
									jQuery(function() {
										jQuery( '#slider-range-min1' ).slider({
											range: 'min',
											value: '<?php echo str_replace("px !important","",$cssStyle['#poll_widget #poll_button .btn-widget']['font-size']); ?>',
											min: 6,
											max: 30,
											change: function( event, ui ) {
												jQuery('#button_font_size' ).val( ui.value + 'px !important' );
											},
											slide: function( event, ui ) {
												jQuery( '#button_font_size' ).val( ui.value + 'px !important' );
												jQuery('#poll_button .btn.btn-widget').attr('style', function(i,s) { return s + 'font-size:'+ui.value+'px !important;' });
											}
										});
										jQuery( '#button_font_size' ).val( jQuery( '#slider-range-min1' ).slider( 'value' ) + 'px !important'  );
									});
								</script>

						  </div>
						  <div class="form-group">
							<label for="button_font_color">Button font color</label>
							<div id="button_font_color_picker"></div>
							<input type='hidden' class='color form-control' name='button_font_color' id='button_font_color' value='<?php echo $cssStyle['#poll_widget #poll_button .btn-widget']['color']; ?>'>
							<input type='hidden' id='button_font_color_change' name='button_font_color_change' value=''>
							<script>
								jQuery(function() {
									jQuery('#button_font_color_picker').colpick({
										flat:true,
										layout:'hex',
										submit:0,
										color:'<?php echo str_replace("# !important","",$cssStyle['#poll_widget #poll_button .btn-widget']['color']); ?>',
										onChange:function(hsb,hex,rgb,el,bySetColor){
											jQuery("#button_font_color").val('#' + hex+" !important");
											jQuery('#poll_button .btn.btn-widget').attr('style', function(i,s) { return s + 'color:#'+hex+' !important;'});
										}
									});
								});
							</script>
						  </div>
						  <div class="form-group">
							<label for="button_background_color">Button background color</label>
							<div id="button_background_color_picker"></div>
							<input type='hidden' class='color form-control' name='button_background_color' id='button_background_color' value='<?php echo $cssStyle['#poll_widget #poll_button .btn-widget']['background']; ?>'>
							<input type='hidden' id='button_background_color_change' name='button_background_color_change' value=''>
							<script>
								jQuery(function() {
									jQuery('#button_background_color_picker').colpick({
										flat:true,
										layout:'hex',
										submit:0,
										color: '<?php echo str_replace("# !important","",$cssStyle['#poll_widget #poll_button .btn-widget']['background']); ?>',
										onChange:function(hsb,hex,rgb,el,bySetColor){
											jQuery("#button_background_color").val('#' + hex+ " !important");
											jQuery('#poll_button .btn.btn-widget').attr('style', function(i,s) { return s + 'background:#'+hex+' !important;'});
										}
									});
								});
							</script>
						  </div>
						  <?php 
								$temp=explode("#",$cssStyle['#poll_widget #poll_button .btn-widget']['border']);
								$temp1=explode("px",$cssStyle['#poll_widget #poll_button .btn-widget']['border']);
						  ?>
						  <div class="form-group">
							<label for="button_border_color">Button border color</label>
							<div id="button_border_color_picker"></div>
							<input type='hidden' class='color form-control' name='button_border_color' id='button_border_color' value='#<?php echo (isset($temp[1]) ? $temp[1] : ''); ?>'>
							<input type='hidden' id='button_border_color_change' name='button_border_color_change' value=''>
							<script>
								jQuery(function() {
									jQuery('#button_border_color_picker').colpick({
										flat:true,
										layout:'hex',
										submit:0,
										color:'<?php echo (isset($temp[1]) ? $temp[1] : ''); ?>',
										onChange:function(hsb,hex,rgb,el,bySetColor){
											jQuery("#button_border_color").val('#' + hex+" !important");
											jQuery('#poll_button .btn-widget').attr('style', function(i,s) { return s + 'border-color:#'+hex+' !important;'});
										}
									});
								});
							</script>
						  </div>
						  <div class='form-group'>
							  <label for='button_border_width'>Button border width</label>
       						  <div id='slider-range-min6'></div>
								<input type='text' name='button_border_width' id='button_border_width' class='slider-amount' readonly style='border:0; color:#f6931f; font-weight:bold;'>
								<input type='hidden' id='button_border_width_change' name='button_border_width_change' value=''>
								<script>
									jQuery(function() {
										jQuery( '#slider-range-min6' ).slider({
											range: 'min',
											value: '<?php echo $temp1[0]; ?>',
											min: 0,
											max: 10,
											change: function( event, ui ) {
												jQuery('#button_border_width').val( ui.value + 'px !important' );
											},
											slide: function( event, ui ) {
												jQuery('#button_border_width').val( ui.value + 'px !important' );
											jQuery('#poll_button .btn-widget').attr('style', function(i,s) { return s + 'border-width:'+ui.value +"px !important;"});
											}
										});
										jQuery( '#button_border_width' ).val( jQuery( '#slider-range-min6' ).slider( 'value' ) + 'px !important'  );
									});
								</script>

						  </div>
						  <div class='form-group'>
							<label for='button_border_radius'>Button border radius</label>
								<div id='slider-range-min7'></div>
								<input type='text' name='button_border_radius' id='button_border_radius' class='slider-amount' readonly style='border:0; color:#f6931f; font-weight:bold;'>
								<input type='hidden' id='button_border_radius_change' name='button_border_radius_change' value=''>
								<script>
									jQuery(function() {
										jQuery( '#slider-range-min7' ).slider({
											range: 'min',
											value: '<?php echo str_replace("px !important","",$cssStyle['#poll_widget #poll_button .btn-widget']['border-radius']); ?>',
											min: 0,
											max: 50,
											change: function( event, ui ) {
												jQuery( '#button_border_radius' ).val( ui.value + 'px !important' );
											},
											slide: function( event, ui ) {
												jQuery( '#button_border_radius' ).val( ui.value + 'px !important' );
												jQuery('#poll_button .btn-widget').attr('style', function(i,s) { return s + 'border-radius:'+ui.value+'px !important;' });
											}
										});
										jQuery( '#button_border_radius' ).val( jQuery( '#slider-range-min7' ).slider( 'value' ) + 'px !important'  );
									});
								</script>
						  </div>
						  <div class="form-group">
							<label for="poll_background_image_file">Poll background image</label> 

							<?php
							if(strpos($cssStyle['#poll_widget']['background'],"url") === 0){
								$temp=explode("(",$cssStyle['#poll_widget']['background']);
								$temp1=explode(")",$temp[1]);
								$imgname=substr(strrchr($temp1[0], "/"), 1); 
								 ?>
								

							<input id="upload_image" type="text" size="36" name="poll_background_image_file" value="<?php echo $temp1[0]; ?>" />

							<input type="file" class="color " name="poll_background_image_file" id="upload_image_button" value="Upload Image">

								<?php
								echo "<div class='preview-wrapper'><span>&#10799;</span><img class='preview-img' width='150' src='".$temp1[0]."'/><input id='poll_background_image' type='hidden' name='poll_background_image' value='".$imgname."'></div><div class='clear'></div>";
							}
						?>
							<script>
								jQuery(document).on('click', 'div.preview-wrapper span',function() {
										jQuery('#upload_image').val('');
										jQuery('.preview-wrapper').remove();
										jQuery("#poll_widget").css("cssText", "background:#"+jQuery('#poll_background_color').val());
										
								});
							</script>
						  </div>
						  <?php 
								if(strpos($cssStyle['#poll_widget']['background'],"#") === 0){
									$poll_bg=str_replace("#","",$cssStyle['#poll_widget']['background']);
								}else{ 
									$poll_bg='FFFFFF';
								}
						  ?>
						  <div class="form-group">
							<label for="poll_background_color">Poll background color</label>
							<div id="poll_background_color_picker"></div>
							<input type='hidden' class='color form-control' name='poll_background_color' id='poll_background_color' value=''>
							<input type='hidden' id='poll_background_color_change' name='poll_background_color_change' value=''>
							<script>
								jQuery(function() {
									jQuery('#poll_background_color_picker').colpick({
										flat:true,
										layout:'hex',
										submit:0,
										color:'<?php echo $poll_bg; ?>',
										onChange:function(hsb,hex,rgb,el,bySetColor){
											jQuery("#poll_background_color").val('#' + hex);
											jQuery("#poll_background_color_change").val('#' + hex);
											jQuery('#poll_widget').attr('style', function(i,s) { return s +';background:#'+hex+' !important;' });
										}
									});
								});
							</script>
						  </div>
						  <?php
								$temp=explode("#",$cssStyle['#poll_widget']['border']);
								$temp1=explode("px",$cssStyle['#poll_widget']['border']);
						  ?>
						  <div class="form-group">
							<label for="poll_border_color">Poll border color</label>
							<div id="poll_border_color_picker"></div>
							<input type='hidden' class='color form-control' name='poll_border_color' id='poll_border_color' value='#<?php echo (isset($temp[1]) ? $temp[1] : ''); ?>'>
							<input type='hidden' id='poll_border_color_change' name='poll_border_color_change' value=''>
							<script>
								jQuery(function() {
									jQuery('#poll_border_color_picker').colpick({
										flat:true,
										layout:'hex',
										submit:0,
										color : '<?php echo (isset($temp[1]) ? $temp[1] : ''); ?>',
										onChange:function(hsb,hex,rgb,el,bySetColor){
											jQuery("#poll_border_color").val('#' + hex+" !important");
											jQuery('#poll_widget').attr('style', function(i,s) { return s + 'border-color:#'+hex+' !important;'});
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
													//$("#poll_answers .answer_label").css('background-color','#' + hex);
													//$("#poll_widget").css("border","#"+hex+' solid '+$("#poll_border_width").val());
												}
											});
										});
									</script>
									
					              </div>
						  <div class='form-group'>
							<label for='poll_border_width' class='col-sm-3 control-label'>Poll border width</label>
									<div id='slider-range-min2'></div>
									<input type='text' name='poll_border_width' id='poll_border_width' class='slider-amount' readonly style='border:0; color:#f6931f; font-weight:bold;'>
									<input type='hidden' id='poll_border_width_change' name='poll_border_width_change' value=''>
									<script>
										jQuery(function() {
											jQuery( '#slider-range-min2' ).slider({
												range: 'min',
												value: '<?php echo $temp1[0]; ?>',
												min: 0,
												max: 10,
												change: function( event, ui ) {
													jQuery( '#poll_border_width' ).val( ui.value + 'px !important' );
												},
												slide: function( event, ui ) {
													jQuery( '#poll_border_width' ).val( ui.value + 'px !important' );
													jQuery( '#qp_b1' ).css('border-width', ui.value + 'px' );
												}
											});
											jQuery( '#poll_border_width' ).val( jQuery( '#slider-range-min2' ).slider( 'value' ) + 'px !important'  );
										});
									</script>

						  </div>
						  <div class='form-group'>
							<label for='poll_border_radius' class='col-sm-3 control-label'>Poll border radius</label>
								<div id='slider-range-min3'></div>
								<input type='text' name='poll_border_radius' id='poll_border_radius' class='slider-amount' readonly style='border:0; color:#f6931f; font-weight:bold;'>
								<input type='hidden' id='poll_border_radius_change' name='poll_border_radius_change' value=''>
								<script>
									jQuery(function() {
										var val = "<?php echo str_replace("px !important","",$cssStyle['#poll_widget']['border-radius']); ?>";
										val = val.replace ( /[^\d.]/g, '' ).trim();
										jQuery( '#slider-range-min3' ).slider({
											range: 'min',
											value: val,
											min: 0,
											max: 50,
											change: function( event, ui ) {
												jQuery( '#poll_border_radius' ).val( ui.value + 'px !important' );
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
			         <!------------------------customize ------------------------->
						<script>
							(function(jQuery){
								jQuery(window).load(function(){
									jQuery('#customize').mCustomScrollbar({
										theme:'dark'
									});
								});
							})(jQuery);
						</script>
						<script>
							(function(jQuery){
								jQuery(window).load(function(){
									jQuery(".box-holder").mCustomScrollbar({
										theme:"minimal"
									});
								});
							})(jQuery);
						</script>
						<script>
							(function(jQuery){
								jQuery(window).load(function(){
									jQuery('#settings').mCustomScrollbar({
										theme:'dark'
									});	
								});
							})(jQuery);
						</script>
						<div id="embed_holder" class="live_form tabbed">
							<?php
							$iframe_string = "<iframe src='".$res['viewUrl']."' width='550' height='362' scrolling='0' frameborder='0'></iframe>";
							?>
							<div class="input-group">
							  <span class="input-group-addon">Share</span>					
							  <input type="text" class="form-control" value="<?php echo 
							  $res['viewUrl']; ?>">
							</div>
							<div class="input-group">
							  <span class="input-group-addon">Embed</span>
							  <input type="text" class="form-control" value="<?php echo $iframe_string; ?>">
							</div>
							<div class="input-group">
							  <a target="_blank" href="https://twitter.com/share?url=https://www.polldeep.com/<?php echo $poll->uniqueid; ?>&text=<?php  echo urlencode($poll->question); ?>" class="btn btn-transparent">Share on Twitter</a>
							  <a target="_blank" href="https://www.facebook.com/sharer.php?u=https://www.polldeep.com/<?php echo $poll->uniqueid; ?>" class="btn btn-transparent">Share on Facebook</a>
							</div> 
						  </div>
						  <p>
							<button type='submit' id="editSubmit" class='editSubmit btn btn-primary btn-lg'>Done</button>
							<a id='view-btn' href='<?php  echo 
							$res['viewUrl']; ?>' class='btn btn-success btn-lg pull-right' target='_blank'>Preview</a>
						  </p>
						  <input type="hidden" class="form-control" id="editForm" name="editForm" value="1">
						  <input type="hidden" class="form-control" id="id" name="id" 
						  value="<?php echo intval($_REQUEST['id']); ?>">
						  <input type="hidden" name="merchantID" value="<?php echo $merchantID ?>" id="merchantID">
						  <input type="hidden" name="secretKey" value="<?php echo $secretKey ?>" id="secretKey">
						   <input type="hidden" class="form-control" id="custom_theme_id" 
						   name="custom_theme_id" value="<?php echo 
						   (isset($_REQUEST['theme']) ? sanitize_text_field($_REQUEST['theme']):$poll->theme); ?>">

					  </form>
				<!--********** End Form ********************* -->
				  </div>
			  </div> 
			 
			  <div class="col-md-7 previewWD content">
			  	<div class="outer-poll-wrap">  
			  		<div class="poll_wrap" id="poll_wrap">  
			  		<!-- Custom Logo start -->  
				  		<div class="poll-display site_logo" id="poll_top_of_bar_logos">
							<?php if (!empty($res['logo'])): ?>
							<?php if($poll->custom_logo) {
							  ?>
							  <a href="<?php echo $res['url']; ?>/static/member_logo/<?php echo $user->custom_logo; ?>">
								<img src="<?php echo $res['url'] ?>/static/member_logo/<?php echo $user->custom_logo ?>" alt="<?php //echo $this->config["title"] ?>">
							  </a>
							
							  <?php
								}else{
							  ?>
							
								<a href="<?php echo $res['url'] ?>"><img src="" alt="<?php  // echo $this->config["title"] ?>"></a>
							<?php
							
								}
							 else: ?>
								<h3><a href="<?php echo $res['url'] ?>"><?php// echo $this->config["title"] ?></a></h3>
							<?php endif ?>
						</div> 
			  		<!-- Custom Logo start -->  
						  <!-- End logo bar -->


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
						  
							
						  <div id="poll_question">
							<h3><?php echo $res['poll']['question']; ?></h3>
						  </div>
						  <ul id="poll_answers">
							  <?php
							  $options=$res['options']; 
							  $key=0;
							  foreach($options as $option):
							   $option=(object)$option;
							   $key++;
							  ?>
								<li id='poll-<?php echo $key;?>'>
									<label>
										<input type='radio' name='answer' value=''> 
										<span><?php echo $option->answer ?></span>
										<div class="answer_label"></div>
									</label>
								</li>
							  <?php endforeach; ?>
						  </ul>
						  <div id="poll_button">
							<button class="btn btn-widget">Vote</button>
							<button class="btn btn-widget" id="view_results_button">View Results</button>
							<?php if(($res['membership_id']!="6" && $res['ispro']!='pro')): ?>
							  <span class="branding pull-right">Powered by<a href="<?php echo $res["url"] ?>"><?php echo $res["from_title"] ?></a>
							  </span>
							<?php endif; ?>
						  </div>
						</div>
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
			     <?php 
					echo html_entity_decode($poll->style);
				  ?>
			<!--Poll Widget -->
			  </div>
		  </div>
	</section>
</div>
