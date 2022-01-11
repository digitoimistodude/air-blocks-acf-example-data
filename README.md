# ACF blocks example data

Try to set example data for ACF blocks automatically based on field type. If theme sets the example data, plugin tries to fill those where suitable. So the theme example data is always the defining place, if automatic data isn't wanted.

## Supported field types

Currently following ACF field types are supported:

* Text
* Textarea
* Wysiwyg
* Link
* Url
* Image
* Gallery
* Relationship
* Repeater
* Select __(only with icon_svg fields)__

## Hooks

The default example datas for fields can be totally modified with general hooks or tweaked a little. For field type tweaks, all filters receive `$field_name` and `$field` as an addigional parameters.

### Modify field default content

Default content for fields can be modified with `air_block_acf_example_data` filter. All filters modifying data receive `$field_type`, `$field_name` and `$field` as an addigional parameters.

#### By field type
```
air_block_acf_example_data/{$field_type}
```

#### By field name
```
air_block_acf_example_data/{$field_name}
```

### Set default svg icon

Set filename for the svg icon that is used as default one for svg icon select fields. Svg file should be placed in `svg/foruser` directory.

```
add_filter( 'air_acf_block_example_data_default_svg_icon', function() {
  return 'icon.svg';
} );
```

### Set gallery images count
By default, all gallery blocks do get assigned with three images. This can be changed via hook for all gallery fields or by field name.

```
add_filter( 'air_block_acf_example_data_gallery_images_count/{$field_name}', function() {
  return 5;
} );


```
add_filter( 'air_block_acf_example_data_gallery_images_count', function() {
  return 5;
} );
```

### Repeater field items count
By default, repeater field tries to use value set to min on determining how many items the repeater should contain. If no value is set, three is used. This can be changed via hook for all gallery fields or by field name.

```
add_filter( 'air_block_acf_example_data_repeater_count/{$field_name}', function() {
  return 5;
} );


```
add_filter( 'air_block_acf_example_data_repeater_count', function() {
  return 5;
} );
```

### Relationship field posts
Modify the WP_Query arguments used to get example data for relationship field.

`air_block_acf_example_data_relationship_query_args/{$field_name}`
`air_block_acf_example_data_relationship_query_args`
