<!DOCTYPE html>
<html lang="en">

<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<link rel="shortcut icon" href="<?= ROOT ?>/favicon.ico" type="image/x-icon">
	<link rel="stylesheet" href="<?= ROOT ?>/assets/css/styles.css">
	<link rel="stylesheet" href="<?= ROOT ?>/assets/css/pages/invoice-detail.css">
	<title>Recipe Roots - Invoice</title>
</head>

<body>
	<?php include '../app/views/layout/header.php' ?>

	<main>
		<h1>Invoice</h1>

		<header>
			<h2>Invoice ID: <?= escape( $invoice['invoiceId'] ) ?></h2>
			<p>Date: <?= escape( date( 'd M Y - H:i:s', strtotime( $invoice['createdAt'] ) ) ) ?></p>
		</header>

		<section class="invoice">
			<?php foreach ( $invoice['purchases'] as $purchase ) : ?>
				<div class="invoice__purchase">
					<object class="card__thumbnail" role="img" aria-label="thumbnail"
						data="<?= escape( $purchase['ingredient']['thumbnail'] ?? '' ) ?>">
						<?= extractTitleLetters( escape( $purchase['ingredient']['ingredient'] ) ) ?>
					</object>
					<div>
						<strong>
							<?= escape( $purchase['ingredient']['ingredient'] ) ?>
							&times;<?= escape( $purchase['amount'] ) ?> (<?= escape( $purchase['ingredient']['unit'] ) ?>)
						</strong>
						<br>
						RM<?= number_format( $purchase['ingredient']['price'] * $purchase['amount'], 2 ) ?> from
						<?= escape( $purchase['profile']['username'] ) ?>
					</div>
				</div>
			<?php endforeach ?>

			<hr>

			<div>
				<p><strong>Subtotal:</strong> RM<?= number_format( escape( $pricing['subtotal'] ), 2 ) ?></p>
				<p><strong>Tax (6%):</strong> RM<?= number_format( escape( $pricing['tax'] ), 2 ) ?></p>
				<p><strong>Total:</strong> RM<?= escape( $pricing['total'] ) ?></p>
			</div>

			<a href="<?= ROOT ?>/ingredients/invoices" class="btn">Back</a>
		</section>

	</main>

	<?php include '../app/views/layout/footer.php' ?>
</body>

</html>