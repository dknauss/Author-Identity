/**
 * Block editor sidebar panel for Content Perspective.
 *
 * Adds a "Content Perspective" panel to the post sidebar
 * using PluginDocumentSettingPanel, allowing editors to set
 * the editorial perspective of each post.
 *
 * @package Byline_Feed
 */

import { registerPlugin } from '@wordpress/plugins';
import { PluginDocumentSettingPanel } from '@wordpress/editor';
import { SelectControl } from '@wordpress/components';
import { useEntityProp } from '@wordpress/core-data';
import { useSelect } from '@wordpress/data';
import { __ } from '@wordpress/i18n';

const PERSPECTIVE_OPTIONS = [
	{ label: __( '— None —', 'byline-feed' ), value: '' },
	{ label: __( 'Personal', 'byline-feed' ), value: 'personal' },
	{ label: __( 'Reporting', 'byline-feed' ), value: 'reporting' },
	{ label: __( 'Analysis', 'byline-feed' ), value: 'analysis' },
	{ label: __( 'Official', 'byline-feed' ), value: 'official' },
	{ label: __( 'Sponsored', 'byline-feed' ), value: 'sponsored' },
	{ label: __( 'Satire', 'byline-feed' ), value: 'satire' },
	{ label: __( 'Review', 'byline-feed' ), value: 'review' },
	{ label: __( 'Announcement', 'byline-feed' ), value: 'announcement' },
	{ label: __( 'Tutorial', 'byline-feed' ), value: 'tutorial' },
	{ label: __( 'Curation', 'byline-feed' ), value: 'curation' },
	{ label: __( 'Fiction', 'byline-feed' ), value: 'fiction' },
	{ label: __( 'Interview', 'byline-feed' ), value: 'interview' },
];

function PerspectivePanel() {
	const postType = useSelect(
		( select ) => select( 'core/editor' ).getCurrentPostType(),
		[]
	);

	const [ meta, setMeta ] = useEntityProp( 'postType', postType, 'meta' );

	const perspective = meta?._byline_perspective || '';

	return (
		<PluginDocumentSettingPanel
			name="byline-feed-perspective"
			title={ __( 'Content Perspective', 'byline-feed' ) }
		>
			<SelectControl
				label={ __( 'Perspective', 'byline-feed' ) }
				value={ perspective }
				options={ PERSPECTIVE_OPTIONS }
				onChange={ ( value: string ) =>
					setMeta( { ...meta, _byline_perspective: value } )
				}
				help={ __(
					'The editorial perspective or intent of this content. Appears in the Byline feed output.',
					'byline-feed'
				) }
			/>
		</PluginDocumentSettingPanel>
	);
}

registerPlugin( 'byline-feed-perspective', {
	render: PerspectivePanel,
	icon: 'admin-users',
} );
