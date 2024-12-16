<!DOCTYPE html>
<html lang="en">

<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<link rel="shortcut icon" href="<?= ROOT ?>/favicon.ico" type="image/x-icon">
	<link rel="stylesheet" href="<?= ROOT ?>/assets/css/styles.css">
	<link rel="stylesheet" href="<?= ROOT ?>/assets/css/layout/admin.css">
	<title>Recipe Roots - Admin</title>
</head>

<body>
	<?php include '../app/views/layout/header.php' ?>

	<main>
		<h1>Admin</h1>

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

		<article class="admin">
			<aside class="admin__nav">
				<ul role="list" class="admin__nav__links">
					<li>
						<a class="admin__nav__links__link admin__nav__links__link--active" href="<?= ROOT ?>/admin">
							Users
						</a>
					</li>
					<li><a class="admin__nav__links__link" href="<?= ROOT ?>/admin/ingredients">Ingredients</a></li>
					<li><a class="admin__nav__links__link" href="<?= ROOT ?>/recipes/browse">Recipes</a></li>
					<li><a class="admin__nav__links__link" href="<?= ROOT ?>/cookbooks/browse">Cookbooks</a></li>
				</ul>

				<div class="admin__nav__btns">
					<a class="btn btn--error" href="<?= ROOT ?>/signout">Sign Out</a>
				</div>
			</aside>

			<div class="admin__forms">
				<form class="admin__editor" method="post">
					<?php injectCsrfToken(); ?>

					<div class="admin__editor__input">
						<label for="email">Email</label>
						<input type="email" name="email" id="email" value="<?= escape( $user['email'] ?? '' ) ?>" required>
					</div>

					<div class="admin__editor__input">
						<label for="currentPassword">Your Current Password</label>
						<input type="password" name="currentPassword" id="currentPassword" required>
					</div>

					<div class="admin__editor__input">
						<label for="newPassword">New Password</label>
						<input type="password" name="newPassword" id="newPassword">
					</div>

					<div class="admin__editor__input">
						<label for="confirmPassword">Confirm New Password</label>
						<input type="password" name="confirmPassword" id="confirmPassword">
					</div>

					<div class="admin__column">
						<div class="admin__editor__input">
							<label>Dietary Type</label>

							<div class="admin__editor__input--radio">
								<label for="none">
									<input type="radio" name="dietaryType" id="none" value="none" checked required>
									None
								</label>
								<label for="vegetarian">
									<input type="radio" name="dietaryType" id="vegetarian" value="vegetarian" <?= ( $user['dietaryType'] ?? '' ) === 'vegetarian' ? 'checked' : '' ?>>
									Vegetarian
								</label>
								<label for="vegan">
									<input type="radio" name="dietaryType" id="vegan" value="vegan" <?= ( $user['dietaryType'] ?? '' ) === 'vegan' ? 'checked' : '' ?>>
									Vegan
								</label>
								<label for="halal">
									<input type="radio" name="dietaryType" id="halal" value="halal" <?= ( $user['dietaryType'] ?? '' ) === 'halal' ? 'checked' : '' ?>>
									Halal
								</label>
							</div>
						</div>

						<div class="admin__editor__input">
							<label>Admin</label>

							<div class="admin__editor__input--radio">
								<label for="no">
									<input type="radio" name="admin" id="no" value="no" checked required>
									No
								</label>
								<label for="yes">
									<input type="radio" name="admin" id="yes" value="yes" <?= ( $user['isAdmin'] ?? 0 ) === 1 ? 'checked' : '' ?>>
									Yes
								</label>
							</div>
						</div>
					</div>

					<button type="submit" class="btn">Save</button>
					<button type="button" popovertarget="delete-account" class="btn btn--error">Delete Account</button>

					<div class="admin__editor__actions">
						<a href="<?= ROOT ?>/admin/edit/profiles/<?= escape( $user['id'] ) ?>" class="btn btn--invert">Profiles</a>
						<a href="<?= ROOT ?>/admin" class="btn btn--invert">Cancel</a>
					</div>
				</form>

				<!-- Pop up for confirm delete -->
				<form popover role="dialog" id="delete-account" class="modal" method="post"
					action="<?= ROOT ?>/admin/delete/<?= escape( $user['id'] ) ?>">
					<?php injectCsrfToken() ?>
					<div>
						<h3>Confirm Delete</h3>
						<p>Are you sure you want to delete this account? <strong>Enter your current password to confirm:</strong>
						</p>

						<div class="admin__editor__input">
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