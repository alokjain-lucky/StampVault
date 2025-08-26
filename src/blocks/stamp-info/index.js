import { registerBlockType } from '@wordpress/blocks';
import { __ } from '@wordpress/i18n';
import { useBlockProps } from '@wordpress/block-editor';
import './editor.scss';
import './style.scss';

registerBlockType( 'stampvault/stamp-info', {
	edit: () => {
		const blockProps = useBlockProps();
		return <p { ...blockProps }>{ __( 'Hello StampVault!', 'stampvault' ) }</p>;
	},
	save: () => {
		return <p { ...useBlockProps.save() }>{ __( 'Hello StampVault!', 'stampvault' ) }</p>;
	}
} );
