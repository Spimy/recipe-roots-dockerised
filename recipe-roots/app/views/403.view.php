<!DOCTYPE html>
<html lang="en">

<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<link rel="shortcut icon" href="<?= ROOT ?>/favicon.ico" type="image/x-icon">
	<link rel="stylesheet" href="<?= ROOT ?>/assets/css/styles.css">
	<title>Recipe Roots - Forbidden</title>
</head>

<body>
	<?php include 'layout/header.php' ?>

	<main>
		<h1>403 Forbidden</h1>

		<?php if ( isset( $message ) ) : ?>
			<p class="errors"><?= escape( $message ) ?></p>
		<?php endif; ?>

		<a href="<?= ROOT ?>" class="btn">Back to Home</a>
	</main>

	<?php include 'layout/footer.php' ?>
</body>

</html>