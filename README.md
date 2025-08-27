## StampVault

StampVault is a WordPress plugin for philatelists to catalog, enrich, and display stamp data using a purpose‑built Custom Post Type, structured taxonomies, REST‑exposed meta fields, and a Gutenberg block that renders a technical “Stamp Info” table dynamically.

---
## Contents
1. Overview
2. Custom Post Type (CPT)
3. Taxonomies (Names, Slugs, Purpose)
4. Meta Fields (Keys & Meaning)
5. Catalog Codes (Structured Meta)
6. Gutenberg Block: `stampvault/stamp-info`
7. Theme & Template Integration Examples
8. REST API Usage
9. Default Editor Template
10. Developer Notes (lightweight)

---
## 1. Overview
After activation you get:
- CPT: `stamps`
- 6 taxonomies for classification
- Rich meta fields (printer, perforations, colors, etc.)
- Dynamic Gutenberg block (server‑side render) to show all technical attributes
- Classic Editor meta box fallback (if block editor disabled for CPT)

---
## 2. Custom Post Type
| Label | Slug | Notes |
|-------|------|-------|
| Stamps | `stamps` | Primary content type for individual stamp records. Supports editor, thumbnail, taxonomies, and custom meta.

Query example:
```php
$stamps = new WP_Query([
	'post_type'      => 'stamps',
	'posts_per_page' => 12,
	'tax_query'      => [
		[ 'taxonomy' => 'countries', 'field' => 'slug', 'terms' => ['india'] ],
	],
	'meta_query'     => [
		[ 'key' => 'denomination', 'value' => '5', 'compare' => '=' ],
	],
]);
```

---
## 3. Taxonomies
All taxonomies are registered with `show_in_rest => true` (usable in Block & REST API) and hierarchical for flexible organization.

| Label | Slug | Purpose |
|-------|------|---------|
| Stamp Sets | `stamp_sets` | Group stamps in the same issued set or series. |
| Themes | `themes` | Thematic subjects (birds, space, history). |
| Stamp Types | `stamp_types` | Type classification (definitive, commemorative, airmail, etc.). |
| Printing Processes | `printing_process` | Production method (engraving, lithography…). |
| Countries | `countries` | Issuing country / territory. |
| Credits | `credits` | Designers, engravers, contributors. |

Fetching terms inside The Loop:
```php
$terms = get_the_terms( get_the_ID(), 'printing_process' );
if ( $terms && ! is_wp_error( $terms ) ) {
	echo esc_html( join( ', ', wp_list_pluck( $terms, 'name' ) ) );
}
```

---
## 4. Meta Fields
All meta keys are single, `show_in_rest => true`, and stored as strings (except `catalog_codes` which is JSON).

| Label | Meta Key | Description |
|-------|----------|-------------|
| Sub Title / Note | `sub_title` | Short secondary title or note. |
| Date of Release | `date_of_release` | YYYY-MM-DD release date. |
| Denomination | `denomination` | Face value (include currency symbol manually if desired). |
| Quantity | `quantity` | Issued quantity / print run. |
| Perforations | `perforations` | Perforation gauge description. |
| Printer | `printer` | Printing company / facility. |
| Watermark | `watermark` | Watermark description. |
| Colors | `colors` | Dominant colors (free text or comma-separated). |
| Catalog Codes | `catalog_codes` | JSON array of catalog/code pairs (see next section). |

Accessing a field in a theme:
```php
echo esc_html( get_post_meta( get_the_ID(), 'denomination', true ) );
```

Meta query example (all stamps printed by a specific printer):
```php
$printer = 'Government Press';
$q = new WP_Query([
	'post_type' => 'stamps',
	'meta_key'  => 'printer',
	'meta_value'=> $printer,
]);
```

---
## 5. Catalog Codes (Structured Meta)
Meta key: `catalog_codes`

Value shape (stored JSON string):
```json
[
	{ "catalog": "Scott", "code": "123a" },
	{ "catalog": "Michel", "code": "456B" }
]
```

Retrieve & decode:
```php
$raw = get_post_meta( get_the_ID(), 'catalog_codes', true );
$codes = json_decode( $raw, true );
if ( is_array( $codes ) ) {
	foreach ( $codes as $c ) {
		printf('<span class="catalog-code"><strong>%s:</strong> %s</span> ', esc_html($c['catalog']), esc_html($c['code']));
	}
}
```

