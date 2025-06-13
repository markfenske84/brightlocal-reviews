<?php
/**
 * GitHub Updater for BrightLocal Reviews plugin.
 *
 * This class hooks into WordPress' native update system (the same that powers updates for
 * wordpress.org hosted plugins) and points it at a public GitHub repository instead.
 *
 * IMPORTANT: If you fork or move the repository, change the $github_owner and $github_repo
 * parameters when instantiating this class in brightlocal-reviews.php.
 *
 * Inspired by numerous open-source examples and the WordPress.org Plugin Handbook.
 *
 * @package BrightLocalReviews
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

if ( ! class_exists( 'BL_Reviews_Updater' ) ) {

    class BL_Reviews_Updater {

        /**
         * GitHub repository owner.
         * @var string
         */
        private $owner;

        /**
         * GitHub repository name.
         * @var string
         */
        private $repo;

        /**
         * Plugin basename (e.g. brightlocal-reviews/brightlocal-reviews.php).
         * @var string
         */
        private $slug;

        /**
         * Absolute path to the main plugin file.
         * @var string
         */
        private $plugin_file;

        /**
         * Cached latest release details.
         * @var object|false
         */
        private $release_info = false;

        /**
         * Constructor.
         *
         * @param string $owner        GitHub account / organisation.
         * @param string $repo         Repository name.
         * @param string $plugin_file  Absolute path to the main plugin file (use __FILE__ from caller).
         */
        public function __construct( $owner, $repo, $plugin_file ) {
            $this->owner       = sanitize_text_field( $owner );
            $this->repo        = sanitize_text_field( $repo );
            $this->plugin_file = $plugin_file;
            $this->slug        = plugin_basename( $plugin_file );

            // Hook into update system.
            add_filter( 'pre_set_site_transient_update_plugins', array( $this, 'check_for_updates' ) );
            add_filter( 'plugins_api', array( $this, 'plugins_api_handler' ), 10, 3 );
        }

        /**
         * Query GitHub API for the latest published release.
         *
         * @return object|false
         */
        private function get_latest_release() {
            if ( false !== $this->release_info ) {
                return $this->release_info; // Re-use cached copy during the same request.
            }

            $url = sprintf( 'https://api.github.com/repos/%s/%s/releases/latest', $this->owner, $this->repo );

            // GitHub expects a user-agent. Without it, it may return 403.
            $response = wp_remote_get( $url, array(
                'timeout' => 15,
                'headers' => array( 'Accept' => 'application/vnd.github.v3+json', 'User-Agent' => 'WordPress/' . get_bloginfo( 'version' ) . '; ' . home_url() ),
            ) );

            if ( is_wp_error( $response ) ) {
                return false;
            }

            $code = wp_remote_retrieve_response_code( $response );
            if ( 200 !== $code ) {
                return false;
            }

            $body = json_decode( wp_remote_retrieve_body( $response ) );
            if ( empty( $body ) || empty( $body->tag_name ) ) {
                return false;
            }

            $this->release_info = $body;
            return $this->release_info;
        }

        /**
         * Inject update data into WordPress update transients.
         *
         * @param stdClass $transient Existing transient.
         * @return stdClass
         */
        public function check_for_updates( $transient ) {
            if ( empty( $transient->checked ) ) {
                return $transient;
            }

            $release = $this->get_latest_release();
            if ( ! $release ) {
                return $transient;
            }

            // Current installed plugin version.
            $plugin_data     = get_plugin_data( $this->plugin_file, false, false );
            $current_version = $plugin_data['Version'];
            $remote_version  = ltrim( $release->tag_name, 'v' ); // remove "v" prefix if present.

            if ( version_compare( $remote_version, $current_version, '<=' ) ) {
                // No update needed.
                return $transient;
            }

            // Determine download URL. Prefer bundled asset > zipball.
            $package_url = '';
            if ( ! empty( $release->assets ) && is_array( $release->assets ) ) {
                $package_url = $release->assets[0]->browser_download_url; // first asset.
            }
            if ( empty( $package_url ) ) {
                $package_url = $release->zipball_url;
            }

            $update              = new stdClass();
            $update->slug        = $this->slug;
            $update->plugin      = $this->slug;
            $update->new_version = $remote_version;
            $update->url         = $release->html_url;
            $update->package     = $package_url;

            $transient->response[ $this->slug ] = $update;

            return $transient;
        }

        /**
         * Provide detailed plugin information (view details popup).
         *
         * @param false|object $result  The result so far.
         * @param string       $action  Type of information request.
         * @param object       $args    Request arguments.
         * @return object|false
         */
        public function plugins_api_handler( $result, $action, $args ) {
            if ( 'plugin_information' !== $action || empty( $args->slug ) || $args->slug !== $this->slug ) {
                return $result;
            }

            $release = $this->get_latest_release();
            if ( ! $release ) {
                return $result;
            }

            $plugin_data = get_plugin_data( $this->plugin_file, false, false );

            $info              = new stdClass();
            $info->name        = $plugin_data['Name'];
            $info->slug        = $this->slug;
            $info->version     = ltrim( $release->tag_name, 'v' );
            $info->author      = $plugin_data['Author'];
            $info->homepage    = $release->html_url;
            $info->download_link = ( ! empty( $release->assets ) && is_array( $release->assets ) ) ? $release->assets[0]->browser_download_url : $release->zipball_url;

            // Sections shown in the modal dialogue.
            $info->sections = array(
                'description' => wp_kses_post( $plugin_data['Description'] ),
                'changelog'   => isset( $release->body ) ? wpautop( $release->body ) : __( 'See release notes on GitHub.', 'brightlocal-reviews' ),
            );

            return $info;
        }
    }
} 