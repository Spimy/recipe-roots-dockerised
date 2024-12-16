<!DOCTYPE html>
<html lang="en">

<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<link rel="shortcut icon" href="<?= ROOT ?>/favicon.ico" type="image/x-icon">
	<link rel="stylesheet" href="<?= ROOT ?>/assets/css/styles.css">
	<link rel="stylesheet" href="<?= ROOT ?>/assets/css/pages/dashboard.css">
	<title>Recipe Roots - Dashboard</title>
</head>

<body>
	<?php include '../app/views/layout/header.php' ?>

	<main>
		<h1>Dashboard</h1>

		<?php if ( isset( $message ) ) : ?>
			<p class="success"><?= escape( $message ) ?></p>
		<?php endif; ?>

		<div class="dashboard">
			<?php if ( count( $dataPoints ) > 0 ) : ?>
				<div id="chartContainer" class="chart"></div>

				<!-- Display a table instead of a chart if there is no JavaScript -->
				<noscript class="chart--table">
					<table role="table" class="editor__ingredients__list">
						<thead role="rowgroup">
							<tr role="row">
								<th role="columnheader">Date</th>
								<th role="columnheader">Revenue</th>
							</tr>
						</thead>

						<tbody role="rowgroup">
							<?php foreach ( $dataPoints as $dataPoint ) : ?>
								<tr role="row">
									<td role="cell"><?= escape( $dataPoint['label'] ) ?></td>
									<td role="cell"><?= escape( $dataPoint['y'] ) ?></td>
								</tr>
							<?php endforeach ?>
						</tbody>
					</table>
				</noscript>
			<?php endif ?>

			<article class="dashboard__produce">
				<header>
					<h2>Your Produce</h2>
					<a href="<?= ROOT ?>/dashboard/produce/add" class="btn btn--add">Add</a>
				</header>

				<section class="grid">
					<?php foreach ( $ingredients as $produce ) : ?>
						<article class="card">
							<object class="card__thumbnail" role="img" aria-label="thumbnail"
								data="<?= escape( $produce['thumbnail'] ?? '' ) ?>">
								<?= extractTitleLetters( escape( $produce['ingredient'] ) ) ?>
							</object>
							<div>
								<div class="card__head">
									<div class="card__head__title">
										<h2><?= escape( $produce['ingredient'] ) ?></h2>
									</div>
									<div class="card__head__info">
										<p>RM<?= escape( $produce['price'] ) ?>/<?= escape( $produce['unit'] ) ?></p>
									</div>
								</div>
								<div class="card__body">
									<div class="card__body__author">
										<p><?= escape( $produce['profile']['username'] ) ?></p>
									</div>
									<a href="<?= ROOT ?>/dashboard/produce/edit/<?= escape( $produce['id'] ) ?>"
										class="btn btn--invert btn--next">
										Edit
									</a>
								</div>
							</div>
						</article>
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
			</article>
	</main>

	<?php include '../app/views/layout/footer.php' ?>

	<?php if ( count( $dataPoints ) > 0 ) : ?>
		<script src="https://cdn.canvasjs.com/ga/canvasjs.min.js"></script>
		<script>
			window.onload = function () {
				var chart = new CanvasJS.Chart('chartContainer', {
					zoomEnabled: true,
					exportEnabled: true,
					exportFileName: `revenue_${Date.now()}`,
					rangeChanged: (event) => event.trigger === 'reset' ? changeToPanMode() : null,
					axisY: {
						title: 'Unit: RM',
						gridDashType: 'shortDash',
						gridThickness: 1,
						gridColor: '#d9d9dd',
						titleFontColor: '#253137',
						labelFontColor: '#253137'
					},
					axisX: {
						titleFontColor: '#253137',
						labelFontColor: '#253137',
						viewportMinimum: 0,
						viewportMaximum: Number(<?= count( $dataPoints ) > 12 ? 11 : count( $dataPoints ) - 1 ?>)
					},
					toolTip: {
						fontColor: '#4cae4f'
					},
					legend: {
						horizontalAlign: 'center',
						verticalAlign: 'bottom',
						fontSize: 15
					},
					data: [{
						type: 'line',
						showInLegend: true,
						legendText: 'Revenue',
						legendMarkerBorderColor: '#4cae4f',
						legendMarkerColor: '#ffffff',
						legendMarkerBorderThickness: 2,
						markerColor: '#ffffff',
						markerBorderColor: '#4cae4f',
						markerBorderThickness: 2,
						color: '#4cae4f',
						toolTipContent: '{label}: RM{y}',
						dataPoints: <?= json_encode( $dataPoints, JSON_NUMERIC_CHECK ); ?>
					}]
				});
				chart.render();
				changeToPanMode()
			}

			function changeToPanMode() {
				var parentElement = document.getElementsByClassName('canvasjs-chart-toolbar');
				var childElement = document.getElementsByTagName('button');
				if (childElement[0].getAttribute('state') === 'pan') {
					childElement[0].click();
				}
			}
		</script>
	<?php endif; ?>
</body>

</html>