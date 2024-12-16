<!DOCTYPE html>
<html lang="en">

<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<link rel="shortcut icon" href="<?= ROOT ?>/favicon.ico" type="image/x-icon">
	<link rel="stylesheet" href="<?= ROOT ?>/assets/css/styles.css">
	<link rel="stylesheet" href="<?= ROOT ?>/assets/css/pages/invoices.css">
	<title>Recipe Roots - Invoice</title>
</head>

<body>
	<?php include '../app/views/layout/header.php' ?>

	<main>
		<h1>Invoices</h1>

		<section class="invoices">
			<?php foreach ( $invoices as $invoice ) : ?>
				<div class="invoices__invoice">
					<div>
						<p><strong><?= escape( $invoice['invoiceId'] ) ?></strong></p>
						<p><?= escape( date( 'd M Y - H:i:s', strtotime( $invoice['createdAt'] ) ) ) ?></p>
					</div>
					<a href="<?= ROOT ?>/ingredients/invoices/<?= escape( $invoice['invoiceId'] ) ?>"
						class="btn btn--invert btn--next">View</a>
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