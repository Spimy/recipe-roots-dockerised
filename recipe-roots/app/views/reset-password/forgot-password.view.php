<!DOCTYPE html>
<html lang="en">

<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<link rel="shortcut icon" href="<?= ROOT ?>/favicon.ico" type="image/x-icon">
	<link rel="stylesheet" href="<?= ROOT ?>/assets/css/styles.css">
	<link rel="stylesheet" href="<?= ROOT ?>/assets/css/layout/auth.css">
	<title>Recipe Roots - Forgot Password</title>
</head>

<body>
	<?php include '../app/views/layout/header.php' ?>

	<main>
		<h1>Forgot Password</h1>

		<?php if ( ! empty( $message ) ) : ?>
			<p class="success"><?= escape( $message ) ?></p>
		<?php endif; ?>

		<?php if ( ! empty( $error ) ) : ?>
			<p class="errors"><?= escape( $error ) ?></p>
		<?php endif ?>

		<form class="auth" method="post">
			<?php injectCsrfToken(); ?>

			<div class="auth__input">
				<label for="email">Email</label>
				<input type="email" name="email" id="email" required>
			</div>

			<button type="submit" class="btn">Reset Password</button>

			<p>Remembered your password? <a href="<?= ROOT ?>/signin">Sign In</a></p>
		</form>

	</main>

	<?php include '../app/views/layout/footer.php' ?>
</body>

</html>