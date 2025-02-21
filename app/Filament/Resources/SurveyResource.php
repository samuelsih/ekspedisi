<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SurveyResource\Pages;
use App\Models\Survey;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\HtmlString;

class SurveyResource extends Resource
{
    protected static ?string $model = Survey::class;

    protected static ?string $navigationIcon = 'heroicon-o-briefcase';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                //
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->query(function () {
                return Survey::query()
                    ->with([
                        'survey_answers:question_id,survey_id,value',
                        'survey_answers.question:id,title',
                        'customer:id,id_customer,name',
                        'channel:id,name',
                        'driver:id,nik,name',
                    ])
                    ->select(['id', 'customer_id', 'channel_id', 'driver_id', 'img_url', 'created_at', 'deleted_at'])
                    ->whereNotNull(['customer_id', 'channel_id', 'driver_id']);
            })
            ->columns([
                Tables\Columns\TextColumn::make('index')->label('No.')->rowIndex(),
                Tables\Columns\TextColumn::make('customer.id_customer')->label('ID Customer')->searchable(),
                Tables\Columns\TextColumn::make('customer.name')->label('Nama Customer')->searchable(),
                Tables\Columns\TextColumn::make('channel.name')->label('Channel'),
                Tables\Columns\TextColumn::make('driver.nik')->label('NIK Supir')->searchable(),
                Tables\Columns\TextColumn::make('driver.name')->label('Nama Supir')->searchable(),
                Tables\Columns\ImageColumn::make('img_url')
                    ->label('Validasi Gambar')
                    ->square()
                    ->size(100),
            ])
            ->filters([
                SelectFilter::make('channel_id')
                    ->relationship('channel', 'name')
                    ->label('Channel'),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make()
                    ->after(function (Survey $record) {
                        $record->survey_answers()->delete();
                    }),
                Tables\Actions\Action::make('view_survey_answers')
                    ->color('primary')
                    ->modal()
                    ->label('Detail')
                    ->icon('heroicon-o-eye')
                    ->modalSubmitAction(false)
                    ->modalCancelActionLabel('Close')
                    ->modalContent(function (Survey $record) {
                        $rows = $record->survey_answers->map(fn ($answer) => "
                            <tr class='border-b text-sm'>
                                <td class='px-4 py-2 text-left'>{$answer->question->title}</td>
                                <td class='px-4 py-2 text-center'>
                                    <div class='flex items-center justify-center space-x-1'>
                                        <span>{$answer->value}</span>
                                        <span style='color: gold;'>&#9733;</span>
                                    </div>
                                </td>
                            </tr>
                        ")->implode('');

                        return new HtmlString("
                            <table>
                                <thead>
                                    <tr>
                                        <th>Question</th>
                                        <th>Rating</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    {$rows}
                                </tbody>
                            </table>
                        ");
                    }),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->before(function (Survey $record) {
                            $record->survey_answers()->delete();
                        }),
                ]),
            ])
            ->headerActions([
                //
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
            'index' => Pages\ListSurveys::route('/'),
        ];
    }

    public static function canEdit(Model $record): bool
    {
        return false;
    }

    public static function canCreate(): bool
    {
        return false;
    }

    public static function canView(Model $record): bool
    {
        return false;
    }
}
