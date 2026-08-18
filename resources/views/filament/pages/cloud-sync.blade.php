<x-filament-panels::page>
    <div class="space-y-4">
        <div class="p-4 bg-white dark:bg-gray-800 rounded-lg shadow">
            <h2 class="text-lg font-bold">How Cloud Synchronization Works</h2>
            <p class="mt-2 text-gray-600 dark:text-gray-300">
                To prevent data conflicts between your offline application and your online website, the synchronization is split into two secure steps:
            </p>
            <ul class="list-disc pl-5 mt-2 space-y-1 text-gray-600 dark:text-gray-300">
                <li><strong>Pull Products:</strong> Downloads the latest Products and Categories from your live website. <em>Warning: This replaces your local products with the cloud version.</em></li>
                <li><strong>Push Sales:</strong> Securely uploads all your local Lends and Orders (Sales) to the cloud. Only new, unsynced sales are pushed to prevent duplicates.</li>
            </ul>
        </div>
    </div>
</x-filament-panels::page>
