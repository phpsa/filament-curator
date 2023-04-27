<?php

namespace Awcodes\Curator\Resources\MediaResource;

use Awcodes\Curator\Resources\MediaResource;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;

class ListMedia extends ListRecords
{
    protected static string $resource = MediaResource::class;

    public function getTitle(): string
    {
        return Str::headline(app('curator')->getPluralResourceLabel());
    }

    protected function getActions(): array
    {
        return [
            Action::make('toggle-table-view')
                ->color('secondary')
                ->label(function (): string {
                    return Session::get('tableLayout') ? __('curator::tables.actions.toggle_table_list') : __('curator::tables.actions.toggle_table_grid');
                })
                ->icon(function (): string {
                    return Session::get('tableLayout') ? 'heroicon-s-list-bullet' : 'heroicon-s-squares-2x2';
                })
                ->action(function (): void {
                    Session::put('tableLayout', ! Session::get('tableLayout'));
                }),
            CreateAction::make(),
        ];
    }
}
