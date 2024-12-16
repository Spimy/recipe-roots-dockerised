<!DOCTYPE html>
<html lang="en">

<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<link rel="shortcut icon" href="<?= ROOT ?>/favicon.ico" type="image/x-icon">
	<link rel="stylesheet" href="<?= ROOT ?>/assets/css/styles.css">
	<link rel="stylesheet" href="<?= ROOT ?>/assets/css/layout/recipe-editor.css">
	<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/easymde/dist/easymde.min.css">
	<title>Recipe Roots - <?= $action ?> Recipe</title>
</head>

<body>
	<?php include '../app/views/layout/header.php' ?>

	<main>
		<h1><?= $action ?> Recipe</h1>

		<?php if ( $action == 'Create' && empty( $_GET['ingredientCount'] ) ) : ?>
			<noscript class="ingredient-count">
				<form method="get">
					<div class="editor__input">
						<label for="ingredient-count">Number of Ingredients</label>
						<input type="text" inputmode="numeric" name="ingredientCount" id="ingredient-count" required>
					</div>
					<button class="btn btn--next">Start Creating</button>
				</form>
			</noscript>
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

			<div class="editor__input">
				<label for="title">Title</label>
				<input type="text" name="title" id="title" value="<?= escape( $data['title'] ?? '' ) ?? null ?>" required>
			</div>

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
						<label for="prepTime">Preparation Time (min)</label>
						<input type="text" inputmode="numeric" name="prepTime" id="prepTime"
							value="<?= escape( $data['prepTime'] ?? '' ) ?? null ?>">
					</div>

					<div class="editor__input">
						<label for="waitingTime">Waiting Time (min)</label>
						<input type="text" inputmode="numeric" name="waitingTime" id="waitingTime"
							value="<?= escape( $data['waitingTime'] ?? '' ) ?? null ?>">
					</div>

					<div class="editor__input">
						<label for="servings">Servings</label>
						<input type="text" inputmode="numeric" name="servings" id="servings"
							value="<?= escape( $data['servings'] ?? '' ) ?? null ?>">
					</div>

					<div class="editor__column">
						<div class="editor__input">
							<label>Dietary Type</label>

							<div class="editor__input--radio">
								<label for="none">
									<input type="radio" name="dietaryType" id="none" value="none" checked required>
									None
								</label>
								<label for="vegetarian">
									<input type="radio" name="dietaryType" id="vegetarian" value="vegetarian" <?= ( $data['dietaryType'] ?? '' ) === 'vegetarian' ? 'checked' : '' ?>>
									Vegetarian
								</label>
								<label for="vegan">
									<input type="radio" name="dietaryType" id="vegan" value="vegan" <?= ( $data['dietaryType'] ?? '' ) === 'vegan' ? 'checked' : '' ?>>
									Vegan
								</label>
								<label for="halal">
									<input type="radio" name="dietaryType" id="halal" value="halal" <?= ( $data['dietaryType'] ?? '' ) === 'halal' ? 'checked' : '' ?>>
									Halal
								</label>
							</div>
						</div>

						<div class="editor__input">
							<label>Public</label>
							<div class="editor__input--radio">
								<label for="no"><input type="radio" name="public" id="no" value="no" required <?= empty( $data['public'] ) ? 'checked' : ( $data['public'] == 'no' ? 'checked' : '' ) ?>>No</label>
								<label for="yes"><input type="radio" name="public" id="yes" value="yes" <?= empty( $data['public'] ) ? '' : ( $data['public'] == 'yes' ? 'checked' : '' ) ?>>Yes</label>
							</div>
						</div>
					</div>
				</div>
			</div>

			<div class="editor__ingredients">
				<h2>Ingredients</h2>

				<noscript>
					<p class="errors" style="margin-top: 0.5rem">
						Some features do not work without JavaScript. Enable JavaScript for the best experience.
					</p>
				</noscript>

				<table role="table" class="editor__ingredients__list">
					<thead role="rowgroup">
						<tr role="row">
							<th role="columnheader">Reorder</th>
							<th role="columnheader">Amount</th>
							<th role="columnheader">Unit</th>
							<th role="columnheader">Ingredient</th>
							<th role="columnheader">Remove</th>
						</tr>
					</thead>

					<?php $units = [ "tbsp", "tsp", "oz", "fl. oz", "qt", "pt", "gal", "lb", "mL", "kg", "g", "cups", "pcs", "head", "cloves", "slices", "cans", "stalks" ] ?>
					<tbody role="rowgroup">
						<?php if ( isset( $data['ingredients'] ) ) : ?>
							<?php foreach ( $data['ingredients'] as $ingredient ) : ?>
								<tr role="row">
									<td role="cell">
										<label draggable="true" ondragend="dragEnd()" ondragover="dragOver(event)"
											ondragstart="dragStart(event)">
											<img src="<?= ROOT ?>/assets/icons/swap.svg" alt="sort">
										</label>
									</td>
									<td role="cell"><input type="text" inputmode="numeric" name="amounts[]" id="amount"
											value="<?= escape( $ingredient['amount'] ?? '' ) ?? null ?>" required></td>
									<td role="cell">
										<select name="units[]" id="unit" class="btn btn--invert" required>
											<option value="" selected disabled>Select</option>
											<?php foreach ( $units as $unit ) : ?>
												<option value="<?= $unit ?>" <?= escape( $ingredient['unit'] ?? '' ) == $unit ? 'selected' : '' ?>>
													<?= $unit ?>
												</option>
											<?php endforeach ?>
										</select>
									</td>
									<td role="cell"><input type="text" name="ingredients[]" id="ingredient"
											value="<?= escape( $ingredient['ingredient'] ?? '' ) ?? null ?>" required></td>
									<td role="cell">
										<button class="remove-ingredient" type="button">
											<img src="<?= ROOT ?>/assets/icons/close.svg" alt="remove">
										</button>
									</td>
								</tr>
							<?php endforeach; ?>
						<?php else : ?>
							<?php for ( $i = 0; $i < ( $_GET['ingredientCount'] ?? 1 ); $i++ ) : ?>
								<tr role="row">
									<td role="cell">
										<label draggable="true" ondragend="dragEnd()" ondragover="dragOver(event)"
											ondragstart="dragStart(event)">
											<img src="<?= ROOT ?>/assets/icons/swap.svg" alt="sort">
										</label>
									</td>
									<td role="cell"><input type="text" inputmode="numeric" name="amounts[]" id="amount" required></td>
									<td role="cell">
										<select name="units[]" id="unit" class="btn btn--invert" required>
											<option value="" selected disabled>Select</option>
											<?php foreach ( $units as $unit ) : ?>
												<option value="<?= $unit ?>"><?= $unit ?></option>
											<?php endforeach ?>
										</select>
									</td>
									<td role="cell"><input type="text" name="ingredients[]" id="ingredient" required></td>
									<td role="cell">
										<button class="remove-ingredient" type="button">
											<img src="<?= ROOT ?>/assets/icons/close.svg" alt="remove">
										</button>
										<phpd>
								</tr>
							<?php endfor; ?>
						<?php endif; ?>

					</tbody>
				</table>

				<button onclick="addIngredient('<?= ROOT ?>')" id="add-ingredient" type="button"
					class="btn btn--invert btn--add">
					Add
				</button>
			</div>

			<div class="editor__input">
				<label for="instructions">Instructions</label>
				<textarea name="instructions" id="instructions"
					required><?= escape( $data['instructions'] ?? '' ) ?? null ?></textarea>
			</div>

			<button type="submit" class="btn">
				<img src="<?= ROOT ?>/assets/icons/save.svg" alt="save icon">
				Save
			</button>
			<a class="btn btn--error" href="<?= ROOT ?>/recipes/<?= $action == 'Edit' ? escape( $data['id'] ?? '' ) : '' ?>">
				Cancel
			</a>
		</form>
	</main>

	<?php include '../app/views/layout/footer.php' ?>

	<script src="https://cdn.jsdelivr.net/npm/easymde/dist/easymde.min.js"></script>
	<script src="<?= ROOT ?>/assets/js/recipe-editor.js"></script>
	<script src="<?= ROOT ?>/assets/js/drag-drop-touch.js"></script>
</body>