<?php
/*
Plugin Name: PollDeep
Plugin URI: https://polldeep.com/
Description: This plugin lets you create , edit, view, analyze, delete, open, close poll from your own wordpress website. So you can manage this all poll  functionalities from your own website rather than polldeep site.

It is an online tool that you can create awesome polls with. A poll created with PollDeep plugin gives more than just the voter’s answer. PollDeep digs deeper into their profile, providing you with their home location, gender and age bracket.
PollDeep helps you to understand what influences the answers to your questions and enables you to quickly spot trends in your home country and around the world.

We will continue adding maps and more features. Our aim is to become the number one online polling tool. Get started now and have a free poll on us. With your PollDeep plugin dashboard, you can create a poll and change the look of it to suit your style and brand. Embed your poll in your website or blog, email it to a closed user group or share it on Facebook and Twitter. Find out the results of your poll and the profiles of your voters. Quickly spot trends and recognize the similarities and differences.

Version: 1.3.0
Author: www.smartinfosys.net
Author URI: https://www.smartinfosys.net/
License: GPL2
*/ 
/*  Copyright 2018 Polldeep  (email : support@polldeep.com)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as
    published by the Free Software Foundation. 

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/
if ( ! defined( 'ABSPATH' ) ) exit;
 
    global $polldeepServer,$preview;
   
	$polldeepServer='https://www.polldeep.com/';
	$preview=''; 
/*
==========================================================
Enqueue registered script  And Style in the admin.
==========================================================
*/
	function polldeep_plugin_admin_scripts() 
	{  
		global $polldeep_admin_page, $polldeep_admin_create_page, $polldeep_admin_dashboard_page, $polldeep_admin_active_page, $polldeep_admin_expired_page;
		
		$hook = $screen = get_current_screen();
		
		if (!in_array($hook->id, array($polldeep_admin_page, $polldeep_admin_create_page, $polldeep_admin_dashboard_page, $polldeep_admin_active_page, $polldeep_admin_expired_page)))
		{    
			//Plugin logo
			wp_register_style( 'plugin-logo', polldeep_plugin_url().'css/logo.css',false, polldeep_get_version(), 'all' );
			wp_enqueue_style( 'plugin-logo' );
			 return;
		}
		if(is_admin() )
		{    
			 // Custom Style Register 

			 wp_register_style( 'jquery-ui-css', polldeep_plugin_url().'css/jquery-ui.css',false, polldeep_get_version(), 'all' ); 
			 wp_register_style( 'bootstrap-min', polldeep_plugin_url().'css/bootstrap.min.css',false, polldeep_get_version(), 'all' ); 
			 wp_register_style( 'styleSmart', polldeep_plugin_url(). 'css/style.css',false, polldeep_get_version(), 'all' ); 
			 wp_register_style( 'selectize', polldeep_plugin_url() .'css/selectize.css', false, polldeep_get_version(), 'all' ); 
			 wp_register_style( 'colpick', polldeep_plugin_url() .'css/colpick.css', false, polldeep_get_version(), 'all' ); 
			 wp_register_style( 'chosen-min', polldeep_plugin_url() .'css/chosen.min.css', false, polldeep_get_version(), 'all' ); 
			 wp_register_style( 'mCustomScrollbar', polldeep_plugin_url().'css/jquery.mCustomScrollbar.css',false, polldeep_get_version(), 'all' );
			 wp_register_style( 'all', polldeep_plugin_url(). 'flat/_all.css',false, polldeep_get_version(), 'all' );
			 wp_register_style( 'responsiveSmart', polldeep_plugin_url(). 'css/responsive.css', false, polldeep_get_version(), 'all' );
			 wp_register_style( 'widgetsmart', polldeep_plugin_url().'css/widgetsmart.css',false, polldeep_get_version(), 'all' );
			 wp_register_style( 'customSmart', polldeep_plugin_url().'css/custom.css',false, polldeep_get_version(), 'all' );
			
			 // Custom Script Register 

			 wp_register_script( 'jquery-ui-js',  polldeep_plugin_url().'js/jquery-ui.js', array( 'jquery' ),false, polldeep_get_version(), 'all'  );
			 wp_register_script( 'ajaxCalls',  polldeep_plugin_url().'js/ajaxCalls.js', array( 'jquery' ),false, polldeep_get_version(), 'all'  );
			 wp_register_script( 'pollScripts',  polldeep_plugin_url().'js/pollScripts.js', array( 'jquery' ),false, polldeep_get_version(), 'all'   );
			 wp_register_script( 'mCustomScrollbar',  polldeep_plugin_url().'js/jquery.mCustomScrollbar.concat.min.js', array( 'jquery' ) ,false, polldeep_get_version(), 'all'  );
			 wp_register_script( 'bootstrap-min',  polldeep_plugin_url().'js/bootstrap.min.js', array( 'jquery' ),false, polldeep_get_version(), 'all'   );
			 wp_register_script( 'icheck',  polldeep_plugin_url().'js/icheck.min.js', array( 'jquery' ) ,false, polldeep_get_version(), 'all'  );wp_register_script( 'icheck',  polldeep_plugin_url().'js/icheck.min.js', array( 'jquery' ) ,false, polldeep_get_version(), 'all'  );
			 wp_register_script( 'colpick',  polldeep_plugin_url().'js/colpick.js', array( 'jquery' ) ,false, polldeep_get_version(), 'all'  );
			 wp_register_script( 'chosen-min',  polldeep_plugin_url().'js/chosen.min.js', array( 'jquery' ) ,false, polldeep_get_version(), 'all'  );
			 wp_register_script( 'fileupload',  polldeep_plugin_url().'js/jquery.fileupload.js', array( 'jquery' ) ,false, polldeep_get_version(), 'all'  );  
			 wp_register_script( 'flot',  polldeep_plugin_url().'js/flot.js', array( 'jquery' ) ,null, false  );  
			 wp_register_script( 'jvector',  polldeep_plugin_url().'js/jvector.js', array( 'jquery' ) ,null, false  );  
			 wp_register_script( 'jvector-world',  polldeep_plugin_url().'js/jvector.world.js', array( 'jquery' ) ,null, false  );  
			 
			 
			 // Wordpress Scripts Enqueue 
			 wp_enqueue_script('media-upload');
			wp_enqueue_script('thickbox');
			wp_enqueue_script('jquery');
			 
			 wp_enqueue_script( 'jquery-core' );
			 wp_enqueue_script( 'jquery-ui-widget' );
			 wp_enqueue_script( 'jquery-ui-sortable' );
			 wp_enqueue_script( 'jquery-ui-selectable' );
			 wp_enqueue_script( 'jquery-ui-resizable' );
			 wp_enqueue_script( 'jquery-ui-mouse' );
			 wp_enqueue_script( 'jquery-ui-progressbar' );
			 wp_enqueue_script( 'jquery-ui-datepicker' );
			 wp_enqueue_script( 'jquery-ui-slider' );
			 wp_enqueue_script( 'fileupload' );
			 
			 if(sanitize_text_field($_GET['action'])=='analyzeAjax')
			 {
			    wp_enqueue_script( 'flot' );
			    wp_enqueue_script( 'jvector' );
			    wp_enqueue_script( 'jvector-world' );
		     }
			 
			 // Custom scripts Enqueue 
			 wp_enqueue_script( 'jquery-ui-js' );
			 wp_enqueue_script( 'chosen-min' );
			 wp_enqueue_script( 'icheck' );
			 wp_enqueue_script( 'mCustomScrollbar' );
			 wp_enqueue_script( 'bootstrap-min' );
			 wp_enqueue_script( 'colpick' );
			 wp_enqueue_script( 'pollScripts' );
			 wp_enqueue_script( 'ajaxCalls' );
			 
			 // Wordpress Style Enqueue
			 wp_enqueue_style('thickbox');
			  
			 wp_enqueue_style( 'jquery-ui-css' );
			 wp_enqueue_style( 'jquery-core' );
			 wp_enqueue_style( 'jquery-ui-widget' );
			 wp_enqueue_style( 'jquery-ui-sortable' );
			 wp_enqueue_style( 'jquery-ui-selectable' );
			 wp_enqueue_style( 'jquery-ui-resizable' );
			 wp_enqueue_style( 'jquery-ui-mouse' );
			 wp_enqueue_style( 'jquery-ui-progressbar' );
			 wp_enqueue_style( 'jquery-ui-datepicker' );
			 wp_enqueue_style( 'bootstrap-min' );
			 wp_enqueue_style( 'customSmart' );
			 
			 // Custom Style Enqueue 
			 wp_enqueue_style( 'all' );
			 wp_enqueue_style( 'selectize' );
			 wp_enqueue_style( 'chosen-min' );
			 wp_enqueue_style( 'colpick' );
			 wp_enqueue_style( 'widgetsmart' );
			 wp_enqueue_style( 'mCustomScrollbar' );
			 wp_enqueue_style( 'styleSmart' );
			 wp_enqueue_style( 'responsiveSmart' );

			 
	    }
	}

	add_action( 'admin_enqueue_scripts', 'polldeep_plugin_admin_scripts' ); 
