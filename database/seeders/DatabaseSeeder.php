<?php

namespace Database\Seeders;

use App\Models\Article;
use App\Models\CampingItem;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        foreach ([
            ['name' => 'Admin Demo', 'email' => 'admin@demo.test', 'password' => 'Admin#2026', 'role' => 'admin', 'phone' => '081234560001'],
            ['name' => 'Staff Demo', 'email' => 'staff@demo.test', 'password' => 'Staff#2026', 'role' => 'staff', 'phone' => '081234560002'],
            ['name' => 'User Demo', 'email' => 'user@demo.test', 'password' => 'User#2026', 'role' => 'user', 'phone' => '081234560003'],
        ] as $account) {
            User::updateOrCreate(['email' => $account['email']], [...$account, 'password' => Hash::make($account['password'])]);
        }

        foreach ([
            ['name' => 'Tenda Dome 4 Orang', 'category' => 'Tenda', 'description' => 'Tenda dome untuk empat orang, disertai rainfly dan pasak.', 'stock_total' => 8, 'stock_damaged' => 0, 'daily_rate' => 85000],
            ['name' => 'Sleeping Bag Hangat', 'category' => 'Tidur', 'description' => 'Sleeping bag untuk kegiatan berkemah di udara sejuk.', 'stock_total' => 15, 'stock_damaged' => 1, 'daily_rate' => 18000],
            ['name' => 'Kompor Portable', 'category' => 'Masak', 'description' => 'Kompor satu tungku dengan tas penyimpanan.', 'stock_total' => 10, 'stock_damaged' => 0, 'daily_rate' => 25000],
            ['name' => 'Carrier 50 Liter', 'category' => 'Tas', 'description' => 'Tas carrier untuk membawa perlengkapan perjalanan.', 'stock_total' => 6, 'stock_damaged' => 0, 'daily_rate' => 40000],
            ['name' => 'Matras Camping', 'category' => 'Tidur', 'description' => 'Matras gulung sebagai alas tidur saat berkemah.', 'stock_total' => 12, 'stock_damaged' => 0, 'daily_rate' => 12000],
            ['name' => 'Headlamp Camping', 'category' => 'Penerangan', 'description' => 'Lampu kepala untuk menerangi jalur dan area perkemahan saat malam.', 'stock_total' => 10, 'stock_damaged' => 0, 'daily_rate' => 10000],
            ['name' => 'Trekking Pole', 'category' => 'Pendakian', 'description' => 'Tongkat trekking untuk membantu menjaga keseimbangan saat berjalan di jalur pendakian.', 'stock_total' => 8, 'stock_damaged' => 0, 'daily_rate' => 12000],
            ['name' => 'Jas Hujan Outdoor', 'category' => 'Pakaian', 'description' => 'Jaket hujan ringan untuk melindungi tubuh saat beraktivitas di luar ruang.', 'stock_total' => 10, 'stock_damaged' => 0, 'daily_rate' => 15000],
        ] as $item) {
            CampingItem::updateOrCreate(['name' => $item['name']], [...$item, 'is_available' => true]);
        }

        foreach ([
            ['title' => 'Tips memilih tenda untuk pemula', 'slug' => 'tips-memilih-tenda-untuk-pemula', 'excerpt' => 'Cocokkan kapasitas, kondisi cuaca, dan kemudahan pemasangan sebelum memilih tenda.', 'content' => "Tentukan jumlah orang yang akan tidur di dalam tenda dan sisakan ruang untuk barang bawaan.\n\nPeriksa kondisi cuaca dan pilih tenda dengan lapisan pelindung yang sesuai. Pastikan semua pasak dan tiang ada sebelum berangkat.\n\nSaat mengambil alat, tanyakan kepada staf jika Anda belum pernah memasang model tenda tersebut."],
            ['title' => 'Checklist alat camping', 'slug' => 'checklist-alat-camping', 'excerpt' => 'Periksa perlengkapan tidur, memasak, pencahayaan, dan barang pribadi sebelum berangkat.', 'content' => "Perlengkapan tidur: tenda, matras, dan sleeping bag.\n\nPerlengkapan memasak: kompor portable dan alat makan yang sesuai.\n\nSebelum meninggalkan rumah, cocokkan jumlah barang dengan rencana perjalanan. Saat pengambilan dan pengembalian, cek kondisi setiap alat bersama staf."],
            ['title' => 'Glamping minimalis', 'slug' => 'glamping-minimalis', 'excerpt' => 'Bawa seperlunya dan pilih alat yang mendukung kebutuhan utama perjalanan.', 'content' => "Glamping minimalis berawal dari rencana yang jelas. Pilih alat sesuai jumlah orang dan lama kegiatan, lalu hindari membawa perlengkapan yang tidak akan digunakan.\n\nPastikan tempat menginap menyediakan fasilitas yang Anda perlukan. Periksa daftar barang sewaan sebelum menyelesaikan reservasi."],
        ] as $article) {
            Article::updateOrCreate(['slug' => $article['slug']], [...$article, 'is_published' => true]);
        }
    }
}
