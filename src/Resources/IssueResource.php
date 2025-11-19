<?php

namespace Visiosoft\Kanban\Resources;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Visiosoft\Kanban\Models\Issue;
use Visiosoft\Kanban\Resources\IssueResource\Pages;

class IssueResource extends Resource
{
    protected static ?string $model = Issue::class;

    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-list';

    protected static ?string $navigationGroup = 'Kanban';

    protected static ?string $navigationLabel = 'Görevler';

    protected static ?string $modelLabel = 'Görev';

    protected static ?string $pluralModelLabel = 'Görevler';

    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Görev Detayları')
                    ->schema([
                        Forms\Components\Select::make('board_id')
                            ->label('Pano')
                            ->relationship('board', 'name')
                            ->required()
                            ->searchable()
                            ->preload(),

                        Forms\Components\TextInput::make('title')
                            ->label('Başlık')
                            ->required()
                            ->maxLength(255)
                            ->columnSpan('full'),

                        Forms\Components\RichEditor::make('description')
                            ->label('Açıklama')
                            ->columnSpan('full'),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Durum & Öncelik')
                    ->schema([
                        Forms\Components\Select::make('status')
                            ->label('Durum')
                            ->options(config('kanban.statuses', [
                                'backlog' => 'Beklemede',
                                'todo' => 'Yapılacak',
                                'in_progress' => 'Devam Ediyor',
                                'review' => 'İncelemede',
                                'done' => 'Tamamlandı',
                            ]))
                            ->default('backlog')
                            ->required()
                            ->native(false),

                        Forms\Components\Select::make('priority')
                            ->label('Öncelik')
                            ->options(config('kanban.priorities', [
                                'low' => 'Düşük',
                                'medium' => 'Orta',
                                'high' => 'Yüksek',
                                'urgent' => 'Acil',
                            ]))
                            ->default('medium')
                            ->required()
                            ->native(false),

                        Forms\Components\TextInput::make('order')
                            ->label('Sıra')
                            ->numeric()
                            ->default(0)
                            ->required(),
                    ])
                    ->columns(3),

                Forms\Components\Section::make('Atama & Tarihler')
                    ->schema([
                        Forms\Components\Select::make('assigned_to')
                            ->label('Atanan Kişi')
                            ->relationship('assignedUser', 'name')
                            ->searchable()
                            ->preload()
                            ->nullable(),

                        Forms\Components\DateTimePicker::make('start_at')
                            ->label('Başlangıç Tarihi')
                            ->nullable(),

                        Forms\Components\DatePicker::make('due_date')
                            ->label('Bitiş Tarihi')
                            ->nullable(),
                    ])
                    ->columns(3),

                Forms\Components\Section::make('Etiketler')
                    ->schema([
                        Forms\Components\TagsInput::make('tags')
                            ->label('Etiketler')
                            ->placeholder('Etiket ekle')
                            ->nullable(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('board.name')
                    ->label('Pano')
                    ->searchable()
                    ->sortable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('title')
                    ->label('Başlık')
                    ->searchable()
                    ->sortable()
                    ->limit(50),

                Tables\Columns\SelectColumn::make('status')
                    ->label('Durum')
                    ->options(config('kanban.statuses', [
                        'backlog' => 'Beklemede',
                        'todo' => 'Yapılacak',
                        'in_progress' => 'Devam Ediyor',
                        'review' => 'İncelemede',
                        'done' => 'Tamamlandı',
                    ]))
                    ->selectablePlaceholder(false),

                Tables\Columns\BadgeColumn::make('priority')
                    ->label('Öncelik')
                    ->colors([
                        'secondary' => 'low',
                        'primary' => 'medium',
                        'warning' => 'high',
                        'danger' => 'urgent',
                    ])
                    ->sortable(),

                Tables\Columns\TextColumn::make('assignedUser.name')
                    ->label('Atanan Kişi')
                    ->searchable()
                    ->sortable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('due_date')
                    ->label('Bitiş Tarihi')
                    ->date()
                    ->sortable()
                    ->color(fn ($record) => $record->due_date && $record->due_date->isPast() && $record->status !== 'done' ? 'danger' : null)
                    ->toggleable(),

                Tables\Columns\TextColumn::make('order')
                    ->label('Sıra')
                    ->numeric()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Oluşturulma')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Güncellenme')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('order')
            ->filters([
                Tables\Filters\SelectFilter::make('board_id')
                    ->label('Pano')
                    ->relationship('board', 'name')
                    ->searchable()
                    ->preload(),

                Tables\Filters\SelectFilter::make('status')
                    ->label('Durum')
                    ->options(config('kanban.statuses', [
                        'backlog' => 'Beklemede',
                        'todo' => 'Yapılacak',
                        'in_progress' => 'Devam Ediyor',
                        'review' => 'İncelemede',
                        'done' => 'Tamamlandı',
                    ]))
                    ->multiple(),

                Tables\Filters\SelectFilter::make('priority')
                    ->label('Öncelik')
                    ->options(config('kanban.priorities', [
                        'low' => 'Düşük',
                        'medium' => 'Orta',
                        'high' => 'Yüksek',
                        'urgent' => 'Acil',
                    ]))
                    ->multiple(),

                Tables\Filters\Filter::make('overdue')
                    ->query(fn ($query) => $query->overdue())
                    ->label('Gecikmiş'),

                Tables\Filters\TrashedFilter::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
                Tables\Actions\ForceDeleteAction::make(),
                Tables\Actions\RestoreAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\ForceDeleteBulkAction::make(),
                    Tables\Actions\RestoreBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListIssues::route('/'),
            'create' => Pages\CreateIssue::route('/create'),
            'edit' => Pages\EditIssue::route('/{record}/edit'),
        ];
    }
}
