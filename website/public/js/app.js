// UI of the demo: tabs, fixer selection and copy buttons. Running PHP lives in wasm.js.

const form = document.querySelector('form[name="typo_fixer"]');

// Tabs (WAI-ARIA tabs pattern, with arrow keys navigation)

const tabs = Array.from(document.querySelectorAll('[role="tab"]'));

const selectTab = (tab, focus = false) => {
    for (const other of tabs) {
        const selected = other === tab;
        other.setAttribute('aria-selected', String(selected));
        other.tabIndex = selected ? 0 : -1;
        document.getElementById(other.getAttribute('aria-controls')).hidden = !selected;
    }
    if (focus) {
        tab.focus();
    }
};

for (const tab of tabs) {
    tab.addEventListener('click', () => selectTab(tab));
    tab.addEventListener('keydown', (e) => {
        const index = tabs.indexOf(tab);
        const next = { ArrowRight: index + 1, ArrowLeft: index - 1, Home: 0, End: tabs.length - 1 }[e.key];
        if (next !== undefined) {
            e.preventDefault();
            selectTab(tabs[(next + tabs.length) % tabs.length], true);
        }
    });
}

// Fixers: counter and select all / none

const checkboxes = Array.from(document.querySelectorAll('input[name="typo_fixer[fixers][]"]'));
const count = document.querySelector('.js-fixers-count');
const checkAll = document.querySelector('.js-check-all');
const uncheckAll = document.querySelector('.js-uncheck-all');

const updateCount = () => {
    const checked = checkboxes.filter((checkbox) => checkbox.checked).length;
    count.textContent = `${checked} / ${checkboxes.length}`;
    checkAll.disabled = checked === checkboxes.length;
    uncheckAll.disabled = checked === 0;
};

const checkEvery = (checked) => {
    for (const checkbox of checkboxes) {
        checkbox.checked = checked;
    }
    // Changing "checked" from JS fires no event: tell the form, so the result is refreshed
    form.dispatchEvent(new Event('change', { bubbles: true }));
};

checkAll.addEventListener('click', () => checkEvery(true));
uncheckAll.addEventListener('click', () => checkEvery(false));
form.addEventListener('change', updateCount);
updateCount();

// Copy button: copies the PHP code on its tab, the fixed HTML otherwise

const copy = document.querySelector('.js-copy');
const copyLabel = copy.querySelector('.js-copy-label');
const copyWhat = copy.querySelector('.js-copy-what');
const phpTab = document.getElementById('tab-php');
const isPhpTab = () => phpTab.getAttribute('aria-selected') === 'true';

for (const tab of tabs) {
    tab.addEventListener('click', () => { copyWhat.textContent = isPhpTab() ? ' PHP' : ' HTML'; });
    tab.addEventListener('keyup', () => { copyWhat.textContent = isPhpTab() ? ' PHP' : ' HTML'; });
}

copy.addEventListener('click', async () => {
    const target = isPhpTab() ? 'phpCode' : 'resultContent';
    copyWhat.hidden = true;
    try {
        await navigator.clipboard.writeText(document.getElementById(target).textContent);
        copyLabel.textContent = 'Copied!';
    } catch {
        copyLabel.textContent = 'Copy failed';
    }
    setTimeout(() => {
        copyLabel.textContent = 'Copy';
        copyWhat.hidden = false;
    }, 1500);
});

// JoliCode footer: a build artifact, the page stays usable without it

const footer = document.querySelector('.js-joli-footer');
fetch('./joli-footer.html')
    .then((response) => response.ok ? response.text() : '')
    .then((html) => { footer.innerHTML = html; })
    .catch(() => {});
