<?php if ( ! defined( 'ABSPATH' ) ) exit; ?>
<?php
global $current_user,$polldeepServer;
$user_id=$current_user->ID;

if(get_user_meta($user_id,'merchantID',true)!='')
{
	$merchantID=get_user_meta($user_id,'merchantID',true);
	$secretKey=get_user_meta($user_id,'secretKey',true);
}
else
{
	$merchantID='';
	$secretKey=''; 
} 
?>
<script>
jQuery(document).ready(function () 
{
	var secretKey='<?php echo $secretKey; ?>';
	var merchantID='<?php echo $merchantID ?>';
	<?php if(isset($_GET['action']) && sanitize_text_field($_GET['action'])=='preview') 
	 {
		?>  
		  var data = localStorage.getItem('data');
		  jQuery('#poll_widget').html(data);
		<?php 
	 }
	 
	 ?> 
});
</script>
<?php
