import { __ } from '@wordpress/i18n';
import { useBlockProps, InspectorControls } from '@wordpress/block-editor';
import { Spinner, PanelBody, Button, SelectControl, TextControl } from '@wordpress/components';
import { useState, useEffect, useRef } from '@wordpress/element';
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
	{ label: 'Catalog Codes', taxonomy: null, meta: 'catalog_codes', type: 'catalog_codes' },
	{ label: 'Themes', taxonomy: 'themes' },
];

// Moved out of Edit component so React preserves state between parent re-renders.
// When defined inline inside Edit, each Edit render created a new function identity making React unmount/remount the panel,
// which caused the form to "disappear" while typing (state reset) after debounced meta persistence triggered a parent re-render.
function CatalogCodesPanel( { metaCatalogCodes, setMetaValue = () => {} } ) {
	const catalogs = ( typeof window !== 'undefined' && window.StampVaultBlockData && Array.isArray( window.StampVaultBlockData.catalogs ) ) ? window.StampVaultBlockData.catalogs : [];
	const parseMeta = () => { try { const p = JSON.parse( metaCatalogCodes || '[]' ); return Array.isArray( p ) ? p : []; } catch(e){ return []; } };
	const [ items, setItems ] = useState( parseMeta );
	const lastPersistedRef = useRef( metaCatalogCodes || '[]' );
	const saveTimerRef = useRef( null );
	// Monitor saving state to flush pending changes before WordPress sends request
	const { isSavingPost, isAutosavingPost } = useSelect( ( select ) => {
		const ed = select( 'core/editor' );
		return {
			isSavingPost: ed.isSavingPost(),
			isAutosavingPost: ed.isAutosavingPost(),
		};
	}, [] );

	const flushCatalogCodes = () => {
		const filled = items.filter( it => it.catalog && it.code );
		const json = JSON.stringify( filled );
		if ( json !== lastPersistedRef.current ) {
			setMetaValue( 'catalog_codes', json );
			lastPersistedRef.current = json;
		}
	};

	// Detect external meta changes (undo/redo etc.) and merge without dropping local blanks
	useEffect( () => {
		const external = metaCatalogCodes || '[]';
		if ( external !== lastPersistedRef.current ) {
			let externalList = [];
			try { const parsed = JSON.parse( external || '[]' ); if ( Array.isArray( parsed ) ) externalList = parsed; } catch(e) {}
			const blanks = items.filter( it => !( it.catalog && it.code ) );
			setItems( [ ...externalList, ...blanks ] );
			lastPersistedRef.current = external;
		}
		// eslint-disable-next-line react-hooks/exhaustive-deps
	}, [ metaCatalogCodes ] );

	// Debounced persistence (shorter delay); also store timer so we can flush
	useEffect( () => {
		if ( saveTimerRef.current ) clearTimeout( saveTimerRef.current );
		saveTimerRef.current = setTimeout( () => {
			flushCatalogCodes();
		}, 200 );
		return () => { if ( saveTimerRef.current ) clearTimeout( saveTimerRef.current ); };
	// eslint-disable-next-line react-hooks/exhaustive-deps
	}, [ items ] );

	// Flush immediately when a save (non-autosave) starts
	useEffect( () => {
		if ( isSavingPost && ! isAutosavingPost ) {
			flushCatalogCodes();
		}
	}, [ isSavingPost, isAutosavingPost ] );

	const updateItem = ( i, patch ) => setItems( items.map( (it,idx) => idx===i ? { ...it, ...patch } : it ) );
	const removeItem = ( i ) => setItems( items.filter( (_it,idx) => idx!==i ) );
	const addItem = () => setItems( [ ...items, { catalog:'', code:'' } ] );

	return (
		<PanelBody title={ __( 'Catalog Codes', 'stampvault' ) } initialOpen={ true }>
			{ items.length === 0 && <p>{ __( 'No catalog codes yet.', 'stampvault' ) }</p> }
			{ items.map( ( item, i ) => (
				<div key={ i } style={ { border:'1px solid #ddd', padding:'8px', borderRadius:4, marginBottom:8, background:'#fff' } }>
					<SelectControl
						label={ __( 'Catalog', 'stampvault' ) }
						value={ item.catalog }
						options={ [ { label: __( 'Select…', 'stampvault' ), value:'' }, ...catalogs.map( c => ( { label:c, value:c } ) ) ] }
						onChange={ v => updateItem( i, { catalog:v } ) }
					/>
					<TextControl
						label={ __( 'Code', 'stampvault' ) }
						value={ item.code || '' }
						onChange={ v => updateItem( i, { code:v } ) }
					/>
					<div style={ { display:'flex', justifyContent:'space-between' } }>
						<Button variant="link" onClick={ () => updateItem( i, { catalog:'', code:'' } ) } disabled={ ! ( item.catalog || item.code ) }>{ __( 'Clear', 'stampvault' ) }</Button>
						<Button isDestructive variant="secondary" onClick={ () => removeItem( i ) }>{ __( 'Delete', 'stampvault' ) }</Button>
					</div>
				</div>
			) ) }
			<Button variant="secondary" icon="plus" onClick={ addItem }>{ __( 'Add Catalog Code', 'stampvault' ) }</Button>
		</PanelBody>
	);
}

export default function Edit() {
	const blockProps = useBlockProps( { className: 'stampvault-stamp-info-table-wrapper' } );
	const meta = useSelect( ( select ) => select( 'core/editor' ).getEditedPostAttribute( 'meta' ) || {} );
	const { editPost } = useDispatch( 'core/editor' );

	const setMetaValue = ( key, value ) => editPost( { meta: { ...meta, [ key ]: value } } );

	// (CatalogCodesPanel moved outside)

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
			if ( row.type === 'catalog_codes' ) {
				let list = [];
				try { const parsed = JSON.parse( value || '[]' ); if ( Array.isArray( parsed ) ) list = parsed; } catch(e) {}
				if ( ! list.length ) return <span>{ __( 'No catalog codes', 'stampvault' ) }</span>;
				return (
					<ul className="sv-catalog-codes-list">
						{ list.map( (c,i) => (
							<li key={ i } className="sv-catalog-codes-item">{ c.catalog }: { c.code }</li>
						) ) }
					</ul>
				);
			}
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
		<>
			<InspectorControls>
				<CatalogCodesPanel metaCatalogCodes={ meta.catalog_codes } setMetaValue={ setMetaValue } />
			</InspectorControls>
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
		</>
	);
}