---
## 6. Gutenberg Block: Stamp Info Table
Block name: `stampvault/stamp-info`

Purpose: Renders a 2‑column technical specification table dynamically (server‑side via `render.php`). The block **does not** store static HTML; it reads current taxonomies & meta each render.

Editor Behavior:
- Left column labels; right column either inline editable inputs (meta) or resolved taxonomy term list.
- Meta fields editable inline: `sub_title`, `date_of_release`, `denomination`, `quantity`, `perforations`, `printer`, `watermark`, `colors`.
- Taxonomies chosen via standard sidebar panels.

Server Render Order (rows):
1. Sub Title (`sub_title`)
2. Stamp Set (taxonomy `stamp_sets`)
3. Date of Issue (`date_of_release`)
4. Denomination (`denomination`)
5. Quantity (`quantity`)
6. Perforation (`perforations`)
7. Printer (`printer`)
8. Printing Process (taxonomy `printing_process`)
9. Watermark (`watermark`)
10. Colors (`colors`)
11. Credits (taxonomy `credits`)
12. Catalog Codes (placeholder row currently – display logic can be extended)
13. Themes (taxonomy `themes`)

Insert Programmatically (optional):
```php
// Inside a migration or programmatic content creation.
wp_insert_post([
	'post_type'   => 'stamps',
	'post_title'  => 'Example Stamp',
	'post_status' => 'publish',
	'post_content'=> '<!-- wp:stampvault/stamp-info /-->'
]);
```

Styling: Front‑end & editor styles are bundled; adjust via overriding CSS targeting `.wp-block-stampvault-stamp-info` in your theme.

---
## 7. Theme & Template Integration
Basic single template snippet (`single-stamps.php`):
```php
get_header();
if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>
	<article <?php post_class(); ?>>
		<h1><?php the_title(); ?></h1>
		<?php if ( has_post_thumbnail() ) the_post_thumbnail( 'large' ); ?>
		<div class="stamp-meta-table">
			<?php
			// If the user inserted the block, its render appears in content.
			the_content();
			?>
		</div>
	</article>
<?php endwhile; endif;
get_footer();
```

Direct manual table (if you choose not to use the block):
```php
$fields = ['denomination','quantity','perforations','printer'];
echo '<table class="manual-stamp-info">';
foreach ( $fields as $key ) {
	$val = get_post_meta( get_the_ID(), $key, true );
	echo '<tr><th>'.esc_html( ucfirst(str_replace('_',' ', $key)) ).'</th><td>'.( $val !== '' ? esc_html($val) : '&mdash;' ).'</td></tr>';
}
echo '</table>';
```

---
## 8. REST API Usage
Base endpoint (WordPress core):
```
/wp-json/wp/v2/stamps
```
Example fetch (JavaScript):
```js
fetch('/wp-json/wp/v2/stamps?per_page=5&_embed')
	.then(r => r.json())
	.then(data => console.log(data));
```
Meta fields appear automatically when registered with `show_in_rest`. Example response fragment:
```json
{
	"id": 101,
	"slug": "example-stamp",
	"meta": {
		"denomination": "5 Rs",
		"printer": "Security Press",
		"catalog_codes": "[{\"catalog\":\"Scott\",\"code\":\"123a\"}]"
	}
}
```

Filtering by meta via REST (core does not natively support arbitrary meta queries without adding `register_rest_field` or custom endpoints). For advanced REST filtering, create a custom route or use WP_Query in PHP.

---
## 9. Default Editor Template
New stamp posts load a default block template (two columns: featured image + Stamp Info block). File: `templates/stamps-default-content.html`. Remove or modify as needed.

---
## 10. Developer Notes
Build tooling: `@wordpress/scripts`. Run:
```bash
npm install
npm run build
```
Composer (PHP autoload / stubs):
```bash
composer install
```
The `build/` directory is generated; ensure you build before distributing.

---
## License & Contributions
Licensed under the GNU General Public License v2.0 or later (GPL-2.0-or-later).

You are free to use, modify, and redistribute this plugin under the terms of the GPL. See `LICENSE` file for the full text.

Contributions (issues, pull requests, documentation improvements) are welcome. By contributing you agree your code will be released under the GPL-2.0-or-later license.

---
Happy collecting & hacking!
