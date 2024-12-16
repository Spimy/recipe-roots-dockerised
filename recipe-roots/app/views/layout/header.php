<header class="navbar">
	<a href="<?= ROOT ?>/">
		<img class="navbar__logo" src="<?= ROOT ?>/assets/images/logo.svg" alt="Logo" class="logo">
	</a>

	<nav class="navbar__menu">
		<div class="navbar__menu__toggle">
			<input type="checkbox" name="toggle-nav" id="toggle-nav">
			<label for="toggle-nav">☰</label>
		</div>

		<ul role="list" class="navbar__menu__items">
			<?php if ( isset( $_SESSION['profile'] ) ) : ?>
				<?php if ( $_SESSION['profile']['type'] == PROFILE_TYPES['user'] ) : ?>
					<li>
						<button id="recipes-toggle" popovertarget="recipes">Recipes</button>
						<menu class="nav-menu" popover id="recipes">
							<a href="<?= ROOT ?>/recipes">Your Recipes</a>
							<a href="<?= ROOT ?>/recipes/browse">Browse Recipes</a>
							<button popovertarget="recipes">&times;</button>
						</menu>
					</li>

					<li>
						<button id="cookbooks-toggle" popovertarget="cookbooks">Cookbooks</button>
						<menu class="nav-menu" popover id="cookbooks">
							<a href="<?= ROOT ?>/cookbooks">Your Cookbooks</a>
							<a href="<?= ROOT ?>/cookbooks/browse">Browse Cookbooks</a>
							<button popovertarget="cookbooks">&times;</button>
						</menu>
					</li>

					<li><a href="<?= ROOT ?>/ingredients">Ingredients</a></li>

					<li>
						<div class="icon-nav">
							<a class="notification-link" href="<?= ROOT ?>/notifications">
								<img src="<?= ROOT ?>/assets/icons/bell.svg" alt="bell">
								<?php
								$notificationModel = new Notification();
								$numNoti = count( $notificationModel->findAll( [ 'receiverId' => $_SESSION['profile']['id'], 'isRead' => 0 ] ) );

								if ( $numNoti > 0 ) :
									?>
									<p><?= $numNoti > 9 ? '9+' : $numNoti; ?></p>
								<?php endif ?>
							</a>
							<a class="cart-link" href="<?= ROOT ?>/ingredients/cart">
								<img src="<?= ROOT ?>/assets/icons/shopping-cart.svg" alt="cart">
							</a>
						</div>
					</li>
				<?php else : ?>
					<li><a href="<?= ROOT ?>/dashboard">Dashboard</a></li>
				<?php endif ?>

				<li>
					<button id="avatar-toggle" popovertarget="avatar">
						<?php if ( isset( $_SESSION['profile']['avatar'] ) ) : ?>
							<img class="avatar" src="<?= escape( $_SESSION['profile']['avatar'] ?? '' ) ?>"
								alt="<?= extractTitleLetters( escape( $_SESSION['profile']['username'] ) ) ?>">
						<?php else : ?>
							<div class="avatar"><?= extractTitleLetters( escape( $_SESSION['profile']['username'] ) ) ?></div>
						<?php endif ?>
					</button>

					<menu class="nav-menu" popover id="avatar">
						<a href="<?= ROOT ?>/settings/profiles/switch?next=/">Switch Profile</a>

						<?php if ( $_SESSION['profile']['type'] == PROFILE_TYPES['user'] ) : ?>
							<a href="<?= ROOT ?>/ingredients/invoices">Invoices</a>
						<?php endif ?>

						<a href="<?= ROOT ?>/settings">Settings</a>

						<?php if ( $_SESSION['profile']['user']['isAdmin'] ) : ?>
							<a href="<?= ROOT ?>/admin">Admin</a>
						<?php endif ?>

						<a href="<?= ROOT ?>/signout">Sign Out</a>
						<button popovertarget="avatar">&times;</button>
					</menu>
				</li>
			<?php else : ?>
				<li><a href="<?= ROOT ?>/">Home</a></li>
				<li><a href="<?= ROOT ?>/signin">Sign In</a></li>
			<?php endif; ?>

			<li class="close-nav"><label for="toggle-nav">&times;</label></li>
		</ul>

	</nav>
</header>