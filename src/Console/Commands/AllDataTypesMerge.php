<?php

namespace Joy\VoyagerMerge\Console\Commands;

use Illuminate\Console\Command;
use Maatwebsite\Excel\Excel;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

class AllDataTypesMerge extends Command
{
    protected $name = 'joy-merge:all';

    protected $description = 'Joy Voyager all DataTypes mergeer';

    public function handle()
    {
        $this->output->title('Starting merge');
        $this->mergeAllDataTypes(
            $this->argument('path'),
            $this->option('disk'),
            $this->option('readerType')
        );
        $this->output->success('Merge successful');
    }

    protected function mergeAllDataTypes(
        string $path,
        string $disk = null,
        string $readerType = Excel::XLSX
    ) {
        $this->output->info(sprintf(
            'Mergeing from <<' . PHP_EOL . 'path : %s',
            $path,
        ));

        $mergeClass = 'joy-voyager-merge.merge';

        $merge = app($mergeClass);

        $merge->set(
            $this->options()
        )->withOutput(
            $this->output
        )->merge(
            $path,
            $disk,
            $readerType
        );
    }

    /**
     * Get the console command arguments.
     *
     * @return array
     */
    protected function getArguments()
    {
        return [
            ['path', InputArgument::REQUIRED, 'The merge file path'],
        ];
    }

    /**
     * Get the console command options.
     *
     * @return array
     */
    protected function getOptions()
    {
        return [
            [
                'disk',
                'd',
                InputOption::VALUE_OPTIONAL,
                'The disk to where you want to merge',
                config('joy-voyager-merge.disk')
            ],
            [
                'readerType',
                'w',
                InputOption::VALUE_OPTIONAL,
                'The readerType in which format you want to merge',
                config('joy-voyager-merge.readerType', 'Xlsx')
            ],
        ];
    }
}
