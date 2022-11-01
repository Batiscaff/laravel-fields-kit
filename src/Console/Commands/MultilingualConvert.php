<?php

namespace Batiscaff\FieldsKit\Console\Commands;

use Batiscaff\FieldsKit\Contracts\PeculiarField;
use Illuminate\Console\Command;

/**
 * Class MultilingualConvert.
 * @package App\Console\Commands
 */
class MultilingualConvert extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fields-kit:multilingual-convert';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Convert peculiar fields to multilingual format or vice versa.';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $isReverse = !config('fields-kit.multilingual.enabled', false);
        $fields    = app(PeculiarField::class)::query()->get();

        foreach ($fields as $field) {
            $field->typeInstance->convertDataToMultilingual($isReverse);
        }

        $this->withProgressBar($fields, function ($field) use ($isReverse) {
            $field->typeInstance->convertDataToMultilingual($isReverse);
        });

        return 0;
    }
}
