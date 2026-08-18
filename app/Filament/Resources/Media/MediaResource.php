<?php

namespace App\Filament\Resources\Media;

use App\Filament\Resources\Media\Pages\ManageMedia;
use App\Models\Media;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\Action;
use Filament\Forms\Components\FileUpload;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Storage;

class MediaResource extends Resource
{
    protected static ?string $model = Media::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-photo';
    protected static \UnitEnum|string|null $navigationGroup = 'Store Management';
    protected static ?string $title = 'Media Library';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                FileUpload::make('path')
                    ->label('Upload New Image')
                    ->image()
                    ->disk('public')
                    ->directory('media')
                    ->visibility('public')
                    ->required()
                    ->afterStateUpdated(function ($state, $set) {
                        if ($state) {
                            $set('name', basename($state));
                            $set('url', Storage::disk('public')->url($state));
                            $set('size', Storage::disk('public')->size($state));
                            $set('mime_type', Storage::disk('public')->mimeType($state));
                        }
                    }),
                \Filament\Forms\Components\Hidden::make('name'),
                \Filament\Forms\Components\Hidden::make('url'),
                \Filament\Forms\Components\Hidden::make('size'),
                \Filament\Forms\Components\Hidden::make('mime_type'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->columns([
                ImageColumn::make('path')
                    ->label('Preview')
                    ->disk('public')
                    ->square(),
                TextColumn::make('name')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('size')
                    ->formatStateUsing(fn ($state) => number_format($state / 1024, 2) . ' KB'),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                Action::make('copy_url')
                    ->label('Copy URL')
                    ->icon('heroicon-o-clipboard')
                    ->color('info')
                    ->action(fn ($record) => $this->dispatch('copy-to-clipboard', url: url($record->url))),
                DeleteAction::make()
                    ->after(fn ($record) => Storage::disk('public')->delete($record->path)),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->after(fn (\Illuminate\Support\Collection $records) => $records->each(fn ($record) => Storage::disk('public')->delete($record->path))),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ManageMedia::route('/'),
        ];
    }
}
