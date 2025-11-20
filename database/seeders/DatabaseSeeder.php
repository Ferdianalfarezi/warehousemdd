<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\Role;
use App\Models\User;
use App\Models\Supplier;
use App\Models\Part;
use App\Models\Barang;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create Roles
        $roles = [
            ['nama' => 'superadmin', 'deskripsi' => 'Full access to all features'],
            ['nama' => 'admin', 'deskripsi' => 'Can manage data but limited user management'],
            ['nama' => 'kepala divisi', 'deskripsi' => 'Can view and approve data'],
            ['nama' => 'operator', 'deskripsi' => 'Can only input data'],
        ];

        foreach ($roles as $role) {
            Role::create($role);
        }

        // Create Default Superadmin User
        User::create([
            'username' => 'superadmin',
            'password' => Hash::make('password'),
            'role_id' => 1, // superadmin
            'status' => 'aktif',
        ]);

        // Create Admin User
        User::create([
            'username' => 'admin',
            'password' => Hash::make('password'),
            'role_id' => 2, // admin
            'status' => 'aktif',
        ]);

        // Create Operator User
        User::create([
            'username' => 'operator',
            'password' => Hash::make('password'),
            'role_id' => 4, // operator
            'status' => 'aktif',
        ]);

        // Create Dummy Suppliers
        $supplier1 = Supplier::create([
            'nama' => 'PT. Maju Jaya',
            'alamat' => 'Jl. Industri No. 123, Jakarta',
        ]);

        $supplier2 = Supplier::create([
            'nama' => 'CV. Sukses Mandiri',
            'alamat' => 'Jl. Raya Bekasi KM 20, Bekasi',
        ]);

        // Create Dummy Parts
        $part1 = Part::create([
            'kode_barang' => 'PRT-001',
            'nama' => 'Bearing 6205',
            'stock' => 100,
            'min_stock' => 20,
            'max_stock' => 200,
            'satuan' => 'pcs',
            'address' => 'Rack A1',
            'line' => 'Line 1',
            'supplier_id' => $supplier1->id,
        ]);

        $part2 = Part::create([
            'kode_barang' => 'PRT-002',
            'nama' => 'Bolt M10',
            'stock' => 500,
            'min_stock' => 100,
            'max_stock' => 1000,
            'satuan' => 'pcs',
            'address' => 'Rack B2',
            'line' => 'Line 2',
            'supplier_id' => $supplier2->id,
        ]);

        // Create Dummy Barangs
        Barang::create([
            'kode_barang' => 'BRG-001',
            'nama' => 'Motor Listrik 1HP',
            'supplier_id' => $supplier1->id,
            'address' => 'Warehouse A',
            'line' => 'Line 1',
            'part_id' => $part1->id,
        ]);

        Barang::create([
            'kode_barang' => 'BRG-002',
            'nama' => 'Conveyor Belt System',
            'supplier_id' => $supplier2->id,
            'address' => 'Warehouse B',
            'line' => 'Line 2',
            'part_id' => $part2->id,
        ]);
    }
}