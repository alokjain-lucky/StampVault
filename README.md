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
This project is open source and licensed under the GPLv2. See the [LICENSE](https://www.gnu.org/licenses/gpl-2.0.html) file for details.

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
