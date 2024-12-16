<!DOCTYPE html>
<html lang="en">

<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<link rel="shortcut icon" href="<?= ROOT ?>/favicon.ico" type="image/x-icon">
	<link rel="stylesheet" href="<?= ROOT ?>/assets/css/styles.css">
	<link rel="stylesheet" href="<?= ROOT ?>/assets/css/layout/auth.css">
	<title>Recipe Roots - Sign In</title>
</head>

<body>
	<?php include 'layout/header.php' ?>

	<main>
		<h1>Sign In</h1>

		<?php if ( ! empty( $_SESSION['signup'] ) ) : ?>
			<p class="success"><?= escape( $_SESSION['signup'] ) ?></p>
			<?php unset( $_SESSION['signup'] ) ?>
		<?php endif; ?>

		<?php if ( ! empty( $_SESSION['profile'] ) ) : ?>
			<p class="success">Currently signed in as <?= escape( $_SESSION['profile']['username'] ) ?></p>
		<?php endif; ?>

		<?php if ( ! empty( $error ) ) : ?>
			<p class="errors"><?= escape( $error ) ?></p>
		<?php endif ?>

		<form class="auth" method="post">
			<?php injectCsrfToken(); ?>

			<div class="auth__input">
				<label for="email">Email</label>
				<input type="email" name="email" id="email" value="<?= escape( $_POST['email'] ?? '' ) ?>" required>
			</div>

			<div class="auth__input">
				<label for="password">Password</label>
				<input type="password" name="password" id="password" required>
			</div>

			<div class="auth__input">
				<div class="auth__input--options">
					<label for="remember">
						<input type="checkbox" name="rememberMe" id="remember" <?= isset( $_POST['rememberMe'] ) ? 'checked' : '' ?>>
						Remember me for 30 days
					</label>

					<a href="<?= ROOT ?>/forgotPassword">Forgot your password?</a>
				</div>
			</div>

			<button type="submit" class="btn">Sign In</button>

			<p>Don't have an account? <a href="<?= ROOT ?>/signup">Sign Up</a></p>
		</form>

	</main>

	<?php include 'layout/footer.php' ?>
</body>

</html>