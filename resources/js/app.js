const LIVEWIRE_ACTION_SELECTORS = [
    'button[wire\\:click]',
    'a[wire\\:click]',
    'input[type="button"][wire\\:click]',
    'input[type="submit"][wire\\:click]',
    'form[wire\\:submit] button[type="submit"]',
    'form[wire\\:submit] button:not([type])',
    'form[wire\\:submit] input[type="submit"]',
].join(', ');

const SELECT_AUTOCOMPLETE_THRESHOLD = 5;

const FIELD_ICON_SELECTORS = [
    'input:not([type="hidden"]):not([type="checkbox"]):not([type="radio"]):not([type="range"]):not([type="file"]):not([type="submit"]):not([type="button"]):not([type="reset"])',
    'select:not([multiple])',
    'textarea',
].join(', ');

const TABLE_ICON_RULES = [
    { keywords: ['name', 'title', 'subject'], icon: 'fa-solid fa-user-tag' },
    { keywords: ['email'], icon: 'fa-solid fa-envelope' },
    { keywords: ['password'], icon: 'fa-solid fa-key' },
    { keywords: ['phone', 'mobile'], icon: 'fa-solid fa-phone' },
    { keywords: ['role'], icon: 'fa-solid fa-user-shield' },
    { keywords: ['team', 'members'], icon: 'fa-solid fa-users' },
    { keywords: ['owner', 'manager', 'assigned'], icon: 'fa-solid fa-user-tie' },
    { keywords: ['status', 'state'], icon: 'fa-solid fa-signal' },
    { keywords: ['priority'], icon: 'fa-solid fa-flag' },
    { keywords: ['type'], icon: 'fa-solid fa-tags' },
    { keywords: ['company', 'account'], icon: 'fa-solid fa-building' },
    { keywords: ['contact'], icon: 'fa-solid fa-address-book' },
    { keywords: ['deal', 'pipeline', 'stage'], icon: 'fa-solid fa-handshake' },
    { keywords: ['case', 'ticket'], icon: 'fa-solid fa-ticket' },
    { keywords: ['campaign'], icon: 'fa-solid fa-bullhorn' },
    { keywords: ['quote'], icon: 'fa-solid fa-file-signature' },
    { keywords: ['invoice', 'payment'], icon: 'fa-solid fa-file-invoice-dollar' },
    { keywords: ['amount', 'price', 'cost', 'revenue', 'total', 'quota', 'balance'], icon: 'fa-solid fa-money-bill-wave' },
    { keywords: ['score', 'rating'], icon: 'fa-solid fa-star' },
    { keywords: ['probability', 'percent'], icon: 'fa-solid fa-percent' },
    { keywords: ['currency'], icon: 'fa-solid fa-coins' },
    { keywords: ['date', 'created', 'updated', 'due', 'login', 'time'], icon: 'fa-solid fa-calendar-days' },
    { keywords: ['notes', 'description', 'comment'], icon: 'fa-solid fa-file-lines' },
    { keywords: ['channel', 'message'], icon: 'fa-solid fa-comments' },
    { keywords: ['avatar', 'image', 'photo'], icon: 'fa-solid fa-image' },
    { keywords: ['actions', 'action'], icon: 'fa-solid fa-gear' },
    { keywords: ['select', 'check'], icon: 'fa-solid fa-square-check' },
];

const FIELD_ICON_RULES = [
    { keywords: ['search'], icon: 'fa-solid fa-magnifying-glass' },
    { keywords: ['name', 'first', 'last', 'title', 'subject'], icon: 'fa-solid fa-user' },
    { keywords: ['email'], icon: 'fa-solid fa-envelope' },
    { keywords: ['password'], icon: 'fa-solid fa-lock' },
    { keywords: ['phone', 'mobile', 'tel'], icon: 'fa-solid fa-phone' },
    { keywords: ['website', 'url'], icon: 'fa-solid fa-globe' },
    { keywords: ['address', 'street', 'city', 'state', 'zip', 'country', 'location'], icon: 'fa-solid fa-location-dot' },
    { keywords: ['company', 'account'], icon: 'fa-solid fa-building' },
    { keywords: ['contact'], icon: 'fa-solid fa-address-book' },
    { keywords: ['deal'], icon: 'fa-solid fa-handshake' },
    { keywords: ['role', 'permission'], icon: 'fa-solid fa-user-shield' },
    { keywords: ['team', 'member'], icon: 'fa-solid fa-users' },
    { keywords: ['owner', 'manager'], icon: 'fa-solid fa-user-tie' },
    { keywords: ['status'], icon: 'fa-solid fa-signal' },
    { keywords: ['priority'], icon: 'fa-solid fa-flag' },
    { keywords: ['type', 'category', 'industry'], icon: 'fa-solid fa-tags' },
    { keywords: ['currency', 'amount', 'price', 'cost', 'revenue', 'total', 'quota', 'budget', 'tax', 'discount'], icon: 'fa-solid fa-money-bill-wave' },
    { keywords: ['probability', 'percent'], icon: 'fa-solid fa-percent' },
    { keywords: ['score', 'rating'], icon: 'fa-solid fa-star' },
    { keywords: ['date', 'time', 'timezone', 'birthday', 'due', 'start', 'end'], icon: 'fa-solid fa-calendar-days' },
    { keywords: ['note', 'description', 'comment', 'details', 'message'], icon: 'fa-solid fa-file-lines' },
    { keywords: ['file', 'upload', 'avatar', 'logo'], icon: 'fa-solid fa-paperclip' },
    { keywords: ['code', 'sku'], icon: 'fa-solid fa-barcode' },
    { keywords: ['source', 'channel', 'campaign'], icon: 'fa-solid fa-bullhorn' },
];

