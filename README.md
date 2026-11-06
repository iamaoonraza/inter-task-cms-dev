# Senior CMS Developer Task

Two separate parts:

- `wordpress/ropstam-services` is a plugin with a Services post type and a price calculator block.
- `shopify` is an Online Store 2.0 section for building a 3-pack bundle.

Neither part has a build step or third party dependency.

## WordPress

Requires WordPress 6.3+ and PHP 7.4+.

### Setup

1. Copy `wordpress/ropstam-services` to `wp-content/plugins/`.
2. Activate **Ropstam Services** under Plugins.
3. Add a few items under **Services** and fill in **Service Details** (base price and delivery time).
4. Edit a page and insert the **Service Price Calculator** block.

### How it works

- `base_price` and `delivery_time_days` are registered with `register_post_meta`, with sanitize and auth callbacks.
- The block is dynamic. `render.php` gets the services through `WP_Query`, ordered by `modified`.
- Every value is escaped on output.
- Scripts and styles are registered on `init`. `render.php` enqueues the calculator script, so it only loads on pages that use the block.
- Prices are handled in cents in JavaScript to avoid rounding errors.

### Changing the add-ons

The brief asks for two meta fields, so add-ons are not stored per service. They apply to all services and can be changed with a filter:

```php
add_filter( 'ropstam_services_addons', function ( $addons ) {
	$addons[] = array( 'label' => 'Rush Support', 'price' => 40 );
	return $addons;
} );
```

### Edge cases

- No services: a message is shown.
- Service without a price: shows "Contact us for pricing" and no calculator.
- JavaScript off: the add-ons stay hidden and the base price still shows.

## Shopify

### Setup

Requires [Shopify CLI](https://shopify.dev/docs/api/shopify-cli) and a development store.

```bash
shopify theme init demo-theme --clone-url https://github.com/Shopify/dawn.git
```

Copy these files into the same folders of the theme:

```text
shopify/sections/bundle-builder.liquid
shopify/assets/bundle-builder.js
shopify/assets/bundle-builder.css
shopify/templates/page.bundle-builder.json
```

Then run it:

```bash
cd demo-theme
shopify theme dev --store your-store.myshopify.com
```

In the theme editor, open the bundle builder section and choose a collection or product list for each group. To use the included template, create a page and assign it the `page.bundle-builder` template.

### How it works

- The section has one `group` block, limited to 3 by `max_blocks`. Each group uses either a collection or a product list, and the product list wins if both are set.
- Each product is a native radio button, so a group allows one choice and works with the keyboard.
- Products with several variants get a dropdown, and sold out variants are disabled.
- The total is updated from the price stored on the selected input.
- `/cart/add.js` receives all the items in one request. Each line has a visible `Bundle` property and a hidden `_bundle_id` property that links them.
- A `bundle:added` event fires on `document` after a successful add, so a theme can refresh its cart drawer.

### Edge cases

- Sold out product: the card is disabled and has a badge.
- Whole group sold out: a message is shown and the button is disabled.
- Group not chosen: a status message names it and focus moves to it.
- Item sells out after the page loaded: the message returned by Shopify is shown.
- Network or server failure: a retry message is shown and the button is enabled again.

## Assumptions and limitations

- WordPress prices are in USD.
- A store discount is not applied. The bundle price is the sum of the three items, because a real discount needs an automatic discount or a Shopify Function.
- Groups show the first 12 products. If those are all sold out, the group counts as sold out.
- The section works with fewer than 3 groups, as the brief says "up to 3". The theme editor shows a notice in that case.
- Selling plans (subscriptions) are not handled.
- The cart icon count updates automatically on Dawn based themes. Other themes can use the `bundle:added` event.

## Demo links

- WordPress: https://taskwp.indoorplantsbuy.com/?page_id=13
- Shopify: https://ropstam-demo.myshopify.com/pages/bundle-builder (password: `your-password`)
