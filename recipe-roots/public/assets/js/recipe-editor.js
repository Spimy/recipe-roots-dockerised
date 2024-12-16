// ==== Input handlers ====
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

const easyMDE = new EasyMDE();
const instructionInput = document.getElementById('instructions');
easyMDE.codemirror.on('change', () => {
	instructionInput.innerHTML = easyMDE.value();
});

// ==== Button functions ====

// Remove buttons
function handleRemove(event) {
	let row = event.target;
	while (row?.tagName.toLowerCase() != 'tr') row = row.parentElement;
	row.remove();
}

function registerRemoveButtons() {
	const removeButtons = document.getElementsByClassName('remove-ingredient');

	[...removeButtons].forEach((removeButton) => {
		removeButton.removeEventListener('click', handleRemove); // Remove to not have duplicate click and free up memory
		removeButton.addEventListener('click', handleRemove);
	});
}

registerRemoveButtons();

// Add button
const units = [
	'tbsp',
	'tsp',
	'oz',
	'fl. oz',
	'qt',
	'pt',
	'gal',
	'lb',
	'mL',
	'kg',
];

function addIngredient(root) {
	const tableBody = document.getElementsByTagName('tbody')[0];
	if (!tableBody) return;

	const newRow = document.createElement('tr');
	newRow.innerHTML = `
		<td role="cell">
			<label draggable="true" ondragend="dragEnd()" ondragover="dragOver(event)" ondragstart="dragStart(event)">
				<img src="${root}/assets/icons/swap.svg" alt="sort">
			</label>
		</td>
		<td role="cell"><input type="text" inputmode="numeric" name="amounts[]" id="amount" required></td>
		<td role="cell">
			<select name="units[]" id="unit" class="btn btn--invert" required>
				<option value="" selected disabled>Select</option>
				${units.map((unit) => `<option value="${unit}">${unit}</option>`).join('\n')}
			</select>
		</td>
		<td role="cell"><input type="text" name="ingredients[]" id="ingredient" required></td>
		<td role="cell">
			<button class="remove-ingredient" type="button">
				<img src="${root}/assets/icons/close.svg" alt="remove">
			</button>
		</td>
		`;

	tableBody.append(newRow);
	registerRemoveButtons();
}

// ==== Sorting function ====
let selected = null;

function getCorrectDrag(target) {
	return target.tagName.toLowerCase() == 'img'
		? target.parentElement.parentElement.parentElement
		: target.parentElement.parentElement;
}

function dragOver(event) {
	if (isBefore(selected, getCorrectDrag(event.target))) {
		getCorrectDrag(event.target).parentNode.insertBefore(
			selected,
			getCorrectDrag(event.target)
		);
	} else {
		getCorrectDrag(event.target).parentNode.insertBefore(
			selected,
			getCorrectDrag(event.target).nextSibling
		);
	}
}

function dragEnd() {
	selected = null;
}

function dragStart(event) {
	event.dataTransfer.effectAllowed = 'move';
	event.dataTransfer.setData('text/plain', null);
	selected = getCorrectDrag(event.target);
}

function isBefore(el1, el2) {
	let cur;
	if (el2.parentNode === el1.parentNode) {
		for (cur = el1.previousSibling; cur; cur = cur.previousSibling) {
			if (cur === el2) return true;
		}
	}
	return false;
}
