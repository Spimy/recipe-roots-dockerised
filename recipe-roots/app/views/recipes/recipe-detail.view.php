<!DOCTYPE html>
<html lang="en">

<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<link rel="shortcut icon" href="<?= ROOT ?>/favicon.ico" type="image/x-icon">
	<link rel="stylesheet" href="<?= ROOT ?>/assets/css/styles.css">
	<link rel="stylesheet" href="<?= ROOT ?>/assets/css/pages/recipe-detail.css">
	<title>Recipe Roots - <?= escape( $recipe['title'] ) ?></title>
</head>

<body>
	<?php include '../app/views/layout/header.php' ?>

	<main>
		<header>
			<div class="heading">
				<h1><?= escape( $recipe['title'] ) ?></h1>

				<div class="heading__dropdown">
					<label for="more"><img src="<?= ROOT ?>/assets/icons/more.svg" alt="more"></label>
					<input type="checkbox" name="more" id="more">

					<menu class="heading__dropdown__menu">
						<?php if ( $recipe['profileId'] === $profile['id'] || $profile['user']['isAdmin'] ) : ?>
							<a href="<?= ROOT ?>/recipes/edit/<?= escape( $recipe['id'] ) ?>">Edit</a>
							<button popovertarget="delete-confirm">Delete</button>
						<?php endif ?>
						<button popovertarget="select-cookbook">Save to Cookbook</button>
					</menu>

					<?php if ( $recipe['profileId'] === $profile['id'] || $profile['user']['isAdmin'] ) : ?>
						<!-- Pop up for confirm delete -->
						<form popover role="dialog" id="delete-confirm" class="modal" method="post"
							action="<?= ROOT ?>/recipes/delete">
							<?php injectCsrfToken() ?>
							<input type="hidden" name="recipeId" value="<?= escape( $recipe['id'] ) ?>">

							<div>
								<h3>Confirm Delete</h3>
								<p>Are you sure you want to delete <strong><?= escape( $recipe['title'] ) ?></strong>?</p>
							</div>

							<div>
								<button type="button" class="btn btn--invert" popovertarget="delete-confirm">Cancel</button>
								<button class="btn btn--error">Delete</button>
							</div>
						</form>
					<?php endif ?>

					<form popover role="dialog" id="select-cookbook" class="modal" method="post"
						action="<?= ROOT ?>/recipes/updateCookbooks">
						<?php injectCsrfToken() ?>
						<input type="hidden" name="recipeId" value="<?= escape( $recipe['id'] ) ?>">

						<div>
							<h3>Save Recipe to Cookbook</h3>
							<ul role="list">
								<?php foreach ( $cookbooks as $cookbook ) : ?>
									<li>
										<label for="cookbook-<?= escape( $cookbook['id'] ) ?>">
											<input type="checkbox" name="cookbooks[]" id="cookbook-<?= escape( $cookbook['id'] ) ?>"
												value="<?= escape( $cookbook['id'] ) ?>" <?= in_array( $cookbook['id'], $savedCookbooks ) ? 'checked' : '' ?>>
											<?= escape( $cookbook['title'] ) ?>
										</label>
									</li>
								<?php endforeach ?>
								<li>
									<a href="<?= ROOT ?>/cookbooks/create?from=<?= escape( $_GET['url'] ) ?>" class="btn btn--add">
										Create Cookbook
									</a>
								</li>
							</ul>
						</div>

						<div>
							<button type="button" class="btn btn--invert" popovertarget="select-cookbook">Cancel</button>
							<button class="btn">Save</button>
						</div>
					</form>
				</div>
			</div>

			<?php if ( ! empty( $recipeErrors ) ) : ?>
				<ul class="errors">
					<?php foreach ( $recipeErrors as $error ) : ?>
						<li class="errors__message"><?= escape( $error ) ?></li>
					<?php endforeach ?>
				</ul>
			<?php endif ?>

			<?php if ( ! empty( $saveToCookbook ) ) : ?>
				<p class="success"><?= escape( $saveToCookbook ) ?></p>
			<?php endif ?>

			<div class="metadata">
				<div class="metadata__info">
					<?php if ( ! $recipe['dietaryType'] ) : ?>
						<img src="<?= ROOT ?>/assets/icons/dietary/none.svg" alt="diet">
					<?php elseif ( $recipe['dietaryType'] === 'vegetarian' ) : ?>
						<img src="<?= ROOT ?>/assets/icons/dietary/vegetarian.svg" alt="diet">
					<?php elseif ( $recipe['dietaryType'] === 'vegan' ) : ?>
						<img src="<?= ROOT ?>/assets/icons/dietary/vegan.svg" alt="diet">
					<?php elseif ( $recipe['dietaryType'] === 'halal' ) : ?>
						<img src="<?= ROOT ?>/assets/icons/dietary/halal.svg" alt="diet">
					<?php endif; ?>
					<div>
						<p>Dietary Type</p>
						<p><?= ucfirst( escape( $recipe['dietaryType'] ?? 'None' ) ) ?></p>
					</div>
				</div>

				<?php if ( $recipe['prepTime'] > 0 ) : ?>
					<div class="metadata__info">
						<img src="<?= ROOT ?>/assets/icons/clock.svg" alt="clock">
						<div>
							<p>Preparation</p>
							<p><?= escape( convertToHoursMins( $recipe['prepTime'] ) ) ?></p>
						</div>
					</div>
				<?php endif; ?>

				<?php if ( $recipe['waitingTime'] > 0 ) : ?>
					<div class="metadata__info">
						<img class="watch" src="<?= ROOT ?>/assets/icons/watch.svg" alt="watch">
						<div>
							<p>Waiting</p>
							<p><?= escape( convertToHoursMins( $recipe['waitingTime'] ) ) ?></p>
						</div>
					</div>
				<?php endif; ?>

				<?php if ( $recipe['servings'] > 0 ) : ?>
					<div class="metadata__info">
						<img src="<?= ROOT ?>/assets/icons/serving.svg" alt="serving">
						<div>
							<p>Servings</p>
							<p><?= escape( $recipe['servings'] ) ?></p>
						</div>
					</div>
				<?php endif; ?>

				<div class="metadata__info">
					<img src="<?= ROOT ?>/assets/icons/globe.svg" alt="globe">
					<div>
						<p>Public</p>
						<p><?= $recipe['public'] ? 'Yes' : 'No' ?></p>
					</div>
				</div>
			</div>
		</header>

		<article class="details">
			<section class="details__thumbnail">
				<object role="img" aria-label="thumbnail" data="<?= escape( $recipe['thumbnail'] ?? '' ) ?>">
					<?= extractTitleLetters( escape( $recipe['title'] ) ) ?>
				</object>
			</section>

			<section class="details__ingredients">
				<h2>Ingredients</h2>
				<table role="table" class="editor__ingredients__list">
					<thead role="rowgroup">
						<tr role="row">
							<th role="columnheader">Amount</th>
							<th role="columnheader">Unit</th>
							<th role="columnheader">Ingredient</th>
						</tr>
					</thead>

					<tbody role="rowgroup">
						<?php foreach ( json_decode( $recipe['ingredients'], true ) as $ingredient ) : ?>
							<tr role="row">
								<td role="cell"><?= escape( $ingredient['amount'] ) ?></td>
								<td role="cell"><?= escape( $ingredient['unit'] ) ?></td>
								<td role="cell"><?= escape( $ingredient['ingredient'] ) ?></td>
							</tr>
						<?php endforeach ?>
					</tbody>
				</table>
				<ol>
				</ol>
			</section>

			<section id="markdown" class="details__instructions">
				<noscript>
					<p><?= nl2br( escape( $recipe['instructions'] ) ) ?></p>
				</noscript>
			</section>

			<section class="details__comments">
				<h2 id="comments">Comments</h2>

				<?php if ( ! empty( $commentErrors ) ) : ?>
					<ul class="errors">
						<?php foreach ( $commentErrors as $error ) : ?>
							<li class="errors__message"><?= escape( $error ) ?></li>
						<?php endforeach ?>
					</ul>
				<?php endif ?>

				<div class="details__comments__container">
					<?php foreach ( $comments as $comment ) : ?>
						<article class="details__comments__comment" id="comment-<?= escape( $comment['id'] ) ?>">
							<div class="details__comments__comment__header">
								<div>
									<object class="avatar" role="img" aria-label="avatar"
										data="<?= escape( $comment['profile']['avatar'] ?? '' ) ?>">
										<?= extractTitleLetters( escape( $comment['profile']['username'] ) ) ?>
									</object>
									<div>
										<p><?= escape( $comment['profile']['username'] ) ?></p>
										<p class="rating">
											<?php for ( $i = 0; $i < min( $comment['rating'], 5 ); $i++ ) : ?>
												<img src="<?= ROOT ?>/assets/icons/star-yellow.svg" alt="rated-star">
											<?php endfor ?>
											<?php for ( $i = min( $comment['rating'], 5 ); $i < 5; $i++ ) : ?>
												<img src="<?= ROOT ?>/assets/icons/star-grey.svg" alt="star">
											<?php endfor ?>
										</p>
									</div>
								</div>

								<?php if ( $comment['profileId'] === $profile['id'] || $profile['user']['isAdmin'] ) : ?>
									<div class="details__comments__comment__header__btns">
										<button popovertarget="edit-comment-<?= escape( $comment['id'] ) ?>">Edit</button>
										<button popovertarget="delete-comment-<?= escape( $comment['id'] ) ?>">Delete</button>
									</div>
								<?php endif; ?>
							</div>
							<p><?= escape( $comment['content'] ) ?></p>
						</article>

						<?php if ( $comment['profileId'] === $profile['id'] || $profile['user']['isAdmin'] ) : ?>
							<!-- Dialog box for editing comment (avoid need for JavaScript) -->
							<form popover role="dialog" id="edit-comment-<?= escape( $comment['id'] ) ?>" class="modal" method="post"
								action="<?= ROOT ?>/recipes/comment/edit">
								<?php injectCsrfToken() ?>
								<input type="hidden" name="recipeId" value="<?= escape( $recipe['id'] ) ?>">
								<input type="hidden" name="commentId" value="<?= escape( $comment['id'] ) ?>">

								<div>
									<h3>Edit Comment</h3>

									<div class="details__comments__editor">
										<textarea name="content" id="comment" placeholder="Write a comment..."
											required><?= escape( $comment['content'] ) ?></textarea>

									</div>
								</div>

								<div>
									<fieldset class="rating-input">
										<input type="radio" value="5" id="stars-star5-<?= escape( $comment['id'] ) ?>" name="rating" required
											<?= $comment['rating'] === 5 ? 'checked' : '' ?>>
										<label for="stars-star5-<?= escape( $comment['id'] ) ?>" title="5 Stars"></label>
										<input type="radio" value="4" id="stars-star4-<?= escape( $comment['id'] ) ?>" name="rating"
											<?= $comment['rating'] === 4 ? 'checked' : '' ?>>
										<label for="stars-star4-<?= escape( $comment['id'] ) ?>" title="4 Stars"></label>
										<input type="radio" value="3" id="stars-star3-<?= escape( $comment['id'] ) ?>" name="rating"
											<?= $comment['rating'] === 3 ? 'checked' : '' ?>>
										<label for="stars-star3-<?= escape( $comment['id'] ) ?>" title="3 Stars"></label>
										<input type="radio" value="2" id="stars-star2-<?= escape( $comment['id'] ) ?>" name="rating"
											<?= $comment['rating'] === 2 ? 'checked' : '' ?>>
										<label for="stars-star2-<?= escape( $comment['id'] ) ?>" title="2 Stars"></label>
										<input type="radio" value="1" id="stars-star1-<?= escape( $comment['id'] ) ?>" name="rating"
											<?= $comment['rating'] === 1 ? 'checked' : '' ?>>
										<label for="stars-star1-<?= escape( $comment['id'] ) ?>" title="1 Stars"></label>
									</fieldset>

									<div>
										<button type="button" class="btn btn--invert"
											popovertarget="edit-comment-<?= escape( $comment['id'] ) ?>">
											Cancel
										</button>
										<button class="btn">Edit</button>
									</div>
								</div>
							</form>

							<!-- Dialog box for confirm delete command -->
							<form popover role="dialog" id="delete-comment-<?= escape( $comment['id'] ) ?>" class="modal" method="post"
								action="<?= ROOT ?>/recipes/comment/delete">
								<?php injectCsrfToken() ?>
								<input type="hidden" name="recipeId" value="<?= escape( $recipe['id'] ) ?>">
								<input type="hidden" name="commentId" value="<?= escape( $comment['id'] ) ?>">

								<div>
									<h3>Confirm Delete</h3>
									<p>Are you sure you want to delete this comment?</p>
								</div>

								<div>
									<button type="button" class="btn btn--invert"
										popovertarget="delete-comment-<?= escape( $comment['id'] ) ?>">
										Cancel
									</button>
									<button class="btn btn--error">Delete</button>
								</div>
							</form>
						<?php endif; ?>
					<?php endforeach; ?>

					<form class="details__comments__editor" action="<?= ROOT ?>/recipes/comment/add" method="post">
						<?php injectCsrfToken() ?>

						<input type="hidden" name="recipeId" value="<?= escape( $recipe['id'] ) ?>">
						<textarea name="content" id="comment" placeholder="Write a comment..." required></textarea>

						<div class="details__comments__editor__footer">
							<fieldset class="rating-input">
								<input type="radio" value="5" id="stars-star5" name="rating" required>
								<label for="stars-star5" title="5 Stars"></label>
								<input type="radio" value="4" id="stars-star4" name="rating">
								<label for="stars-star4" title="4 Stars"></label>
								<input type="radio" value="3" id="stars-star3" name="rating">
								<label for="stars-star3" title="3 Stars"></label>
								<input type="radio" value="2" id="stars-star2" name="rating">
								<label for="stars-star2" title="2 Stars"></label>
								<input type="radio" value="1" id="stars-star1" name="rating">
								<label for="stars-star1" title="1 Stars"></label>
							</fieldset>

							<button type="submit" class="btn">Comment</button>
						</div>
					</form>
				</div>
			</section>
		</article>
	</main>

	<?php include '../app/views/layout/footer.php' ?>

	<script src="https://cdn.jsdelivr.net/npm/marked/marked.min.js"></script>
	<script>
		document.getElementById('markdown').innerHTML = marked.parse(`<?= escape( $recipe['instructions'] ) ?>`);
	</script>
</body>

</html>