<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\AboutUs;
use Illuminate\Support\Facades\DB;

class AboutUsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Clear existing data
        DB::table('about_us')->truncate();
        
        // Create sample entry
        AboutUs::create([
            'title' => 'O naszym sklepie',
            'content' => 'Witamy w naszym sklepie internetowym! Jesteśmy firmą z wieloletnim doświadczeniem, oferującą wysokiej jakości produkty w konkurencyjnych cenach. Naszą misją jest zapewnienie klientom najlepszych doświadczeń zakupowych i profesjonalnej obsługi.

Misja: Dostarczanie innowacyjnych rozwiązań i produktów najwyższej jakości, które spełniają oczekiwania nawet najbardziej wymagających klientów.

Wizja: Bycie liderem w branży e-commerce poprzez ciągłe doskonalenie usług i oferowanie wyjątkowych doświadczeń zakupowych.

Wartości: Jakość, innowacyjność, profesjonalizm, zaufanie klientów i ciągłe doskonalenie naszych usług.',
        ]);

        $this->command->info('About us table seeded successfully!');
    }
}
