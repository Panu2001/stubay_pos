<div>
    <?php
$__split = function ($name, $params = []) {
    return [$name, $params];
};
[$__name, $__params] = $__split(\Filament\Resources\Pages\ManageRecords::class, ['resource' => $resource]);

$__keyOuter = $__key ?? null;

$__key = null;
$__componentSlots = [];

$__key ??= \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::generateKey('lw-3566699470-0', $__key);

$__html = app('livewire')->mount($__name, $__params, $__key, $__componentSlots);

echo $__html;

unset($__html);
unset($__key);
$__key = $__keyOuter;
unset($__keyOuter);
unset($__name);
unset($__params);
unset($__componentSlots);
unset($__split);
?>

    <div x-data x-on:print-cash-receipt.window="window.open($event.detail.url, '_blank')">
    </div>
</div>
<?php /**PATH C:\Users\kanes\OneDrive\Documents\pos\resources\views\filament\resources\cash-entry\manage.blade.php ENDPATH**/ ?>