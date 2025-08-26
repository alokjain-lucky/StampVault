import { registerBlockType } from '@wordpress/blocks';
import Edit from './edit';
import './editor.scss';
import './style.scss';

registerBlockType( 'stampvault/stamp-info', {
	edit: Edit,
	save: () => null // dynamic render via PHP
} );
