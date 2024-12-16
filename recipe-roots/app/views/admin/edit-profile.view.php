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
				<?php foreach ( $profiles as $index => $profile ) : ?>
					<form class="admin__editor" method="post" enctype="multipart/form-data">
						<h2><?= $profile['type'] === PROFILE_TYPES['user'] ? 'User' : 'Farmer' ?> Profile</h2>

						<?php injectCsrfToken(); ?>
						<input type="hidden" name="profileId" id="profileId-<?= $profile['id'] ?>" value="<?= $profile['id'] ?>">

						<div class="admin__editor__fields">
							<div>
								<div class="admin__editor__input">
									<label for="avatar-<?= $profile['id'] ?>">Avatar</label>

									<div class="input__file">
										<label for="avatar-<?= $profile['id'] ?>">
											<img src="<?= ROOT ?>/assets/icons/image-picker.svg" alt="image picker">

											<?php if ( isset( $profile['avatar'] ) ) : ?>
												<img src="<?= escape( $profile['avatar'] ?? '' ) ?>" alt="" class="input__file--preview">
												<input type="text" name="thumbnail" value="<?= escape( $profile['avatar'] ?? '' ) ?>" hidden>
											<?php endif; ?>

											<noscript>
												<p>Image preview does not work without JavaScript.</p>
												<p>Refer to file name below instead.</p>
											</noscript>
										</label>
										<input type="file" name="avatar" id="avatar-<?= $profile['id'] ?>" accept=".png, .gif, .jpeg, .jpg">
									</div>
								</div>
							</div>

							<div>
								<div class="admin__editor__input">
									<label for="username-<?= $profile['id'] ?>">Username</label>
									<input type="text" name="username" id="username-<?= $profile['id'] ?>"
										value="<?= escape( $profile['username'] ) ?>" required>
								</div>

								<div class="admin__editor__input">
									<label for="currentPassword-<?= $profile['id'] ?>">Your Current Password</label>
									<input type="password" name="currentPassword" id="currentPassword-<?= $profile['id'] ?>" required>
								</div>

								<button type="submit" class="btn">Save</button>
							</div>
						</div>
					</form>
				<?php endforeach; ?>

				<div class="admin__editor__actions">
					<a href="<?= ROOT ?>/admin/edit/account/<?= escape( $userId ) ?>" class="btn btn--invert">Account</a>
					<a href="<?= ROOT ?>/admin" class="btn btn--invert">Cancel</a>
				</div>
			</div>
		</article>
	</main>

	<?php include '../app/views/layout/footer.php' ?>
	<script>
		// ==== Input handlers ====
		const avatarInputs = document.querySelectorAll('[id^=avatar]');

		avatarInputs.forEach(avatarInput => {
			avatarInput.addEventListener('change', (event) => {
				const avatar = event.target.files[0];
				if (!avatar) return;

				const label = avatarInput.parentElement.getElementsByTagName('label')[0];
				if (!label) return;

				let avatarPreview = label.getElementsByClassName('input__file--preview')[0];
				if (avatarPreview) {
					avatarPreview.src = URL.createObjectURL(avatar);
				} else {
					avatarPreview = document.createElement('img');
					avatarPreview.classList.add('input__file--preview');
					avatarPreview.src = URL.createObjectURL(avatar);
					label.append(avatarPreview);
				}
			});
		});
	</script>
</body>

</html>