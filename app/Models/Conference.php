<?php

namespace App\Models;

use App\Enums\Region;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Fieldset;
use Filament\Forms\Components\MarkdownEditor;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Get;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Conference extends Model
{
    use HasFactory;

    protected $casts = [
        'id' => 'integer',
        'start_date' => 'date',
        'end_date' => 'date',
        'region' => Region::class,
        'venue_id' => 'integer',
    ];

    public function venue(): BelongsTo
    {
        return $this->belongsTo(Venue::class);
    }

    public function speakers(): BelongsToMany
    {
        return $this->belongsToMany(Speaker::class);
    }

    public function talks(): BelongsToMany
    {
        return $this->belongsToMany(Talk::class);
    }

    public static function getForm()
    {
        return [
            Tabs::make()
                ->columnSpanFull()
                ->tabs([
                    Tabs\Tab::make('Conference Details')
                        ->schema([
                            TextInput::make('name')
                                ->columnSpanFull()
                                ->label('Conference Name')
                                ->required()
                                ->default('my conference here')
                                ->hint('here is the hint')
                                ->maxLength(60),
                            MarkdownEditor::make('description')
                                ->columnSpan(1)
                                ->required(),
                            DatePicker::make('start_date')
                                ->native(false)
                                ->required(),
                            DatePicker::make('end_date')
                                ->native(false)
                                ->required(),
                            Fieldset::make('status')
                                ->columns(1)
                                ->schema([
                                    Select::make('status')
                                        ->options([
                                            'draft' => 'Draft',
                                            'published' => 'Published',
                                            'archived' => 'Archived',
                                        ])
                                        ->required(),
                                    Toggle::make('is_published')
                                        ->default(true),

                                ]),
                        ]),
                    Tabs\Tab::make('Locations')
                        ->columns(1)
                        ->schema([
                            Select::make('region')
                                ->enum(Region::class)
                                ->live()
                                ->options(Region::class),
                            Select::make('venue_id')
                                ->searchable()
                                ->preload()
                                ->createOptionForm(Venue::getForm())
                                ->editOptionForm(Venue::getForm())
                                ->relationship('venue', 'name', function (Builder $query, Get $get) {
                                    return $query->where('region', $get('region'));
                                }),
                        ])

                ]),


//            Section::make('Conference Details')
//                ->columns(2)
//            ->schema([
//
//            ]),

//            Section::make('Location')
//            ->schema([
//
//            ]),


//            CheckboxList::make('speakers')
//                ->relationship('speakers', 'name')
//                ->columnSpanFull()
//                ->options(
//                    Speaker::all()->pluck('name', 'id')
//                )
//                ->required(),
        ];
    }
}
