<?php
/*
Plugin Name: Mini Program API
Plugin URI: https://www.imahui.com/minapp/1044.html
Description: 由 丸子小程序团队 基于 WordPress REST 创建小程序应用 API 数据接口。免费开源，实现 WordPress 连接小程序应用数据。<a href="https://developer.wordpress.org/rest-api/" taraget="_blank">WP REST API 使用帮助</a>。
Version: 1.2.6
Author:  艾码汇
Author URI: https://www.imahui.com/
requires at least: 4.9.5
tested up to: 5.3.2
*/

define('MINI_PROGRAM_REST_API', plugin_dir_path(__FILE__));
define('MINI_PROGRAM_API_URL', plugin_dir_url(__FILE__ ));
define('MINI_PROGRAM_API_PLUGIN',  __FILE__);

add_action( 'plugins_loaded', function () {
	include( MINI_PROGRAM_REST_API.'include/include.php' );
	include( MINI_PROGRAM_REST_API.'router/router.php' );
} );

add_filter( 'plugin_action_links', function( $links, $file ) {
	if ( plugin_basename( __FILE__ ) !== $file ) {
		return $links;
	}
	$settings_link = '<a href="admin.php?page=miniprogram">' . esc_html__( '设置', 'imahui' ) . '</a>';
	array_unshift( $links, $settings_link );
	return $links;
}, 10, 2 );

add_filter( 'plugin_row_meta', function( $links, $file ) {
	if ( plugin_basename( __FILE__ ) !== $file ) {
		return $links;
	}
	$detail_link = sprintf( '<a href="%s" target="%s" aria-label="%s" data-title="%s">%s</a>',
		esc_url( 'https://www.weitimes.com' ),
		esc_attr( "_blank" ),
		esc_attr( '更多关于 丸子小程序 的信息' ),
		esc_attr( '丸子小程序' ),
		esc_html( '丸子小程序' )
	);
	$more_link = array( 'detail' => $detail_link );
	$links = array_merge( $links, $more_link );
	return $links;
}, 10, 2 );

register_activation_hook(__FILE__, function () {
	add_role( 'wechat', '小程序', array( 'read' => true, 'level_0' => true ) );
});

if(function_exists('register_nav_menus')) {
	register_nav_menus( array(
		'minapp-menu' => __( '小程序导航' )
	) );
}

function wp_miniprogram_option($option_name) {
	$options = get_option('minapp');
	if($options) {
		if (array_key_exists($option_name,$options)) {
			return $options[$option_name];
		} else {
			return false;
		}	
	} else {
		return false;
	}
}

function mp_install_subscribe_message_table() {
    global $wpdb;
    $vpush = $wpdb->prefix . 'applets_subscribe_user';
    $history = $wpdb->prefix . 'applets_subscribe_message';
    $charset_collate = $wpdb->get_charset_collate();
    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    if( $wpdb->get_var("SHOW TABLES LIKE '$vpush'") != $vpush ) :
        $vpush_sql = "CREATE TABLE `".$vpush."` (
            `id` INT NOT NULL AUTO_INCREMENT, PRIMARY KEY(id),
            `openid` VARCHAR(120) DEFAULT NULL COMMENT 'OpenID',
            `template` VARCHAR(120) DEFAULT NULL COMMENT '模板ID',
            `count` INT DEFAULT NULL COMMENT '统计',
            `pages` VARCHAR(20) DEFAULT NULL COMMENT '页面',
            `platform` VARCHAR(20) DEFAULT NULL COMMENT '平台',
            `program` VARCHAR(80) DEFAULT NULL COMMENT '小程序',
            `date` DATETIME NOT NULL COMMENT '时间'
        ) $charset_collate;";
        dbDelta($vpush_sql);
    endif;
    if( $wpdb->get_var("SHOW TABLES LIKE '$history'") != $history ) :
        $history_sql = "CREATE TABLE `".$history."` (
            `id` INT NOT NULL AUTO_INCREMENT, PRIMARY KEY(id),
            `task` INT DEFAULT NULL COMMENT '任务ID',
            `openid` VARCHAR(120) DEFAULT NULL COMMENT 'OpenID',
            `template` VARCHAR(120) DEFAULT NULL COMMENT '模板ID',
            `pages` VARCHAR(80) DEFAULT NULL COMMENT '页面',
            `program` VARCHAR(80) DEFAULT NULL COMMENT '小程序',
            `errcode` VARCHAR(20) DEFAULT NULL COMMENT '错误码',
            `errmsg` VARCHAR(240) DEFAULT NULL COMMENT '错误信息',
            `date` DATETIME NOT NULL COMMENT '时间'
        ) $charset_collate;";
        dbDelta($history_sql);
    endif;
}

if( !function_exists('get_minapp_option') ) {
	function is_wechat_miniprogram() {
		if( isset($_SERVER['HTTP_USER_AGENT']) && isset($_SERVER['HTTP_REFERER']) ) {
			return ! empty( $_SERVER['HTTP_USER_AGENT'] ) && ! empty( $_SERVER['HTTP_REFERER'] ) && preg_match( '/servicewechat\.com/i', $_SERVER['HTTP_REFERER'] );
		}
		return false;
	}
	function is_tencent_miniprogram() {
		if( isset($_SERVER['HTTP_USER_AGENT']) && isset($_SERVER['HTTP_REFERER']) ) {
			return ! empty( $_SERVER['HTTP_USER_AGENT'] ) && ! empty( $_SERVER['HTTP_REFERER'] ) && preg_match( '/qq\.com/i', $_SERVER['HTTP_REFERER'] );
		}
		return false;
	}
	function is_smart_miniprogram() {
		if( isset($_SERVER['HTTP_USER_AGENT']) && isset($_SERVER['HTTP_REFERER']) ) {
			return ! empty( $_SERVER['HTTP_USER_AGENT'] ) && ! empty( $_SERVER['HTTP_REFERER'] ) && ( preg_match( '/smartapps\.cn/i', $_SERVER['HTTP_REFERER'] ) || preg_match( '/smartapp\.baidu\.com/i', $_SERVER['HTTP_REFERER'] ) );
		}
		return false;
	}
	function is_toutiao_miniprogram() {
		if( isset($_SERVER['HTTP_USER_AGENT']) && isset($_SERVER['HTTP_REFERER']) ) {
			return ! empty( $_SERVER['HTTP_USER_AGENT'] ) && ! empty( $_SERVER['HTTP_REFERER'] ) && preg_match( '/tmaservice\.developer\.toutiao\.com/i', $_SERVER['HTTP_REFERER'] );
		}
		return false;
	}
	function is_miniprogram() {
		if( is_wechat_miniprogram() || is_tencent_miniprogram() || is_smart_miniprogram() || is_toutiao_miniprogram() ) {
			return true;
		} else {
			return false;
		}
	}
}
function is_debug( ) {
	$debug = wp_miniprogram_option('debug');
	return $debug;
}