<x-public-layout :title="'Billet '.$ticket->code">
    <div class="mx-auto max-w-md px-6 py-12">
        <x-ticket-card :ticket="$ticket" />
    </div>
</x-public-layout>
