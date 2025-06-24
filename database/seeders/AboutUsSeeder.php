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
        // Upewnij się, że tabela jest pusta przed wypełnieniem
        DB::table('about_us')->truncate();
        
        // Tworzenie przykładowego wpisu
        AboutUs::create([
            'title' => 'O naszym sklepie',
            'content' => 'Witamy w naszym sklepie internetowym! Jesteśmy firmą z wieloletnim doświadczeniem, oferującą wysokiej jakości produkty w konkurencyjnych cenach. Naszą misją jest zapewnienie klientom najlepszych doświadczeń zakupowych i profesjonalnej obsługi.',
            'mission' => 'Dostarczanie innowacyjnych rozwiązań i produktów najwyższej jakości, które spełniają oczekiwania nawet najbardziej wymagających klientów.',
            'vision' => 'Bycie liderem w branży e-commerce poprzez ciągłe doskonalenie usług i oferowanie wyjątkowych doświadczeń zakupowych.',
            'values' => 'Jakość, innowacyjność, profesjonalizm, zaufanie klientów i ciągłe doskonalenie naszych usług.',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $this->command->info('Tabela about_us została pomyślnie wypełniona przykładowymi danymi!');
    }
}