let decorationFrameId = null;
const selectAutocompleteInstances = new WeakMap();
const managedAutocompleteSelects = new Set();

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

function normalizeIconText(value) {
    return (value ?? '')
        .toLowerCase()
        .replace(/[^a-z0-9\s]/g, ' ')
        .replace(/\s+/g, ' ')
        .trim();
}

function resolveIcon(rawValue, rules, fallbackIcon) {
    const normalized = normalizeIconText(rawValue);

    if (!normalized) {
        return fallbackIcon;
    }

    const matchedRule = rules.find((rule) => {
        return rule.keywords.some((keyword) => {
            return normalized.includes(keyword);
        });
    });

    return matchedRule?.icon ?? fallbackIcon;
}

function firstLabelForField(field) {
    if (field.labels?.length) {
        return field.labels[0];
    }

    const parent = field.parentElement;
    if (parent) {
        const directLabel = parent.querySelector(':scope > label, :scope > [data-flux-label]');
        if (directLabel instanceof HTMLElement) {
            return directLabel;
        }
    }

    const fluxField = field.closest('[data-flux-field]');
    if (fluxField) {
        const fluxLabel = fluxField.querySelector('[data-flux-label]');
        if (fluxLabel instanceof HTMLElement) {
            return fluxLabel;
        }
    }

    const id = field.getAttribute('id');
    if (id) {
        const associatedLabel = document.querySelector(`label[for="${CSS.escape(id)}"]`);
        if (associatedLabel instanceof HTMLElement) {
            return associatedLabel;
        }
    }

    return null;
}

function fieldDescriptor(field) {
    const label = firstLabelForField(field);

    return [
        field.getAttribute('name'),
        field.getAttribute('id'),
        field.getAttribute('placeholder'),
        field.getAttribute('aria-label'),
        label?.textContent,
    ]
        .filter(Boolean)
        .join(' ');
}

function addLeadingIcon(target, iconClass, markerClass) {
    if (!target || target.dataset.crmIconized === '1') {
        return;
    }

    const icon = document.createElement('i');
    icon.className = `${markerClass} ${iconClass}`;
    icon.setAttribute('aria-hidden', 'true');
    target.insertBefore(icon, target.firstChild);
    target.dataset.crmIconized = '1';
}

function decorateTableHeader(headerCell) {
    if (!(headerCell instanceof HTMLElement) || headerCell.dataset.crmTableDecorated === '1') {
        return;
    }

    const iconTarget = headerCell.querySelector('button, a, span, div') ?? headerCell;
    const iconClass = resolveIcon(iconTarget.textContent, TABLE_ICON_RULES, 'fa-solid fa-table-columns');
    const normalized = normalizeIconText(iconTarget.textContent);

    if (!/[a-z0-9]/.test(normalized)) {
        return;
    }

    addLeadingIcon(iconTarget, iconClass, 'crm-table-heading-icon');
    iconTarget.classList.add('crm-heading-with-icon');
    headerCell.dataset.crmTableDecorated = '1';
}

