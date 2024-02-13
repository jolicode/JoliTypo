const allBoxes = document.getElementsByClassName('js-box');
for (const box of allBoxes) {
  box.addEventListener('click', checkIfAllOrNoneChecked);
}
const checkButton = document.getElementsByClassName('js-check-all')[0];
const allCheckboxes = document.getElementsByClassName('js-checkbox');
checkButton.addEventListener('click', checkAction);

// If any checkbox is checked, set the button to uncheck all
// If no checkbox is checked, set the button to check all
function checkIfAllOrNoneChecked () {
  for (const checkbox of allCheckboxes) {
    if (checkbox.checked) {
      return setCheckButtonTrue();
    }
  }

  return setCheckButtonFalse();
}

// Checks or unchecks all checkboxes
function checkAction (e) {
  e.preventDefault();

  if (this.dataset.allchecked === 'false') {
    setCheckButtonTrue();

    for (const checkbox of allCheckboxes) {
      checkbox.checked = true;
    }

    return;
  }

  setCheckButtonFalse()

  for (const checkbox of allCheckboxes) {
    checkbox.checked = false;
  }
}

function setCheckButtonTrue () {
  checkButton.dataset.allchecked = 'true';
  checkButton.textContent = 'Uncheck all';
}

function setCheckButtonFalse () {
  checkButton.dataset.allchecked = 'false';
  checkButton.textContent = 'Check all';
}

// Click behavior for switching between Client and HTML version

const clientButton = document.getElementsByClassName('form__result-button-client')[0];
const htmlButton = document.getElementsByClassName('form__result-button-html')[0];
const configButton = document.getElementsByClassName('form__result-button-config')[0];
const clientResult = document.getElementsByClassName('form__result-client')[0];
const htmlResult = document.getElementsByClassName('form__result-html')[0];
const configResult = document.getElementsByClassName('form__result-config')[0];

if (undefined !== clientResult && undefined !== htmlResult && undefined !== configResult) {
  clientButton.addEventListener('click', toggleResults);
  htmlButton.addEventListener('click', toggleResults);
  configButton.addEventListener('click', toggleResults);
}

function toggleResults (e) {
  if (!e.target.classList.contains('u-c(darkgrey)')) {
    const allButtons = [clientButton, htmlButton, configButton];
    allButtons.forEach(button => button.classList.remove('u-c(darkgrey)'));
    e.target.classList.add('u-c(darkgrey)');

    const allResults = [clientResult, htmlResult, configResult];
    allResults.forEach(result => result.classList.remove('visible-result'));
    switch (e.target) {
      case clientButton:
        clientResult.classList.add('visible-result');
        break;
      case htmlButton:
        htmlResult.classList.add('visible-result');
        break;
      case configButton:
        configResult.classList.add('visible-result');
        break;
    }
  }
}

// If the "fr" language is selected, check the "FrenchNoBreakSpace" filter
const frenchNoBreakSpaceCheckbox = document.querySelector("input[value='FrenchNoBreakSpace']");
const languageSelect = document.getElementById('typo_fixer_locale');

languageSelect.addEventListener('change', toggleFrench);

function toggleFrench(e) {
  frenchNoBreakSpaceCheckbox.checked = e.target.value === 'fr';
}
