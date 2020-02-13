import attributes from './config';
import Inspector from './inspector';
import icon from 'AssetSources/js/icon';

const { registerBlockType } = wp.blocks;
const { Fragment } = wp.element;
const { TextControl, ServerSideRender, SelectControl } = wp.components;
const { __ } = wp.i18n;

/**
 * Register block
 */
export default registerBlockType( 'gravityview/gv-entry-link', {
	category: 'gravityview',
	title: __( 'GravityView Entry Link', 'gv-gutenberg' ),
	icon,
	keywords: [ 'gv', __( 'GravityView', 'gv-gutenberg' ) ],
	attributes,
	transforms: {
		from: [
			{
				type: 'shortcode',
				tag: [ 'gv_entry_link' ],
				attributes: {
					view_id: {
						type: 'string',
						shortcode: ( { named: { view_id } } ) => {
							return view_id;
						},
					},
					entry_id: {
						type: 'string',
						shortcode: ( { named: { entry_id } } ) => {
							return entry_id;
						},
					},
					action: {
						type: 'string',
						shortcode: ( { named: { action } } ) => {
							return action;
						},
					},
					post_id: {
						type: 'string',
						shortcode: ( { named: { post_id } } ) => {
							return post_id;
						},
					},
					return: {
						type: 'string',
						shortcode: ( ref ) => {
							return ref.named.return;
						},
					},
					link_atts: {
						type: 'string',
						shortcode: ( { named: { link_atts } } ) => {
							return link_atts;
						},
					},
					field_values: {
						type: 'string',
						shortcode: ( { named: { field_values } } ) => {
							return field_values;
						},
					},
					content: {
						type: 'string',
						shortcode: ( ref, data ) => {
							return data.shortcode.content;
						},
					},

				},
			},
		],
	},
	edit: props => {
		const { attributes, setAttributes } = props;
		const viewLists = [
			{
				value: '',
				label: __( 'Select a View', 'gv-gutenberg' ),
			},
			...GV_GUTENBERG.view_list,
		];

		return [
			<Inspector { ...{ setAttributes, ...props } } />,
			<Fragment>
				{
					( ! attributes.preview || attributes.view_id === '' || attributes.view_id === 'Select a View' || attributes.entry_id === '' ) &&
					<div className="gravity-view-shortcode-preview">
						<img src={ `${ GV_GUTENBERG.img_url }logo.png` } alt={ __( 'GravityView', 'gv-gutenberg' ) } />
						<div className="field-container">
							<SelectControl
								value={ attributes.view_id }
								options={ viewLists }
								onChange={ view_id => {
									setAttributes( {
										view_id,
									} );
								} }
							/>
							{
								attributes.view_id !== '' && attributes.view_id !== 'Select a View' &&
								<TextControl
									placeholder={ __( 'Entry ID', 'gv-gutenberg' ) }
									value={ attributes.entry_id }
									type="number"
									min="0"
									onChange={ entry_id => {
										setAttributes( {
											entry_id,
										} );
									} }
								/>
							}
							{
								attributes.view_id !== '' && attributes.view_id !== 'Select a View' && attributes.entry_id !== '' &&
								<TextControl
									placeholder={ __( 'Link Text', 'gv-gutenberg' ) }
									value={ attributes.content }
									onChange={ content => {
										setAttributes( {
											content,
										} );
									} }
								/>
							}
						</div>
					</div>
				}
				{
					( attributes.preview && attributes.view_id !== '' && attributes.view_id !== 'Select a View' && attributes.entry_id !== '' ) &&
					<ServerSideRender
						block="gravityview/gv-entry-link"
						attributes={ attributes }
					/>
				}
			</Fragment>,
		];
	},
	save() {
		// Rendering in PHP
		return null;
	},
} );