function decorateFormControl(field) {
    if (!(field instanceof HTMLElement) || field.dataset.crmFieldDecorated === '1') {
        return;
    }

    const type = normalizeIconText(field.getAttribute('type'));
    const descriptor = fieldDescriptor(field);
    const iconClass = resolveIcon(descriptor || type, FIELD_ICON_RULES, 'fa-solid fa-pen-to-square');

    const label = firstLabelForField(field);
    if (label instanceof HTMLElement && label.dataset.crmLabelDecorated !== '1') {
        addLeadingIcon(label, iconClass, 'crm-field-label-icon');
        label.classList.add('crm-label-with-icon');
        label.dataset.crmLabelDecorated = '1';
    }

    if (field.hasAttribute('data-flux-control')) {
        field.dataset.crmFieldDecorated = '1';
        return;
    }

    if (field instanceof HTMLSelectElement) {
        field.dataset.crmFieldDecorated = '1';
        return;
    }

    if (!field.closest('.crm-input-icon-wrap')) {
        const wrapper = document.createElement('span');
        wrapper.className = 'crm-input-icon-wrap';
        field.parentNode?.insertBefore(wrapper, field);
        wrapper.appendChild(field);

        const icon = document.createElement('i');
        icon.className = `crm-input-icon ${iconClass}`;
        icon.setAttribute('aria-hidden', 'true');
        wrapper.prepend(icon);
    }

    field.classList.add('crm-iconized-field');
    field.dataset.crmFieldDecorated = '1';
}

function decorateFontAwesomeIcons(root = document) {
    root.querySelectorAll('table thead th').forEach((headerCell) => {
        decorateTableHeader(headerCell);
    });

    root.querySelectorAll(FIELD_ICON_SELECTORS).forEach((field) => {
        decorateFormControl(field);
    });
}

function shouldEnableSelectAutocomplete(select) {
    if (!(select instanceof HTMLSelectElement)) {
        return false;
    }

    if (select.multiple || select.hasAttribute('data-no-autocomplete') || select.hasAttribute('data-flux-control')) {
        return false;
    }

    const sizeAttribute = Number.parseInt(select.getAttribute('size') ?? '1', 10);
    if (Number.isFinite(sizeAttribute) && sizeAttribute > 1) {
        return false;
    }

    return select.options.length > SELECT_AUTOCOMPLETE_THRESHOLD;
}

function ensureSelectId(select) {
    if (select.id && select.id.trim() !== '') {
        return select.id;
    }

    const generatedId = `crm-select-${Math.random().toString(36).slice(2, 10)}`;
    select.id = generatedId;

    return generatedId;
}

function normalizedOptionLabel(option) {
    if (!option) {
        return '';
    }

    return (option.textContent ?? '').trim();
}

function collectSelectableOptions(select) {
    return Array.from(select.options)
        .filter((option) => !option.disabled)
        .map((option) => ({
            value: option.value,
            label: normalizedOptionLabel(option),
            selected: option.selected,
        }));
}

function closeSelectAutocomplete(instance, resetToSelected = false) {
    instance.open = false;
    instance.highlightedIndex = -1;
    instance.panel.classList.remove('is-open');
    instance.input.setAttribute('aria-expanded', 'false');

    if (resetToSelected) {
        const selectedLabel = normalizedOptionLabel(instance.select.selectedOptions[0] ?? null);
        instance.input.value = selectedLabel;
    }
}

function renderSelectAutocompleteOptions(instance) {
    const options = collectSelectableOptions(instance.select);
    const searchTerm = normalizeIconText(instance.input.value);
    const filteredOptions = searchTerm === ''
        ? options
        : options.filter((option) => normalizeIconText(option.label).includes(searchTerm));

    instance.options = filteredOptions;
    instance.list.innerHTML = '';

    if (filteredOptions.length === 0) {
        const emptyState = document.createElement('li');
        emptyState.className = 'crm-select-autocomplete-empty';
        emptyState.textContent = 'No matches found';
        instance.list.appendChild(emptyState);
        instance.highlightedIndex = -1;

        return;
    }

    if (instance.highlightedIndex >= filteredOptions.length) {
        instance.highlightedIndex = filteredOptions.length - 1;
    }

    filteredOptions.forEach((option, index) => {
        const item = document.createElement('li');

        const button = document.createElement('button');
        button.type = 'button';
        button.className = 'crm-select-autocomplete-option';
        button.textContent = option.label;
        button.dataset.value = option.value;

        if (option.value === instance.select.value) {
            button.classList.add('is-selected');
        }

        if (index === instance.highlightedIndex) {
            button.classList.add('is-highlighted');
        }

        button.addEventListener('mousedown', (event) => {
            event.preventDefault();
        });

        button.addEventListener('click', () => {
            if (instance.select.value !== option.value) {
                instance.select.value = option.value;
                instance.select.dispatchEvent(new Event('input', { bubbles: true }));
                instance.select.dispatchEvent(new Event('change', { bubbles: true }));
            }

            instance.input.value = option.label;
            closeSelectAutocomplete(instance);
        });

        item.appendChild(button);
        instance.list.appendChild(item);
    });
}

