<?php
	global $ultimatemember;
	$user_id = get_current_user_id();
	um_fetch_user( $user_id );
?>
<div class="umlw-wrapper">
	<!-- Avatar Section -->
	<div class="umlw-login-avatar">
		<?php if ( $show_avatar ) { ?>
		<div class="um-col-131">
			<a href="<?php echo esc_url( um_user_profile_url() ); ?>" class="um-profile-photo-img" title="<?php /* translators: Placeholder for user display name. */ echo sprintf( esc_attr__( 'Welcome %s', 'login-widget-for-ultimate-member' ), esc_attr( um_user( 'display_name' ) ) ); ?>"><?php echo wp_kses_post( get_avatar( $user_id ) ); ?></a>
		</div>
		<?php } ?>
		<div class="uml-header-info">
			<?php if ( $show_profile_url ) { ?>
			<strong>
				<a href="<?php echo esc_url( um_user_profile_url() ); ?>" class="uml-name-link"><?php echo esc_html( um_user( 'display_name' ) ); ?></a>
			</strong>
			<?php } ?>
			<div>
				<?php do_action( 'umlw_before_logout', $user_id ); ?>
				<?php if ( $show_edit_profile ) { ?>
				<div class="uml-profile-link">
					<a href="<?php echo esc_url( um_edit_profile_url() ); ?>" class="real_url"><?php esc_html_e( 'Edit Profile', 'login-widget-for-ultimate-member' ); ?></a>
				</div>
				<?php } ?>
				<?php if ( $show_account ) { ?>
				<div class="uml-profile-link">
					<a href="<?php echo esc_url( um_get_core_page( 'account' ) ); ?>" class="real_url"><?php esc_html_e( 'My Account', 'login-widget-for-ultimate-member' ); ?></a>
				</div>
				<?php } ?>
				<?php if ( $show_logout ) { ?>
				<div class="uml-profile-link">
					<a href="<?php echo esc_url( um_get_core_page( 'logout' ) ); ?>" class="real_url"><?php esc_html_e( 'Logout', 'login-widget-for-ultimate-member' ); ?></a>
				</div>
				<?php } ?>
				<?php do_action( 'umlw_after_logout', $user_id ); ?>
			</div>
		</div>
		<div class="um-clear"></div>
	</div>
	<div class="um-clear"></div>
	<!-- /Avatar Section -->
	<!-- Nav Section -->
	<?php
	if ( function_exists( 'UM' ) ) {

		if ( ! $show_profile_tabs || ! UM()->options()->get( 'profile_menu' ) ) {
			return;
		}

		// get active tabs
		$tabs = UM()->profile()->tabs_active();

		$tabs = apply_filters( 'um_user_profile_tabs', $tabs );

		UM()->user()->tabs = $tabs;

		// need enough tabs to continue
		if ( count( $tabs ) <= 1 ) {
			return;
		}

		$active_tab = UM()->profile()->active_tab();

		if ( ! isset( $tabs[ $active_tab ] ) ) {
			$active_tab                    = 'main';
			UM()->profile()->active_tab    = $active_tab;
			UM()->profile()->active_subnav = null;
		}

		// Move default tab priority
		$default_tab = UM()->options()->get( 'profile_menu_default_tab' );
		$dtab        = ( isset( $tabs[ $default_tab ] ) ) ? $tabs[ $default_tab ] : 'main';
		if ( isset( $tabs[ $default_tab ] ) ) {
			unset( $tabs[ $default_tab ] );
			$dtabs[ $default_tab ] = $dtab;
			$tabs                  = $dtabs + $tabs;
		}
	} else {
		// get active tabs
		$tabs = $ultimatemember->profile->tabs_active();

		$tabs = apply_filters( 'um_user_profile_tabs', $tabs );

		$ultimatemember->user->tabs = $tabs;

		// need enough tabs to continue
		if ( count( $tabs ) <= 1 ) {
			return;
		}

		$active_tab = $ultimatemember->profile->active_tab();

		if ( ! isset( $tabs[ $active_tab ] ) ) {
			$active_tab                             = 'main';
			$ultimatemember->profile->active_tab    = $active_tab;
			$ultimatemember->profile->active_subnav = null;
		}

		// Move default tab priority
		$default_tab = UM()->options()->get( 'profile_menu_default_tab' );
		$dtab        = ( isset( $tabs[ $default_tab ] ) ) ? $tabs[ $default_tab ] : 'main';
		if ( isset( $tabs[ $default_tab ] ) ) {
			unset( $tabs[ $default_tab ] );
			$dtabs[ $default_tab ] = $dtab;
			$tabs                  = $dtabs + $tabs;
		}
	}
	?>
		<div class="umlw-profile-nav">
		<?php do_action( 'umlw_before_nav', $user_id ); ?>
		<?php
		foreach ( $tabs as $id => $tab ) {

			if ( isset( $tab['hidden'] ) ) {
					continue;
			}

			if ( function_exists( 'UM' ) ) {

				$nav_link = um_user_profile_url( um_user( 'ID' ) );
				$nav_link = remove_query_arg( 'um_action', $nav_link );
				$nav_link = remove_query_arg( 'subnav', $nav_link );
				$nav_link = add_query_arg( 'profiletab', $id, $nav_link );
				$nav_link = apply_filters( "um_profile_menu_link_{$id}", $nav_link );
			} else {
				$nav_link = $ultimatemember->permalinks->get_current_url( get_option( 'permalink_structure' ) );
				$nav_link = um_user_profile_url();
				$nav_link = remove_query_arg( 'um_action', $nav_link );
				$nav_link = remove_query_arg( 'subnav', $nav_link );
				$nav_link = add_query_arg( 'profiletab', $id, $nav_link );
				$nav_link = apply_filters( "um_profile_menu_link_{$id}", $nav_link );
			}
			?>
		<div class="umlw-profile-nav-item um-profile-nav-<?php echo esc_attr( $id ); ?> <?php
		if ( ! UM()->options()->get( 'profile_menu_icons' ) ) {
			echo 'without-icon'; }
		?>
			<?php
			if ( $id === $active_tab ) {
						echo 'active'; }
			?>
">
			<a href="<?php echo esc_url( $nav_link ); ?>" title="<?php echo esc_attr( $tab['name'] ); ?>">
				<i class="<?php echo esc_attr( $tab['icon'] ); ?>"></i>
				<?php if ( isset( $tab['notifier'] ) && $tab['notifier'] > 0 ) { ?>
				<span class="um-tab-notifier uimob500-show uimob340-show uimob800-show"><?php echo esc_html( $tab['notifier'] ); ?></span>
				<?php } ?>
				<span class="uimob500-hide uimob340-hide uimob800-hide umlw-title"><?php echo esc_html( $tab['name'] ); ?></span>
			</a>
		</div>
		<?php } ?>
		<?php do_action( 'umlw_after_nav', $user_id ); ?>
		<div class="um-clear"></div>
		</div>
	<!-- /Nav Section -->
</div>
<?php
um_reset_user();
?>
