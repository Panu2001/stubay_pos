<?php

namespace App\Filament\Resources\Lends\Lends\Pages;

use App\Filament\Resources\Lends\Lends\LendResource;
use App\Models\Setting;
use Filament\Actions\Action;
use Filament\Resources\Pages\EditRecord;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\View;

class CustomerLendHistory extends EditRecord
{
    protected static string $resource = LendResource::class;

    protected static ?string $title = 'Customer Lend History';

    protected function getFormActions(): array
    {
        return [];
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        return $data;
    }

    public function getTitle(): string
    {
        return 'Lend History - ' . $this->getRecord()->name;
    }

    public function content(Schema $schema): Schema
    {
        return $schema
            ->components([
                View::make('filament.resources.lends.customer-lend-history')
                    ->viewData(fn (): array => $this->getCustomerLendHistoryViewData()),
            ]);
    }

    public function getRelationManagers(): array
    {
        return [];
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('back_to_lends')
                ->label('Back to Customer Lends')
                ->icon('heroicon-o-arrow-left')
                ->color('gray')
                ->url(fn () => LendResource::getUrl('edit', ['record' => $this->getRecord()])),
        ];
    }

    private function getCustomerLendHistoryViewData(): array
    {
        $customer = $this->getRecord();
        $lends = $customer
            ->lends()
            ->with(['order.items'])
            ->orderByDesc('created_at')
            ->get();

        $totalLends = 0;
        $totalPaid = 0;
        $outstanding = 0;
        $openLends = 0;

        foreach ($lends as $lend) {
            $totalLends += (float) $lend->total_amount;
            $totalPaid += (float) $lend->paid_amount;
            $outstanding += (float) $lend->remaining_amount;

            if ($lend->status !== 'paid') {
                $openLends++;
            }
        }

        return [
            'customer' => $customer,
            'currency' => Setting::get('currency', '$'),
            'lends' => $lends,
            'stats' => [
                'total_lends' => $totalLends,
                'total_paid' => $totalPaid,
                'outstanding' => $outstanding,
                'open_lends' => $openLends,
            ],
        ];
    }
}
