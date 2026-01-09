<?php
/**
 * Plugin Name: ACF blocks example data
 * Description: Try to set example data for ACF blocks automatically based on field type.
 * Plugin URI: https://dude.fi
 * Author: Digitoimisto Dude Oy
 * Author URI: https://dude.fi
 * Version: 1.0.3
 * License: GPL2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 *
 * @package Air_Blocks_ACF_Example_Data
 */

namespace Air_Blocks_ACF_Example_Data;

/**
* Github updater for this plugin.
*/
require 'plugin-update-checker/plugin-update-checker.php';
use YahnisElsts\PluginUpdateChecker\v5\PucFactory;

$update_checker = PucFactory::buildUpdateChecker( 'http://githubupdates.dude.fi/plugins/digitoimistodude/air-blocks-acf-example-data', __FILE__, 'air-blocks-acf-example-data' );

if ( ! is_admin() ) {
  return;
}

require 'create-blocks-page.php';

// Wider preview to ease out staring the small preview,
// width should be viewportWidth / 2 for optimal experience.
add_filter( 'admin_footer', function () { ?>
  <style>
    .block-editor-inserter__preview-container {
      width: 600px !important;
    }
  </style>
<?php } );

add_filter( 'acf/register_block_type_args', __NAMESPACE__ . '\maybe_set_block_example_data' );
function maybe_set_block_example_data( $block ) {
  $block_example_data = [];

  // Get fields for this block
  $block_fields = acf_get_block_fields( $block );
  if ( empty( $block_fields ) ) {
    return $block;
  }

  $skip_block = apply_filters( "air_block_acf_example_data_skip/{$block['name']}", false, $block ); // phpcs:ignore
  $skip_block = apply_filters( 'air_block_acf_example_data_skip/' . str_replace( 'acf/', '', $block['name'] ), false, $block ); // phpcs:ignore
  $skip_block = apply_filters( 'air_block_acf_example_data_skip', false, $block );

  if ( $skip_block ) {
    return $block;
  }

  // Loop fields
  foreach ( $block_fields as $block_field ) {

    // Try to get the example data
    $field_example_data = get_field_type_example_data( $block_field['type'], $block_field['name'], $block_field );
    if ( empty( $field_example_data ) ) {
      continue;
    }

    $block_example_data[ $block_field['name'] ] = $field_example_data;
  }

  // Create example base for blocks if does not exist already
  if ( ! isset( $block['example'] ) || empty( $block['example'] ) ) {
    $block['example'] = [
      'viewportWidth' => 1200,
      'attributes'    => [
        'mode' => 'preview',
        'data' => [],
      ],
    ];
  }

  // Merge manually set example data with automated ones, using always the manual ones if available
  $block['example']['attributes']['data'] = wp_parse_args( $block['example']['attributes']['data'], $block_example_data );

  // Force ID for the block to work
  $block['id'] = time();

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
      // Try to set default only for svg icon fields
      if ( false !== strpos( $field_name, 'icon_svg' ) ) {
        $data = apply_filters( 'air_blocks_acf_example_data_default_svg_icon', null );
      }
    break;

    case 'image':
      // Get random image
      $example_data_image_id = get_image();

      // Return id as default
      $data = $example_data_image_id;

      // Change the return based on format selected on field
      if ( 'url' === $field['return_format'] ) {
        $data = wp_get_attachment_url( $example_data_image_id, 'large' );
      } elseif ( 'array' === $field['return_format'] ) {
        $data = acf_get_attachment( $example_data_image_id );
      }
    break;

    case 'gallery':
      // How many images should be shown on the gallery
      $x_times = apply_filters( "air_block_acf_example_data_gallery_images_count/{$field_name}", 3, $field_name, $field ); // phpcs:ignore
      $x_times = apply_filters( 'air_block_acf_example_data_gallery_images_count', 3, $field_name, $field );

      // Get images for the gallery
      for ( $x = 0; $x < $x_times; $x++ ) {
        $image_id = get_image();

        // Change the return based on format selected on field
        if ( 'url' === $field['return_format'] ) {
          $data[] = wp_get_attachment_url( $image_id, 'large' );
        } elseif ( 'array' === $field['return_format'] ) {
          $data[] = acf_get_attachment( $image_id );
        }
      }
      break;

    case 'relationship':
      $query_args = [
        'post_type'       => $field['post_type'],
        'posts_per_page'  => ! empty( $field['min'] ) ? $field['min'] : 3,
      ];

      $query_args = apply_filters( "air_block_acf_example_data_relationship_query_args/{$field_name}", $query_args, $field_name, $field ); // phpcs:ignore
      $query_args = apply_filters( 'air_block_acf_example_data_relationship_query_args', $query_args, $field_name, $field );

      // Get posts with args set for field
      $example_posts = get_posts( $query_args );

      if ( ! empty( $example_posts ) ) {
        // Rerturn WP_Post objects as default
        $data = $example_posts;

        // Change the return based on format selected on field
        if ( 'id' === $field['return_format'] ) {
          $data = wp_list_pluck( $example_posts, 'ID' );
        }
      }

      break;

    case 'repeater':
      $sub_set_data = [];

      if ( ! empty( $field['sub_fields'] ) ) {
        // Loop repeaters sub fields
        foreach ( $field['sub_fields'] as $sub_field ) {
          // Get example data for the sub field
          $sub_field_data = get_field_type_example_data( $sub_field['type'], $sub_field['name'], $sub_field );

          // Maybe add field and example data to parent field
          if ( ! empty( $sub_field_data ) ) {
            $sub_set_data[ $sub_field['name'] ] = $sub_field_data;
          }
        }

        // Try to determine how many times the repeater should be shown
        $x_times = ! empty( $field['min'] ) ? $field['min'] : 3;
        $x_times = apply_filters( "air_block_acf_example_data_repeater_count/{$field_name}", $x_times, $field_name, $field ); // phpcs:ignore
        $x_times = apply_filters( 'air_block_acf_example_data_repeater_count', $x_times, $field_name, $field );

        // Duplicate the one repeater field subset
        for ( $x = 0; $x < $x_times; $x++ ) {
          $data[] = $sub_set_data;
        }
      }

      break;
    
    case 'group':
      $sub_set_data = [];

      if ( ! empty( $field['sub_fields'] ) ) {
        // Loop group sub fields
        foreach ( $field['sub_fields'] as $sub_field ) {
          // Get example data for the sub field
          $sub_field_data = get_field_type_example_data( $sub_field['type'], $sub_field['name'], $sub_field );

          // Maybe add field and example data to parent field
          if ( ! empty( $sub_field_data ) ) {
            $sub_set_data[ $sub_field['name'] ] = $sub_field_data;
          }
        }

        $data = $sub_set_data;
      }

      break;

  }

  // Allow filtering the example data for specific field type, name or in general
  $data = apply_filters( "air_block_acf_example_data/{$field_type}", $data, $field_type, $field_name, $field ); // phpcs:ignore
  $data = apply_filters( "air_block_acf_example_data/{$field_name}", $data, $field_type, $field_name, $field ); // phpcs:ignore
  $data = apply_filters( 'air_block_acf_example_data', $data, $field_type, $field_name, $field );

  return $data;
} // end get_field_type_example_data

function get_image() {
  $media_query = new \WP_Query( [
    'post_type'       => 'attachment',
    'post_status'     => 'inherit',
    'orderby'         => 'rand',
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
} // end get_image

function get_posts( $query_args ) {
  if ( empty( $query_args ) ) {
    return;
  }

  $posts_query = new \WP_Query( $query_args );

  if ( ! $posts_query->have_posts() ) {
    return false;
  }

  return $posts_query->posts;
} // end get_posts
