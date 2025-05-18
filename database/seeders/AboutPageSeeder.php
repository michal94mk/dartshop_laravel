<?php

namespace Database\Seeders;

use App\Models\AboutPage;
use Illuminate\Database\Seeder;

class AboutPageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Usuwamy wszystkie istniejące strony "O nas" przed dodaniem nowych
        AboutPage::truncate();

        $aboutPages = [
            [
                'title' => 'O DartShop',
                'content' => '<p>DartShop to specjalistyczny sklep internetowy z wyposażeniem do gry w rzutki. Nasza firma została założona w 2022 roku przez pasjonatów tego sportu, którzy postanowili dzielić się swoją pasją z innymi.</p>
                <p>Oferujemy szeroki wybór wysokiej jakości produktów od renomowanych producentów, zapewniając profesjonalną obsługę i szybką dostawę. Naszym celem jest promowanie gry w rzutki w Polsce oraz dostarczanie najlepszego sprzętu zarówno dla amatorów, jak i profesjonalistów.</p>
                <p>W naszym asortymencie znajdziesz:</p>
                <ul>
                    <li>Lotki typu steel i soft</li>
                    <li>Tarcze elektroniczne i sizalowe</li>
                    <li>Akcesoria do gry w rzutki</li>
                    <li>Części zamienne</li>
                    <li>Gadżety związane z darts</li>
                </ul>',
                'meta_title' => 'O firmie DartShop',
                'meta_description' => 'DartShop - specjalistyczny sklep z wyposażeniem do gry w rzutki.',
                'display_order' => 1,
                'is_active' => true,
            ],
            [
                'title' => 'Nasza misja',
                'content' => '<h2>Nasza misja</h2>
                <p>W DartShop wierzymy, że każdy może czerpać radość z gry w rzutki - niezależnie od poziomu zaawansowania. Nasza misja to:</p>
                <ul>
                    <li>Popularyzacja darta jako sportu dla wszystkich</li>
                    <li>Edukacja w zakresie zasad gry i technik rzutu</li>
                    <li>Dostarczanie sprzętu najwyższej jakości w przystępnych cenach</li>
                    <li>Budowanie społeczności pasjonatów rzutków w Polsce</li>
                    <li>Wspieranie polskich zawodników i wydarzeń związanych z rzutkami</li>
                </ul>
                <p>Codziennie pracujemy nad tym, aby zapewnić naszym klientom nie tylko najlepszy sprzęt, ale również wiedzę i inspirację do doskonalenia swoich umiejętności.</p>',
                'meta_title' => 'Nasza misja',
                'meta_description' => 'Misja DartShop - popularyzacja darta, edukacja i wspieranie pasjonatów rzutków w Polsce.',
                'display_order' => 2,
                'is_active' => true,
            ],
            [
                'title' => 'Zespół DartShop',
                'content' => '<h2>Nasz zespół</h2>
                <p>Za DartShop stoi zespół pasjonatów gry w rzutki, którzy łączą swoją pasję z profesjonalizmem w obsłudze klienta.</p>',
                'meta_title' => 'Nasz zespół',
                'meta_description' => 'Poznaj zespół DartShop - pasjonatów gry w rzutki z wieloletnim doświadczeniem.',
                'display_order' => 3,
                'is_active' => true,
            ],
        ];

        foreach ($aboutPages as $page) {
            AboutPage::create($page);
        }
    }
}
