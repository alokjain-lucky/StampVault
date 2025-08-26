import { __ } from '@wordpress/i18n';
import { useBlockProps } from '@wordpress/block-editor';
import { Spinner } from '@wordpress/components';
import { useSelect, useDispatch } from '@wordpress/data';
import { useMemo } from '@wordpress/element';

// Define rows (taxonomy or meta). Using native inputs
const ROWS = [
	{ label: 'Sub Title', taxonomy: null, meta: 'sub_title', type: 'text' },
	{ label: 'Stamp Set', taxonomy: 'stamp_sets' },
	{ label: 'Date of Issue', taxonomy: null, meta: 'date_of_release', type: 'date' },
	{ label: 'Denomination', taxonomy: null, meta: 'denomination', type: 'text' },
	{ label: 'Quantity', taxonomy: null, meta: 'quantity', type: 'text' },
	{ label: 'Perforation', taxonomy: null, meta: 'perforations', type: 'text' },
	{ label: 'Printer', taxonomy: null, meta: 'printer', type: 'text' },
	{ label: 'Printing Process', taxonomy: 'printing_process' },
	{ label: 'Watermark', taxonomy: null, meta: 'watermark', type: 'text' },
	{ label: 'Colors', taxonomy: null, meta: 'colors', type: 'text' },
	{ label: 'Credits', taxonomy: 'credits' },
	{ label: 'Catalog Codes', taxonomy: null },
	{ label: 'Themes', taxonomy: 'themes' },
];

export default function Edit() {
	const blockProps = useBlockProps( { className: 'stampvault-stamp-info-table-wrapper' } );
	const meta = useSelect( ( select ) => select( 'core/editor' ).getEditedPostAttribute( 'meta' ) || {}, [] );
	const { editPost } = useDispatch( 'core/editor' );

	const setMetaValue = ( key, value ) => editPost( { meta: { ...meta, [ key ]: value } } );

	const taxonomyData = useSelect( ( select ) => {
		const core = select( 'core' );
		const editor = select( 'core/editor' );
		const data = {};
		ROWS.filter( ( r ) => r.taxonomy ).forEach( ( r ) => {
			let ids = editor.getEditedPostAttribute( r.taxonomy );
			if ( ! Array.isArray( ids ) ) ids = [];
			const terms = ids.length ? core.getEntityRecords( 'taxonomy', r.taxonomy, { include: ids, per_page: ids.length } ) : [];
			const isResolving = ids.length ? select( 'core/data' ).isResolving( 'core', 'getEntityRecords', [ 'taxonomy', r.taxonomy, { include: ids, per_page: ids.length } ] ) : false;
			data[ r.taxonomy ] = { ids, terms, isResolving };
		} );
		return data;
	}, [] );

	const renderCell = ( row ) => {
		if ( row.taxonomy ) {
			const t = taxonomyData[ row.taxonomy ];
			if ( ! t ) return <span className="sv-placeholder sv-taxonomy-placeholder">{ __( 'Select from sidebar', 'stampvault' ) }</span>;
			if ( t.isResolving ) return <Spinner />;
			return <span>{ ( t.terms && t.terms.length ) ? t.terms.map( ( term ) => term.name ).join( ', ' ) : __( 'Select from sidebar', 'stampvault' ) }</span>;
		}
		if ( row.meta ) {
			const value = meta[ row.meta ] || '';
			if ( row.type === 'date' ) {
				return (
					<input
						type="date"
						className="sv-inline-input sv-date-input"
						value={ value }
						onChange={ ( e ) => setMetaValue( row.meta, e.target.value ) }
					/>
				);
			}
			return (
				<input
					type="text"
					className="sv-inline-input"
					value={ value }
					placeholder={ __( 'Enter value', 'stampvault' ) }
					onChange={ ( e ) => setMetaValue( row.meta, e.target.value ) }
				/>
			);
		}
		return <span className="sv-placeholder">{ __( '—', 'stampvault' ) }</span>;
	};

	const rows = useMemo( () => ROWS, [] );

	return (
		<div { ...blockProps }>
			<table className="stampvault-stamp-info-table is-editable">
				<tbody>
				{ rows.map( ( row ) => (
					<tr key={ row.label }>
						<th scope="row">{ __( row.label, 'stampvault' ) }</th>
						<td className="sv-value sv-editable">{ renderCell( row ) }</td>
					</tr>
				) ) }
				</tbody>
			</table>
		</div>
	);
}
