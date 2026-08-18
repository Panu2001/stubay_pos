<?php

namespace App\Filament\Pages;

use App\Models\Order;
use App\Models\Product;
use Filament\Actions\Action;
use Filament\Pages\Page;
use Filament\Schemas\Schema;
use Filament\Forms\Components\DatePicker;
use Filament\Schemas\Components\Grid;
use Filament\Forms\Components\Select;
use Filament\Schemas\Components\Section;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use App\Filament\Widgets\SalesReportWidget;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\DB;
use BackedEnum;
use UnitEnum;

class SalesReports extends Page implements HasTable
{
    use InteractsWithTable;

    protected static string | BackedEnum | null $navigationIcon = 'heroicon-o-document-chart-bar';
    protected static string | UnitEnum | null $navigationGroup = 'Reports';
    protected static ?string $title = 'Sales & Inventory Reports';
    
    protected string $view = 'filament.pages.sales-reports';

    public ?array $data = [];

    public function mount(): void
    {
        $this->fillForm([
            'start_date' => now()->startOfMonth()->format('Y-m-d'),
            'end_date' => now()->format('Y-m-d'),
            'type' => 'sales',
        ]);
    }

    protected function fillForm(array $data): void
    {
        $this->data = $data;
        $this->getSchema('form')->fill($data);
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Filters')
                    ->schema([
                        Grid::make(3)
                            ->schema([
                                DatePicker::make('start_date')
                                    ->label('Start Date')
                                    ->live()
                                    ->afterStateUpdated(fn () => $this->resetTable()),
                                DatePicker::make('end_date')
                                    ->label('End Date')
                                    ->live()
                                    ->afterStateUpdated(fn () => $this->resetTable()),
                                Select::make('type')
                                    ->label('Report Type')
                                    ->options([
                                        'sales' => 'Sales Detail',
                                        'inventory' => 'Inventory Status',
                                    ])
                                    ->default('sales')
                                    ->live()
                                    ->afterStateUpdated(fn () => $this->resetTable()),
                            ]),
                    ]),
            ])
            ->statePath('data');
    }

    public function table(Table $table): Table
    {
        $type = $this->data['type'] ?? 'sales';

        if ($type === 'inventory') {
            return $this->getInventoryTable($table);
        }

        return $this->getSalesTable($table);
    }

    protected function getSalesTable(Table $table): Table
    {
        return $table
            ->query(
                Order::query()
                    ->when($this->data['start_date'] ?? null, fn ($q, $date) => $q->whereDate('created_at', '>=', $date))
                    ->when($this->data['end_date'] ?? null, fn ($q, $date) => $q->whereDate('created_at', '<=', $date))
            )
            ->columns([
                TextColumn::make('created_at')->label('Date')->dateTime()->sortable(),
                TextColumn::make('order_number')->label('Order #')->searchable(),
                TextColumn::make('customer.name')->label('Customer'),
                TextColumn::make('payment_method')->badge(),
                TextColumn::make('total_cost')
                    ->label('Cost Amount')
                    ->money('LKR')
                    ->summarize(\Filament\Tables\Columns\Summarizers\Sum::make()->money('LKR')),
                TextColumn::make('total')
                    ->label('Revenue')
                    ->money('LKR')
                    ->summarize(\Filament\Tables\Columns\Summarizers\Sum::make()->money('LKR')),
                TextColumn::make('total_profit')
                    ->label('Profit')
                    ->money('LKR')
                    ->summarize(\Filament\Tables\Columns\Summarizers\Sum::make()->money('LKR')),
            ])
            ->headerActions([
                Action::make('print_pdf')
                    ->label('Print / Save PDF')
                    ->icon('heroicon-o-printer')
                    ->color('info')
                    ->url(fn () => route('admin.export.csv', [
                        'type' => 'sales',
                        'format' => 'print',
                        'start_date' => $this->data['start_date'] ?? null,
                        'end_date' => $this->data['end_date'] ?? null,
                    ]))
                    ->openUrlInNewTab(),
                Action::make('export_csv')
                    ->label('Export CSV')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->url(fn () => route('admin.export.csv', [
                        'type' => 'sales',
                        'format' => 'csv',
                        'start_date' => $this->data['start_date'] ?? null,
                        'end_date' => $this->data['end_date'] ?? null,
                    ])),
            ]);
    }

    protected function getInventoryTable(Table $table): Table
    {
        return $table
            ->query(Product::query())
            ->columns([
                TextColumn::make('name')->searchable()->sortable(),
                TextColumn::make('category.name')->label('Category'),
                TextColumn::make('price')->money('LKR'),
                TextColumn::make('cost_price')->money('LKR'),
                TextColumn::make('stock_quantity')
                    ->label('Current Stock')
                    ->badge()
                    ->color(fn ($state) => $state <= 10 ? 'danger' : 'success'),
                TextColumn::make('stock_value')
                    ->label('Stock Value (Cost)')
                    ->state(fn ($record) => $record->stock_quantity * $record->cost_price)
                    ->money('LKR'),
            ])
            ->headerActions([
                Action::make('print_pdf')
                    ->label('Print / Save PDF')
                    ->icon('heroicon-o-printer')
                    ->color('info')
                    ->url(fn () => route('admin.export.csv', [
                        'type' => 'inventory',
                        'format' => 'print',
                    ]))
                    ->openUrlInNewTab(),
                Action::make('export_csv')
                    ->label('Export CSV')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->url(fn () => route('admin.export.csv', [
                        'type' => 'inventory',
                        'format' => 'csv',
                    ])),
            ]);
    }

    protected function getHeaderWidgets(): array
    {
        if (($this->data['type'] ?? 'sales') === 'inventory') {
            return [];
        }

        return [
            SalesReportWidget::make([
                'startDate' => $this->data['start_date'] ?? null,
                'endDate' => $this->data['end_date'] ?? null,
            ]),
        ];
    }
}
