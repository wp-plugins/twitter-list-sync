<?php


class TwitterListSyncSettingsPage
{
	/**
	 * Holds the values to be used in the fields callbacks
	 */
	private $options;

	/**
	 * Start up
	 */
	public function __construct()
	{
		add_action( 'admin_menu', array( $this, 'add_plugin_page' ) );
		add_action( 'admin_init', array( $this, 'page_init' ) );
	}

	/**
	 * Add options page
	 */
	public function add_plugin_page()
	{
		// This page will be under "Settings"
		add_options_page(
			'Twitter List Sync Admin',
			'Twitter List Sync',
			'manage_options',
			'twitter-list-sync-setting-admin',
			array( $this, 'create_admin_page' )
		);
	}

	/**
	 * Options page callback
	 */
	public function create_admin_page()
	{
		// Set class property
		$this->options = get_option( 'twitter_list_sync_option_name' );
		?>
		<div class="wrap">
			<h2>Twitter List Sync Settings</h2>           
			<form method="post" action="options.php">
			<?php
				// This prints out all hidden setting fields
				settings_fields( 'twitter_list_sync_option_group' );
				do_settings_sections( 'twitter-list-sync-setting-admin' );
				submit_button();
			?>
			</form>
		</div>
		<?php
	}

	/**
	 * Register and add settings
	 */
	public function page_init()
	{
		register_setting(
			'twitter_list_sync_option_group', // Option group
			'twitter_list_sync_option_name', // Option name
			array( $this, 'sanitize' ) // Sanitize
		);

		add_settings_section(
			'setting_section_id', // ID
			'My Custom Settings', // Title
			array( $this, 'print_section_info' ), // Callback
			'twitter-list-sync-setting-admin' // Page
		);

		add_settings_field(
			'oauth_access_token', // ID
			'OAuth Access Token', // Title
			array( $this, 'oauth_access_token_callback' ), // Callback
			'twitter-list-sync-setting-admin', // Page
			'setting_section_id' // Section
		);

		add_settings_field(
			'oauth_access_token_secret',
			'OAuth Access Token Secret',
			array( $this, 'oauth_access_token_secret_callback' ),
			'twitter-list-sync-setting-admin',
			'setting_section_id'
		);

		add_settings_field(
			'consumer_key',
			'Consumer Key',
			array( $this, 'consumer_key_callback' ),
			'twitter-list-sync-setting-admin',
			'setting_section_id'
		);

		add_settings_field(
			'consumer_secret',
			'Consumer Secret',
			array( $this, 'consumer_secret_callback' ),
			'twitter-list-sync-setting-admin',
			'setting_section_id'
		);

		add_settings_field(
			'list_slug',
			'List Slug',
			array( $this, 'list_slug_callback' ),
			'twitter-list-sync-setting-admin',
			'setting_section_id'
		);

		add_settings_field(
			'list_owner_screen_name',
			'List Owner Screen Name',
			array( $this, 'list_owner_screen_name_callback' ),
			'twitter-list-sync-setting-admin',
			'setting_section_id'
		);
	}

	/**
	 * Sanitize each setting field as needed
	 *
	 * @param array $input Contains all settings fields as array keys
	 */
	public function sanitize( $input )
	{
		$new_input = array();

		if ( isset( $input['oauth_access_token'] ) ) {
			$new_input['oauth_access_token'] = sanitize_text_field( $input['oauth_access_token'] );
		}

		if ( isset( $input['oauth_access_token_secret'] ) ) {
			$new_input['oauth_access_token_secret'] = sanitize_text_field( $input['oauth_access_token_secret'] );
		}

		if ( isset( $input['consumer_key'] ) ) {
			$new_input['consumer_key'] = sanitize_text_field( $input['consumer_key'] );
		}

		if ( isset( $input['consumer_secret'] ) ) {
			$new_input['consumer_secret'] = sanitize_text_field( $input['consumer_secret'] );
		}

		if ( isset( $input['list_slug'] ) ) {
			$new_input['list_slug'] = sanitize_text_field( $input['list_slug'] );
		}

		if ( isset( $input['list_owner_screen_name'] ) ) {
			$new_input['list_owner_screen_name'] = sanitize_text_field( $input['list_owner_screen_name'] );
		}

		return $new_input;
	}

	/**
	 * Print the Section text
	 */
	public function print_section_info()
	{
	 	print 'Enter your settings below:';
	}

	public function oauth_access_token_callback()
	{
		printf(
			'<input type="text" id="oauth_access_token" name="twitter_list_sync_option_name[oauth_access_token]" value="%s" />',
			isset( $this->options['oauth_access_token'] ) ? esc_attr( $this->options['oauth_access_token'] ) : ''
		);
	}

	public function oauth_access_token_secret_callback()
	{
		printf(
			'<input type="text" id="oauth_access_token_secret" name="twitter_list_sync_option_name[oauth_access_token_secret]" value="%s" />',
			isset( $this->options['oauth_access_token_secret'] ) ? esc_attr( $this->options['oauth_access_token_secret'] ) : ''
		);
	}

	public function consumer_key_callback()
	{
		printf(
			'<input type="text" id="consumer_key" name="twitter_list_sync_option_name[consumer_key]" value="%s" />',
			isset( $this->options['consumer_key'] ) ? esc_attr( $this->options['consumer_key'] ) : ''
		);
	}

	public function consumer_secret_callback()
	{
		printf(
			'<input type="text" id="consumer_secret" name="twitter_list_sync_option_name[consumer_secret]" value="%s" />',
			isset( $this->options['consumer_secret'] ) ? esc_attr( $this->options['consumer_secret'] ) : ''
		);
	}

	public function list_slug_callback()
	{
		printf(
			'<input type="text" id="list_slug" name="twitter_list_sync_option_name[list_slug]" value="%s" />',
			isset( $this->options['list_slug'] ) ? esc_attr( $this->options['list_slug'] ) : ''
		);
	}

	public function list_owner_screen_name_callback()
	{
		printf(
			'<input type="text" id="list_owner_screen_name" name="twitter_list_sync_option_name[list_owner_screen_name]" value="%s" />',
			isset( $this->options['list_owner_screen_name'] ) ? esc_attr( $this->options['list_owner_screen_name'] ) : ''
		);
	}

}

if ( is_admin() ) {
	$my_settings_page = new TwitterListSyncSettingsPage();
}