/*
==========================================================
 Returns current plugin version.
==========================================================
*/     
	function polldeep_get_version() {return '1.0';}

/*
==========================================================
Returns current plugin url
==========================================================
*/ 

	function polldeep_plugin_url() 
	{
		return plugin_dir_url( __FILE__ );
	}

/*
==========================================================
Returns current plugin Path
==========================================================
*/ 

	function polldeep_plugin_path() 
	{
		return plugin_dir_path( __FILE__ );
	}
	
/*
==========================================================
Add Manu
==========================================================
*/ 

	add_action('admin_menu', 'polldeep_plugin_setup_menu');
	function polldeep_plugin_setup_menu()
	{
		global $polldeep_admin_page, $polldeep_admin_create_page, $polldeep_admin_dashboard_page, $polldeep_admin_active_page, $polldeep_admin_expired_page;
			$polldeep_admin_page = add_menu_page( 'Polldeep', 'Polldeep', 'manage_options', 'polldeep', 'polldeep_poll_function' ,'dashicons-welcome-widgets-menus', 20);
	   
			$polldeep_admin_create_page = add_submenu_page( 'polldeep', 'Create Poll', 'Create Poll',
				'manage_options','create-poll', 'polldeep_createpoll_functions');
			$polldeep_admin_dashboard_page = add_submenu_page( 'polldeep', 'Dashboard', 'Dashboard',
				'manage_options','polldeep-dash', 'polldeep_Dashboard_function');
	   
			$polldeep_admin_active_page = add_submenu_page( 'polldeep', 'Active Polls', 'Active Polls',
				'manage_options','active-polls', 'polldeep_activepoll_function');
				
			$polldeep_admin_expired_page = add_submenu_page( 'polldeep', 'Expired Polls', 'Expired Polls',
				'manage_options','expired-polls', 'polldeep_expired_function');
			
	} 

