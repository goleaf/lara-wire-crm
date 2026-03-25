const LIVEWIRE_ACTION_SELECTORS = [
    'button[wire\\:click]',
    'a[wire\\:click]',
    'input[type="button"][wire\\:click]',
    'input[type="submit"][wire\\:click]',
    'form[wire\\:submit] button[type="submit"]',
    'form[wire\\:submit] button:not([type])',
    'form[wire\\:submit] input[type="submit"]',
].join(', ');

function tagLivewireActionElements(root = document) {
    root.querySelectorAll(LIVEWIRE_ACTION_SELECTORS).forEach((element) => {
        element.setAttribute('data-livewire-action', '1');
    });
}

function cleanupActiveStates(root = document) {
    root.querySelectorAll('[data-livewire-action="1"].is-livewire-active:not([data-loading])').forEach((element) => {
        element.classList.remove('is-livewire-active');
    });
}

document.addEventListener('DOMContentLoaded', () => {
    tagLivewireActionElements();

    const observer = new MutationObserver((mutations) => {
        mutations.forEach((mutation) => {
            if (mutation.type === 'attributes' && mutation.attributeName === 'data-loading') {
                const element = /** @type {HTMLElement} */ (mutation.target);

                if (!element.hasAttribute('data-loading')) {
                    element.classList.remove('is-livewire-active');
                }
            }
        });
    });

    observer.observe(document.body, {
        subtree: true,
        attributes: true,
        attributeFilter: ['data-loading'],
    });
});

document.addEventListener('livewire:navigated', () => {
    tagLivewireActionElements();
    cleanupActiveStates();
});

document.addEventListener('click', (event) => {
    const target = /** @type {HTMLElement|null} */ (event.target instanceof HTMLElement ? event.target : null);
    const actionElement = target?.closest('[data-livewire-action="1"]');

    if (!actionElement) {
        return;
    }

    actionElement.classList.add('is-livewire-active');

    window.setTimeout(() => {
        if (!actionElement.hasAttribute('data-loading')) {
            actionElement.classList.remove('is-livewire-active');
        }
    }, 500);
}, true);
