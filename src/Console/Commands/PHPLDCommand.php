<?php

/**
 * NOTICE OF LICENSE
 *
 * This source file is licenced under the Software License Agreement
 * that is bundled with this package in the file LICENSE.md.
 * It is also available through the world-wide-web at this URL:
 * https://intelekt.net.pl/pages/regulamin
 *
 * With the purchase or the installation of the software in your application
 * you accept the licence agreement.
 *
 * @author    Mariusz Wysokiński <kontakt@intelekt.net.pl>
 * @copyright Since 2019 INTELEKT - Usługi Komputerowe Mariusz Wysokiński
 * @license   https://intelekt.net.pl/pages/regulamin
 */

namespace N1ebieski\IDir\Console\Commands;

use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Composer;
use Illuminate\Database\DatabaseManager as DB;
use Illuminate\Contracts\Cache\Factory as Cache;
use Illuminate\Contracts\Config\Repository as Config;
use Illuminate\Contracts\Translation\Translator as Lang;

class PHPLDCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'idir:phpld {--workers=1}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import database from PHPLD directory script';

    /**
     * Undocumented function
     *
     * @param Composer $composer
     * @param Lang $lang
     * @param DB $db
     * @param Config $config
     * @param Cache $cache
     */
    public function __construct(
        protected Composer $composer,
        protected Lang $lang,
        protected DB $db,
        protected Config $config,
        protected Cache $cache
    ) {
        parent::__construct();
    }

    /**
     * Undocumented function
     *
     * @return void
     */
    protected function confirmation(): void
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
    protected function validateConnectionDatabase(): void
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
        $this->cache->store('system')->put(
            'workers',
            $this->option('workers'),
            Carbon::now()->addMinutes(5)
        );

        $bar = $this->output->createProgressBar(9);

        $this->line("iDir PHPLD importer");
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

        $this->call('vendor:publish', ['--tag' => 'icore.migrations', '--force' => true]);

        $this->line("\n");

        $this->call('vendor:publish', ['--tag' => 'idir.migrations', '--force' => true]);

        $this->line("\n");

        $bar->advance();

        $this->line("\n");

        $this->line($this->lang->get('icore::install.publish.factories'));

        $this->line("\n");

        $this->call('vendor:publish', ['--tag' => 'icore.factories', '--force' => true]);

        $this->line("\n");

        $this->call('vendor:publish', ['--tag' => 'idir.factories', '--force' => true]);

        $this->line("\n");

        $bar->advance();

        $this->line("\n");

        $this->line($this->lang->get('icore::install.publish.Seeders'));

        $this->line("\n");

        $this->call('vendor:publish', ['--tag' => 'icore.seeders', '--force' => true]);

        $this->line("\n");

        $this->call('vendor:publish', ['--tag' => 'idir.seeders', '--force' => true]);

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

        $this->call('migrate:fresh', ['--path' => 'database/migrations/vendor/icore', '--force' => true]);

        $this->line("\n");

        $this->call('migrate', ['--path' => 'database/migrations/vendor/idir', '--force' => true]);

        $this->line("\n");

        $this->call('migrate', ['--path' => 'database/migrations/2019_12_14_000001_create_personal_access_tokens_table.php', '--force' => true]);

        $this->line("\n");

        $bar->advance();

        $this->line("\n");

        $this->line($this->lang->get('icore::install.seed'));

        $this->line("\n");

        $this->call('db:seed', ['--class' => 'N1ebieski\ICore\Database\Seeders\Install\InstallSeeder', '--force' => true]);

        $this->line("\n");

        $this->call('db:seed', ['--class' => 'N1ebieski\IDir\Database\Seeders\Install\InstallSeeder', '--force' => true]);

        $this->line("\n");

        $bar->advance();

        $this->line("\n");

        $this->line($this->lang->get('icore::install.register_superadmin'));

        $this->line("\n");

        $this->call('icore:superadmin', []);

        $this->line("\n");

        $bar->advance();

        $this->line("\n");

        $this->line($this->lang->get('idir::import.seed'));

        $this->line("\n");

        $this->call('db:seed', ['--class' => 'N1ebieski\IDir\Database\Seeders\PHPLD\PHPLDSeeder', '--force' => true]);

        $this->line("\n");

        $bar->finish();
    }
}
