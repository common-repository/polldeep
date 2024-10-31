<?php if ( ! defined( 'ABSPATH' ) ) exit; ?>
<?php
global $current_user,$polldeepServer;
$user_id=$current_user->ID;
$merchantID=get_user_meta($user_id,'merchantID',true);
$secretKey=get_user_meta($user_id,'secretKey',true);
?> 
<div class="createpoll user-dash">
	<section>
	<div class="dashboard">
		<div class="col-md-9 content">
		   <div class="row">
			<div class="col-md-6">
				<h2>Polldeep</h2> 
				  <div class="notAuthenticate">
						<div class="updated notice notice-success is-dismissible below-h2" style="display:none" id="message"><p></p></div>
						<form id='authenticate_form' method='get' class="box-holder" action='admin.php?page=polldeep'>
							<div class="form-group">
								<label for="merchantID">Merchant Id</label>
								<input type="text" name="merchantID" placeholder="Enter Merchant Id" value="<?php echo $merchantID ?>" id="merchantID" class="form-control">
							</div>
							<div class="form-group">
								<label for="merchantID">Secret Key</label>
								<input type="text" name="secretKey" placeholder="Enter Secret Key" value="<?php echo $secretKey ?>" id="secretKey" class="form-control">
							</div>
							<input type='hidden' value='polldeep-dash' name="page"/>		
							<input type='hidden' value='1' name="pagenum"/>	
							<input type='hidden' value='<?php echo admin_url('admin-ajax.php') ?>' id="ajaxurl" name="ajaxurl"/>	
							<input type='hidden' id="domain" value='<?php echo $_SERVER['SERVER_NAME']; ?>' name="domain"/>	
							<input type="submit" id="authenticateBtn" class="btn btn-primary" name="authenticateBtn" value="Authenticate">
						</form>
					</div>
				</div>
			</div>
		</div>
	</div>
	</section>
</div>
