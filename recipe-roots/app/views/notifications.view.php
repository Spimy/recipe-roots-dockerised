<!DOCTYPE html>
<html lang="en">

<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<link rel="shortcut icon" href="<?= ROOT ?>/favicon.ico" type="image/x-icon">
	<link rel="stylesheet" href="<?= ROOT ?>/assets/css/styles.css">
	<link rel="stylesheet" href="<?= ROOT ?>/assets/css/pages/notifications.css">
	<title>Recipe Roots - Notifications</title>
</head>

<body>
	<?php include '../app/views/layout/header.php' ?>

	<main>
		<header class="heading">
			<h1>Notifications</h1>
			<form method="post">
				<?php injectCsrfToken() ?>
				<button type="submit" class="btn">Mark All Read</button>
			</form>
		</header>

		<section class="notifications">
			<?php foreach ( $notifications as $notification ) : ?>
				<div class="notifications__notification">
					<div>
						<?php if ( $notification['isRead'] ) : ?><s><?php endif ?>
							<p>
								<?php
								$final = str_replace(
									'{link}',
									'<a href="' . $notification['link'] . '">',
									escape( $notification['message'] )
								);
								$final = str_replace(
									'{/link}',
									'</a>',
									$final
								);
								$final = str_replace(
									'{bold}',
									'<strong>',
									$final
								);
								$final = str_replace(
									'{/bold}',
									'</strong>',
									$final
								);

								echo $final;
								?>
							</p>
						<?php if ( $notification['isRead'] ) : ?></s><?php endif ?>
						<p><?= escape( date( 'd M Y - H:i:s', strtotime( $notification['createdAt'] ) ) ) ?></p>
					</div>
					<form method="post">
						<?php injectCsrfToken() ?>
						<input type="hidden" name="notificationId" value="<?= $notification['id'] ?>">

						<button type="submit" class="btn btn--invert">
							Mark <?= $notification['isRead'] ? 'Unread' : 'Read' ?>
						</button>
					</form>
				</div>
			<?php endforeach ?>
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
	</main>

	<?php include '../app/views/layout/footer.php' ?>
</body>

</html>