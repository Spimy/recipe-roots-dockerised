<!DOCTYPE html>
<html lang="en">

<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<link rel="shortcut icon" href="<?= ROOT ?>/favicon.ico" type="image/x-icon">
	<link rel="stylesheet" href="<?= ROOT ?>/assets/css/styles.css">
	<link rel="stylesheet" href="<?= ROOT ?>/assets/css/layout/admin.css">
	<title>Recipe Roots - Admin</title>
</head>

<body>
	<?php include '../app/views/layout/header.php' ?>

	<main>
		<h1>Admin</h1>

		<?php if ( ! empty( $message ) ) : ?>
			<p class="success"><?= escape( $message ) ?></p>
		<?php endif; ?>

		<article class="admin">
			<aside class="admin__nav">
				<ul role="list" class="admin__nav__links">
					<li>
						<a class="admin__nav__links__link admin__nav__links__link--active" href="<?= ROOT ?>/admin">
							Users
						</a>
					</li>
					<li><a class="admin__nav__links__link" href="<?= ROOT ?>/admin/ingredients">Ingredients</a></li>
					<li><a class="admin__nav__links__link" href="<?= ROOT ?>/recipes/browse">Recipes</a></li>
					<li><a class="admin__nav__links__link" href="<?= ROOT ?>/cookbooks/browse">Cookbooks</a></li>
				</ul>

				<div class="admin__nav__btns">
					<a class="btn btn--error" href="<?= ROOT ?>/signout">Sign Out</a>
				</div>
			</aside>

			<div class="admin__users">
				<?php foreach ( $users as $user ) : ?>
					<div class="admin__users__container">
						<p>
							<strong><?= $user['email'] ?></strong>
							<br>
							Created: <?= escape( date( 'd M Y - H:i:s', strtotime( $user['createdAt'] ) ) ) ?>
						</p>
						<a href="<?= ROOT ?>/admin/edit/account/<?= escape( $user['id'] ) ?>" class="btn btn--invert">Edit</a>
					</div>
				<?php endforeach; ?>

				<?php if ( $totalPages > 1 ) : ?>
					<div class="paginator">
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
					</div>
				</div>
			<?php endif ?>
		</article>
	</main>

	<?php include '../app/views/layout/footer.php' ?>
</body>

</html>