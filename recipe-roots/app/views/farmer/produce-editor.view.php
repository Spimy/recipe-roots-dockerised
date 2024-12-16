<!DOCTYPE html>
<html lang="en">

<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<link rel="shortcut icon" href="<?= ROOT ?>/favicon.ico" type="image/x-icon">
	<link rel="stylesheet" href="<?= ROOT ?>/assets/css/styles.css">
	<link rel="stylesheet" href="<?= ROOT ?>/assets/css/layout/produce-editor.css">
	<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/easymde/dist/easymde.min.css">
	<title>Recipe Roots - <?= $action ?> Produce</title>
</head>

<body>
	<?php include '../app/views/layout/header.php' ?>

	<main>
		<h1><?= $action ?> Produce</h1>

		<?php if ( isset( $message ) ) : ?>
			<p class="success"><?= escape( $message ) ?></p>
		<?php endif; ?>

		<?php if ( ! empty( $errors ) ) : ?>
			<ul class="errors">
				<?php foreach ( $errors as $error ) : ?>
					<li class="errors__message"><?= escape( $error ) ?></li>
				<?php endforeach ?>
			</ul>
		<?php endif ?>

		<form class="editor" method="post" enctype="multipart/form-data">
			<?php injectCsrfToken(); ?>

			<div class="grid">
				<div class="editor__input">
					<label for="thumbnail">Thumbnail</label>
					<div class="input__file">
						<label for="thumbnail">
							<img src="<?= ROOT ?>/assets/icons/image-picker.svg" alt="image picker">

							<?php if ( isset( $data['thumbnail'] ) ) : ?>
								<img src="<?= escape( $data['thumbnail'] ?? '' ) ?>" alt="" class="input__file--preview">
								<input type="text" name="thumbnail" value="<?= escape( $data['thumbnail'] ?? '' ) ?>" hidden>
							<?php endif; ?>

							<noscript>
								<p>Image preview does not work without JavaScript.</p>
								<p>Refer to file name below instead.</p>
							</noscript>
						</label>
						<input type="file" name="thumbnail" id="thumbnail" accept=".png, .gif, .jpeg, .jpg">
					</div>
				</div>

				<div class="editor--metadata">
					<div class="editor__input">
						<label for="ingredient">Ingredient</label>
						<input type="text" name="ingredient" id="ingredient"
							value="<?= escape( $data['ingredient'] ?? '' ) ?? null ?>" required>
					</div>

					<div class="editor__column">
						<div class="editor__input">
							<label for="price">Price (RM)</label>
							<input type="text" inputmode="numeric" name="price" id="price"
								value="<?= escape( $data['price'] ?? '' ) ?? null ?>" required>
						</div>

						<span>/</span>

						<div class="editor__input">
							<label for="unit">Unit</label>
							<div class="filter__input">
								<select class="btn btn--invert" name="unit" id="unit" required>
									<?php foreach ( INGREDIENT_UNITS as $unit ) : ?>
										<option value="<?= $unit ?>"><?= $unit ?></option>
									<?php endforeach; ?>
								</select>
							</div>
						</div>
					</div>

					<button type="submit" class="btn">
						<img src="<?= ROOT ?>/assets/icons/save.svg" alt="save icon">
						Save
					</button>

					<?php if ( $action === 'Edit' ) : ?>
						<button popovertarget="delete-confirm" type="button" class="btn btn--error">
							<img src="<?= ROOT ?>/assets/icons/trash.svg" alt="delete icon">
							Delete
						</button>
					<?php endif; ?>

					<?php if ( isset( $_GET['from'] ) ) : ?>
						<a class="btn btn--error" href="<?= ROOT ?>/<?= escape( $_GET['from'] ) ?>">
							Cancel
						</a>
					<?php else : ?>
						<a class="btn btn--error" href="<?= ROOT ?>/dashboard">
							Cancel
						</a>
					<?php endif ?>
				</div>
			</div>
		</form>

		<!-- Pop up for confirm delete -->
		<?php if ( $action === 'Edit' ) : ?>
			<form popover role="dialog" id="delete-confirm" class="modal" method="post"
				action="<?= ROOT ?>/dashboard/produce/delete<?= isset( $_GET['from'] ) ? '?from=' . $_GET['from'] : '' ?>">
				<?php injectCsrfToken() ?>
				<input type="hidden" name="ingredientId" value="<?= escape( $data['id'] ) ?>">

				<div>
					<h3>Confirm Delete</h3>
					<p>Are you sure you want to delete <strong><?= escape( $data['ingredient'] ) ?></strong>?</p>
				</div>

				<div>
					<button type="button" class="btn btn--invert" popovertarget="delete-confirm">Cancel</button>
					<button class="btn btn--error">Delete</button>
				</div>
			</form>
		<?php endif; ?>
	</main>

	<?php include '../app/views/layout/footer.php' ?>
	<script>
		const thumbnailInput = document.getElementById('thumbnail');

		thumbnailInput.addEventListener('change', (event) => {
			const thumbnail = event.target.files[0];
			if (!thumbnail) return;

			const label = thumbnailInput.parentElement.getElementsByTagName('label')[0];
			if (!label) return;

			let thumbnailPreview = label.getElementsByClassName(
				'input__file--preview'
			)[0];
			if (thumbnailPreview) {
				thumbnailPreview.src = URL.createObjectURL(thumbnail);
			} else {
				thumbnailPreview = document.createElement('img');
				thumbnailPreview.classList.add('input__file--preview');
				thumbnailPreview.src = URL.createObjectURL(thumbnail);
				label.append(thumbnailPreview);
			}
		});
	</script>
</body>