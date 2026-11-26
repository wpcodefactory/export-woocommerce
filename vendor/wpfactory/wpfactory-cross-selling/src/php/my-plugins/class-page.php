<?php
/**
 * WPFactory Cross-Selling - My Plugins - Page.
 *
 * WPFactory menu > My Plugins. Replaces the Recommendations page (its URL redirects here).
 *
 * @version 1.2.1
 * @since   1.2.0
 * @author  WPFactory
 */

namespace WPFactory\WPFactory_Cross_Selling\My_Plugins;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

if ( ! class_exists( 'WPFactory\WPFactory_Cross_Selling\My_Plugins\Page', false ) ) {

	/**
	 * Page.
	 *
	 * @version 1.2.0
	 * @since   1.2.0
	 */
	class Page {

		/**
		 * Page slug.
		 *
		 * @since 1.2.0
		 */
		const SLUG = 'wpfactory-my-plugins';

		/**
		 * Old Recommendations page slug (redirects here).
		 *
		 * @since 1.2.0
		 */
		const OLD_SLUG = 'wpfactory-cross-selling';

		/**
		 * User meta: last catalog version the user saw.
		 *
		 * @since 1.2.2
		 */
		const SEEN_META = 'wpfcs_my_plugins_seen';

		/**
		 * User meta key used by 1.2.0 and 1.2.1. Read when the new key is not set yet, never deleted
		 * (an older copy of the library may still read it on the same site).
		 *
		 * @since 1.2.2
		 */
		const OLD_SEEN_META = 'wpf_myplugins_seen';

		/**
		 * All Access offer.
		 *
		 * @since 1.2.0
		 */
		const ALL_ACCESS_URL = 'https://wpfactory.com/all-access-lifetime-deal/';

		/**
		 * Contact page.
		 *
		 * @since 1.2.0
		 */
		const CONTACT_URL = 'https://wpfactory.com/contact-us/';

		/**
		 * Page hook suffix.
		 *
		 * @since 1.2.0
		 *
		 * @var string
		 */
		protected static $hook = '';

		/**
		 * The menu dot was shown in this request (the page marks the catalog as seen on load).
		 *
		 * @since 1.2.0
		 *
		 * @var bool
		 */
		protected static $dot = false;

		/**
		 * init.
		 *
		 * @version 1.2.1
		 * @since   1.2.0
		 */
		static function init() {
			if ( class_exists( '\WPFactory\WPFactory_Admin_Menu\WPFactory_Admin_Menu' ) ) {
				\WPFactory\WPFactory_Admin_Menu\WPFactory_Admin_Menu::get_instance();
			}
			add_action( 'admin_menu', array( __CLASS__, 'register_menu' ), 99 );
			add_action( 'admin_menu', array( __CLASS__, 'pin_last' ), PHP_INT_MAX );
			add_action( 'admin_enqueue_scripts', array( __CLASS__, 'enqueue' ) );
			add_action( 'admin_head', array( __CLASS__, 'print_menu_css' ) );
		}

		/**
		 * url.
		 *
		 * @version 1.2.0
		 * @since   1.2.0
		 *
		 * @return string
		 */
		static function url() {
			return admin_url( 'admin.php?page=' . self::SLUG );
		}

		/**
		 * parent_slug.
		 *
		 * @version 1.2.0
		 * @since   1.2.0
		 *
		 * @return string
		 */
		static function parent_slug() {
			if ( class_exists( '\WPFactory\WPFactory_Admin_Menu\WPFactory_Admin_Menu' ) ) {
				return \WPFactory\WPFactory_Admin_Menu\WPFactory_Admin_Menu::get_instance()->get_menu_slug();
			}
			return 'wpfactory';
		}

		/**
		 * has_unseen.
		 *
		 * @version 1.2.2
		 * @since   1.2.0
		 *
		 * @return bool
		 */
		static function has_unseen() {
			$user_id = get_current_user_id();
			$seen    = get_user_meta( $user_id, self::SEEN_META, true );
			if ( '' === $seen ) {
				$seen = get_user_meta( $user_id, self::OLD_SEEN_META, true );
			}
			return ( Catalog::CATALOG_VERSION > (int) $seen );
		}

		/**
		 * menu_title.
		 *
		 * Sidebar label: a grid icon, then the title. No dot here: the new-plugins dot
		 * is shown on the top-bar tab only.
		 *
		 * @version 1.2.1
		 * @since   1.2.1
		 *
		 * @param string $title Menu title.
		 *
		 * @return string
		 */
		static function menu_title( $title ) {
			$icon = '<svg class="wpfcs-mp-menu-icon" width="14" height="14" viewBox="0 0 14 14" aria-hidden="true" focusable="false">' .
				'<rect x="0" y="0" width="6" height="6" rx="1.5" fill="currentColor"/>' .
				'<rect x="8" y="0" width="6" height="6" rx="1.5" fill="currentColor"/>' .
				'<rect x="0" y="8" width="6" height="6" rx="1.5" fill="currentColor"/>' .
				'<rect x="8" y="8" width="6" height="6" rx="1.5" fill="currentColor"/>' .
				'</svg>';
			return $icon . esc_html( $title );
		}

		/**
		 * menu_css.
		 *
		 * Styles the My Plugins item in the WPFactory sidebar submenu only (matched by its link):
		 * a divider above it, tool blue text that turns white on hover and when current, and the icon.
		 *
		 * @version 1.2.1
		 * @since   1.2.1
		 *
		 * @return string
		 */
		static function menu_css() {
			$a = '#adminmenu .wp-submenu a[href$="page=' . self::SLUG . '"]';
			return $a . '{position:relative;margin-top:13px;color:#6BBFFF;font-weight:600}' .
				$a . '::before{content:"";position:absolute;left:0;right:0;top:-7px;height:1px;background:rgba(255,255,255,.15)}' .
				$a . ':hover,' . $a . ':focus,' . $a . '.current{color:#FFFFFF}' .
				$a . ' .wpfcs-mp-menu-icon{display:inline-block;width:14px;height:14px;margin-right:6px;vertical-align:-2px}';
		}

		/**
		 * print_menu_css.
		 *
		 * @version 1.2.1
		 * @since   1.2.1
		 */
		static function print_menu_css() {
			if ( '' === self::$hook ) {
				return;
			}
			echo '<style id="wpfcs-my-plugins-menu-css">' . self::menu_css() . '</style>' . "\n"; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- fixed CSS, no user input.
		}

		/**
		 * register_menu.
		 *
		 * Runs after the old copies (priority 10): their Recommendations submenu is removed,
		 * and its URL redirects here before WordPress checks access to it.
		 *
		 * @version 1.2.1
		 * @since   1.2.0
		 */
		static function register_menu() {
			if ( isset( $_GET['page'] ) && self::OLD_SLUG === $_GET['page'] && current_user_can( Plugin::capability() ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
				wp_safe_redirect( self::url() );
				exit;
			}
			$parent = self::parent_slug();
			remove_submenu_page( $parent, self::OLD_SLUG );

			self::$dot  = self::has_unseen();
			$title      = __( 'My Plugins', 'wpfactory-cross-selling' );
			self::$hook = add_submenu_page( $parent, $title, self::menu_title( $title ), Plugin::capability(), self::SLUG, array( __CLASS__, 'render' ) );
			if ( self::$hook ) {
				add_action( 'load-' . self::$hook, array( __CLASS__, 'on_load' ) );
			}
		}

		/**
		 * pin_last.
		 *
		 * Moves My Plugins to the end of the WPFactory submenu after every plugin has added its items,
		 * so it stays last in the sidebar and in the top bar (built from the same submenu).
		 *
		 * @version 1.2.0
		 * @since   1.2.0
		 */
		static function pin_last() {
			global $submenu;
			$parent = self::parent_slug();
			if ( empty( $submenu[ $parent ] ) || ! is_array( $submenu[ $parent ] ) ) {
				return;
			}
			$submenu[ $parent ] = self::move_last( $submenu[ $parent ], self::SLUG ); // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited -- reorders, nothing is removed.
		}

		/**
		 * move_last.
		 *
		 * @version 1.2.0
		 * @since   1.2.0
		 *
		 * @param array  $entries Submenu entries ( title, capability, slug, ... ).
		 * @param string $slug    Slug to move.
		 *
		 * @return array
		 */
		static function move_last( array $entries, $slug ) {
			foreach ( $entries as $key => $entry ) {
				if ( isset( $entry[2] ) && $slug === $entry[2] ) {
					unset( $entries[ $key ] );
					$entries[] = $entry;
					break;
				}
			}
			return $entries;
		}

		/**
		 * on_load.
		 *
		 * @version 1.2.0
		 * @since   1.2.0
		 */
		static function on_load() {
			update_user_meta( get_current_user_id(), self::SEEN_META, Catalog::CATALOG_VERSION );
		}

		/**
		 * enqueue.
		 *
		 * @version 1.2.0
		 * @since   1.2.0
		 *
		 * @param string $hook_suffix Current admin page.
		 */
		static function enqueue( $hook_suffix ) {
			if ( '' === self::$hook || $hook_suffix !== self::$hook ) {
				return;
			}
			// The library's one stylesheet and script, built from src/scss and src/js by webpack.
			// Own handle: older copies of the library register "wpfactory-cross-selling" with their own file.
			$ver    = Plugin::version();
			$suffix = ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min' );
			wp_enqueue_style( 'wpfcs-my-plugins', Plugin::url( 'assets/css/admin' . $suffix . '.css' ), array(), $ver );
			wp_enqueue_script( 'wpfcs-my-plugins', Plugin::url( 'assets/js/admin' . $suffix . '.js' ), array(), $ver, true );
			wp_localize_script( 'wpfcs-my-plugins', 'wpfcsMyPlugins', array(
				'ajaxUrl' => admin_url( 'admin-ajax.php' ),
				'nonce'   => wp_create_nonce( Installer::NONCE ),
				'i18n'    => array(
					'installing' => __( 'Installing...', 'wpfactory-cross-selling' ),
					'activate'   => __( 'Activate', 'wpfactory-cross-selling' ),
					'activating' => __( 'Activating...', 'wpfactory-cross-selling' ),
					'activated'  => __( 'Activated', 'wpfactory-cross-selling' ),
					'pro'        => __( 'Pro', 'wpfactory-cross-selling' ),
					'free'       => __( 'Free', 'wpfactory-cross-selling' ),
					'settings'   => __( 'Settings', 'wpfactory-cross-selling' ),
					'wporg'      => __( 'View on WordPress.org', 'wpfactory-cross-selling' ),
					'newTab'     => __( '(opens in a new tab)', 'wpfactory-cross-selling' ),
					'error'      => __( 'Something went wrong. Reload the page and try again.', 'wpfactory-cross-selling' ),
				),
			) );
		}

		/**
		 * template.
		 *
		 * Prints a template from the library's templates/my-plugins/ folder. The path comes from
		 * the copy of the library that runs the page (the newest one), like its assets.
		 *
		 * @version 1.2.2
		 * @since   1.2.2
		 *
		 * @param string $name Template name, without ".php".
		 * @param array  $args Variables for the template.
		 */
		static function template( $name, array $args = array() ) {
			$file = Plugin::path( 'templates/my-plugins/' . sanitize_file_name( $name ) . '.php' );
			if ( ! is_readable( $file ) ) {
				return;
			}
			extract( $args, EXTR_SKIP ); // phpcs:ignore WordPress.PHP.DontExtract.extract_extract -- template variables, as in get_template().
			include $file;
		}

		/**
		 * render.
		 *
		 * @version 1.2.2
		 * @since   1.2.0
		 */
		static function render() {
			if ( ! current_user_can( Plugin::capability() ) ) {
				return;
			}
			$state = Site_State::from_wp();
			$items = Catalog::get_items();
			$owned = self::sort_owned( $items, $state );
			self::template( 'page', array(
				'state'   => $state,
				'woo'     => class_exists( 'WooCommerce' ),
				'items'   => $items,
				'picks'   => Rules_Engine::get_picks( $state ),
				'owned'   => $owned,
				'subline' => self::get_subline( count( $owned ) ),
			) );
		}

		/**
		 * is_owned_active.
		 *
		 * In use when the free or the Pro version is active (a free copy running next to an
		 * installed but inactive Pro is in use).
		 *
		 * @version 1.2.3
		 * @since   1.2.0
		 *
		 * @param array      $item  Catalog item.
		 * @param Site_State $state Site state.
		 *
		 * @return bool
		 */
		static function is_owned_active( $item, $state ) {
			$found = $state->find( $item );
			return ( $found['pro_active'] || $found['free_active'] );
		}

		/**
		 * sort_owned.
		 *
		 * Installed catalog items, active first and inactive last, catalog order inside each group.
		 *
		 * @version 1.2.0
		 * @since   1.2.0
		 *
		 * @param array      $items Catalog items.
		 * @param Site_State $state Site state.
		 *
		 * @return array
		 */
		static function sort_owned( array $items, $state ) {
			$active   = array();
			$inactive = array();
			foreach ( $items as $id => $item ) {
				if ( ! $state->is_installed( $item ) ) {
					continue;
				}
				if ( self::is_owned_active( $item, $state ) ) {
					$active[ $id ] = $item;
				} else {
					$inactive[ $id ] = $item;
				}
			}
			return $active + $inactive;
		}

		/**
		 * get_subline.
		 *
		 * @version 1.2.0
		 * @since   1.2.0
		 *
		 * @param int $installed Installed WPFactory and WBW plugins, active or not.
		 *
		 * @return string
		 */
		static function get_subline( $installed ) {
			if ( $installed < 1 ) {
				return __( 'Here\'s what fits how you sell.', 'wpfactory-cross-selling' );
			}
			/* translators: %s: number of installed WPFactory plugins. */
			$have = sprintf( _n( 'You have %s WPFactory plugin.', 'You have %s WPFactory plugins.', $installed, 'wpfactory-cross-selling' ), number_format_i18n( $installed ) );
			return $have . ' ' . __( 'Here\'s what else fits how you sell.', 'wpfactory-cross-selling' );
		}

		/**
		 * get_topbar_links.
		 *
		 * The WPFactory submenu entries the user can open, for the top bar.
		 *
		 * @version 1.2.2
		 * @since   1.2.2
		 *
		 * @return array[] { href, label, current }
		 */
		static function get_topbar_links() {
			global $submenu;
			$parent  = self::parent_slug();
			$entries = isset( $submenu[ $parent ] ) ? (array) $submenu[ $parent ] : array();
			$links   = array();
			foreach ( $entries as $entry ) {
				if ( empty( $entry[2] ) || ( ! empty( $entry[1] ) && ! current_user_can( $entry[1] ) ) ) {
					continue;
				}
				$slug  = $entry[2];
				$label = trim( wp_strip_all_tags( self::strip_menu_extras( $entry[0] ) ) );
				if ( '' === $label ) {
					continue;
				}
				$links[] = array(
					'href'    => ( false !== strpos( $slug, '.php' ) ? admin_url( $slug ) : admin_url( 'admin.php?page=' . $slug ) ),
					'label'   => $label,
					'current' => ( self::SLUG === $slug ),
				);
			}
			return $links;
		}

		/**
		 * strip_menu_extras.
		 *
		 * Removes what WordPress and plugins add next to a submenu title: screen reader text
		 * and count bubbles (awaiting-mod, update-plugins, menu-counter, ...).
		 *
		 * @version 1.2.3
		 * @since   1.2.3
		 *
		 * @param string $title Submenu title HTML.
		 *
		 * @return string
		 */
		static function strip_menu_extras( $title ) {
			$title = preg_replace( '/<span class="screen-reader-text">.*?<\/span>/s', '', (string) $title );
			return preg_replace( '/<span[^>]*class=(["\'])(?:[^"\']*\s)?(?:awaiting-mod|update-plugins|menu-counter|plugin-count|count-\d+)(?:\s[^"\']*)?\1[^>]*>.*?<\/span>(\s*<\/span>)?/s', '', $title );
		}

		/**
		 * render_topbar.
		 *
		 * @version 1.2.2
		 * @since   1.2.0
		 */
		static function render_topbar() {
			self::template( 'top-bar', array(
				'links' => self::get_topbar_links(),
				'dot'   => self::$dot,
				'logo'  => Plugin::url( 'assets/img/wpfactory-logo-icon.svg' ),
			) );
		}

		/**
		 * get_owned_rows.
		 *
		 * Chip: "Pro" when Pro is active, "Free" when the free version is active, else "Inactive".
		 * Action:
		 * - nothing active: Activate (Pro if installed, else free);
		 * - free active, Pro installed but off: Activate Pro;
		 * - free active, no Pro installed: Upgrade to Pro (when there is a Pro);
		 * - otherwise the plugin's Settings link, if it has one.
		 *
		 * @version 1.2.3
		 * @since   1.2.2
		 *
		 * @param array      $owned Installed catalog items.
		 * @param Site_State $state Site state.
		 *
		 * @return array[] { item, chip_type, chip_label, action, action_url }
		 */
		static function get_owned_rows( array $owned, $state ) {
			$rows         = array();
			$can_activate = current_user_can( 'activate_plugins' );
			foreach ( $owned as $item ) {
				$found = $state->find( $item );
				$row   = array(
					'item'       => $item,
					'chip_type'  => 'neutral',
					'chip_label' => __( 'Inactive', 'wpfactory-cross-selling' ),
					'action'     => '',
					'action_url' => '',
				);
				if ( $found['pro_active'] ) {
					$row['chip_type']  = 'ok';
					$row['chip_label'] = __( 'Pro', 'wpfactory-cross-selling' );
				} elseif ( $found['free_active'] ) {
					$row['chip_label'] = __( 'Free', 'wpfactory-cross-selling' );
				}
				if ( ! $found['pro_active'] && ! $found['free_active'] ) {
					$row['action'] = ( $can_activate ? 'activate' : '' );
				} elseif ( ! $found['pro_active'] && '' !== $found['pro'] ) {
					$row['action'] = ( $can_activate ? 'activate_pro' : '' );
				} elseif ( ! $found['pro_active'] && '' !== $item['pro_url'] ) {
					$row['action']     = 'upgrade';
					$row['action_url'] = $item['pro_url'];
				} else {
					$settings = Installer::get_settings_url( $found['pro_active'] ? $found['pro'] : $found['free'] );
					if ( '' !== $settings ) {
						$row['action']     = 'settings';
						$row['action_url'] = $settings;
					}
				}
				$rows[] = $row;
			}
			return $rows;
		}

		/**
		 * render_owned.
		 *
		 * @version 1.2.2
		 * @since   1.2.0
		 *
		 * @param array      $owned Installed catalog items.
		 * @param Site_State $state Site state.
		 */
		static function render_owned( array $owned, $state ) {
			self::template( 'owned', array( 'rows' => self::get_owned_rows( $owned, $state ) ) );
		}

		/**
		 * render_picks.
		 *
		 * @version 1.2.2
		 * @since   1.2.0
		 *
		 * @param array $picks Rules_Engine::get_picks().
		 */
		static function render_picks( array $picks ) {
			$cards = array();
			foreach ( $picks as $pick ) {
				$item = Catalog::get_item( $pick['id'] );
				if ( $item ) {
					$cards[] = array(
						'item'   => $item,
						'reason' => $pick['reason'],
					);
				}
			}
			self::template( 'picks', array( 'cards' => $cards ) );
		}

		/**
		 * render_catalog.
		 *
		 * @version 1.2.2
		 * @since   1.2.0
		 *
		 * @param array      $items      Catalog items.
		 * @param Site_State $state      Site state.
		 * @param bool       $woo        WooCommerce is active.
		 * @param string[]   $picked_ids Ids shown in "Picked for your store".
		 */
		static function render_catalog( array $items, $state, $woo, array $picked_ids ) {
			$shown = array();
			foreach ( $items as $id => $item ) {
				if ( Catalog::is_available( $item ) && ! $state->is_installed( $item ) && ( $woo || ! $item['store'] ) ) {
					$shown[ $id ] = $item;
				}
			}
			if ( empty( $shown ) ) {
				return;
			}
			$shown = self::top_first( $shown );
			$tabs  = array();
			foreach ( Catalog::get_jobs() as $job => $label ) {
				$count = count( array_filter( $shown, function ( $item ) use ( $job ) {
					return ( 'top' === $job ? $item['top'] : $job === $item['job'] );
				} ) );
				if ( $count > 0 ) {
					$tabs[ $job ] = $label;
				}
			}
			self::template( 'catalog', array(
				'shown'      => $shown,
				'tabs'       => $tabs,
				'first'      => key( $tabs ),
				'picked_ids' => $picked_ids,
			) );
		}

		/**
		 * top_first.
		 *
		 * Top plugins first, in Catalog::TOP order, then the rest in catalog order.
		 * Every tab and search result keeps this order.
		 *
		 * @version 1.2.0
		 * @since   1.2.0
		 *
		 * @param array $items Catalog items keyed by id.
		 *
		 * @return array
		 */
		static function top_first( array $items ) {
			$top = array();
			foreach ( Catalog::TOP as $id ) {
				if ( isset( $items[ $id ] ) ) {
					$top[ $id ] = $items[ $id ];
				}
			}
			return $top + $items;
		}

		/**
		 * get_logo_src.
		 *
		 * @version 1.2.2
		 * @since   1.2.2
		 *
		 * @param array $item Catalog item.
		 *
		 * @return string Bundled icon, wp.org icon, or "" for the letters badge.
		 */
		static function get_logo_src( $item ) {
			if ( '' !== $item['icon_file'] ) {
				return Plugin::url( 'assets/img/icons/' . $item['icon_file'] );
			}
			return $item['icon_url'];
		}

		/**
		 * render_actions.
		 *
		 * @version 1.2.2
		 * @since   1.2.0
		 *
		 * @param array  $item    Catalog item.
		 * @param string $context "pick" or "row".
		 */
		static function render_actions( $item, $context ) {
			$pro_only    = Catalog::is_pro_only( $item );
			$can_install = ( ! $pro_only && Installer::can_install() );
			self::template( 'actions', array(
				'item'        => $item,
				'main'        => ( 'pick' === $context ? 'wpfcs-ui-btn-primary' : 'wpfcs-ui-btn-secondary' ),
				'pro_only'    => $pro_only,
				'can_install' => $can_install,
				'wporg_url'   => ( $pro_only || $can_install ? '' : Installer::wporg_url( $item ) ),
			) );
		}

		/**
		 * external_link.
		 *
		 * New tab, rel="noopener noreferrer", no tracking params.
		 *
		 * @version 1.2.0
		 * @since   1.2.0
		 *
		 * @param string $url   URL.
		 * @param string $text  Link text.
		 * @param string $class CSS classes.
		 *
		 * @return string Escaped HTML.
		 */
		static function external_link( $url, $text, $class ) {
			return sprintf(
				'<a class="%s" href="%s" target="_blank" rel="noopener noreferrer">%s<span class="screen-reader-text"> %s</span></a>',
				esc_attr( $class ),
				esc_url( $url ),
				esc_html( $text ),
				esc_html__( '(opens in a new tab)', 'wpfactory-cross-selling' )
			);
		}

	}

}
