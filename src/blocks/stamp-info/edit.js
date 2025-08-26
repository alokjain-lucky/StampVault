import { __ } from '@wordpress/i18n';
import { useBlockProps } from '@wordpress/block-editor';
import { Spinner } from '@wordpress/components';
import { useSelect } from '@wordpress/data';
import { useEntityProp } from '@wordpress/core-data';
import { useMemo } from '@wordpress/element';

// Row definitions; map label -> taxonomy slug (or null for placeholders to be handled later).
const ROWS = [
	{ label: 'Stamp Set', taxonomy: 'stamp_sets' },
	{ label: 'Date of Issue', taxonomy: null },
	{ label: 'Denomination', taxonomy: null },
	{ label: 'Quantity', taxonomy: null },
	{ label: 'Perforation', taxonomy: null },
	{ label: 'Printer', taxonomy: null },
	{ label: 'Printing Process', taxonomy: 'printing_process' },
	{ label: 'Watermark', taxonomy: null },
	{ label: 'Colors', taxonomy: null },
	{ label: 'Credits', taxonomy: 'credits' },
	{ label: 'Catalog Codes', taxonomy: null },
	{ label: 'Themes', taxonomy: 'themes' },
];

// Helper to format a list of term objects into a comma-separated string.
function formatTerms( terms ) {
	if ( ! terms || ! terms.length ) return '—';
	return terms.map( ( t ) => t.name ).join( ', ' );
}

export default function Edit() {
	const blockProps = useBlockProps( { className: 'stampvault-stamp-info-table-wrapper' } );

	// Current post type (should be 'stamps').
	const postType = useSelect( ( select ) => select( 'core/editor' ).getCurrentPostType(), [] );

	// For each taxonomy row, gather selected term IDs via useEntityProp.
	const taxonomySelections = {};
	ROWS.forEach( ( row ) => {
		if ( row.taxonomy ) {
			// Each call to useEntityProp must be unconditional & stable; build list then iterate separately is tricky.
			// To satisfy hooks rules, we handle via inline conditional componentization below.
		}
	} );

	// Collect data for taxonomies via a single selector pass (term objects for selected IDs), track loading states.
	const taxonomyData = useSelect( ( select ) => {
		const core = select( 'core' );
		const editor = select( 'core/editor' );
		const data = {};
		ROWS.filter( r => r.taxonomy ).forEach( ( r ) => {
			// Selected term IDs for this taxonomy from the post entity.
			// Attempt to use entity prop access pattern: edited post attribute key == taxonomy slug.
			// If unavailable, fallback to empty list.
			let ids = editor.getEditedPostAttribute( r.taxonomy );
			if ( ! Array.isArray( ids ) ) ids = [];
			const terms = ids.length ? core.getEntityRecords( 'taxonomy', r.taxonomy, { include: ids, per_page: ids.length } ) : [];
			const isResolving = ids.length ? select( 'core/data' ).isResolving( 'core', 'getEntityRecords', [ 'taxonomy', r.taxonomy, { include: ids, per_page: ids.length } ] ) : false;
			data[ r.taxonomy ] = { ids, terms, isResolving };
		} );
		return data;
	}, [] );

	// Fallback text builder.
	const getValueForRow = ( row ) => {
		if ( ! row.taxonomy ) {
			return <span className="sv-placeholder">{ __( '— value —', 'stampvault' ) }</span>;
		}
		const t = taxonomyData[ row.taxonomy ];
		if ( ! t ) return <span className="sv-placeholder">{ __( '—', 'stampvault' ) }</span>;
		if ( t.isResolving ) return <Spinner />;
		return <span>{ formatTerms( t.terms || [] ) }</span>;
	};

	// Memoize rows for performance (not critical but keeps rerenders tidy).
	const rows = useMemo( () => ROWS, [] );

	return (
		<div { ...blockProps }>
			<table className="stampvault-stamp-info-table">
				<tbody>
					{ rows.map( ( row ) => (
						<tr key={ row.label }>
							<th scope="row">{ __( row.label, 'stampvault' ) }</th>
							<td className="sv-value">{ getValueForRow( row ) }</td>
						</tr>
					) ) }
				</tbody>
			</table>
		</div>
	);
}
