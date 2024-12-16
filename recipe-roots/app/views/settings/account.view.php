<!DOCTYPE html>
<html lang="en">

<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<link rel="shortcut icon" href="<?= ROOT ?>/favicon.ico" type="image/x-icon">
	<link rel="stylesheet" href="<?= ROOT ?>/assets/css/styles.css">
	<link rel="stylesheet" href="<?= ROOT ?>/assets/css/layout/settings.css">
	<title>Recipe Roots - Settings</title>
</head>

<body>
	<?php include '../app/views/layout/header.php' ?>

	<main>
		<h1>Settings</h1>

		<?php if ( ! empty( $message ) ) : ?>
			<p class="success"><?= escape( $message ) ?></p>
		<?php endif; ?>

		<?php if ( ! empty( $errors ) ) : ?>
			<ul class="errors">
				<?php foreach ( $errors as $error ) : ?>
					<li class="errors__message"><?= escape( $error ) ?></li>
				<?php endforeach ?>
			</ul>
		<?php endif ?>

		<article class="settings">
			<aside class="settings__nav">
				<ul role="list" class="settings__nav__links">
					<li>
						<a class="settings__nav__links__link settings__nav__links__link--active" href="<?= ROOT ?>/settings">
							Account
						</a>
					</li>
					<li><a class="settings__nav__links__link" href="<?= ROOT ?>/settings/profiles">Profiles</a></li>
				</ul>

				<div class="settings__nav__btns">
					<a class="btn btn--error" href="<?= ROOT ?>/signout">Sign Out</a>
				</div>
			</aside>

			<div class="settings__forms">
				<form class="settings__editor" method="post">
					<?php injectCsrfToken(); ?>

					<div class="settings__editor__input">
						<label for="email">Email</label>
						<input type="email" name="email" id="email" value="<?= escape( $account['email'] ?? '' ) ?>" required>
					</div>

					<div class="settings__editor__input">
						<label for="currentPassword">Current Password</label>
						<input type="password" name="currentPassword" id="currentPassword" required>
					</div>

					<div class="settings__editor__input">
						<label for="newPassword">New Password</label>
						<input type="password" name="newPassword" id="newPassword"
							pattern='^.*(?=.{8,})(?=.*[a-zA-Z])(?=.*\d)(?=.*[@!#$%&? "]).*$'>
						<small>
							Password must contain at least one letter, one digit and one character (@!#$%&?) and be at least 8
							characters
							long
						</small>
					</div>

					<div class="settings__editor__input">
						<label for="confirmPassword">Confirm New Password</label>
						<input type="password" name="confirmPassword" id="confirmPassword">
					</div>

					<div class="settings__editor__input">
						<label>Dietary Type</label>

						<div class="settings__editor__input--radio">
							<label for="none">
								<input type="radio" name="dietaryType" id="none" value="none" checked required>
								None
							</label>
							<label for="vegetarian">
								<input type="radio" name="dietaryType" id="vegetarian" value="vegetarian" <?= ( $account['dietaryType'] ?? '' ) === 'vegetarian' ? 'checked' : '' ?>>
								Vegetarian
							</label>
							<label for="vegan">
								<input type="radio" name="dietaryType" id="vegan" value="vegan" <?= ( $account['dietaryType'] ?? '' ) === 'vegan' ? 'checked' : '' ?>>
								Vegan
							</label>
							<label for="halal">
								<input type="radio" name="dietaryType" id="halal" value="halal" <?= ( $account['dietaryType'] ?? '' ) === 'halal' ? 'checked' : '' ?>>
								Halal
							</label>
						</div>
					</div>

					<button type="submit" class="btn">Save</button>
					<button type="button" popovertarget="delete-account" class="btn btn--error">Delete Account</button>
				</form>

				<!-- Pop up for confirm delete -->
				<form popover role="dialog" id="delete-account" class="modal" method="post"
					action="<?= ROOT ?>/settings/delete">
					<?php injectCsrfToken() ?>
					<div>
						<h3>Confirm Delete</h3>
						<p>Are you sure you want to delete your account? <strong>Enter your current password to confirm:</strong>
						</p>

						<div class="settings__editor__input">
							<input type="password" name="currentPassword" id="currentPassword" required>
						</div>
					</div>

					<div>
						<button type="button" class="btn btn--invert" popovertarget="delete-account">Cancel</button>
						<button class="btn btn--error">Delete</button>
					</div>
				</form>
			</div>
		</article>
	</main>

	<?php include '../app/views/layout/footer.php' ?>
</body>

</html>