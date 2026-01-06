<?php

namespace App\Console\Commands;

use App\Modules\User\Models\Hobby;
use Illuminate\Console\Command;

class PruneHobbies extends Command
{
    protected $signature = 'app:prune-hobbies';
    protected $description = 'Menghapus hobi yang tidak terpakai';
    public function handle()
    {
        $deleted = Hobby::doesntHave('users')->delete();
        $this->info("Berhasil menghapus {$deleted} hobi sampah (typo/tidak terpakai).");
    }
}
