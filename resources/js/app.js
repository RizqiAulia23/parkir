const main = () => document.querySelector("main");

const sleep = (ms) => new Promise((resolve) => setTimeout(resolve, ms));

const reducedMotion = () => window.matchMedia("(prefers-reduced-motion: reduce)").matches;

// Client-side swap so the React sidebar never unmounts on a menu switch.
function enhance() {
    const kendaraanSelect = document.getElementById('id_kendaraan');
    const kendaraanBaru = document.getElementById('kendaraan-baru');

    const syncKendaraan = () => {
        if (!kendaraanSelect || !kendaraanBaru) {
            return;
        }

        const isBaru = kendaraanSelect.value === '';

        kendaraanBaru.classList.toggle('hidden', !isBaru);
        kendaraanBaru.querySelectorAll('input, select').forEach((input) => {
            input.disabled = !isBaru;
        });
    };

    kendaraanSelect?.addEventListener('change', syncKendaraan);
    syncKendaraan();

    document.querySelectorAll('[data-flash]').forEach((element) => {
        setTimeout(() => {
            element.style.transition = 'opacity 0.3s ease';
            element.style.opacity = '0';
            setTimeout(() => element.remove(), 300);
        }, 6000);
    });
}

function activeIdFrom(doc) {
    const config = doc.querySelector('#sidebar-root[data-config]');
    if (!config) {
        return null;
    }
    try {
        return JSON.parse(config.dataset.config ?? '{}').activeId ?? null;
    } catch {
        return null;
    }
}

let navigating = false;

async function visit(href) {
    const target = main();
    if (!target || navigating) {
        window.location.href = href;
        return;
    }

    navigating = true;
    const animation = reducedMotion() ? Promise.resolve() : (target.classList.add('is-leaving'), sleep(180));

    try {
        const response = await fetch(href, { headers: { 'X-Requested-With': 'XMLHttpRequest' } });
        if (!response.ok) {
            throw new Error(String(response.status));
        }

        const doc = new DOMParser().parseFromString(await response.text(), 'text/html');
        const next = doc.querySelector('main');
        if (!next || !doc.getElementById('sidebar-root')) {
            throw new Error('not an app page');
        }

        await animation;

        target.replaceChildren(...next.childNodes);
        target.classList.remove('is-leaving', 'page-enter');
        void target.offsetWidth;
        target.classList.add('page-enter');

        const title = doc.querySelector('#app-title');
        if (title) {
            document.getElementById('app-title')?.replaceChildren(...title.childNodes);
        }
        document.title = doc.title;

        document.getElementById('sidebar-overlay')?.classList.add('hidden');
        document.getElementById('sidebar')?.classList.add('-translate-x-full');

        window.dispatchEvent(new CustomEvent('app:navigate', { detail: { activeId: activeIdFrom(doc) } }));
        window.scrollTo({ top: 0 });
        enhance();
    } catch {
        window.location.href = href;
        return;
    } finally {
        navigating = false;
    }
}

document.addEventListener('DOMContentLoaded', () => {
    const sidebar = document.getElementById('sidebar');
    const overlay = document.getElementById('sidebar-overlay');
    const toggle = document.getElementById('sidebar-toggle');
    const close = document.getElementById('sidebar-close');

    const bukaSidebar = () => {
        sidebar?.classList.remove('-translate-x-full');
        overlay?.classList.remove('hidden');
    };

    const tutupSidebar = () => {
        sidebar?.classList.add('-translate-x-full');
        overlay?.classList.add('hidden');
    };

    toggle?.addEventListener('click', bukaSidebar);
    close?.addEventListener('click', tutupSidebar);
    overlay?.addEventListener('click', tutupSidebar);

    document.addEventListener('keydown', (event) => {
        if (event.key === 'Escape') {
            tutupSidebar();
        }
    });

    enhance();

    document.addEventListener('click', (event) => {
        if (event.defaultPrevented || event.button !== 0 || event.metaKey || event.ctrlKey || event.shiftKey || event.altKey) {
            return;
        }

        const anchor = event.target.closest('a[href]');
        if (!anchor || anchor.target === '_blank' || anchor.hasAttribute('download')) {
            return;
        }

        const url = new URL(anchor.href, location.href);
        if (url.origin !== location.origin || !/^https?:$/.test(url.protocol)) {
            return;
        }

        event.preventDefault();

        if (url.pathname === location.pathname && url.search === location.search && url.hash === location.hash) {
            return;
        }

        history.pushState({ href: url.href }, '', url.href);
        visit(url.href);
    });

    window.addEventListener('popstate', () => visit(location.href));
});