function syncSelectAutocomplete(select) {
    const instance = selectAutocompleteInstances.get(select);

    if (!instance) {
        return;
    }

    const descriptor = fieldDescriptor(select);
    const iconClass = resolveIcon(descriptor, FIELD_ICON_RULES, 'fa-solid fa-list');
    instance.icon.className = `crm-select-autocomplete-icon ${iconClass}`;

    const selectedLabel = normalizedOptionLabel(select.selectedOptions[0] ?? null);
    const firstOptionLabel = normalizedOptionLabel(select.options[0] ?? null);
    instance.input.placeholder = firstOptionLabel !== '' ? firstOptionLabel : 'Search and select...';

    if (document.activeElement !== instance.input) {
        instance.input.value = selectedLabel;
    }

    renderSelectAutocompleteOptions(instance);
}

function moveSelectAutocompleteHighlight(instance, direction) {
    if (instance.options.length === 0) {
        instance.highlightedIndex = -1;

        return;
    }

    const current = instance.highlightedIndex;

    if (current === -1) {
        instance.highlightedIndex = direction > 0 ? 0 : instance.options.length - 1;
    } else {
        const nextIndex = current + direction;
        if (nextIndex < 0) {
            instance.highlightedIndex = instance.options.length - 1;
        } else if (nextIndex >= instance.options.length) {
            instance.highlightedIndex = 0;
        } else {
            instance.highlightedIndex = nextIndex;
        }
    }

    renderSelectAutocompleteOptions(instance);
}

function openSelectAutocomplete(instance) {
    instance.open = true;
    instance.panel.classList.add('is-open');
    instance.input.setAttribute('aria-expanded', 'true');
    renderSelectAutocompleteOptions(instance);
}

function mountSelectAutocomplete(select) {
    if (selectAutocompleteInstances.has(select)) {
        syncSelectAutocomplete(select);

        return;
    }

    const selectId = ensureSelectId(select);

    const wrapper = document.createElement('div');
    wrapper.className = 'crm-select-autocomplete';

    const descriptor = fieldDescriptor(select);
    const iconClass = resolveIcon(descriptor, FIELD_ICON_RULES, 'fa-solid fa-list');

    const icon = document.createElement('i');
    icon.className = `crm-select-autocomplete-icon ${iconClass}`;
    icon.setAttribute('aria-hidden', 'true');

    const input = document.createElement('input');
    input.type = 'text';
    input.autocomplete = 'off';
    input.spellcheck = false;
    input.className = `${select.className} crm-select-autocomplete-input`;
    input.setAttribute('role', 'combobox');
    input.setAttribute('aria-autocomplete', 'list');
    input.setAttribute('aria-expanded', 'false');
    input.setAttribute('aria-controls', `${selectId}-autocomplete-list`);

    const caret = document.createElement('i');
    caret.className = 'crm-select-autocomplete-caret fa-solid fa-chevron-down';
    caret.setAttribute('aria-hidden', 'true');

    const panel = document.createElement('div');
    panel.className = 'crm-select-autocomplete-panel';

    const list = document.createElement('ul');
    list.id = `${selectId}-autocomplete-list`;
    list.className = 'crm-select-autocomplete-list';
    list.setAttribute('role', 'listbox');

    panel.appendChild(list);
    wrapper.appendChild(icon);
    wrapper.appendChild(input);
    wrapper.appendChild(caret);
    wrapper.appendChild(panel);

    select.insertAdjacentElement('afterend', wrapper);
    select.classList.add('crm-select-autocomplete-native');
    select.setAttribute('data-crm-autocomplete-enhanced', '1');

    const instance = {
        select,
        wrapper,
        input,
        icon,
        panel,
        list,
        options: [],
        open: false,
        highlightedIndex: -1,
        onInput: null,
        onFocus: null,
        onBlur: null,
        onKeydown: null,
        onSelectChange: null,
        onDocumentClick: null,
    };

    instance.onInput = () => {
        instance.highlightedIndex = -1;
        openSelectAutocomplete(instance);
    };

    instance.onFocus = () => {
        openSelectAutocomplete(instance);
    };

    instance.onBlur = () => {
        window.setTimeout(() => {
            if (!instance.wrapper.contains(document.activeElement)) {
                closeSelectAutocomplete(instance, true);
            }
        }, 120);
    };

    instance.onKeydown = (event) => {
        if (event.key === 'ArrowDown') {
            event.preventDefault();
            if (!instance.open) {
                openSelectAutocomplete(instance);
            } else {
                moveSelectAutocompleteHighlight(instance, 1);
            }

            return;
        }

        if (event.key === 'ArrowUp') {
            event.preventDefault();
            if (!instance.open) {
                openSelectAutocomplete(instance);
            } else {
                moveSelectAutocompleteHighlight(instance, -1);
            }

            return;
        }

        if (event.key === 'Enter') {
            if (!instance.open || instance.highlightedIndex < 0 || instance.highlightedIndex >= instance.options.length) {
                return;
            }

            event.preventDefault();
            const selectedOption = instance.options[instance.highlightedIndex];
            if (!selectedOption) {
                return;
            }

            if (instance.select.value !== selectedOption.value) {
                instance.select.value = selectedOption.value;
                instance.select.dispatchEvent(new Event('input', { bubbles: true }));
                instance.select.dispatchEvent(new Event('change', { bubbles: true }));
            }

            instance.input.value = selectedOption.label;
            closeSelectAutocomplete(instance);

            return;
        }

        if (event.key === 'Escape') {
            closeSelectAutocomplete(instance, true);
        }
    };

    instance.onSelectChange = () => {
        syncSelectAutocomplete(select);
    };

    instance.onDocumentClick = (event) => {
        const target = event.target instanceof HTMLElement ? event.target : null;
        if (!target || instance.wrapper.contains(target)) {
            return;
        }

        closeSelectAutocomplete(instance, true);
    };

    input.addEventListener('input', instance.onInput);
    input.addEventListener('focus', instance.onFocus);
    input.addEventListener('blur', instance.onBlur);
    input.addEventListener('keydown', instance.onKeydown);
    select.addEventListener('change', instance.onSelectChange);
    document.addEventListener('click', instance.onDocumentClick, true);

    selectAutocompleteInstances.set(select, instance);
    managedAutocompleteSelects.add(select);

    syncSelectAutocomplete(select);
}

