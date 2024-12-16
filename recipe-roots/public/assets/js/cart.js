const from = document.querySelector('input[name="from"]');

function stepDown(inputId) {
	const input = document.getElementById(inputId);
	input.stepDown();
	debounceTriggerChange(input);
}

function stepUp(inputId) {
	const input = document.getElementById(inputId);
	input.stepUp();
	debounceTriggerChange(input);
}

let debounceTimer;
function debounceTriggerChange(input, delay = 300) {
	clearTimeout(debounceTimer);
	debounceTimer = setTimeout(() => {
		triggerChange(input);
	}, delay);
}

function triggerChange(input) {
	const event = new Event('change', { bubbles: true });
	input.dispatchEvent(event);
}

function registerInputs() {
	const ingredientAmountInputs = document.querySelectorAll('input[id^=amount]');

	ingredientAmountInputs.forEach(async (amountInput) => {
		amountInput.addEventListener('change', async () => {
			const formData = new FormData();
			formData.append('ingredientId', amountInput.id.split('amount').pop());
			formData.append('amount', amountInput.value);
			formData.append('csrfToken', csrfToken);
			formData.append('from', from.value);

			await fetch(`${root}/ingredients/cart`, {
				method: 'POST',
				credentials: 'include',
				body: formData,
			})
				.then(async (response) => {
					console.log(
						response.statusText === 'OK'
							? 'Updated Cart'
							: 'Could not update cart'
					);
					return await response.text();
				})
				.then(async (html) => {
					const parser = new DOMParser();
					const doc = parser.parseFromString(html, 'text/html');

					const section = document.querySelector('section.grid');
					const newGrid = doc.querySelector('section.grid');
					const review = document.getElementById('review');
					const newReview = doc.getElementById('review');

					if (!newGrid) {
						section.classList.add('empty');
						section.classList.remove('grid');
						section.innerHTML = doc.querySelector('section.empty').innerHTML;
					} else {
						section.innerHTML = doc.querySelector('section.grid').innerHTML;
					}

					if (review && newReview) {
						review.innerHTML = newReview.innerHTML;
					}

					if (review && !newReview) {
						review.remove();
					}

					registerInputs();
				});
		});
	});
}

registerInputs();
