<x-filament-panels::page>
    <form wire:submit="save">
        {{ $this->form }}
        
        <div class="mt-6 flex items-center gap-3">
            <x-filament::button type="submit">
                Save Configurations
            </x-filament::button>

            <x-filament::button
                type="button"
                color="gray"
                icon="heroicon-o-paper-airplane"
                wire:click="sendTestEmail"
                wire:loading.attr="disabled"
            >
                Send Test Email
            </x-filament::button>
        </div>
    </form>
</x-filament-panels::page>
