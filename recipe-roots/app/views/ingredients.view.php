<!DOCTYPE html>
<html lang="en">

<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<link rel="shortcut icon" href="<?= ROOT ?>/favicon.ico" type="image/x-icon">
	<link rel="stylesheet" href="<?= ROOT ?>/assets/css/styles.css">
	<link rel="stylesheet" href="<?= ROOT ?>/assets/css/layout/browse.css">
	<title>Recipe Roots - Ingredients</title>
</head>

<body>
	<?php include '../app/views/layout/header.php' ?>
	<?php unset( $_GET['url'] ) ?>

	<main>
		<header>
			<h1>Buy Ingredients</h1>

			<?php if ( ! empty( $errors ) ) : ?>
				<ul class="errors">
					<?php foreach ( $errors as $error ) : ?>
						<li class="errors__message"><?= escape( $error ) ?></li>
					<?php endforeach ?>
				</ul>
			<?php endif ?>

			<form class="filter" method="GET">
				<div class="filter__input">
					<input type="text" name="filter" id="filter" value="<?= escape( $_GET['filter'] ?? '' ) ?>">
					<button class="filter__input__submit" type="submit">
						<img src="<?= ROOT ?>/assets/icons/search.svg" alt="search icon">
					</button>
				</div>

				<div class="filter__input">
					<div></div>
					<a class="btn btn--invert btn--error" href="?page=1">Reset</a>
				</div>
			</form>
		</header>

		<section class="grid">
			<input type="hidden" name="from" value="ingredients">
			<?php foreach ( $ingredients as $ingredient ) : ?>
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

								<input class="btn btn--invert" type="number" inputmode="numeric" min="0" max="99"
									value="<?= isset( $_COOKIE['cart'] ) ? json_decode( $_COOKIE['cart'], true )[ $ingredient['id'] ] ?? '0' : '0' ?>"
									name="amount" id="amount<?= $ingredient['id'] ?>" disabled>

								<button class="btn btn--invert" onclick="stepUp('amount<?= $ingredient['id'] ?>')" title="Increase amount"
									aria-label="Increase amount">
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

		<?php if ( $totalPages > 1 ) : ?>
			<section class="paginator">
				<div>
					<a class="btn" href="
						?
						<?= ! empty( $_GET['filter'] ) ? 'filter=' . escape( $_GET['filter'] ) . '&' : '' ?>
						page=1">
						«
					</a>
					<a class="btn" href="
						?
						<?= ! empty( $_GET['filter'] ) ? 'filter=' . escape( $_GET['filter'] ) . '&' : '' ?>
						page=<?= $currentPage == 1 ? $currentPage : $currentPage - 1 ?>">
						←
					</a>
				</div>
				<div>
					<?php foreach ( getPaginatorPages( $currentPage, $totalPages ) as $page ) : ?>
						<a href="
							?
							<?= ! empty( $_GET['filter'] ) ? 'filter=' . escape( $_GET['filter'] ) . '&' : '' ?>
							page=<?= escape( $page ) ?>" class="btn <?= $page == $currentPage ? 'selected' : '' ?>">
							<?= escape( $page ) ?>
						</a>
					<?php endforeach; ?>
				</div>
				<div>
					<a class="btn" href="
						?
						<?= ! empty( $_GET['filter'] ) ? 'filter=' . escape( $_GET['filter'] ) . '&' : '' ?>
						page=<?= $currentPage == $totalPages ? $totalPages : $currentPage + 1 ?>">
						→
					</a>
					<a class="btn" href="
						?
						<?= ! empty( $_GET['filter'] ) ? 'filter=' . escape( $_GET['filter'] ) . '&' : '' ?>
						page=<?= $totalPages ?>">
						»
					</a>
				</div>
			</section>
		<?php endif ?>
	</main>

	<?php include '../app/views/layout/footer.php' ?>
	<script>
		const root = "<?= ROOT ?>";
		const csrfToken = "<?= escape( $_SESSION['csrfToken'] ) ?>";
	</script>
	<script src="<?= ROOT ?>/assets/js/cart.js"></script>
</body>

</html>