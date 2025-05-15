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
                'meta_title' => 'O nas | DartShop - Sklep z wyposażeniem do gry w rzutki',
                'meta_description' => 'Poznaj historię i misję sklepu DartShop - twojego kompletnego źródła wyposażenia do gry w rzutki. Szeroki wybór lotek, tarcz i akcesoriów.',
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
                'meta_title' => 'Nasza misja | DartShop - Pasja do rzutków',
                'meta_description' => 'Poznaj misję DartShop - popularyzacja darta, edukacja i dostarczanie najlepszego sprzętu do gry w rzutki. Wspieramy rozwój społeczności darta w Polsce.',
                'display_order' => 2,
                'is_active' => true,
            ],
            [
                'title' => 'Zespół DartShop',
                'content' => '<h2>Nasz zespół</h2>
                <p>Za DartShop stoi zespół pasjonatów gry w rzutki, którzy łączą swoją pasję z profesjonalizmem w obsłudze klienta.</p>
                
                <div class="team-member">
                    <h3>Adam Nowak - Założyciel</h3>
                    <p>Pasjonat rzutków z 15-letnim doświadczeniem. Uczestnik wielu turniejów krajowych i międzynarodowych. Jego misją jest dzielenie się wiedzą i pasją z innymi graczami.</p>
                </div>
                
                <div class="team-member">
                    <h3>Katarzyna Kowalska - Specjalista ds. Produktów</h3>
                    <p>Odpowiedzialna za dobór asortymentu i kontakty z dostawcami. Dba o to, by w naszej ofercie znajdowały się tylko sprawdzone i wysokiej jakości produkty.</p>
                </div>
                
                <div class="team-member">
                    <h3>Michał Wiśniewski - Ekspert Techniczny</h3>
                    <p>Doradza klientom w doborze sprzętu dopasowanego do ich umiejętności i stylu gry. Prowadzi również poradniki i szkolenia z technik rzutu.</p>
                </div>
                
                <p>Nasz zespół jest do Twojej dyspozycji. Zawsze chętnie doradzimy i pomożemy w wyborze odpowiedniego sprzętu.</p>',
                'meta_title' => 'Zespół DartShop | Poznaj nas bliżej',
                'meta_description' => 'Poznaj zespół DartShop - grupę pasjonatów i ekspertów gry w rzutki, którzy stoją za naszym sklepem. Profesjonalne doradztwo i obsługa.',
                'display_order' => 3,
                'is_active' => true,
            ],
            [
                'title' => 'Historia firmy',
                'content' => '<h2>Historia DartShop</h2>
                <p>Historia DartShop rozpoczęła się w 2022 roku, gdy grupa przyjaciół i pasjonatów rzutków postanowiła stworzyć miejsce, gdzie każdy miłośnik tego sportu znajdzie wszystko, czego potrzebuje.</p>
                
                <h3>Początki</h3>
                <p>Początkowo działaliśmy jako mały sklep internetowy prowadzony po godzinach, oferując podstawowy asortyment lotek i akcesoriów. Szybko jednak zyskaliśmy uznanie klientów dzięki naszej wiedzy, pasji i zaangażowaniu.</p>
                
                <h3>Rozwój</h3>
                <p>W ciągu kolejnych miesięcy systematycznie rozszerzaliśmy naszą ofertę, nawiązywaliśmy współpracę z czołowymi producentami sprzętu do darta i budowaliśmy społeczność wokół naszej marki. Uruchomiliśmy blog z poradami, zaczęliśmy organizować małe turnieje i warsztaty dla początkujących.</p>
                
                <h3>Obecnie</h3>
                <p>Dziś DartShop to jeden z wiodących sklepów z wyposażeniem do gry w rzutki w Polsce. Nadal kierujemy się tymi samymi wartościami co na początku: pasją, jakością i autentycznością. Cieszymy się, że możemy być częścią rosnącej społeczności darterów w Polsce i przyczynić się do popularyzacji tego sportu.</p>
                
                <p>Dziękujemy, że jesteś z nami!</p>',
                'meta_title' => 'Historia DartShop | Od pasji do profesjonalnego sklepu',
                'meta_description' => 'Poznaj historię DartShop - od małego projektu pasjonatów do wiodącego sklepu z wyposażeniem do gry w rzutki w Polsce.',
                'display_order' => 4,
                'is_active' => true,
            ],
            [
                'title' => 'Dlaczego warto nam zaufać',
                'content' => '<h2>Dlaczego warto wybrać DartShop?</h2>
                <p>Wybierając DartShop, stawiasz na sklep prowadzony przez prawdziwych pasjonatów rzutków, którzy rozumieją potrzeby graczy na każdym poziomie zaawansowania.</p>
                
                <h3>Co nas wyróżnia:</h3>
                
                <div class="feature">
                    <h4>Wiedza i doświadczenie</h4>
                    <p>Nasz zespół to aktywni gracze z wieloletnim doświadczeniem. Znamy każdy produkt z naszej oferty, ponieważ sami ich używamy.</p>
                </div>
                
                <div class="feature">
                    <h4>Wyselekcjonowany asortyment</h4>
                    <p>W naszej ofercie znajdziesz tylko sprawdzony i wysokiej jakości sprzęt. Współpracujemy z renomowanymi producentami i osobiście testujemy produkty przed włączeniem ich do oferty.</p>
                </div>
                
                <div class="feature">
                    <h4>Profesjonalne doradztwo</h4>
                    <p>Pomagamy w doborze sprzętu dopasowanego do Twoich potrzeb, stylu gry i budżetu. Zawsze udzielamy szczerych i merytorycznych porad.</p>
                </div>
                
                <div class="feature">
                    <h4>Społeczność</h4>
                    <p>Kupując w DartShop, stajesz się częścią społeczności pasjonatów. Organizujemy wydarzenia, prowadzimy blog z poradami i budujemy miejsce, gdzie miłośnicy rzutków mogą się spotykać i wymieniać doświadczeniami.</p>
                </div>
                
                <div class="feature">
                    <h4>Obsługa klienta</h4>
                    <p>Dbamy o satysfakcję naszych klientów na każdym etapie zakupów - od wyboru produktu, przez szybką wysyłkę, po wsparcie posprzedażowe.</p>
                </div>
                
                <p>Dołącz do tysięcy zadowolonych klientów, którzy zaufali DartShop!</p>',
                'meta_title' => 'Dlaczego DartShop | Twój zaufany sklep z akcesoriami do rzutków',
                'meta_description' => 'Sprawdź, dlaczego warto wybrać DartShop - wiedza, wyselekcjonowany asortyment, profesjonalne doradztwo i społeczność pasjonatów rzutków.',
                'display_order' => 5,
                'is_active' => true,
            ],
        ];

        foreach ($aboutPages as $page) {
            AboutPage::create($page);
        }
    }
}
