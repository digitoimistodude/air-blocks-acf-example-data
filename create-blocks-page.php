<?php
/**
 * @Author: Elias Kautto
 * @Date: 2022-01-11 15:02:49
 * @Last Modified by:   Timi Wahalahti
 * @Last Modified time: 2022-01-11 18:24:17
 *
 * @package air-blocks
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 */

namespace ACF_Blocks_Example_Data;

add_action( 'init', function () {
  add_rewrite_rule( '^blocks', 'index.php?airallblocks=true', 'top' );
} );

add_filter( 'query_vars', function ( $vars ) {
  $vars[] = 'airallblocks';

  return $vars;
} );

add_filter( 'template_include', function ( $template ) {
  if ( ! get_query_var( 'airallblocks', false ) ) {
    return $template;
  }

  if ( ! function_exists( 'acf_get_block_types' ) ) {
    return $template;
  }

  if ( 'production' === wp_get_environment_type() && ! is_user_logged_in() ) {
    return $template;
  }

  return plugin_dir_path( __FILE__ ) . '/template-all-blocks.php';
} );
