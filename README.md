# ACF blocks example data

Try to set example data for ACF blocks automatically based on field type.

## Hooks

### Set default svg icon

Set filename for the svg icon that is used as default one for svg icon select fields. Svg file should be placed in `svg/foruser` directory.

```
add_filter( 'air_acf_block_example_data_default_svg_icon', function() {
  return 'koulu.svg';
} );
```
