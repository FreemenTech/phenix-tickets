<x-public-layout title="Scanner les billets">
    <div class="mx-auto max-w-lg px-6 py-10">
        <h1 class="font-display text-3xl font-bold">Scanner les billets</h1>
        <p class="mt-2 text-stone-600">Placez le QR code du billet devant la caméra.</p>

        <div id="reader" class="mt-6 overflow-hidden rounded-3xl bg-black"></div>
        <div id="result" class="hidden"></div>

        <form id="manual" class="mt-8 flex gap-3">
            <input id="manual-code" class="field" placeholder="Ou saisissez le code du billet" autocomplete="off">
            <button type="submit" class="btn-outline">Valider</button>
        </form>
    </div>

    <script src="https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>
    <script>
        const csrf = @js(csrf_token());
        const validateUrl = @js(route('scanner.validate'));
        const box = document.getElementById('result');
        let busy = false;
        let lastCode = '';
        let lastAt = 0;

        function show(data) {
            const ok = data.result === 'ok';
            box.className = 'mt-6 rounded-3xl p-6 text-center ' + (ok ? 'bg-green-100 text-green-900' : 'bg-red-100 text-red-900');
            box.replaceChildren();

            const title = document.createElement('p');
            title.className = 'font-display text-2xl font-bold';
            title.textContent = data.title || 'Erreur';
            box.appendChild(title);

            (data.lines || []).forEach(function (line) {
                const p = document.createElement('p');
                p.className = 'mt-1 text-sm';
                p.textContent = line;
                box.appendChild(p);
            });
        }

        async function check(code) {
            const now = Date.now();
            // On ignore le même QR scanné en boucle pendant 8 secondes
            if (busy || (code === lastCode && now - lastAt < 8000)) { return; }
            busy = true;
            lastCode = code;
            lastAt = now;

            try {
                const response = await fetch(validateUrl, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': csrf
                    },
                    body: JSON.stringify({ code: code })
                });
                show(await response.json());
            } catch (e) {
                show({ result: 'error', title: 'Erreur réseau', lines: ['Réessayez.'] });
            }

            busy = false;
        }

        document.getElementById('manual').addEventListener('submit', function (event) {
            event.preventDefault();
            const input = document.getElementById('manual-code');
            if (input.value.trim() !== '') {
                lastCode = '';
                check(input.value.trim());
                input.value = '';
            }
        });

        const scanner = new Html5Qrcode('reader');
        scanner.start(
            { facingMode: 'environment' },
            { fps: 10, qrbox: { width: 250, height: 250 } },
            function (text) { check(text); },
            function () {}
        ).catch(function () {
            document.getElementById('reader').innerHTML = '<p class="p-6 text-white">Caméra indisponible. Utilisez la saisie manuelle ci-dessous.</p>';
        });
    </script>
</x-public-layout>
