<!DOCTYPE html>
<html lang="en">

<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<link rel="shortcut icon" href="<?= ROOT ?>/favicon.ico" type="image/x-icon">
	<link rel="stylesheet" href="<?= ROOT ?>/assets/css/styles.css">
	<link rel="stylesheet" href="<?= ROOT ?>/assets/css/pages/home.css">
	<title>Recipe Roots</title>
</head>

<body>
	<?php include 'layout/header.php' ?>

	<main>
		<section class="hero">
			<?php if ( isset( $_GET['delete'] ) && $_GET['delete'] === 'success' ) : ?>
				<p class="success"><?= 'Your account has been deleted successfully' ?></p>
			<?php endif; ?>

			<h1 class="hero__content__title">Your Kitchen Assistant</h1>
			<p class="hero__content__subtitle">
				Create and share your recipes with fellow enthusiasts with easy access
				to all your cooking needs
			</p>
			<a href="<?= ROOT ?>/signup" class="btn btn--next">Get started</a>
		</section>

		<section class="features">
			<h2>Why Us?</h2>
			<div class="features__items grid">
				<div>
					<img src="<?= ROOT ?>/assets/images/illustrations/devices.svg" alt="Showcasing responsiveness" />
					<div>
						<h3>View on any device</h3>
						<p>
							Whether you cook with your phone, tablet, or your laptop nearby, you can use any of them!
						</p>
					</div>
				</div>
				<div>
					<img src="<?= ROOT ?>/assets/images/illustrations/community.svg" alt="Community driven" />
					<div>
						<h3>Community Driven</h3>
						<p>
							You can find all the recipes you need shared by other fellow cooks!
						</p>
					</div>
				</div>
				<div>
					<img src="<?= ROOT ?>/assets/images/illustrations/purchase.svg" alt="Easily access ingredients" />
					<div>
						<h3>Easy Ingredients</h3>
						<p>
							Ran out of ingredients? Check out the latest and freshest produce from your local farmers!
						</p>
					</div>
				</div>
			</div>
		</section>

		<section class="faq">
			<h2>FAQ</h2>

			<details class="faq__item">
				<summary class="faq__item__question">Are my recipes public?</summary>
				<p class="faq__item__answer">
					Your recipes can either be private or public depending on your
					preference.
				</p>
			</details>

			<details class="faq__item">
				<summary class="faq__item__question">Do I need an account for each device?</summary>
				<p class="faq__item__answer">
					There is no need for an account for each device. As long as you log
					into your account on a device, you'll only always need one account.
				</p>
			</details>

			<details class="faq__item">
				<summary class="faq__item__question">Can I have both a farmer and normal user account?</summary>
				<p class="faq__item__answer">
					Yes, most certainly! If you produce ingredients and also have a
					knack for creating outstanding recipes, then feel free to have access
					to both accounts.
				</p>
			</details>

			<details class="faq__item">
				<summary class="faq__item__question">How long do ingredients take to be delivered?</summary>
				<p class="faq__item__answer">
					Your ingredients should be delivered to you within 1-2 hours after
					placing your order.
				</p>
			</details>
		</section>
	</main>

	<?php include 'layout/footer.php' ?>
</body>

</html>