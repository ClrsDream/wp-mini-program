<?php
	global $pagenow;
	if( isset( $_REQUEST['settings-updated'] ) ) {
		$adverts = array('ad_i_type','ad_t_type','ad_d_type','ad_p_type','ad_v_type');
		foreach( $adverts as $advert) {
			wp_cache_delete( $advert,$advert.'_group' );
		}
		echo '<div id="settings_updated" class="updated notice is-dismissible">'."\n".'<p><strong>设置已更新保存。</strong></p>'."\n".'<button type="button" class="notice-dismiss"><span class="screen-reader-text">忽略此通知。</span></button>'."\n".'</div>'; 
	}
?>
<div class="wrap">
	<h2 class="mp-nav-tab-wrapper wp-clearfix">
		<?php miniprogram_options_nav_menu(); ?>
	</h2>
	
	<div id="section" class="section-container wp-clearfix">
		<form id="<?php echo $option["id"]; ?>" method="post" action="options.php" enctype="multipart/form-data">
			<?php settings_fields( $option['group'] ); ?>
			<?php miniprogram_options_container( $option['options'] ); ?>
			<?php do_settings_sections( $option['group'] ); ?>
			<?php submit_button(); ?>
		</form>
	</div><!-- / #container -->
</div><!-- / .wrap -->
<div id="scroll-bar">
	<div id="goTop" class="scroll-up"><a href="javascript:;" title="直达顶部">▲</a></div>
	<div id="down" class="scroll-down"><a href="javascript:;" title="直达底部">▼</a></div>
</div>