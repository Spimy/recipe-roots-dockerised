<!DOCTYPE html>
<html lang="en">

<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<link rel="shortcut icon" href="<?= ROOT ?>/favicon.ico" type="image/x-icon">
	<link rel="stylesheet" href="<?= ROOT ?>/assets/css/styles.css">
	<link rel="stylesheet" href="<?= ROOT ?>/assets/css/layout/browse.css">
	<title>Recipe Roots - Recipes</title>
</head>

<body>
	<?php include '../app/views/layout/header.php' ?>

	<main>
		<header>
			<div class="heading">
				<h1><?= $browse ? 'Browse' : 'Your' ?> Recipes</h1>
				<a href="<?= ROOT ?>/recipes/create" class="btn btn--add">Create</a>
			</div>

			<form class="filter" method="GET">
				<div class="filter__input">
					<input type="text" name="filter" id="filter" value="<?= escape( $_GET['filter'] ?? '' ) ?>">
					<button class="filter__input__submit" type="submit">
						<img src="<?= ROOT ?>/assets/icons/search.svg" alt="search icon">
					</button>
				</div>

				<div class="filter__input">
					<select class="btn btn--invert" name="dietary" id="dietary" onchange="this.form.submit()">
						<option value="none" selected disabled>Dietary Type</option>
						<option value="none" <?= ( ! $dietaryType || $dietaryType === 'none' ) ? 'selected' : '' ?>>None</option>
						<option value="vegetarian" <?= $dietaryType === 'vegetarian' ? 'selected' : '' ?>>Vegetarian</option>
						<option value="vegan" <?= $dietaryType === 'vegan' ? 'selected' : '' ?>>Vegan</option>
						<option value="halal" <?= $dietaryType === 'halal' ? 'selected' : '' ?>>Halal</option>
					</select>

					<a class="btn btn--invert btn--error" href="?page=1">Reset</a>
				</div>
			</form>
		</header>

		<section class="grid">
			<?php foreach ( $recipes as $recipe ) : ?>
				<article class="card">
					<object class="card__thumbnail" role="img" aria-label="thumbnail"
						data="<?= escape( $recipe['thumbnail'] ?? '' ) ?>">
						<?= extractTitleLetters( escape( $recipe['title'] ) ) ?>
					</object>
					<div>
						<div class="card__head">
							<div class="card__head__title">
								<h2><?= escape( $recipe['title'] ) ?></h2>
							</div>
							<div class="card__head__info">
								<?php for ( $i = 0; $i < min( escape( $recipe['rating'] ), 5 ); $i++ ) : ?>
									<img src="<?= ROOT ?>/assets/icons/star-yellow.svg" alt="yellow star">
								<?php endfor ?>
								<?php for ( $i = min( escape( $recipe['rating'] ), 5 ); $i < 5; $i++ ) : ?>
									<img src="<?= ROOT ?>/assets/icons/star-grey.svg" alt="grey star">
								<?php endfor ?>
							</div>
						</div>
						<div class="card__body">
							<div class="card__body__author">
								<p><?= escape( $recipe['profile']['username'] ) ?></p>
							</div>
							<a href="<?= ROOT ?>/recipes/<?= escape( $recipe['id'] ) ?>" class="btn btn--invert btn--next">View</a>
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