@props(['event' => null])
@php
    $currentUrl = $event?->cover_image ? asset('storage/'.$event->cover_image) : null;
    $fx = old('cover_focus_x', $event->cover_focus_x ?? 50);
    $fy = old('cover_focus_y', $event->cover_focus_y ?? 50);
@endphp
<div x-data="{
        src: @js($currentUrl),
        x: {{ (int) $fx }},
        y: {{ (int) $fy }},
        pick(e) {
            const r = e.currentTarget.getBoundingClientRect();
            this.x = Math.round(Math.min(100, Math.max(0, (e.clientX - r.left) / r.width * 100)));
            this.y = Math.round(Math.min(100, Math.max(0, (e.clientY - r.top) / r.height * 100)));
        },
        changed(e) {
            const file = e.target.files[0];
            if (file) { this.src = URL.createObjectURL(file); this.x = 50; this.y = 50; }
        },
        bg(ratio) {
            return 'aspect-ratio:' + ratio + ';background-size:cover;background-repeat:no-repeat;background-image:url(' + this.src + ');background-position:' + this.x + '% ' + this.y + '%';
        }
    }">
    <label class="field-label" for="poster">Affiche ou visuel du billet</label>
    <input class="field" id="poster" name="poster" type="file" accept="image/*" @change="changed($event)">
    <p class="mt-1 text-sm text-stone-600">
        Elle sert de fond à votre billet sur le site. JPG ou PNG, 5 Mo maximum.
        @if ($currentUrl) Laissez vide pour garder l'affiche actuelle. @endif
    </p>
    @error('poster') <p class="field-error">{{ $message }}</p> @enderror

    <div x-show="src" x-cloak class="mt-5">
        <p class="field-label">Quelle partie de l'affiche mettre en avant ?</p>
        <p class="text-sm text-stone-600">Cliquez sur l'affiche : le point choisi reste visible quand l'image est recadrée sur le billet.</p>

        <div class="relative mt-3 cursor-crosshair overflow-hidden rounded-xl" style="max-width: 28rem">
            <img :src="src" alt="Affiche" class="block w-full" @click="pick($event)">
            <span class="pointer-events-none absolute h-6 w-6 -translate-x-1/2 -translate-y-1/2 rounded-full border-2 border-white"
                  :style="'left:' + x + '%;top:' + y + '%;box-shadow:0 0 0 2px #e4361b'"></span>
        </div>

        <div class="mt-4 grid gap-4 sm:grid-cols-2">
            <div>
                <p class="mb-1 text-xs text-stone-500">Carte sur l'accueil</p>
                <div class="rounded-xl" :style="bg('7 / 2')"></div>
            </div>
            <div>
                <p class="mb-1 text-xs text-stone-500">Billet</p>
                <div class="rounded-xl" :style="bg('2 / 1')"></div>
            </div>
        </div>
    </div>

    <input type="hidden" name="cover_focus_x" :value="x">
    <input type="hidden" name="cover_focus_y" :value="y">
</div>
