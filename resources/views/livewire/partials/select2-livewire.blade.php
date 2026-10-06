<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css">
<script>
    window.ckLoadSelect2 ??= () => {
        if (window.jQuery?.fn?.select2) {
            return Promise.resolve();
        }

        const muat = (src) => new Promise((resolve, reject) => {
            const script = document.createElement('script');
            script.src = src;
            script.onload = resolve;
            script.onerror = reject;
            document.head.appendChild(script);
        });

        window.__ckSelect2Promise ??= (window.jQuery
            ? Promise.resolve()
            : muat('https://cdn.jsdelivr.net/npm/jquery@3.7.1/dist/jquery.min.js')
        ).then(() => window.jQuery.fn.select2
            ? null
            : muat('https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js'));

        return window.__ckSelect2Promise;
    };

    window.ckSelect2Livewire ??= (el, $wire, model) => {
        window.ckLoadSelect2().then(() => {
            if (!el.isConnected) {
                return;
            }

            const $el = window.jQuery(el);
            if ($el.data('select2')) {
                return;
            }

            $el.select2({
                width: '100%',
                placeholder: el.dataset.placeholder,
                allowClear: true,
                dropdownParent: window.jQuery(document.body),
                language: {
                    noResults: () => el.dataset.empty,
                    searching: () => 'Mencari…',
                },
            });
            $el.val(String($wire.get(model) ?? '')).trigger('change.select2');
            $el.on('change', () => $wire.set(model, $el.val() || '', false));

            $wire.$watch(model, (nilai) => {
                nilai = String(nilai ?? '');
                if (el.isConnected && ($el.val() || '') !== nilai) {
                    $el.val(nilai).trigger('change.select2');
                }
            });
        });
    };
</script>
