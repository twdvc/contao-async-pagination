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
        listContainer: null,
        filterContainer: null,
        filterItems: [],
    };
    this.config = {
        // moduleSelector: 'data-ajax-reload-element',
    };
    this.state = {
        isLoading: false,
        currentFilter: {},
        shouldReplaceContainer: false, // TODO
    };

    let self = this;

    this.load = async function(filter) {
        const params = new URLSearchParams(filter);
        const endpointType = self.elements.listContainer.getAttribute('data-type');
        const endpointId = self.elements.listContainer.getAttribute('data-id');

        try {
            self.setLoadingState(true);

            const request = await fetch(`/_dvc/ajax/${endpointType}/${endpointId}?${params.toString()}`, {
                method: 'GET',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                },
            });

            if (request.status !== 200) {
                return;
            }

            const result = await request.json();

            if (typeof result.html === 'undefined') {
                return;
            }

            self.state.currentFilter = filter;

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
        console.log(self.state.currentFilter);

        for (const item of self.elements.filterItems) {
            const filterOfCurrentItem = self.getFilterFromElement(item);
            const [filterKey, filterValue] = Object.entries(filterOfCurrentItem[0] ?? []);

            console.log('entries', Object.entries(filterOfCurrentItem), filterKey, filterValue);

            continue;
            const isActive = (() => {
                if (Object.keys(self.state.currentFilter).indexOf(filterKey) < 0 && filterValue === null) {
                    return true;
                }

                return self.state.currentFilter[filterKey] === filterValue;
            })();

            item.setAttribute('data-active', isActive);
        }
    };

    this.replace = function(newContent) {
        const replaceElement = self.getReloadElement();
        const parent = replaceElement.parentElement;
        const template = document.createElement('template');
        template.innerHTML = newContent;

        const newElement = template.content.children.length === 1 ? template.content.children[0] : null;

        if (newElement === null) {
            return;
        }

        parent.replaceChild(newElement, replaceElement);

        window.requestAnimationFrame(() => {
            if (this.state.shouldReplaceContainer) {
                self.elements.container = newElement;
            }
            else {
                self.elements.list = newElement;
            }

            self.initInnerEventListener();
        });
    };

    this.onFilterChange = function(event) {
        event.preventDefault();

        const targetElement = event.currentTarget;
        let filter = {};

        switch (targetElement.tagName) {
            case 'A':
                const targetHref = new URL(targetElement.href);

                if (targetHref.searchParams.size > 0) {
                    filter = Object.assign({}, filter, Object.fromEntries(targetHref.searchParams.entries()));
                }

                const newFilter = self.getFilterFromElement(targetElement);
                filter = Object.assign(filter, newFilter);

                // const filterKey = targetElement.getAttribute('data-pagination-filter-key');
                // const filterValue = targetElement.getAttribute('data-pagination-filter-value');

                // if (filterKey === null || filterValue === null) {
                //     break;
                // }

                // filter[filterKey] = filterValue;
                break;

            case 'SELECT':
                // TODO
                break;
                // filter = targetElement.value;
                // break;

            default:
                break;
        }

        self.load(filter);
    };

    this.getReloadElement = function() {
        // TODO
        if (this.state.shouldReplaceContainer) {
            return this.elements.container;
        }

        return this.elements.list;
    }

    this.getFilterFromElement = function(element) {
        const filterKey = element.getAttribute('data-pagination-filter-key');
        const filterValue = element.getAttribute('data-pagination-filter-value');

        if (filterKey === null || filterValue === null) {
            return {};
        }

        const result = {};
        result[filterKey] = filterValue;

        return result;
    }

    this.initInnerEventListener = function() {
        const innerElements = self.elements.container.querySelectorAll('.pagination a');

        for (const link of innerElements) {
            link.addEventListener('click', self.onFilterChange);
        }
    };

    this.init = function() {
        self.elements.container = container;
        // TODO
        self.elements.listContainer = container.querySelector(`[data-element="paginated"]`);
        self.elements.list = self.elements.listContainer.children[0];

        self.elements.filterContainer = container.querySelector('[data-element="categories"]');
        self.elements.filterItems = self.elements.filterContainer?.querySelectorAll('a') ?? [];
        self.elements.filterSelect = self.elements.filterContainer?.querySelector('select') ?? null;

        // TODO
        self.state.shouldReplaceContainer = self.elements.container.getAttribute(self.config.moduleSelector) !== null;

        console.log(self.elements);

        self.initInnerEventListener();

        for (const item of self.elements.filterItems) {
            item.addEventListener('click', self.onFilterChange);

            if (item.getAttribute('data-active') == 'true') {
                self.state.currentFilter = Object.assign(self.state.currentFilter, self.getFilterFromElement(item));
            }
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
