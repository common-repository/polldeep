<?php if ( ! defined( 'ABSPATH' ) ) exit; ?>
<style>
.jvectormap-label {
    position: absolute;
    display: none;
    border: solid 1px #CDCDCD;
    -webkit-border-radius: 3px;
    -moz-border-radius: 3px;
    border-radius: 3px;
    background: #292929;
    color: white;
    font-family: sans-serif, Verdana;
    font-size: smaller;
    padding: 3px;
} 

.jvectormap-zoomin, .jvectormap-zoomout {
    position: absolute;
    left: 10px;
    -webkit-border-radius: 3px;
    -moz-border-radius: 3px;
    border-radius: 3px;
    background: #292929;
    padding: 3px;
    color: white;
    padding: 3px;
    margin-bottom: 5px;
    cursor: pointer;
    line-height: 10px;
    text-align: center;
}

.jvectormap-zoomin {
    top: 10px;
}

.jvectormap-zoomout {
    top: 30px;
}
</style>
<div class="dashboardpoll">
			<div class="updated notice notice-success is-dismissible below-h2" style="display:none" id="message"><p></p></div>
			<input type='hidden' value='<?php echo admin_url('admin-ajax.php') ?>' id="ajaxurl" name="ajaxurl"/>
			<input type='hidden' value='<?php echo intval($_REQUEST['id']); ?>' id="pollid" name="pollid"/>	
			
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
			    'body' => array('merchantID'=>sanitize_text_field($merchantID),'secretKey'=>sanitize_text_field($secretKey),'id'=>intval($_REQUEST['id'])),
			   );
	
			$result = wp_remote_post(  $polldeepServer.'/user/analyzeajax', $args );
			
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
			 <div class="row">
		       <div class="col-md-11">
 				<div class="panel panel-default panel-dark">
				  <div class="panel-heading">
				  	Vote Charts
				  	<?php //if($res['ispro']=='pro' && $res["export"]): ?>
<!--
				  		<a href="<?php echo $res['exporturl']; ?>" class="btn btn-primary btn-xs pull-right">Export</a>
-->
				  	<?php //endif; ?>
				  </div>      
				  <div class="panel-body">
				     <div id="vote-chart" class='chart'></div>  
				  </div>  
				</div>	 				
 				<div class="panel panel-default panel-dark">
				  <div class="panel-heading">Country Analysis (Click country for more info)</div>  
				  <div class="panel-body">
				    <div class="col-md-6">
				      <div id="country-map" class='chart'></div>
				    </div>
				    <div class="col-md-6">
				      <div id="country_list">
				      	<h4>Top Countries</h4>
					      <ol> 
					      <?php  
					      $topcountries=$res['topcountries']['top_countries'];
					      
					      foreach ($topcountries as $country => $count):?>
					      <?php
					       $code['Australia']='AU'; 
					       $code['India']='IN'; 
					      ?>
					        <li><a href="javascript:void(0)" class="get_stats get_statsmap" data-id="<?php echo intval($_REQUEST['id']); ?>" data-request="country" data-value="<?php echo $code[$country]; ?>" data-target="country_list"><?php echo $country ?></a> <span class="label label-primary pull-right"><?php echo $count ?></span></li>
					      <?php endforeach ?>
					      </ol>
				      </div>
				    </div>     
				  </div>  
				</div>	
				<div class="row">
					<div class="col-md-6">
						<div class="panel panel-default panel-dark">
						  <div class="panel-heading">IP Analysis</div>      
						  <div class="panel-body">
					      <ol>
					      <?php 
					      $ips=$res['ips'];
					     
					      foreach ($ips as $ip):?>
					        <li><?php echo $ip['ip']; ?> <span class="label label-primary pull-right"><?php echo $ip['count']; ?></span></li>
					      <?php endforeach ?>
					      </ol>				     
						  </div>  
						</div>				
					</div>
					<div class="col-md-6">
						<div class="panel panel-default panel-dark">
						  <div class="panel-heading">Referral Analysis</div>      
						  <div class="panel-body">
								<div id="source">
							    <ol>
									
						      <?php
						     $refs=$res['refs'];
						      foreach ($refs as $ref):?>
						        <?php
						        if (empty($ref['domain'])): ?>
										<li>Direct, email and others<span class="label label-primary pull-right"><?php echo $ref['count']; ?></span></li>			        	
									<?php else: ?>
										<li><a href="<?php echo $res['serverUrl']; ?>" class="get_stats" data-id="<?php echo intval($_REQUEST['id']); ?>" data-request="source" data-value="<?php echo $ref['domain']; ?>" data-target="source"><?php echo $ref['domain']; ?></a> <span class="label label-primary pull-right"><?php echo $ref['count'];	 ?></span></li>								
						        <?php endif ?>
						      <?php endforeach ?>
						      </ol>									
								</div>
						  </div>  
						</div>				
					</div>
				</div>					
				</div>					
		  </div>	
		  				
       </section>
       <input type="hidden" value="<?php echo $merchantID; ?>" name="merchantID" id="merchantID">
	   <input type="hidden" value="<?php echo $secretKey; ?>" name="secretKey" id="secretKey">
</div> 

<script>
 <?php
 $data1=$res['chartsAjax']['data1'];
 $data=$res['chartsAjax']['data'];
 ?>
var options = {
            series: {
              lines: { show: true, lineWidth: 2,fill: true},
              //bars: { show: true,lineWidth: 1 },
              points: { show: true, lineWidth: 2 },
              shadowSize: 0
            },
            grid: { hoverable: true, clickable: true, tickColor: 'transparent', borderWidth:0 },
            colors: ['#FFFFFF', '#F11010', '#1F2227'],
            xaxis: {ticks:[<?php echo $data1; ?>], tickDecimals: 0, color: '#fff'},
            yaxis: {ticks:3, tickDecimals: 0, color: '#fff'},
            xaxes: [ { mode: 'time'} ]
        };
        var data = [{
            data: [<?php echo $data;  ?>]
        }];
        jQuery.plot('#vote-chart', data ,options);
</script>
<script type='text/javascript'>
var data=<?php echo $res['topcountries']['country'];  ?>; jQuery('#country-map').vectorMap({
	  map: 'world_mill_en',
	  backgroundColor: 'transparent',
	  series: {
		regions: [{
		  values: data,
		  scale: ['#74CBFA', '#0da1f5'],
		  normalizeFunction: 'polynomial'
		}]
	  },
	  onRegionLabelShow: function(e, el, code){
			return false;
	  },
	  onRegionOut: function(element, code, region){
		var el = jQuery('.jvectormap-label').first();
		if(el.hasClass('clicked')) {
			el.html('');
			el.removeClass('clicked');
		}
	  } 
	});
</script>
