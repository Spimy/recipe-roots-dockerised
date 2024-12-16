<!DOCTYPE html>
<html lang="en">

<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<link rel="shortcut icon" href="<?= ROOT ?>/favicon.ico" type="image/x-icon">
	<link rel="stylesheet" href="<?= ROOT ?>/assets/css/styles.css">
	<link rel="stylesheet" href="<?= ROOT ?>/assets/css/layout/auth.css">
	<title>Recipe Roots - Sign Up</title>
</head>

<body>
	<?php include 'layout/header.php' ?>

	<main>
		<h1>Sign Up</h1>

		<?php if ( ! empty( $errors ) ) : ?>
			<ul class="errors">
				<?php foreach ( $errors as $error ) : ?>
					<li class="errors__message"><?= escape( $error ) ?></li>
				<?php endforeach ?>
			</ul>
		<?php endif ?>

		<form class="auth" method="post">
			<?php injectCsrfToken(); ?>

			<div class="auth__input">
				<label>You are a</label>

				<div class="auth__input--radio auth__input--type">
					<label for="user">
						<input type="radio" name="accountType" id="user" value="user" <?= ( $_POST['accountType'] ?? '' ) === 'user' ? 'checked' : '' ?> required>
						User
					</label>
					<label for="farmer">
						<input type="radio" name="accountType" id="farmer" value="farmer" <?= ( $_POST['accountType'] ?? '' ) === 'farmer' ? 'checked' : '' ?>>
						Farmer
					</label>
				</div>
			</div>

			<div class="auth__input">
				<label for="email">Email</label>
				<input type="email" name="email" id="email"
					pattern="^[a-zA-Z0-9.!#$%&'*+/=?^_`{|}~-]+@[a-zA-Z0-9](?:[a-zA-Z0-9-]{0,61}[a-zA-Z0-9])?(?:\.[a-zA-Z0-9](?:[a-zA-Z0-9-]{0,61}[a-zA-Z0-9])?)*$"
					value="<?= escape( $_POST['email'] ?? '' ) ?>" required>
				<small>Invalid Email</small>
			</div>

			<div class="auth__input">
				<label for="username">Username</label>
				<input type="text" name="username" id="username" pattern="^.{1,16}$"
					value="<?= escape( $_POST['username'] ?? '' ) ?>" required>
				<small>Username cannot be longer than 16 characters</small>
			</div>

			<div class="auth__input">
				<label for="password">Password</label>
				<input type="password" name="password" id="password"
					pattern='^.*(?=.{8,})(?=.*[a-zA-Z])(?=.*\d)(?=.*[@!#$%&? "]).*$' required>
				<small>
					Password must contain at least one letter, one digit and one character (@!#$%&?) and be at least 8 characters
					long
				</small>
			</div>

			<div class="auth__input">
				<label for="confirm-password">Confirm Password</label>
				<input type="password" name="confirmPassword" id="confirm-password" required>
			</div>

			<div class="auth__input">
				<label>Dietary Type</label>

				<div class="auth__input--radio">
					<label for="none">
						<input type="radio" name="dietaryType" id="none" value="none" checked required>
						None
					</label>
					<label for="vegetarian">
						<input type="radio" name="dietaryType" id="vegetarian" value="vegetarian" <?= ( $_POST['dietaryType'] ?? '' ) === 'vegetarian' ? 'checked' : '' ?>>
						Vegetarian
					</label>
					<label for="vegan">
						<input type="radio" name="dietaryType" id="vegan" value="vegan" <?= ( $_POST['dietaryType'] ?? '' ) === 'vegan' ? 'checked' : '' ?>>
						Vegan
					</label>
					<label for="halal">
						<input type="radio" name="dietaryType" id="halal" value="halal" <?= ( $_POST['dietaryType'] ?? '' ) === 'halal' ? 'checked' : '' ?>>
						Halal
					</label>
				</div>
			</div>

			<button type="submit" class="btn">Sign Up</button>

			<p>Already have an account? <a href="<?= ROOT ?>/signin">Sign In</a></p>
		</form>

	</main>

	<?php include 'layout/footer.php' ?>
</body>

</html>