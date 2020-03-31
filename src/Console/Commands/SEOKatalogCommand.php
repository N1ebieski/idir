<?php

namespace N1ebieski\IDir\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Composer;
use Illuminate\Contracts\Translation\Translator as Lang;
use Illuminate\Database\DatabaseManager as DB;
use Illuminate\Contracts\Config\Repository as Config;

class SEOKatalogCommand extends Command
{
    /**
     * [protected description]
     * @var Composer
     */
    protected $composer;

    /**
     * Undocumented variable
     *
     * @var Config
     */
    protected $config;

    /**
     * Undocumented variable
     *
     * @var Lang
     */
    protected $lang;

    /**
     * Undocumented variable
     *
     * @var DB
     */
    protected $db;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'idir:seokatalog';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import database from SEOKatalog directory script';

    /**
     * Undocumented function
     *
     * @param Composer $composer
     * @param Lang $lang
     * @param DB $db
     */
    public function __construct(Composer $composer, Lang $lang, DB $db, Config $config)
    {
        parent::__construct();

        $this->composer = $composer;
        $this->config = $config;
        $this->lang = $lang;
        $this->db = $db;
    }

    /**
     * Undocumented function
     *
     * @return void
     */
    protected function confirmation() : void
    {
        $this->info($this->lang->get('idir::import.import'));

        if (!$this->confirm($this->lang->get('icore::install.confirm'))) {
            exit;
        }
    }

    /**
     * Undocumented function
     *
     * @return void
     */
    protected function validateConnectionDatabase() : void
    {
        $this->line($this->lang->get('icore::install.validate.connection_database'));

        try {
            $this->db->connection('import')->getPdo();
        } catch (\Exception $e) {
            $this->error($e->getMessage());
            exit;
        }

        $this->info('OK');
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $bar = $this->output->createProgressBar(9);
        
        $this->line("iDir SEOKatalog importer");
        $this->line("Author: Mariusz Wysokiński");
        $this->line("Version: {$this->config->get('idir.version')}");
        $this->line("\n");
        $this->confirmation();
        $this->line("\n");
        $bar->start();
        $this->line("\n");
        $this->validateConnectionDatabase();
        $this->line("\n");
        $bar->advance();
        $this->line("\n");
        $this->line($this->lang->get('icore::install.publish.migrations'));
        $this->line("\n");
        $this->call('vendor:publish', ['--tag' => 'icore.migrations', '--force' => true], $this->getOutput());
        $this->line("\n");
        $this->call('vendor:publish', ['--tag' => 'idir.migrations', '--force' => true], $this->getOutput());
        $this->line("\n");
        $bar->advance();
        $this->line("\n");
        $this->line($this->lang->get('icore::install.publish.factories'));
        $this->line("\n");
        $this->call('vendor:publish', ['--tag' => 'icore.factories', '--force' => true], $this->getOutput());
        $this->line("\n");
        $this->call('vendor:publish', ['--tag' => 'idir.factories', '--force' => true], $this->getOutput());
        $this->line("\n");
        $bar->advance();
        $this->line("\n");
        $this->line($this->lang->get('icore::install.publish.seeds'));
        $this->line("\n");
        $this->call('vendor:publish', ['--tag' => 'icore.seeds', '--force' => true], $this->getOutput());
        $this->line("\n");
        $this->call('vendor:publish', ['--tag' => 'idir.seeds', '--force' => true], $this->getOutput());
        $this->line("\n");
        $bar->advance();
        $this->line("\n");
        $this->line($this->lang->get('icore::install.dump'));
        $this->line("\n");
        $this->composer->dumpOptimized();
        $this->line("\n");
        $this->info("OK");
        $this->line("\n");
        $bar->advance();
        $this->line("\n");
        $this->line($this->lang->get('icore::install.migrate'));
        $this->line("\n");
        $this->call('migrate:fresh', ['--path' => 'database/migrations/vendor/icore', '--force' => true], $this->getOutput());
        $this->line("\n");
        $this->call('migrate', ['--path' => 'database/migrations/vendor/idir', '--force' => true], $this->getOutput());
        $this->line("\n");
        $bar->advance();
        $this->line("\n");
        $this->line($this->lang->get('icore::install.seed'));
        $this->line("\n");
        $this->call('db:seed', ['--class' => 'N1ebieski\ICore\Seeds\Install\InstallSeeder', '--force' => true], $this->getOutput());
        $this->line("\n");
        $this->call('db:seed', ['--class' => 'N1ebieski\IDir\Seeds\Install\InstallSeeder', '--force' => true], $this->getOutput());
        $this->line("\n");
        $bar->advance();
        $this->line("\n");
        $this->line($this->lang->get('icore::install.register_superadmin'));
        $this->line("\n");
        $this->call('icore:superadmin', [], $this->getOutput());
        $this->line("\n");
        $bar->advance();
        $this->line("\n");
        $this->line($this->lang->get('idir::import.seed'));
        $this->line("\n");
        $this->call('db:seed', ['--class' => 'N1ebieski\IDir\Seeds\SEOKatalog\SEOKatalogSeeder', '--force' => true], $this->getOutput());
        $this->info("\n");
        $bar->finish();
    }
}
