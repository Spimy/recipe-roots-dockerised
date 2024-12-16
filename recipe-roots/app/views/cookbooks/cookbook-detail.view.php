<!DOCTYPE html>
<html lang="en">

<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<link rel="shortcut icon" href="<?= ROOT ?>/favicon.ico" type="image/x-icon">
	<link rel="stylesheet" href="<?= ROOT ?>/assets/css/styles.css">
	<link rel="stylesheet" href="<?= ROOT ?>/assets/css/layout/browse.css">
	<link rel="stylesheet" href="<?= ROOT ?>/assets/css/pages/cookbook-detail.css">
	<title>Recipe Roots - <?= escape( $cookbook['title'] ) ?></title>
</head>

<body>
	<?php include '../app/views/layout/header.php' ?>

	<main>
		<header>
			<div class="heading">
				<h1><?= escape( $cookbook['title'] ) ?></h1>
				<?php if ( $cookbook['profileId'] === $profile['id'] || $profile['user']['isAdmin'] ) : ?>
					<a href="<?= ROOT ?>/cookbooks/edit/<?= escape( $cookbook['id'] ) ?>" class="btn">Edit</a>
				<?php endif ?>
			</div>
		</header>

		<article>
			<aside class="cookbook">
				<object class="cookbook__thumbnail" role="img" aria-label="thumbnail"
					data="<?= escape( $cookbook['thumbnail'] ?? '' ) ?>">
					<?= extractTitleLetters( escape( $cookbook['title'] ) ) ?>
				</object>

				<div class="cookbook__info">
					<div class="cookbook__info__visibility">
						<img src="<?= ROOT ?>/assets/icons/globe.svg" alt="globe">
						<p><?= $cookbook['public'] ? 'Public' : 'Private' ?></p>
					</div>
					<p class="cookbook__info__description"><?= escape( $cookbook['description'] ) ?></p>
				</div>
			</aside>

			<div class="recipes">
				<section class="grid">
					<?php foreach ( $recipes as $recipe ) : ?>
						<div class="card">
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
						</div>
					<?php endforeach; ?>
				</section>

				<?php if ( $totalPages > 1 ) : ?>
					<section class="paginator">
						<div>
							<a class="btn" href="?page=1">«</a>
							<a class="btn" href="?page=<?= $currentPage == 1 ? $currentPage : $currentPage - 1 ?>">←</a>
						</div>
						<div>
							<?php foreach ( getPaginatorPages( $currentPage, $totalPages ) as $page ) : ?>
								<a href="?page=<?= escape( $page ) ?>" class="btn <?= $page == $currentPage ? 'selected' : '' ?>">
									<?= escape( $page ) ?>
								</a>
							<?php endforeach; ?>
						</div>
						<div>
							<a class="btn" href="?page=<?= $currentPage == $totalPages ? $totalPages : $currentPage + 1 ?>">→</a>
							<a class="btn" href="?page=<?= $totalPages ?>">»</a>
						</div>
					</section>
				<?php endif ?>
			</div>
		</article>
	</main>

	<?php include '../app/views/layout/footer.php' ?>
</body>

</html>