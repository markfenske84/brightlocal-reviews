<?php
/**
 * Bitbucket Updater for BrightLocal Reviews plugin.
 *
 * Works similarly to the GitHub updater but talks to the Bitbucket Cloud API instead.
 * Fetches the newest tag (sorted by commit date) and exposes the update so that
 * WordPress can install the new ZIP automatically.
 *
 * Tested with public repositories. If your repository is private you will need to
 * add authentication (e.g. Basic Auth or OAuth) – see inline notes.
 *
 * @package BrightLocalReviews
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

if ( ! class_exists( 'BL_Reviews_Bitbucket_Updater' ) ) {

    class BL_Reviews_Bitbucket_Updater {

        /**
         * Workspace (a.k.a. owner).
         * @var string
         */
        private $workspace;

        /**
         * Repository slug.
         * @var string
         */
        private $repo;

        /**
         * Plugin slug (brightlocal-reviews/brightlocal-reviews.php).
         * @var string
         */
        private $slug;

        /**
         * Absolute path to main plugin file ( __FILE__ ).
         * @var string
         */
        private $plugin_file;

        /**
         * Cached API response for current request cycle.
         * @var object|false
         */
        private $latest_tag = false;

        /**
         * Constructor.
         *
         * @param string $workspace   Bitbucket workspace.
         * @param string $repo        Repository slug.
         * @param string $plugin_file Absolute path to main plugin file.
         */
        public function __construct( $workspace, $repo, $plugin_file ) {
            $this->workspace   = sanitize_text_field( $workspace );
            $this->repo        = sanitize_text_field( $repo );
            $this->plugin_file = $plugin_file;
            $this->slug        = plugin_basename( $plugin_file );

            // Tie into WP update hooks.
            add_filter( 'pre_set_site_transient_update_plugins', array( $this, 'inject_update' ) );
            add_filter( 'plugins_api', array( $this, 'plugins_api' ), 10, 3 );
        }

        /**
         * Query Bitbucket for the latest tag.
         *
         * @return object|false
         */
        private function get_latest_tag() {
            if ( false !== $this->latest_tag ) {
                return $this->latest_tag;
            }

            $url = sprintf( 'https://api.bitbucket.org/2.0/repositories/%s/%s/refs/tags?sort=-target.date&pagelen=1', $this->workspace, $this->repo );

            // If you need authentication for a private repo, add 'headers' => array( 'Authorization' => 'Basic '. base64_encode('username:app_password') )
            $response = wp_remote_get( $url, array(
                'timeout' => 15,
                'headers' => array( 'User-Agent' => 'WordPress/' . get_bloginfo( 'version' ) . '; ' . home_url() ),
            ) );

            if ( is_wp_error( $response ) ) {
                return false;
            }

            $code = wp_remote_retrieve_response_code( $response );
            if ( 200 !== $code ) {
                return false;
            }

            $body = json_decode( wp_remote_retrieve_body( $response ) );
            if ( empty( $body->values ) || ! is_array( $body->values ) ) {
                return false;
            }

            $this->latest_tag = $body->values[0]; // newest tag.
            return $this->latest_tag;
        }

        /**
         * Inject update data.
         *
         * @param stdClass $transient WP update transient.
         * @return stdClass
         */
        public function inject_update( $transient ) {
            if ( empty( $transient->checked ) ) {
                return $transient;
            }

            $tag = $this->get_latest_tag();
            if ( ! $tag ) {
                return $transient;
            }

            $remote_version = ltrim( $tag->name, 'v' );

            $plugin_data     = get_plugin_data( $this->plugin_file, false, false );
            $current_version = $plugin_data['Version'];

            if ( version_compare( $remote_version, $current_version, '<=' ) ) {
                return $transient; // No update required.
            }

            // Build download URL: https://bitbucket.org/{workspace}/{repo}/get/{tag}.zip
            $package_url = sprintf( 'https://bitbucket.org/%s/%s/get/%s.zip', $this->workspace, $this->repo, $tag->name );

            $update              = new stdClass();
            $update->slug        = $this->slug;
            $update->plugin      = $this->slug;
            $update->new_version = $remote_version;
            $update->url         = sprintf( 'https://bitbucket.org/%s/%s/src/%s/', $this->workspace, $this->repo, $tag->target->hash );
            $update->package     = $package_url;

            $transient->response[ $this->slug ] = $update;
            return $transient;
        }

        /**
         * Supplies plugin details for "View version details" modal.
         */
        public function plugins_api( $result, $action, $args ) {
            if ( 'plugin_information' !== $action || empty( $args->slug ) || $args->slug !== $this->slug ) {
                return $result;
            }

            $tag = $this->get_latest_tag();
            if ( ! $tag ) {
                return $result;
            }

            $plugin_data = get_plugin_data( $this->plugin_file, false, false );

            $info              = new stdClass();
            $info->name        = $plugin_data['Name'];
            $info->slug        = $this->slug;
            $info->version     = ltrim( $tag->name, 'v' );
            $info->author      = $plugin_data['Author'];
            $info->homepage    = sprintf( 'https://bitbucket.org/%s/%s', $this->workspace, $this->repo );
            $info->download_link = sprintf( 'https://bitbucket.org/%s/%s/get/%s.zip', $this->workspace, $this->repo, $tag->name );

            $info->sections = array(
                'description' => wp_kses_post( $plugin_data['Description'] ),
                'changelog'   => __( 'See commit history or release notes on Bitbucket.', 'brightlocal-reviews' ),
            );

            return $info;
        }
    }
} 