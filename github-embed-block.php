<?php
/**
 * @link              https://jeanbaptisteaudras.com
 * @since             0.1
 * @package           GitHub Embed Block
 *
 * Plugin Name:       GitHub Embed Block
 * Plugin URI:        https://jeanbaptisteaudras.com/github-embed-block-gutenberg-wordpress/
 * Description:       Easily embed GitHub repositories in Gutenberg Editor.
 * Version:           0.1
 * Author:            audrasjb
 * Author URI:        https://jeanbaptisteaudras.com
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       github-embed-block
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

function geb_embed_repository( $attributes ) {

	$github_url = trim( $attributes['github_url'] );

	if ( '' === trim( $github_url ) ) {
		$content = '<p>Use the Sidebar to add the URL of the GitHub Repository to embed.</p>';
	} else {
		if ( filter_var( $github_url, FILTER_VALIDATE_URL ) ) {
			if ( strpos( $github_url, 'https://github.com/' ) === 0 ) {
				if ( get_transient( '_geb_repository_' . sanitize_title_with_dashes( $github_url ) ) ) {
					$data = json_decode( get_transient( '_geb_repository_' . sanitize_title_with_dashes( $github_url ) ) );
					$content = '
<div class="geb-br-wrapper">
	<img class="geb-br-header-logo" src="' . plugin_dir_url( __FILE__ ) . '/images/github.svg" alt="GitHub Card" />
	<div class="geb-br-avatar">
		<img class="geb-br-header-avatar" src="' . $data->owner->avatar_url . '" alt="" width="150" height="150" />
	</div>
	<div class="geb-br-main">
		<p class="geb-br-title">
			<strong><a target="_blank" rel="noopener noreferrer" href="' . $data->html_url . '">' . $data->name . ' <span class="screen-reader-text">(this link opens in a new window)</span></a></strong>
			<em>by <a target="_blank" rel="noopener noreferrer" href="' . $data->owner->html_url . '">' . $data->owner->login . ' <span class="screen-reader-text">(this link opens in a new window)</span></a></em>
		</p>
		<p class="geb-br-description">' . $data->description . '</p>
		<p class="geb-br-footer">
			<span class="geb-br-subscribers">
				<img src="' . plugin_dir_url( __FILE__ ) . '/images/subscribe.svg" alt="Github" /> 
				' . $data->subscribers_count . ' Subscribers
			</span>
			<span class="geb-br-watchers">
				<img src="' . plugin_dir_url( __FILE__ ) . '/images/watch.svg" alt="Github" /> 
				' . $data->watchers_count . ' Watchers
			</span>
			<span class="geb-br-forks">
				<img src="' . plugin_dir_url( __FILE__ ) . '/images/fork.svg" alt="Github" /> 
				' . $data->forks_count . ' Forks
			</span>
			<a target="_blank" rel="noopener noreferrer" class="geb-br-link" href="' . $data->html_url . '">Check out this repository on GitHub.com <span class="screen-reader-text">(this link opens in a new window)</span></a>
		</p>
	</div>
</div>
					';
				} else {
					$slug = str_replace( 'https://github.com/', '', $github_url );
					$request = wp_remote_get( 'https://api.github.com/repos/' . $slug );
					$body = wp_remote_retrieve_body( $request );
					$data = json_decode( $body );
					if ( ! is_wp_error( $response ) ) {
						set_transient( '_geb_repository_' . sanitize_title_with_dashes( $github_url ), json_encode( $data ) );
						$content = '
<div class="geb-br-wrapper">
	<img class="geb-br-header-logo" src="' . plugin_dir_url( __FILE__ ) . '/images/github.svg" alt="GitHub Card" />
	<div class="geb-br-avatar">
		<img class="geb-br-header-avatar" src="' . $data->owner->avatar_url . '" alt="" width="150" height="150" />
	</div>
	<div class="geb-br-main">
		<p class="geb-br-title">
			<strong><a target="_blank" rel="noopener noreferrer" href="' . $data->html_url . '">' . $data->name . ' <span class="screen-reader-text">(this link opens in a new window)</span></a></strong>
			<em>by <a target="_blank" rel="noopener noreferrer" href="' . $data->owner->html_url . '">' . $data->owner->login . ' <span class="screen-reader-text">(this link opens in a new window)</span></a></em>
		</p>
		<p class="geb-br-description">' . $data->description . '</p>
		<p class="geb-br-footer">
			<span class="geb-br-subscribers">
				<img src="' . plugin_dir_url( __FILE__ ) . '/images/subscribe.svg" alt="Github" /> 
				' . $data->subscribers_count . ' Subscribers
			</span>
			<span class="geb-br-watchers">
				<img src="' . plugin_dir_url( __FILE__ ) . '/images/watch.svg" alt="Github" /> 
				' . $data->watchers_count . ' Watchers
			</span>
			<span class="geb-br-forks">
				<img src="' . plugin_dir_url( __FILE__ ) . '/images/fork.svg" alt="Github" /> 
				' . $data->forks_count . ' Forks
			</span>
			<a target="_blank" rel="noopener noreferrer" class="geb-br-link" href="' . $data->html_url . '">Check out this repository on GitHub.com <span class="screen-reader-text">(this link opens in a new window)</span></a>
		</p>
	</div>
</div>
						';
					} else {
						$content = '<p>No information available. Please check your URL.</p>';
					}
				}
			} else {
				$content = '<p>Use the Sidebar to add the URL of the GitHub Repository to embed.</p>';
			}
		} else {
			$content = '<p>Use the Sidebar to add the URL of the GitHub Repository to embed.</p>';
		}
	}

	return $content;
}
function geb_enqueue_scripts() {
	wp_register_script(
		'geb-repository-editor',
		plugins_url( 'repository-block.js', __FILE__ ),
		array( 'wp-blocks', 'wp-components', 'wp-element', 'wp-i18n', 'wp-editor' ),
		filemtime( plugin_dir_path( __FILE__ ) . 'repository-block.js' )
	);
	wp_register_style(
		'geb-repository-editor',
		plugins_url( 'repository-block.css', __FILE__ ),
		array(),
		filemtime( plugin_dir_path( __FILE__ ) . 'repository-block.css' )
	);
	wp_register_style(
		'geb-repository',
		plugins_url( 'repository-block.css', __FILE__ ),
		array(),
		filemtime( plugin_dir_path( __FILE__ ) . 'repository-block.css' )
	);
	register_block_type( 'github-embed-block/repository', array(
		'editor_script'   => 'geb-repository-editor',
		'editor_style'    => 'geb-repository-editor',
		'style'           => 'geb-repository',
		'render_callback' => 'geb_embed_repository',
		'attributes'      => array(
			'github_url' => array( 'type' => 'string' ),
		),
	) );
}
add_action( 'init', 'geb_enqueue_scripts' );
