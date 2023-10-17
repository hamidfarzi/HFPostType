# HFPostType

Easy Custom Post Type and Custom Taxonomy generator for WordPress

## Usage

1-Copy files in your project
2-Include Class

```
require_once plugin_dir_path(__FILE__) . 'inc/HFPostType/class-hf-posttype.php';
```

3-Setup Posttype as sample:

```
$drug = new \HFAddon\HFPostType('Drug', 'drug', ['taxonomies' => ['drug-cat']]);
```

4-Setup Taxonomies as sample:

```
$drug->set_post_tax(
        'Drug category',
        'drug-cat',
        [
            'extra_fields' => [
                [
                    'name' => '_order',
                    'label' => __('Order', 'hfcv'),
                    'type' => 'text',
                    'default' => '1',
                ],
            ],
            'extra_columns' => [
                '_order' => 'Order'
            ]
        ]
    );
```

5-Enjoy Coding ;)
