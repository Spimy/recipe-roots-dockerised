<!DOCTYPE html>
<html lang="en">

<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<link rel="shortcut icon" href="<?= ROOT ?>/favicon.ico" type="image/x-icon">
	<link rel="stylesheet" href="<?= ROOT ?>/assets/css/styles.css">
	<link rel="stylesheet" href="<?= ROOT ?>/assets/css/layout/browse.css">
	<link rel="stylesheet" href="<?= ROOT ?>/assets/css/pages/cart.css">
	<title>Recipe Roots - Cart</title>
</head>

<body>
	<?php include '../app/views/layout/header.php' ?>
	<?php unset( $_GET['url'] ) ?>

	<main>
		<header>
			<h1>Your Cart</h1>

			<?php if ( ! empty( $errors ) ) : ?>
				<ul class="errors">
					<?php foreach ( $errors as $error ) : ?>
						<li class="errors__message"><?= escape( $error ) ?></li>
					<?php endforeach ?>
				</ul>
			<?php endif ?>
		</header>

		<?php if ( count( $cart ) === 0 ) : ?>
			<section class="empty">
				<h2>You have not added anything to your cart yet.</h2>
				<a href="<?= ROOT ?>/ingredients" class="btn btn--next">Start browsing ingredients</a>
			</section>
		<?php else : ?>
			<article class="cart">
				<section class="grid">
					<input type="hidden" name="from" value="ingredients/cart">

					<?php foreach ( $cart as $ingredient ) : ?>
						<article class="card">
							<object class="card__thumbnail" role="img" aria-label="thumbnail"
								data="<?= escape( $ingredient['thumbnail'] ?? '' ) ?>">
								<?= extractTitleLetters( escape( $ingredient['ingredient'] ) ) ?>
							</object>
							<div>
								<div class="card__head">
									<div class="card__head__title">
										<h2><?= escape( $ingredient['ingredient'] ) ?></h2>
									</div>
									<div class="card__head__info">
										<p>RM<?= escape( $ingredient['price'] ) ?>/<?= escape( $ingredient['unit'] ) ?></p>
									</div>
								</div>
								<div class="card__body">
									<div class="card__body__author">
										<p><?= escape( $ingredient['profile']['username'] ) ?></p>
									</div>

									<div class="card__body__num">
										<button class="btn btn--invert" onclick="stepDown('amount<?= $ingredient['id'] ?>')"
											title="Decrease amount" aria-label="Decrease amount">
											-
										</button>

										<input class="btn btn--invert" type="number" min="0" max="99"
											value="<?= isset( $_COOKIE['cart'] ) ? json_decode( $_COOKIE['cart'], true )[ $ingredient['id'] ] ?? '0' : '0' ?>"
											name="amount" id="amount<?= $ingredient['id'] ?>" disabled>

										<button class="btn btn--invert" onclick="stepUp('amount<?= $ingredient['id'] ?>')"
											title="Increase amount" aria-label="Increase amount">
											+
										</button>

										<!-- If JavaScript is not loaded, then the amount needs to be submitted to the server directly to add to cart -->
										<!-- This will cause the page to refresh but this is unfortunately how it is without JavaScript -->
										<noscript>
											<button popovertarget="confirm-cart-<?= escape( $ingredient['id'] ) ?>" type="button"
												class="btn btn--invert">
												<?= isset( $_COOKIE['cart'] ) ? ( json_decode( $_COOKIE['cart'], true )[ $ingredient['id'] ] ?? 0 > 0 ? 'Update' : 'Add to' ) : 'Add to' ?>
												Cart
												<?= isset( $_COOKIE['cart'] ) ? ( json_decode( $_COOKIE['cart'] ?? '', true )[ $ingredient['id'] ] ?? 0 ? ' (' . json_decode( $_COOKIE['cart'], true )[ $ingredient['id'] ] . ') ' : '' ) : '' ?>
											</button>

											<!-- Pop up for amount input -->
											<form popover role="dialog" id="confirm-cart-<?= escape( $ingredient['id'] ) ?>" class="modal"
												method="post" action="<?= ROOT ?>/ingredients/cart?<?= http_build_query( $_GET ) ?>">
												<?php injectCsrfToken() ?>
												<input type="hidden" name="ingredientId" value="<?= escape( $ingredient['id'] ) ?>">
												<input type="hidden" name="from" value="ingredients/cart">

												<div>
													<h3>Confirm Amount</h3>
													<p>
														Price: RM<?= escape( $ingredient['price'] ) ?>/<?= escape( $ingredient['unit'] ) ?>
														<br>
														Enter the <strong>amount</strong> of <strong><?= escape( $ingredient['unit'] ) ?></strong> you
														want
														to purchase:
													</p>

													<div class="editor__input">
														<input type="number" name="amount" id="amount" min="0" max="99"
															value="<?= isset( $_COOKIE['cart'] ) ? json_decode( $_COOKIE['cart'], true )[ $ingredient['id'] ] ?? '0' : '0' ?>"
															inputmode="numeric" required>
													</div>
												</div>

												<div>
													<button type="button" class="btn btn--invert"
														popovertarget="confirm-cart-<?= escape( $ingredient['id'] ) ?>">
														Cancel
													</button>
													<button class="btn">
														<?= isset( $_COOKIE['cart'] ) ? ( json_decode( $_COOKIE['cart'], true )[ $ingredient['id'] ] ?? 0 > 0 ? 'Update' : 'Add' ) : 'Add' ?>
													</button>
												</div>
											</form>
										</noscript>
									</div>
								</div>
							</div>
						</article>
					<?php endforeach; ?>
				</section>

				<aside id="review">
					<h2>Review Order</h2>
					<dl>
						<dt>Your items</dt>
						<dd>RM<?= $pricing['subtotal'] ?></dd>

						<dt>Shipping</dt>
						<dd>Free</dd>

						<dt>Tax (6%)</dt>
						<dd>RM<?= $pricing['tax'] ?></dd>
					</dl>

					<hr>

					<dl>
						<dt>Estimated total</dt>
						<dd>RM<?= $pricing['total'] ?></dd>
					</dl>

					<form action="<?= ROOT ?>/ingredients/checkout" method="post">
						<?= injectCsrfToken() ?>
						<button class="btn">Checkout</button>
					</form>
				</aside>
			</article>
		<?php endif; ?>
	</main>

	<?php include '../app/views/layout/footer.php' ?>
	<script>
		const root = "<?= ROOT ?>";
		const csrfToken = "<?= escape( $_SESSION['csrfToken'] ) ?>";
	</script>
	<script src="<?= ROOT ?>/assets/js/cart.js"></script>
</body>

</html>