function destroySelectAutocomplete(select) {
    const instance = selectAutocompleteInstances.get(select);

    if (!instance) {
        return;
    }

    instance.input.removeEventListener('input', instance.onInput);
    instance.input.removeEventListener('focus', instance.onFocus);
    instance.input.removeEventListener('blur', instance.onBlur);
    instance.input.removeEventListener('keydown', instance.onKeydown);
    instance.select.removeEventListener('change', instance.onSelectChange);
    document.removeEventListener('click', instance.onDocumentClick, true);

    instance.wrapper.remove();
    instance.select.classList.remove('crm-select-autocomplete-native');
    instance.select.removeAttribute('data-crm-autocomplete-enhanced');

    selectAutocompleteInstances.delete(select);
    managedAutocompleteSelects.delete(select);
}

function enhanceSelectAutocompletes(root = document) {
    managedAutocompleteSelects.forEach((select) => {
        if (!document.body.contains(select) || !shouldEnableSelectAutocomplete(select)) {
            destroySelectAutocomplete(select);
        }
    });

    root.querySelectorAll('select').forEach((select) => {
        if (!(select instanceof HTMLSelectElement)) {
            return;
        }

        if (!shouldEnableSelectAutocomplete(select)) {
            if (selectAutocompleteInstances.has(select)) {
                destroySelectAutocomplete(select);
            }

            return;
        }

        if (!selectAutocompleteInstances.has(select)) {
            mountSelectAutocomplete(select);

            return;
        }

        syncSelectAutocomplete(select);
    });
}

function queueDecoration() {
    if (decorationFrameId !== null) {
        return;
    }

    decorationFrameId = window.requestAnimationFrame(() => {
        decorationFrameId = null;
        decorateFontAwesomeIcons();
        enhanceSelectAutocompletes();
        tagLivewireActionElements();
    });
}

function observeDomInsertions() {
    const observer = new MutationObserver((mutations) => {
        const hasInsertedNodes = mutations.some((mutation) => {
            return mutation.type === 'childList' && mutation.addedNodes.length > 0;
        });

        if (hasInsertedNodes) {
            queueDecoration();
        }
    });

    observer.observe(document.body, {
        subtree: true,
        childList: true,
    });
}

document.addEventListener('DOMContentLoaded', () => {
    tagLivewireActionElements();
    decorateFontAwesomeIcons();
    enhanceSelectAutocompletes();
    observeDomInsertions();

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
    decorateFontAwesomeIcons();
    enhanceSelectAutocompletes();
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

document.addEventListener('livewire:init', () => {
    if (typeof window.Livewire?.hook !== 'function') {
        return;
    }

    window.Livewire.hook('morphed', () => {
        queueDecoration();
    });
});
