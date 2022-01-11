<?php
/**
 * TODO: Add here the name and meaning of this file, create-blocks-page.php
 *
 * TODO: Add description here for this filefile called create-blocks-page.
 *
 * @Author:		Elias Kautto
 * @Date:   		2022-01-11 15:02:49
 * @Last Modified by:   Elias Kautto
 * @Last Modified time: 2022-01-11 17:13:32
 *
 * @package air-blocks
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 */

namespace ACF_Blocks_Example_Data;

add_action( 'init', __NAMESPACE__ . '\air_blocks_add_rewrite_rules' );
function air_blocks_add_rewrite_rules() {
  add_rewrite_rule( '^blocks', 'index.php?orderby=true', 'top' );
}

// add_filter( 'query_vars', __NAMESPACE__ . '\air_blocks_query_vars' );
function air_blocks_query_vars( $vars ) {
  $vars[] = 'test';

  return $vars;
}

add_filters( 'template_include', function( $template ) {
  var_dump( $template );

  return $template;
} );