/*
==========================================================
All Page Functions
==========================================================
*/ 		

	function polldeep_poll_function() 
	{   
		include_once( polldeep_plugin_path() . 'authenticate.php' );
	}

	function polldeep_createpoll_functions()
	{   
		global $current_user;
		$user_id=$current_user->ID;
		$merchantID=get_user_meta($user_id,'merchantID',true);
		$secretKey=get_user_meta($user_id,'secretKey',true);

		if($merchantID=='' || $secretKey=='')
		{   
			include_once( polldeep_plugin_path() . 'authenticate.php' );
		}  
		else if(sanitize_text_field($_GET['action'])=='preview')
		{   
		   echo preview();
		}
		else
		{
			include_once( polldeep_plugin_path() . 'createPoll.php' );
		}
	}

	function polldeep_dashboard_function()
	{   
		include_once( polldeep_plugin_path() . 'dashboard.php' );
	}
		
	function polldeep_activepoll_function()
	{
	   include_once( polldeep_plugin_path() . 'activePoll.php' );
	} 

	function polldeep_expired_function()
	{  
	   include_once( polldeep_plugin_path() . 'expiredPoll.php' );
	}

	function polldeep_editPoll()
	{  
	   include_once( polldeep_plugin_path() . 'editPoll.php' );
	}

	function polldeep_pollShare()
	{  
	   include_once( polldeep_plugin_path() . 'pollshare.php' );
	}

	function polldeep_analyze()
	{  
	   include_once( polldeep_plugin_path() . 'analyze.php' );
	}

	function polldeep_preview()
	{  
	   include_once( polldeep_plugin_path() . 'preview.php' );
	}
	include_once( polldeep_plugin_path() . 'upload.php' );

