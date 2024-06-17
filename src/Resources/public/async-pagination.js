const documentReady = function() {
    const items = document.querySelectorAll('[data-module="pagination-reload"]');

    for (const item of items) {
        new ListReload(item);
    }
};

const ListReload = function (container) {
    this.name = 'ListReload';
    this.elements = {
        container: null,
        list: null,
        filterContainer: null,
        filterItems: [],
    };
    this.config = {
        moduleSelector: 'data-ajax-reload-element',
    };
    this.state = {
        isLoading: false,
        currentUrl: null,
    };

    let self = this;

    this.load = async function(url) {
        const reloadId = self.elements.list.getAttribute(self.config.moduleSelector);
        const params = new URLSearchParams([
            ['ajax_reload_element', reloadId],
        ]);

        try {
            self.setLoadingState(true);

            const request = await fetch(`/${url}`, {
                method: 'POST',
                body: params,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
            });

            const result = await request.json();

            if (typeof result.html === 'undefined') {
                return;
            }

            self.state.currentUrl = url;

            self.render();
            self.transition(self.replace.bind(self, result.html));
            self.setLoadingState(false);
        }
        catch (error) {
            console.error(error);
        }
    };

    this.setLoadingState = function(newState) {
        self.state.isLoading = newState;
    }

    this.transition = function(callback) {
        if (!document.startViewTransition) {
            callback();
            return;
        }

        document.startViewTransition(() => callback());
    };

    this.render = function() {
        for (const item of self.elements.filterItems) {
            const isActive = item.getAttribute('href') == self.state.currentUrl;
            item.setAttribute('data-active', isActive);
        }
    };

    this.replace = function(newContent) {
        const parent = self.elements.list.parentElement;
        const template = document.createElement('template');
        template.innerHTML = newContent;

        const newElement = template.content.children.length === 1 ? template.content.children[0] : null;

        if (newElement === null) {
            return;
        }

        parent.replaceChild(newElement, self.elements.list);

        window.requestAnimationFrame(() => {
            self.elements.list = parent.querySelector(`[${self.config.moduleSelector}]`);
            self.initInnerEventListener();
        });
    };

    this.onFilterChange = function(event) {
        event.preventDefault();

        const targetElement = event.currentTarget;
        let target = null;

        switch (targetElement.tagName) {
            case 'A':
                target = targetElement.getAttribute('href');
                break;

            case 'SELECT':
                target = targetElement.value;
                break;

            default:
                break;
        }

        if (target === null) {
            return;
        }

        self.load(target);
    };

    this.initInnerEventListener = function() {
        const innerElements = self.elements.container.querySelectorAll('.pagination a');

        for (const link of innerElements) {
            link.addEventListener('click', self.onFilterChange);
        }
    };

    this.init = function() {
        self.elements.container = container;
        self.elements.list = container.querySelector(`[${self.config.moduleSelector}]`);
        self.elements.filterContainer = container.querySelector('.mod_newscategories');
        self.elements.filterItems = self.elements.filterContainer?.querySelectorAll('a') ?? [];
        self.elements.filterSelect = self.elements.filterContainer?.querySelector('select') ?? null;

        self.initInnerEventListener();

        for (const item of self.elements.filterItems) {
            item.addEventListener('click', self.onFilterChange);
        }

        if (self.elements.filterSelect !== null) {
            self.elements.filterSelect.addEventListener('change', self.onFilterChange);
        }
    }();
};

documentReady(function() {
    const items = document.querySelectorAll('[data-module="pagination-reload"]');

    for (const item of items) {
        new ListReload(item);
    }
});
