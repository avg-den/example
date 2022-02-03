<?php


namespace App\Rapture\Repositories;

use App\Rapture\Database\Selectable;
use App\Rapture\Files\c;
use App\Rapture\Sync\SyncGenerator;
use App\Rapture\Tools\Tools;

/**
 * Class BaseRepository
 * @package App\Rapture\Repositories
 * Содержит подключение синхронизатора, работы с файлами и тулера
 */
abstract class BaseRepository extends Selectable
{
    protected SyncGenerator $SYNC;
    protected SyncGenerator $FILES;
    protected Tools $TOOLS;


    public function __construct()
    {
          parent::__construct();
//        $this->SYNC  = inj(FilesGenerator::class);
//        $this->FILES = inj(SyncGenerator::class);
//        $this->TOOLS = inj(Tools::class);
    }
}