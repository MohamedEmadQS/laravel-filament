<?php

namespace App\Filament\Resources;

use App\Filament\Resources\EmployeeResource\Pages;
use App\Filament\Resources\EmployeeResource\RelationManagers;
use App\Models\City;
use App\Models\Employee;
use App\Models\State;
use DeepCopy\Filter\Filter;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Infolists\Components\Section;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Collection;

class EmployeeResource extends Resource
{
    protected static ?string $model = Employee::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('User Name')
                    ->description('Put the user name details in.')
                    ->schema([
                        Forms\Components\TextInput::make('first_name')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('last_name')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('middle_name')
                            ->required()
                            ->maxLength(255),
                    ])->columns(3),
                Forms\Components\Section::make('Country')
                    ->schema([
                        Forms\Components\Select::make('country_id')
                            ->required()
                            ->searchable()
                            ->preload()
                            ->live()
                            ->relationship('country', titleAttribute: 'name')
                            ->afterStateUpdated(function (Set $set) {
                                $set('state_id', null);
                                $set('city_id', null);
                            })
                            ->native(false),
                        Forms\Components\Select::make('state_id')
                            ->required()
                            ->label('State')
                            ->searchable()
                            ->preload()
                            ->live()
                            ->afterStateUpdated(fn (Set $set) => $set('city_id', null))
                            ->options(fn (Get $get): Collection => State::query()
                                ->where('country_id', $get('country_id'))
                                ->pluck('name', 'id'))
                            ->native(false),
                        Forms\Components\Select::make('city_id')
                            ->required()
                            ->searchable()
                            ->label('City')
                            ->live()
                            ->preload()
                            ->options(fn (Get $get): Collection => City::query()
                                ->where('state_id', $get('state_id'))
                                ->pluck('name', 'id'))
                            ->native(false),
                    ])->columns(3),

                Forms\Components\Section::make('Department Details')
                    ->description('Employee Department')
                    ->schema([
                        Forms\Components\Select::make('department_id')
                            ->required()
                            ->searchable()
                            // ->multiple()
                            ->preload()
                            ->relationship('department', titleAttribute: 'name')
                            ->native(false),
                    ]),
                Forms\Components\Section::make('User Address')
                    ->description('Put the address details in.')
                    ->schema([
                        Forms\Components\TextInput::make('address')
                            ->required()
                            ->maxLength(255)
                            ->columnSpan(2),
                        Forms\Components\TextInput::make('zip_code')
                            ->required()
                            ->maxLength(255)
                            ->columnSpan(2),
                    ])->columns(4),

                Forms\Components\DatePicker::make('date_of_birth')
                    ->required()
                    ->native(false),
                Forms\Components\DatePicker::make('date_hired')
                    ->required()
                    ->native(false)
                    // ->columnSpanFull()
                    ->columnSpan(2),
            ])->columns(4);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('country.name')
                    ->sortable()
                    ->searchable(isIndividual: true),
                Tables\Columns\TextColumn::make('state.name')
                    ->sortable()
                    ->searchable(isIndividual: true),
                Tables\Columns\TextColumn::make('city.name')
                    ->sortable()
                    ->searchable(isIndividual: true),
                Tables\Columns\TextColumn::make('department.name')
                    ->sortable(),
                Tables\Columns\TextColumn::make('first_name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('last_name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('middle_name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('address')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('zip_code')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('date_of_birth')
                    ->date()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('date_hired')
                    ->date()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\TrashedFilter::make(),
                SelectFilter::make('country_id')->searchable()->preload()->relationship('country', titleAttribute: 'name')->label('Country'),
                SelectFilter::make('state_id')->relationship('state', titleAttribute: 'name')->label('State'),
                SelectFilter::make('city_id')->relationship('city', titleAttribute: 'name')->label('City'),
            ], FiltersLayout::AboveContentCollapsible)->filtersFormColumns(4)
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make()
                    ->successNotificationTitle('Employee  Deleted Successfully'),
            ])
            ->defaultSort('first_name', 'desc')
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\ForceDeleteBulkAction::make(),
                    Tables\Actions\RestoreBulkAction::make(),
                ]),
            ]);
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::where('team_id', auth()->user()->team_id)->count();
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return static::getModel()::count() > 10 ? 'warning' : 'primary';
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Section::make('Full NAME')
                    ->description('NAME OF THE EMPLOYEE')
                    ->schema([
                        TextEntry::make('first_name')->label('First Name'),
                        TextEntry::make('middle_name')->label('middle Name'),
                        TextEntry::make('last_name')->label('last Name'),
                    ])->columns(3),
                Section::make('Address')
                    ->description('ADDRESS OF THE EMPLOYEE')
                    ->schema([
                        TextEntry::make('address')->label('Address'),
                        TextEntry::make('zip_code')->label('Zip Code'),
                    ])->columns(2),
                Section::make('Date Of Birth')
                    ->description('DATE OF BIRTH OF THE EMPLOYEE')
                    ->schema([
                        TextEntry::make('date_of_birth')->label('Date Of Birth'),
                    ])->columns(1),
                TextEntry::make('department.name')->label('Department')
            ]);
    }
    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListEmployees::route('/'),
            'create' => Pages\CreateEmployee::route('/create'),
            // 'view' => Pages\ViewEmployee::route('/{record}'),
            'edit' => Pages\EditEmployee::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }
}
