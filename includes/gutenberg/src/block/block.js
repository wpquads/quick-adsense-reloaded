
import edit from './edit';
//  Import CSS.
import './editor.scss';
import './style.scss';

const { __ } = wp.i18n; // Import __() from wp.i18n
const { registerBlockType } = wp.blocks; // Import registerBlockType() from wp.blocks

registerBlockType('wp-quads/adds', {
	// Block name. Block names must be string that contains a namespace prefix. Example: my-plugin/my-custom-block.
	title: __('WP QUADS'), // Block title.
	icon: 'welcome-widgets-menus', // Block icon from Dashicons → https://developer.wordpress.org/resource/dashicons/.
	category: 'common', // Block category — Group blocks together based on common traits E.g. common, formatting, layout widgets, embed.
	keywords: [
		__('adds'),
		__('add'),
		__('quads'),
		__('wp quads'),
	],
	attributes: { //Attributes
		selectedAdd: {
			type: 'string',
			default: 'Random Ads',
		},
	},
	edit,

	save: (props) => {
		const { attributes } = props;
		const { selectedAdd } = attributes;
		let toshortcode = selectedAdd;
		if (selectedAdd == 'Random Ads')
			toshortcode = '[quads id=RndAds]';
		return (
			<div >
				{toshortcode}
			</div>
		);
	},
});
