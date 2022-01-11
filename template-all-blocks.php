<?php
/**
 * TODO: Add here the name and meaning of this file, template-all-blocks.php
 *
 * TODO: Add description here for this filefile called template-all-blocks.
 *
 * @Author:		Elias Kautto
 * @Date:   		2022-01-11 17:03:34
 * @Last Modified by:   Timi Wahalahti
 * @Last Modified time: 2022-01-11 18:21:53
 *
 * @package air-blocks
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 */

$blocks = acf_get_block_types();

get_header(); ?>

<main class="site-main">
  <div class="air-blocks-list" id="blocks">

    <?php foreach ( $blocks as $block ) {
      if ( ! isset( $block['example'] ) ) {
        continue;
      }

      if ( ! isset( $block['example']['attributes']['data'] ) || empty( $block['example']['attributes']['data'] ) ) {
        continue;
      }

      $block['data'] = $block['example']['attributes']['data'];

      echo acf_rendered_block( $block, '', true, 0 );
    } ?>

  </div>
</main>

<?php get_footer();
