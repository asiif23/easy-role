<?php

namespace Asiifdev\EasyRole\Commands;

use Illuminate\Console\Command;
use Asiifdev\EasyRole\Models\Role;
use Carbon\Carbon;

class BuatRole extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'easy-role:buat-role 
                            {nama : Masukkan Nama Rolenya}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command Untuk membuat role baru';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $this->info('Proses Pembuatan Role Baru..');
        $roles = Role::all();
        $progressBar = $this->output->createProgressBar($roles->count());
        $progressBar->setMessage('Meghitung Total Role yang sudah ada..');
        $progressBar->start();
        $progressBar->setBarCharacter('*');
        foreach($roles as $customer) {
            //Do whatever processing that is required per customer
            $progressBar->advance();
        }
        $progressBar->finish();
        $this->newLine();
        $cek = Role::where('name', $this->argument('nama'))->get();
        $this->alert('Total Role yang sudah ada ' . count($roles));
        $this->table(
            ['Nama role', 'dibuat pada'],
            Role::all(['name', 'created_at'])->toArray(), 'default'
        );
        if(count($cek) > 0){
            $this->error('Role dengan nama ' . $this->argument('nama') . ' Sudah ada!');
            return;
        }
        else{
            Role::create([
                'name' => $this->argument('nama'),
                'guard_name' => 'web',
            ]);
            // if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
            //     system('cls');
            // } else {
                // }
            system('clear');
            system('cls');
            $created = Carbon::now()->format('Y-m-d H:i:s');
            $this->alert('Role dengan nama ' . $this->argument('nama') . ' Berhasil dibuat pada ' . $created);
            $this->table(
                ['Nama role', 'dibuat pada'],
                Role::all(['name', 'created_at'])->toArray(), 'default'
            );
        }
    }
}
