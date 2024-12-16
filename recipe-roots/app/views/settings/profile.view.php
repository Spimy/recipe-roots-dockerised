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
					<li><a class="settings__nav__links__link" href="<?= ROOT ?>/settings">Account</a></li>
					<li>
						<a class="settings__nav__links__link settings__nav__links__link--active"
							href="<?= ROOT ?>/settings/profiles">
							Profiles
						</a>
					</li>
				</ul>

				<div class="settings__nav__btns">
					<a class="btn btn--error" href="<?= ROOT ?>/signout">Sign Out</a>
				</div>
			</aside>

			<div class="settings__forms">
				<?php foreach ( $profiles as $index => $profile ) : ?>
					<form class="settings__editor" method="post" action="<?= ROOT ?>/settings/profiles/update"
						enctype="multipart/form-data">
						<h2><?= $profile['type'] === PROFILE_TYPES['user'] ? 'User' : 'Farmer' ?> Profile</h2>

						<?php injectCsrfToken(); ?>
						<input type="hidden" name="profileId" id="profileId-<?= $profile['id'] ?>" value="<?= $profile['id'] ?>">

						<div class="settings__editor__fields">
							<div>
								<div class="settings__editor__input">
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
								<div class="settings__editor__input">
									<label for="username-<?= $profile['id'] ?>">Username</label>
									<input type="text" name="username" id="username-<?= $profile['id'] ?>"
										value="<?= escape( $profile['username'] ) ?>" required>
								</div>

								<div class="settings__editor__input">
									<label for="currentPassword-<?= $profile['id'] ?>">Current Password</label>
									<input type="password" name="currentPassword" id="currentPassword-<?= $profile['id'] ?>" required>
								</div>

								<?php if ( $profile['id'] !== $_SESSION['profile']['id'] ) : ?>
									<a href="<?= ROOT ?>/settings/profiles/switch<?= isset( $_GET['next'] ) ? '?next=' . escape( $_GET['next'] ) : '' ?>"
										class="btn btn--invert">
										Switch
									</a>
								<?php endif; ?>
								<button type="submit" class="btn">Save</button>
							</div>
						</div>
					</form>

					<?php if ( $index !== count( $profiles ) - 1 ) : ?>
						<hr>
					<?php endif; ?>
				<?php endforeach; ?>

				<?php if ( count( $profiles ) === 1 ) : ?>
					<hr>

					<form class="settings__editor" method="post" enctype="multipart/form-data"
						action="<?= ROOT ?>/settings/profiles/create">
						<h2>Create <?= $profiles[0]['type'] === PROFILE_TYPES['user'] ? 'Farmer' : 'User' ?> Profile</h2>

						<?php injectCsrfToken(); ?>

						<div class="settings__editor__fields">
							<div>
								<div class="settings__editor__input">
									<label for="avatar">Avatar</label>

									<div class="input__file">
										<label for="avatar">
											<img src="<?= ROOT ?>/assets/icons/image-picker.svg" alt="image picker">

											<noscript>
												<p>Image preview does not work without JavaScript.</p>
												<p>Refer to file name below instead.</p>
											</noscript>
										</label>
										<input type="file" name="avatar" id="avatar" accept=".png, .gif, .jpeg, .jpg">
									</div>
								</div>
							</div>

							<div>
								<div class="settings__editor__input">
									<label for="username">Username</label>
									<input type="text" name="username" id="username" required>
								</div>

								<div class="settings__editor__input">
									<label for="currentPassword">Current Password</label>
									<input type="password" name="currentPassword" id="currentPassword" required>
								</div>

								<button type="submit" class="btn">Create Profile</button>
							</div>
						</div>
					</form>
				<?php endif; ?>
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