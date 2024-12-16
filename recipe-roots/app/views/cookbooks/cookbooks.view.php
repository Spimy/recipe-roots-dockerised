<!DOCTYPE html>
<html lang="en">

<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<link rel="shortcut icon" href="<?= ROOT ?>/favicon.ico" type="image/x-icon">
	<link rel="stylesheet" href="<?= ROOT ?>/assets/css/styles.css">
	<link rel="stylesheet" href="<?= ROOT ?>/assets/css/layout/browse.css">
	<link rel="stylesheet" href="<?= ROOT ?>/assets/css/layout/cookbooks.css">
	<title>Recipe Roots - Cookbooks</title>
</head>

<body>
	<?php include '../app/views/layout/header.php' ?>

	<main>
		<header>
			<div class="heading">
				<h1><?= $browse ? 'Browse' : 'Your' ?> Cookbooks</h1>
				<a href="<?= ROOT ?>/cookbooks/create" class="btn btn--add">Create</a>
			</div>

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

		<?php if ( ! empty( $message ) ) : ?>
			<p class="success"><?= escape( $message ) ?></p>
		<?php endif; ?>

		<section class="grid">
			<?php foreach ( $cookbooks as $cookbook ) : ?>
				<article class="cookbook">
					<object class="cookbook__thumbnail" role="img" aria-label="thumbnail"
						data="<?= escape( $cookbook['thumbnail'] ?? '' ) ?>">
						<?= extractTitleLetters( escape( $cookbook['title'] ) ) ?>
					</object>
					<div class="cookbook__details">
						<h2><?= escape( $cookbook['title'] ) ?></h2>
						<p><?= escape( $cookbook['description'] ) ?></p>
						<footer class="cookbook__details__footer">
							<div class="cookbook__details__footer__metadata">
								<p><?= escape( $cookbook['profile']['username'] ) ?></p>
								<div class="cookbook__details__footer__metadata__rating">
									<?php for ( $i = 0; $i < min( escape( $cookbook['rating'] ), 5 ); $i++ ) : ?>
										<img src="<?= ROOT ?>/assets/icons/star-yellow.svg" alt="yellow star">
									<?php endfor ?>
									<?php for ( $i = min( escape( $cookbook['rating'] ), 5 ); $i < 5; $i++ ) : ?>
										<img src="<?= ROOT ?>/assets/icons/star-grey.svg" alt="grey star">
									<?php endfor ?>
								</div>
							</div>
							<a href="<?= ROOT ?>/cookbooks/<?= escape( $cookbook['id'] ) ?>" class="btn btn--invert">Read</a>
						</footer>
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
						<?= ! empty( $_GET['dietary'] ) ? 'dietary=' . escape( $_GET['dietary'] ) . '&' : '' ?>
						page=1">
						«
					</a>
					<a class="btn" href="
						?
						<?= ! empty( $_GET['filter'] ) ? 'filter=' . escape( $_GET['filter'] ) . '&' : '' ?>
						<?= ! empty( $_GET['dietary'] ) ? 'dietary=' . escape( $_GET['dietary'] ) . '&' : '' ?>
						page=<?= $currentPage == 1 ? $currentPage : $currentPage - 1 ?>">
						←
					</a>
				</div>
				<div>
					<?php foreach ( getPaginatorPages( $currentPage, $totalPages ) as $page ) : ?>
						<a href="
							?
							<?= ! empty( $_GET['filter'] ) ? 'filter=' . escape( $_GET['filter'] ) . '&' : '' ?>
							<?= ! empty( $_GET['dietary'] ) ? 'dietary=' . escape( $_GET['dietary'] ) . '&' : '' ?>
							page=<?= escape( $page ) ?>" class="btn <?= $page == $currentPage ? 'selected' : '' ?>">
							<?= escape( $page ) ?>
						</a>
					<?php endforeach; ?>
				</div>
				<div>
					<a class="btn" href="
						?
						<?= ! empty( $_GET['filter'] ) ? 'filter=' . escape( $_GET['filter'] ) . '&' : '' ?>
						<?= ! empty( $_GET['dietary'] ) ? 'dietary=' . escape( $_GET['dietary'] ) . '&' : '' ?>
						page=<?= $currentPage == $totalPages ? $totalPages : $currentPage + 1 ?>">
						→
					</a>
					<a class="btn" href="
						?
						<?= ! empty( $_GET['filter'] ) ? 'filter=' . escape( $_GET['filter'] ) . '&' : '' ?>
						<?= ! empty( $_GET['dietary'] ) ? 'dietary=' . escape( $_GET['dietary'] ) . '&' : '' ?>
						page=<?= $totalPages ?>">
						»
					</a>
				</div>
			</section>
		<?php endif ?>
	</main>

	<?php include '../app/views/layout/footer.php' ?>
</body>

</html>