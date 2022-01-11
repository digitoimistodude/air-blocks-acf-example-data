<?php
/**
 * TODO: Add here the name and meaning of this file, air-blocks-acf-example-data.php
 *
 * TODO: Add description here for this filefile called air-blocks-acf-example-data.
 *
 * @Author:		Elias Kautto
 * @Date:   		2022-01-11 13:19:55
 * @Last Modified by:   Elias Kautto
 * @Last Modified time: 2022-01-11 15:47:17
 *
 * @package air-blocks
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 */


/**
 * Plugin Name: ACF blocks example data
 * Description: Try to set example data for ACF blocks automatically based on field type.
 * Plugin URI: https://dude.fi
 * Author: Digitoimisto Dude Oy
 * Author URI: https://dude.fi
 * Version: 0.1.0
 * License: GPL2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 *
 * @Author: Timi Wahalahti
 * @Date:   2022-01-11 09:49:59
 * @Last Modified by:   Timi Wahalahti
 * @Last Modified time: 2022-01-11 13:03:18
 */

namespace ACF_Blocks_Example_Data;

require "create-blocks-page.php";

add_filter( 'acf/register_block_type_args', __NAMESPACE__ . '\maybe_set_block_example_data' );
function maybe_set_block_example_data( $block ) {
  $block_example_data = [];

  $block_fields = acf_get_block_fields( $block );
  if ( empty( $block_fields ) ) {
    return $block;
  }

  foreach ( $block_fields as $block_field ) {
    $field_example_data = get_field_type_example_data( $block_field['type'], $block_field['name'], $block_field );
    if ( empty( $field_example_data ) ) {
      continue;
    }

    $block_example_data[ $block_field['name'] ] = $field_example_data;
  }

  $block['example'] = [
    'attributes' => [
      'mode' => 'preview',
      'data' => $block_example_data,
    ],
    'viewportWidth' => 1400,
  ];

  return $block;
} // ens maybe_set_block_example_data

function get_field_type_example_data( $field_type, $field_name = null, $field = null ) {
  $data = null;

  switch ( $field_type ) {
    case 'text':
      $data = 'Lorem ipsum dolor sit amet';
      break;

    case 'textarea':
      $data = 'Pellentesque tincidunt nulla nisi, eget vehicula turpis tincidunt ut. Fusce pharetra justo nulla, sed porttitor nunc varius faucibus. Ut faucibus justo eu elementum dictum. Etiam non leo id nisl iaculis dapibus. In venenatis ipsum non lorem egestas ultrices.';
      break;

    case 'wysiwyg':
      $data = '<p>Nam molestie nec tortor. <a href="#">Donec placerat</a> leo sit amet velit. Vestibulum id justo ut vitae massa. <strong>Proin in dolor mauris consequat aliquam.</strong> Donec ipsum, vestibulum ullamcorper venenatis augue. Aliquam tempus nisi in auctor vulputate, erat felis pellentesque augue nec, pellentesque lectus justo nec erat. Aliquam et nisl. Quisque sit amet dolor in justo pretium condimentum.</p>';
      break;

    case 'link':
      $data = [
        'url'   => get_site_url(),
        'title' => 'Lorem ipsum dolor',
      ];
      break;

    case 'url':
      $data = get_site_url();
      break;

    case 'select':
      if ( false !== strpos( $field_name, 'icon_svg' ) ) {
        $data = apply_filters( 'air_acf_block_example_data_default_svg_icon', null );
      }
      break;

    case 'image':
      $example_data_image_id = get_example_data_image();

      // default return id
      $data = $example_data_image_id;

      if ( 'url' === $field['return_format'] ) {
        $data = wp_get_attachment_url( $example_data_image_id, 'large' );
      } elseif ( 'array' === $field['return_format'] ) {
        $data = acf_get_attachment( $example_data_image_id );
      }
      break;

    case 'relationship':
      $example_posts = get_example_data_posts( [
        'post_type'       => $field['post_type'],
        'posts_per_page'  => ! empty( $field['min'] ) ? $field['min'] : 3,
      ] );

      if ( ! empty( $example_posts ) ) {
        $data = $example_posts;

        if ( 'id' === $field['return_format'] ) {
          $data = wp_list_pluck( $example_posts, 'ID' );
        }
      }

      break;

    case 'repeater':
      $sub_set_data = [];

      if ( ! empty( $field['sub_fields'] ) ) {
        foreach ( $field['sub_fields'] as $sub_field ) {
          $sub_field_data = get_field_type_example_data( $sub_field['type'], $sub_field['name'], $sub_field );

          if ( ! empty( $sub_field_data ) ) {
            $sub_set_data[ $sub_field['name'] ] = $sub_field_data;
          }
        }

        $x_times = ! empty( $field['min'] ) ? $field['min'] : 3;

        for ( $x = 0; $x < $x_times; $x++ ) {
          $data[] = $sub_set_data;
        }
      }

      break;
  }

  $data = apply_filters( "air_acf_block_example_data/{$field_type}", $data, $field_type, $field_name, $field );
  $data = apply_filters( "air_acf_block_example_data/{$field_name}", $data, $field_type, $field_name, $field );
  $data = apply_filters( 'air_acf_block_example_data', $data, $field_type, $field_name, $field );

  return $data;
} // end get_field_type_example_data

function get_example_data_image() {
  $media_query = new \WP_Query( [
    'post_type'       => 'attachment',
    'post_status'     => 'inherit',
    'post_mime_type'  => [ 'image/jpeg', 'image/gif', 'image/png' ],
    'posts_per_page'  => 1,
  ] );

  if ( ! $media_query->have_posts() ) {
    return false;
  }

  $media_ids = wp_list_pluck( $media_query->posts, 'ID' );
  if ( empty( $media_ids ) ) {
    return false;
  }

  return $media_ids[0];
} // end get_example_data_image

function get_example_data_posts( $query_args ) {
  if ( empty( $query_args ) ) {
    return;
  }

  $posts_query = new \WP_Query( $query_args );

  if ( ! $posts_query->have_posts() ) {
    return false;
  }

  return $posts_query->posts;
} // end get_example_data_posts
