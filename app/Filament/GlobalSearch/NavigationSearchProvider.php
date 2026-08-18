<?php

namespace App\Filament\GlobalSearch;

use Filament\Facades\Filament;
use Filament\GlobalSearch\Providers\Contracts\GlobalSearchProvider;
use Filament\GlobalSearch\GlobalSearchResult;
use Filament\GlobalSearch\GlobalSearchResults;
use Illuminate\Support\Str;

class NavigationSearchProvider implements GlobalSearchProvider
{
    public function getResults(string $query): ?GlobalSearchResults
    {
        $query = Str::lower($query);
        $results = GlobalSearchResults::make();
        
        $navigation = Filament::getNavigation();
        
        $matches = [];
        
        foreach ($navigation as $group) {
            // Some versions return items directly, others return groups
            if (method_exists($group, 'getItems')) {
                foreach ($group->getItems() as $item) {
                    if (Str::contains(Str::lower($item->getLabel()), $query)) {
                        $matches[] = new GlobalSearchResult(
                            title: $item->getLabel(),
                            url: $item->getUrl(),
                            details: $group->getLabel() ? "Settings > {$group->getLabel()}" : 'Dashboard Setting',
                        );
                    }
                }
            }
        }
        
        if (empty($matches)) {
            return null;
        }

        $results->category('Settings & Navigation', $matches);

        return $results;
    }
}
