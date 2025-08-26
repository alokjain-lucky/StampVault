# StampVault

StampVault is a WordPress plugin for stamp collectors (philatelists) to create a digital inventory of their collections and showcase them on their websites easily.

## Features
- Add, manage, and display your stamp collection online
- Showcase your collection with customizable displays
- Built for philatelists by a philatelist

## Custom Post Type
StampVault registers a custom post type called `Stamps` for managing individual stamp entries. Each stamp can have its own details, images, and custom fields, making it easy to catalog and display your collection.

## Custom Taxonomies
StampVault provides several taxonomies to organize your stamps:
- **Stamp Sets:** Group stamps that belong to the same set or series.
- **Themes:** Thematic topics depicted on the stamps (e.g., birds, sports, history).
- **Stamp Types:** Classification such as definitive, commemorative, airmail, etc.
- **Printing Process:** The printing technique used (e.g., lithography, engraving).
- **Countries:** The country or territory that issued the stamp.
- **Credits:** Designers, engravers, or other contributors to the stamp.

## Meta Fields & Catalog Codes
StampVault provides a set of custom meta fields for each stamp, including Sub Title/Note, Date of Release (with date picker), Denomination, Quantity, Perforations, Watermark, and Colors.

A flexible Catalog Codes field allows you to add multiple catalog codes per stamp, each with a dropdown for catalog name (Scott, Michel, Stanley Gibbons, etc.) and a code input. The UI for this field is modern, dynamic, and now organized in its own folder for maintainability.

- Meta box UI assets (JS/CSS) are modular and loaded only for the Classic Editor.
- Gutenberg support for meta fields and catalog codes is planned via custom blocks.


## License
This repository is private. All rights reserved.

If you have received access to this code, you may use it according to the terms provided by the repository owner. Redistribution or public sharing is not permitted without explicit permission.

## Contributing
Contributions are welcome! Please open issues or submit pull requests on the GitHub repository.

## Composer Support

StampVault now uses Composer for PHP dependency management. If you wish to contribute or run the plugin in a development environment, install Composer dependencies with:

```
composer install
```

The `vendor/` directory is excluded from version control. Please run the above command after cloning the repository.

---

*This plugin is in early development. Stay tuned for updates!*

## Development (Blocks & Build Pipeline)
StampVault uses `@wordpress/scripts` for Gutenberg block development.

Current strategy: The `build/` directory is NOT committed to git (option 3). You must run a build before creating a distributable zip so end‑users get compiled block assets.

### Prerequisites
- Node.js 18+ (LTS recommended)
- npm 8+
- PHP 7.4+ (plugin code targets 7.2 minimum, but develop on a modern version)

### Install JS & PHP dependencies
```
npm install
composer install
```

### Available npm scripts
- `npm start` – Watch mode for block development (`src/`).
- `npm run build` – Produce production assets in `build/` (minified + `.asset.php`).
- `npm run lint:js` – Lint JS.
- `npm run format` – Format source.

### Packaging a Release (Zip)
1. Ensure a clean working tree (commit or stash changes).
2. Install dependencies (first time): `npm install && composer install`.
3. Build assets: `npm run build` (creates `build/blocks/...`).
4. (Optional) Remove dev dependencies for production zip: `composer install --no-dev --optimize-autoloader`.
5. Create a zip excluding development-only folders (e.g. `node_modules`, `src`, `.git`, `docs`). Example command:
	 - macOS/Linux (from plugin root):
		 ```bash
		 zip -r stampvault.zip . \
			 -x "node_modules/*" "src/*" \
			 -x ".git/*" "docs/*" ".vscode/*" "composer.phar" \
			 -x "*.zip"
		 ```
6. Distribute `stampvault.zip` (it must include the `build/` directory created in step 3).

If you prefer committing `build/` again, remove it from `.gitignore` and ensure you rebuild before each release.

## Release Checklist
1. Bump version constant in `stampvault.php` (and update header if needed).
2. Run `npm run build` (required because `build/` is ignored).
3. Verify `build/blocks/*` contains block.json + *.asset.php + compiled JS/CSS.
4. Run `composer install --no-dev --optimize-autoloader` (if making production zip).
5. Package zip excluding dev folders (see Packaging section).
6. Fresh install test: Remove existing plugin, upload zip, activate, confirm block appears in editor.
7. Tag release in git.


---
Happy collecting!
