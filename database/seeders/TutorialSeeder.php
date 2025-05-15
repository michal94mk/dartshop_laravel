<?php

namespace Database\Seeders;

use App\Models\Tutorial;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

class TutorialSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Usuwamy wszystkie istniejące poradniki przed dodaniem nowych
        Tutorial::truncate();

        $tutorials = [
            [
                'title' => 'Podstawy gry w rzutki dla początkujących',
                'slug' => 'podstawy-gry-w-rzutki-dla-poczatkujacych',
                'content' => '<h2>Podstawy gry w rzutki dla początkujących</h2>
                <p>Gra w rzutki to świetna rozrywka, która może przerodzić się w pasję na lata. Ten poradnik przeprowadzi Cię przez podstawy, które pozwolą Ci rozpocząć przygodę z darts.</p>
                
                <h3>Sprzęt dla początkujących</h3>
                <p>Na start potrzebujesz:</p>
                <ul>
                    <li><strong>Lotki</strong> - najlepiej rozpocząć od lotek steel o wadze 20-24g</li>
                    <li><strong>Tarcza</strong> - najlepiej sizalowa, która jest standardem w profesjonalnych rozgrywkach</li>
                    <li><strong>Mata ochronna</strong> - zabezpieczy Twoją ścianę przed uszkodzeniami</li>
                </ul>
                
                <h3>Prawidłowa postawa</h3>
                <p>Podstawą dobrego rzutu jest stabilna postawa:</p>
                <ul>
                    <li>Stań bokiem do tarczy</li>
                    <li>Ustaw stopy w wygodnej pozycji, lekko rozstawione</li>
                    <li>Przenieś ciężar ciała na przednią nogę</li>
                    <li>Trzymaj ramię luźno, bez napięcia</li>
                </ul>
                
                <h3>Technika trzymania lotki</h3>
                <p>Nie ma jednej poprawnej techniki - musisz znaleźć to, co działa dla Ciebie. Jednak kilka wskazówek:</p>
                <ul>
                    <li>Trzymaj lotkę trzema palcami (kciuk, wskazujący i środkowy)</li>
                    <li>Nie ściskaj lotki zbyt mocno</li>
                    <li>Upewnij się, że trzymasz lotkę równo</li>
                </ul>
                
                <h3>Technika rzutu</h3>
                <p>Podstawowa technika rzutu:</p>
                <ol>
                    <li>Unieś lotkę na wysokość oczu</li>
                    <li>Skieruj łokieć w stronę tarczy</li>
                    <li>Odciągnij rękę do tyłu</li>
                    <li>Wykonaj płynny rzut, wypuszczając lotkę na wysokości oczu</li>
                    <li>Zakończ ruch, wskazując palcami na cel</li>
                </ol>
                
                <h3>Podstawowe gry</h3>
                <p>Najlepsze gry dla początkujących:</p>
                <ul>
                    <li><strong>501</strong> - rozpoczynasz z 501 punktami i musisz dojść do zera, kończąc na podwójnym polu</li>
                    <li><strong>Around the Clock</strong> - trafiaj po kolei w pola od 1 do 20</li>
                    <li><strong>Cricket</strong> - zamykaj pola od 15 do 20 oraz środek</li>
                </ul>
                
                <h3>Jak trenować</h3>
                <p>Kilka wskazówek treningowych:</p>
                <ul>
                    <li>Ćwicz regularnie, nawet jeśli to tylko 15-20 minut dziennie</li>
                    <li>Skup się na powtarzalności ruchów</li>
                    <li>Trenuj celowanie w konkretne pola, zamiast rzucać bez celu</li>
                    <li>Nagrywaj swoje rzuty, aby analizować technikę</li>
                </ul>
                
                <p>Pamiętaj, że najważniejsza jest regularność i cierpliwość. Każdy profesjonalny gracz zaczynał od podstaw!</p>',
                'featured_image' => null,
                'thumbnail_image' => null,
                'category' => 'Podstawy',
                'difficulty' => 'beginner',
                'is_published' => true,
                'published_at' => Carbon::now()->subDays(30),
                'meta_title' => 'Podstawy gry w rzutki dla początkujących | Poradnik',
                'meta_description' => 'Poznaj podstawy gry w rzutki - sprzęt, postawę, technikę rzutu i pierwsze gry. Praktyczny poradnik dla osób rozpoczynających przygodę z dartem.',
                'excerpt' => 'Gra w rzutki to świetna rozrywka, która może przerodzić się w pasję na lata. Ten poradnik przeprowadzi Cię przez podstawy, które pozwolą Ci rozpocząć przygodę z darts.',
            ],
            [
                'title' => 'Jak wybrać pierwsze lotki?',
                'slug' => 'jak-wybrac-pierwsze-lotki',
                'content' => '<h2>Jak wybrać pierwsze lotki?</h2>
                <p>Wybór pierwszych lotek to ważna decyzja, która może znacząco wpłynąć na Twoje początkowe doświadczenia z darts. W tym poradniku pomożemy Ci dokonać właściwego wyboru.</p>
                
                <h3>Rodzaje lotek</h3>
                <p>Na rynku dostępne są dwa główne typy lotek:</p>
                <ul>
                    <li><strong>Steel tip (stalowe)</strong> - używane z tradycyjnymi tarczami sizalowymi</li>
                    <li><strong>Soft tip (miękkie)</strong> - używane z elektronicznymi tarczami</li>
                </ul>
                <p>Dla początkujących polecamy lotki steel tip, ponieważ są standardem w sporcie i pozwalają lepiej rozwinąć technikę.</p>
                
                <h3>Waga lotek</h3>
                <p>Waga lotek wpływa na tor lotu i stabilność:</p>
                <ul>
                    <li><strong>Lekkie (14-18g)</strong> - wymagają więcej siły, ale oferują płaską trajektorię</li>
                    <li><strong>Średnie (19-22g)</strong> - najbardziej uniwersalne, idealne dla początkujących</li>
                    <li><strong>Ciężkie (23-26g)</strong> - wymagają mniej siły, stabilniejszy lot</li>
                </ul>
                <p>Dla początkujących najlepszym wyborem są lotki o wadze 20-22g.</p>
                
                <h3>Materiał baryłki</h3>
                <p>Baryłka (środkowa część lotki) może być wykonana z różnych materiałów:</p>
                <ul>
                    <li><strong>Mosiądz</strong> - tańszy, lżejszy, dobry na początek</li>
                    <li><strong>Tungsten (wolfram)</strong> - droższy, gęstszy, pozwala na cieńsze baryłki</li>
                </ul>
                <p>Na start wystarczą lotki mosiężne, ale jeśli masz większy budżet, warto zainwestować w lotki z 80-90% zawartością wolframu.</p>
                
                <h3>Kształt baryłki</h3>
                <p>Kształt baryłki wpływa na sposób trzymania i kontrolę:</p>
                <ul>
                    <li><strong>Prosta (straight)</strong> - klasyczny kształt, uniwersalny</li>
                    <li><strong>Torpedo (bomb)</strong> - cięższa z tyłu, stabilizuje lot</li>
                    <li><strong>Barrel (beczka)</strong> - szersza w środku, wygodna w chwycie</li>
                </ul>
                <p>Dla początkujących najlepszy jest kształt prosty lub barrel.</p>
                
                <h3>Lotki a styl gry</h3>
                <p>Twój styl rzutu wpływa na dobór lotek:</p>
                <ul>
                    <li>Jeśli rzucasz z dużą siłą - wybierz lżejsze lotki</li>
                    <li>Jeśli rzucasz delikatnie - wybierz cięższe lotki</li>
                    <li>Jeśli masz mniejsze dłonie - wybierz cieńsze baryłki</li>
                </ul>
                
                <h3>Nasze rekomendacje dla początkujących</h3>
                <p>Oto kilka modeli, które polecamy początkującym:</p>
                <ol>
                    <li>Harrows Ace - uniwersalne lotki wolframowe 22g</li>
                    <li>Winmau Blade 5 Dual Core - profesjonalna tarcza sizalowa</li>
                    <li>Red Dragon Starter Set - kompletny zestaw dla początkujących</li>
                </ol>
                
                <p>Pamiętaj, że najważniejsze jest, aby lotki były wygodne dla Ciebie. Każdy gracz ma swoje preferencje, które wykształcają się z czasem i praktyką.</p>',
                'featured_image' => null,
                'thumbnail_image' => null,
                'category' => 'Sprzęt',
                'difficulty' => 'beginner',
                'is_published' => true,
                'published_at' => Carbon::now()->subDays(25),
                'meta_title' => 'Jak wybrać pierwsze lotki? | Poradnik dla początkujących',
                'meta_description' => 'Dowiedz się, jak wybrać odpowiednie lotki dla początkującego - waga, materiał, kształt i inne cechy, które warto wziąć pod uwagę przy zakupie pierwszych lotek.',
                'excerpt' => 'Wybór pierwszych lotek to ważna decyzja, która może znacząco wpłynąć na Twoje początkowe doświadczenia z darts. W tym poradniku pomożemy Ci dokonać właściwego wyboru.',
            ],
            [
                'title' => 'Techniki treningowe dla średniozaawansowanych graczy',
                'slug' => 'techniki-treningowe-dla-sredniozaawansowanych-graczy',
                'content' => '<h2>Techniki treningowe dla średniozaawansowanych graczy</h2>
                <p>Gdy opanowałeś już podstawy gry w rzutki, czas na podniesienie swoich umiejętności na wyższy poziom. Ten poradnik przedstawia zaawansowane techniki treningowe dla średniozaawansowanych graczy.</p>
                
                <h3>Rozgrzewka i przygotowanie mentalne</h3>
                <p>Przed każdym treningiem lub grą:</p>
                <ul>
                    <li>Wykonaj 5-10 minut rozgrzewki ramion i nadgarstków</li>
                    <li>Zrób kilka rzutów rozgrzewkowych, skupiając się na technice</li>
                    <li>Poświęć chwilę na wizualizację swoich rzutów</li>
                    <li>Ustaw jasny cel treningowy na daną sesję</li>
                </ul>
                
                <h3>Trening precyzji</h3>
                <p>Ćwiczenia na poprawę celności:</p>
                <ol>
                    <li><strong>Ćwiczenie "Bob\'s 27"</strong>:
                        <ul>
                            <li>Rozpocznij z 27 punktami</li>
                            <li>Celuj po kolei w podwójne pola od 1 do 20 oraz w bull</li>
                            <li>Za każde trafienie dodaj wartość podwójnego pola do swojego wyniku</li>
                            <li>Za każdy nietrafiony rzut odejmij wartość pola</li>
                            <li>Celem jest zakończenie z jak największą liczbą punktów</li>
                        </ul>
                    </li>
                    <li><strong>Trening "121"</strong>:
                        <ul>
                            <li>Celem jest zamknięcie 121 punktów w 9 rzutach</li>
                            <li>Trzy rzuty na T20, trzy na T19, trzy na D12</li>
                            <li>Licz każde trafienie</li>
                        </ul>
                    </li>
                    <li><strong>Ćwiczenie "Round the Clock Doubles"</strong>:
                        <ul>
                            <li>Traf po kolei wszystkie podwójne pola od 1 do 20</li>
                            <li>Mierz czas i staraj się poprawiać swój rekord</li>
                        </ul>
                    </li>
                </ol>
                
                <h3>Trening finiszów</h3>
                <p>Umiejętność kończenia legów jest kluczowa:</p>
                <ul>
                    <li>Trenuj systematycznie wszystkie finisze od 2 do 170</li>
                    <li>Skup się szczególnie na finiszach 61-100</li>
                    <li>Naucz się typowych ścieżek do zakończenia (np. T20-D20 dla 100)</li>
                    <li>Ćwicz podwójne pola, szczególnie D16, D20, D8 i D12</li>
                </ul>
                
                <h3>Trening mentalny</h3>
                <p>Aspekt mentalny jest równie ważny jak techniczny:</p>
                <ul>
                    <li>Trenuj pod presją (np. z czasomierzem)</li>
                    <li>Organizuj mini-turnieje ze znajomymi</li>
                    <li>Nagrywaj swoje rzuty i analizuj technikę</li>
                    <li>Prowadź dziennik treningowy, notując postępy i problemy</li>
                </ul>
                
                <h3>Plan treningowy</h3>
                <p>Przykładowy tygodniowy plan treningowy:</p>
                <ul>
                    <li><strong>Poniedziałek</strong>: Trening podwójnych pól (30 min)</li>
                    <li><strong>Wtorek</strong>: Trening potrójnych pól (30 min)</li>
                    <li><strong>Środa</strong>: Trening finiszów 61-100 (30 min)</li>
                    <li><strong>Czwartek</strong>: Gra w 501 przeciwko aplikacji lub znajomym</li>
                    <li><strong>Piątek</strong>: Trening mieszany (ćwiczenie słabszych elementów)</li>
                    <li><strong>Weekend</strong>: Rozgrywki z innymi graczami, mini-turniej</li>
                </ul>
                
                <p>Pamiętaj, że kluczem do sukcesu jest regularność i skupienie na jakości, nie na ilości rzutów. Lepiej trenować 30 minut z pełną koncentracją niż 2 godziny bez zaangażowania.</p>',
                'featured_image' => null,
                'thumbnail_image' => null,
                'category' => 'Trening',
                'difficulty' => 'intermediate',
                'is_published' => true,
                'published_at' => Carbon::now()->subDays(15),
                'meta_title' => 'Techniki treningowe dla średniozaawansowanych graczy | Poradnik',
                'meta_description' => 'Odkryj zaawansowane techniki treningowe dla średniozaawansowanych graczy w rzutki - ćwiczenia precyzji, finiszów i planowanie treningów.',
                'excerpt' => 'Gdy opanowałeś już podstawy gry w rzutki, czas na podniesienie swoich umiejętności na wyższy poziom. Ten poradnik przedstawia zaawansowane techniki treningowe dla średniozaawansowanych graczy.',
            ],
            [
                'title' => 'Zasady gry w turniejach darts - co musisz wiedzieć',
                'slug' => 'zasady-gry-w-turniejach-darts-co-musisz-wiedziec',
                'content' => '<h2>Zasady gry w turniejach darts - co musisz wiedzieć</h2>
                <p>Uczestnictwo w turniejach to ekscytujący krok w rozwoju każdego dartera. Ten poradnik przybliży najważniejsze zasady i etykietę turniejową, których powinieneś przestrzegać.</p>
                
                <h3>Rodzaje turniejów</h3>
                <p>Najpopularniejsze formaty turniejów to:</p>
                <ul>
                    <li><strong>Knock-out</strong> - pojedyncza porażka eliminuje z turnieju</li>
                    <li><strong>Round-robin</strong> - każdy z każdym w grupie, najlepsi awansują</li>
                    <li><strong>Szwajcarski</strong> - system kojarzenia par w zależności od wyników</li>
                    <li><strong>Podwójna eliminacja</strong> - potrzebujesz dwóch porażek, aby odpaść</li>
                </ul>
                
                <h3>Standardowe formaty gry</h3>
                <p>Najczęściej spotykane formaty gry w turniejach:</p>
                <ul>
                    <li><strong>501 Double Out</strong> - klasyczny format, start od 501 punktów, zakończenie na podwójnym</li>
                    <li><strong>Cricket</strong> - popularne w turniejach amatorskich i rozgrywkach drużynowych</li>
                    <li><strong>Best of X legs</strong> - wygrywa ten, kto pierwszy zdobędzie określoną liczbę legów</li>
                    <li><strong>Best of X sets</strong> - set składa się z kilku legów, wygrywa ten, kto zdobędzie więcej setów</li>
                </ul>
                
                <h3>Zasady dotyczące rozgrzewki</h3>
                <p>Przed meczem:</p>
                <ul>
                    <li>Zazwyczaj masz prawo do 6-9 rzutów rozgrzewkowych</li>
                    <li>Nie przekraczaj dozwolonego czasu na rozgrzewkę</li>
                    <li>Rozrzucaj się na tej tarczy, na której będziesz grał</li>
                    <li>Przestrzegaj kolejności rozgrzewki (najczęściej pierwszy rozgrzewa się gracz wymieniony jako pierwszy w drabince)</li>
                </ul>
                
                <h3>Etykieta turniejowa</h3>
                <p>Zasady zachowania podczas turnieju:</p>
                <ul>
                    <li>Zachowaj ciszę, gdy przeciwnik rzuca</li>
                    <li>Stój spokojnie, nie wykonuj gwałtownych ruchów w polu widzenia rzucającego</li>
                    <li>Poczekaj, aż przeciwnik zejdzie z linii rzutu, zanim podejdziesz</li>
                    <li>Nie komentuj głośno rzutów - ani swoich, ani przeciwnika</li>
                    <li>Zawsze gratuluj przeciwnikowi po meczu, niezależnie od wyniku</li>
                    <li>Nie opuszczaj areny turniejowej bez zgody organizatora</li>
                </ul>
                
                <h3>Liczenie punktów</h3>
                <p>Zasady dotyczące liczenia:</p>
                <ul>
                    <li>W turniejach oficjalnych punkty liczy wyznaczony sędzia (caller)</li>
                    <li>W mniejszych turniejach liczenie może należeć do zawodników</li>
                    <li>Jeśli liczysz, stój nieruchomo przy tablicy</li>
                    <li>Nie wyjmuj lotek z tarczy, dopóki wynik nie zostanie zapisany</li>
                    <li>W przypadku wątpliwości co do wyniku, decyzję podejmuje sędzia główny</li>
                </ul>
                
                <h3>Co zabrać na turniej</h3>
                <p>Lista rzeczy, które powinieneś mieć ze sobą:</p>
                <ul>
                    <li>Co najmniej dwa komplety lotek (na wypadek uszkodzenia)</li>
                    <li>Zapasowe piórka i trzony</li>
                    <li>Woda lub inny napój</li>
                    <li>Lekką przekąskę (turnieje mogą trwać wiele godzin)</li>
                    <li>Wygodny strój (większość turniejów ma dress code)</li>
                </ul>
                
                <h3>Dress code</h3>
                <p>Standardowy dress code w turniejach:</p>
                <ul>
                    <li>Ciemne spodnie (nie dżinsy)</li>
                    <li>Koszula z kołnierzykiem (najczęściej turniejowa)</li>
                    <li>Ciemne buty (nie sportowe)</li>
                    <li>Niektóre turnieje wymagają też kamizelki</li>
                </ul>
                
                <p>Pamiętaj, że udział w turniejach to świetna okazja do rozwoju - zarówno pod względem umiejętności, jak i mentalnym. Niezależnie od wyniku, każdy turniej to cenne doświadczenie!</p>',
                'featured_image' => null,
                'thumbnail_image' => null,
                'category' => 'Turnieje',
                'difficulty' => 'intermediate',
                'is_published' => true,
                'published_at' => Carbon::now()->subDays(10),
                'meta_title' => 'Zasady gry w turniejach darts - co musisz wiedzieć | Poradnik',
                'meta_description' => 'Poznaj najważniejsze zasady, etykietę i formaty gry obowiązujące podczas turniejów darts. Praktyczny przewodnik dla graczy planujących start w zawodach.',
                'excerpt' => 'Uczestnictwo w turniejach to ekscytujący krok w rozwoju każdego dartera. Ten poradnik przybliży najważniejsze zasady i etykietę turniejową, których powinieneś przestrzegać.',
            ],
            [
                'title' => 'Zaawansowane strategie gry w 501 dla profesjonalistów',
                'slug' => 'zaawansowane-strategie-gry-w-501-dla-profesjonalistow',
                'content' => '<h2>Zaawansowane strategie gry w 501 dla profesjonalistów</h2>
                <p>Gra 501 na poziomie profesjonalnym wymaga nie tylko precyzji, ale także doskonałego zrozumienia strategii. Ten poradnik omawia zaawansowane koncepcje, które mogą podnieść Twoją grę na najwyższy poziom.</p>
                
                <h3>Strategia otwarcia</h3>
                <p>Pierwsze rzuty w legu są kluczowe:</p>
                <ul>
                    <li>Zawsze celuj w T20 w pierwszych 2-3 kolejkach (o ile nie masz konkretnego powodu, by robić inaczej)</li>
                    <li>Jeśli pierwszy rzut trafi w pojedyncze 1 lub 5, rozważ przejście na T19 dla pozostałych rzutów w kolejce</li>
                    <li>Jeśli pierwszy rzut trafi w pojedyncze 20, zostań przy T20 - złe rzuty często poprzedzają serie dobrych</li>
                    <li>Śledź swoje statystyki, aby znaleźć najbardziej efektywny dla Ciebie wzorzec otwarcia</li>
                </ul>
                
                <h3>Strategie punktowe</h3>
                <p>Jak efektywnie zarządzać punktami:</p>
                <ul>
                    <li><strong>Rule of 3</strong>: jeśli pozostało Ci więcej niż 171 punktów, celuj w T20; od 131 do 170 celuj w T19; poniżej 131 używaj kombinacji T20, T19, T18, D20</li>
                    <li><strong>Pola bezpieczne</strong>: jeśli trafisz T20 pierwszym rzutem, a drugim pojedyncze 1, trzecim celuj w T18 (zamiast T19), aby uniknąć przekroczenia 301 punktów w drugiej kolejce</li>
                    <li><strong>Strategia 186</strong>: kiedy masz 186 punktów, zamiast standardowej sekwencji T20-T20-T20 (która zostawia trudny finisz 6), rozważ T20-T19-T19 (zostawia 50) lub T20-T20-T18 (zostawia D6)</li>
                </ul>
                
                <h3>Zaawansowane strategie finiszu</h3>
                <p>Kluczowe koncepcje przy finiszach:</p>
                <ul>
                    <li><strong>Ścieżki alternatywne</strong>: naucz się alternatywnych dróg do finiszy (np. dla 100: T20-D20 lub T16-D26 lub T19-D12 lub T18-D14)</li>
                    <li><strong>Strategia N-D16</strong>: gdy to możliwe, prowadź punkty tak, by zostało Ci 32, 40, 48, 56... (liczby dzielone przez 8 + 8), które można zakończyć na D16 (twoje ulubione podwójne)</li>
                    <li><strong>Strategia D18/D19</strong>: większość graczy rzadziej trenuje te podwójne, więc jeśli jesteś w nich dobry, warto kierować swoje finisze w ich stronę</li>
                    <li><strong>Analiza bogey numbers</strong>: zidentyfikuj swoje problematyczne liczby (np. 138, 159) i opracuj specjalne strategie ich zakończenia</li>
                </ul>
                
                <h3>Psychologia gry na wysokim poziomie</h3>
                <p>Aspekty mentalne profesjonalnej gry:</p>
                <ul>
                    <li><strong>Rytm gry</strong>: znajdź swoje optimum tempa - nie za szybko, nie za wolno</li>
                    <li><strong>Rytuały przedrzutowe</strong>: ustandaryzuj swoje zachowanie przed każdym rzutem</li>
                    <li><strong>Zarządzanie oddechem</strong>: pracuj nad kontrolą oddychania, szczególnie w momentach presji</li>
                    <li><strong>Powrót po błędzie</strong>: opracuj techniki szybkiego mentalnego resetu po złym rzucie</li>
                    <li><strong>Kontrola emocji</strong>: ukryj zarówno pozytywne, jak i negatywne emocje przed przeciwnikiem</li>
                </ul>
                
                <h3>Analityczne podejście do gry</h3>
                <p>Jak wykorzystać dane do poprawy:</p>
                <ul>
                    <li>Prowadź szczegółowe statystyki swoich meczów (scoring average, procent podwójnych, procent trójek)</li>
                    <li>Analizuj wzorce swojej gry (np. lepsze/gorsze fazy meczu)</li>
                    <li>Śledź skuteczność na poszczególnych podwójnych polach</li>
                    <li>Identyfikuj specyficzne dla ciebie mocne i słabe strony</li>
                </ul>
                
                <h3>Strategie meczowe przeciwko różnym typom graczy</h3>
                <p>Jak dostosować grę do przeciwnika:</p>
                <ul>
                    <li><strong>Przeciwko szybkim graczom</strong>: utrzymuj własne tempo, nie daj się wciągnąć w zbyt szybką grę</li>
                    <li><strong>Przeciwko wolnym graczom</strong>: wykorzystaj czas oczekiwania na mentalną regenerację</li>
                    <li><strong>Przeciwko lepszym scorującym</strong>: maksymalna koncentracja na podwójnych, aby wykorzystać każdą szansę</li>
                    <li><strong>Przeciwko lepszym finiszującym</strong>: agresywne scorowanie, aby wywierać presję</li>
                </ul>
                
                <p>Pamiętaj, że na najwyższym poziomie różnica między zwycięstwem a porażką często nie leży w technice, ale w strategii i psychologii. Doskonalenie tych aspektów może dać Ci przewagę nad równie utalentowanymi technicznie przeciwnikami.</p>',
                'featured_image' => null,
                'thumbnail_image' => null,
                'category' => 'Strategia',
                'difficulty' => 'advanced',
                'is_published' => true,
                'published_at' => Carbon::now()->subDays(5),
                'meta_title' => 'Zaawansowane strategie gry w 501 dla profesjonalistów | Poradnik',
                'meta_description' => 'Odkryj zaawansowane strategie gry w 501 na poziomie profesjonalnym - taktyki punktowania, finisze, psychologię gry i analizę statystyczną.',
                'excerpt' => 'Gra 501 na poziomie profesjonalnym wymaga nie tylko precyzji, ale także doskonałego zrozumienia strategii. Ten poradnik omawia zaawansowane koncepcje, które mogą podnieść Twoją grę na najwyższy poziom.',
            ]
        ];

        foreach ($tutorials as $tutorial) {
            Tutorial::create($tutorial);
        }
    }
}
