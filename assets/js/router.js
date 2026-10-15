(function () {
    'use strict';

    function currentPage() {
        return new URLSearchParams(location.search).get('page') || 'home';
    }

    function navigate(page, params = {}) {
        const url = new URL(window.location.href);
        url.searchParams.set('page', page);
        for (const [k, v] of Object.entries(params)) {
            url.searchParams.set(k, v);
        }
        location.href = url.toString();
    }

    function initLinks() {
        document.querySelectorAll('a[data-route]').forEach((a) => {
            a.addEventListener('click', (e) => {
                const page = a.dataset.route;
                if (!page) return;
                e.preventDefault();
                navigate(page);
            });
        });
    }

    window.PMRouter = {
        current: currentPage,
        navigate,
        init: initLinks
    };

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initLinks);
    } else {
        initLinks();
    }
})();