/*
==========================================================
CurlRequest 
==========================================================
*/ 	

	function polldeep_curlRequest()
	{	
		global $polldeepServer; 
		
		$call=sanitize_text_field($_REQUEST['call']);
		
		if($call=='authenticateUser')
		{
			$url = $polldeepServer.'/user/authenticate';
			$body = array(
			   'merchantID'=>sanitize_text_field($_REQUEST['merchantID']),
			   'secretKey'=>sanitize_text_field($_REQUEST['secretKey']),
			   'domain'=>$_SERVER['SERVER_NAME']
			  );
		}
		else if($call=='editAjax')
		{   
			$data=$_REQUEST;
			unset($data['action']);
			unset($data['call']);
			$url = $polldeepServer.'/user/editAjax';
			$body=$data;
		}
		else if($call=='createAjax')
		{   
			$data=$_REQUEST;
			unset($data['action']);
			$url = $polldeepServer.'/user/createAjax';
			$body=$data;
		}
		else if($call=='pollshareAjax')
		{  
			if(!isset($_REQUEST['recipients']))
			{
				$url = $polldeepServer.'/user/pollshareAjax';
				$body =array(
				   'merchantID'=>sanitize_text_field($_REQUEST['merchantID']),
				   'secretKey'=>sanitize_text_field($_REQUEST['secretKey']),
				   'id'=>intval($_REQUEST['id']),
				   'server'=>$polldeepServer
				 );
			}
			else
			{   
				$comment=urlencode(sanitize_text_field($_REQUEST['comment']));
				
				$url = $polldeepServer.'/user/pollshareAjax';
				$body =array(
				  'merchantID'=>sanitize_text_field($_REQUEST['merchantID']),
				  'secretKey'=>sanitize_text_field($_REQUEST['secretKey']),
				  'id'=>intval($_REQUEST['id']),
				  'recipients'=>sanitize_text_field($_REQUEST['recipients']),
				  'comment'=>$comment,
				  'server'=>$polldeepServer
				);
			}
		}
		else if($call=='serverAjax')
		{
			$url = $polldeepServer.'/user/serverAjax';
			$body =array(
				'merchantID'=>sanitize_text_field($_REQUEST['merchantID']),
				'secretKey'=>sanitize_text_field($_REQUEST['secretKey']),
				'id'=>intval($_REQUEST['id']),
				'request'=>sanitize_text_field($_REQUEST['request']),
				'value'=>sanitize_text_field($_REQUEST['value'])
			  );
		}
		else if($call=='deleteAjax')
		{
			$url = $polldeepServer.'/user/deleteAjax';
			$body =array(
			   'merchantID'=>sanitize_text_field($_REQUEST['merchantID']),
			   'secretKey'=>sanitize_text_field($_REQUEST['secretKey']),
			   'delete-id'=>sanitize_text_field($_REQUEST['deleteid'])
			   );
		}
		else if($call=='analyzeajax')
		{
			$url = $polldeepServer.'/user/analyzeajax';
			$body =array(
			   'merchantID'=>sanitize_text_field($_REQUEST['merchantID']),
			   'secretKey'=>sanitize_text_field($_REQUEST['secretKey']),
			   'id'=>intval($_REQUEST['id'])
			 ); 
		} else if ($call == 'upload_file_to_polldeep' && current_user_can('administrator')) {
			$url = $polldeepServer.'/user/fileUploadAjax';
			$body = array(
				'merchantID'=>sanitize_text_field($_REQUEST['merchantID']),
			  	'secretKey'=>sanitize_text_field($_REQUEST['secretKey']),
			  	'filename' => sanitize_text_field($_REQUEST['filename']),
			  	'myfile' => sanitize_text_field($_REQUEST['myfile'])
			);
		} else if($call == 'upload_file_to_polldeep' && !current_user_can('administrator')) {
			$unauthorized_error = array(
						"unauthorized"	=> 'true',
						);
				echo json_encode($unauthorized_error);  	
				die; 
		}
		
		 $args=array(
			'method' => 'POST',
			'timeout' => 45,
			'redirection' => 5,
			'httpversion' => '1.0',
			'blocking' => true,
			'headers' =>   array(),
			'cookies' => array(),
			'body' => $body,
		   ); 
		$result = wp_remote_post( $url, $args );
		if ($call == 'upload_file_to_polldeep') { 
			if (trim($result['body']) == 'file_upload_fails') {
				$failed_array = array("failed"=> "File upload on the polldeed server failed" );
				echo json_encode($failed_array);
			} else {
				$success_array = array(
							"success"	=> sanitize_file_name($_REQUEST['myfile'] ),
							"file_path" 	=> $result['body']
							);
				echo json_encode($success_array);  	 
			}
		} else {
			echo $result['body'];  
		}
		if($call=='authenticateUser')
		{
			$res = json_decode($result['body'], true);
			if($res['status']=='succ')
			{
				global $current_user;
				$user_id=$current_user->ID;
				$merchantID = sanitize_text_field($_REQUEST['merchantID']);
				$secretKey = sanitize_text_field($_REQUEST['secretKey']);
				update_user_meta( $user_id, 'merchantID', $merchantID);
				update_user_meta( $user_id, 'secretKey', $secretKey);
			}
		} 
		if(isset($_REQUEST['recipients']))
		{
			$res = json_decode($result['body'], true);
			 $headers  = 'From: '.$res["from"]."\r\n";
			 $headers  .= 'MIME-Version: 1.0' . "\r\n";
			 $headers .= 'Content-type: text/html; charset=utf-8' . "\r\n";
			 $subject=$res['subject'];
			 $message=$res['content'];
			 mail( $res["to"], $subject, $message, $headers);
			
		} 
		die();
	}
	add_action('wp_ajax_polldeep_curlRequest', 'polldeep_curlRequest');
	add_action('wp_ajax_nopriv_polldeep_curlRequest', 'polldeep_curlRequest');
	
/*
==========================================================
Footer Script File
==========================================================
*/ 	
   function polldeep_footer_script()
	{  
		global $polldeep_admin_page, $polldeep_admin_create_page, $polldeep_admin_dashboard_page, $polldeep_admin_active_page, $polldeep_admin_expired_page;
	
		$hook = $screen = get_current_screen();
	
		if (in_array($hook->id, array($polldeep_admin_page, $polldeep_admin_create_page, $polldeep_admin_dashboard_page, $polldeep_admin_active_page, $polldeep_admin_expired_page)))
		{
		   if(sanitize_text_field($_GET['action'])!='analyzeAjax')
			{
			  include_once( polldeep_plugin_path() . 'script.php' );
			}
		}
	}
	add_action('admin_footer','polldeep_footer_script');
