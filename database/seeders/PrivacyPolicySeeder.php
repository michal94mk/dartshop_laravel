<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\PrivacyPolicy;

class PrivacyPolicySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        PrivacyPolicy::create([
            'title' => 'Polityka Prywatności DartShop',
            'content' => $this->getDefaultContent(),
            'is_active' => true,
        ]);
    }

    private function getDefaultContent()
    {
        return '
<h2>1. Informacje ogólne</h2>
<p>Niniejsza Polityka Prywatności określa zasady przetwarzania i ochrony danych osobowych przekazanych przez Użytkowników w związku z korzystaniem z serwisu DartShop.</p>

<h2>2. Administrator danych</h2>
<p>Administratorem danych osobowych jest DartShop z siedzibą w Polsce.</p>
<p><strong>Kontakt:</strong> hello@dartshop.pl</p>
<p><strong>Telefon:</strong> +48 123 456 789</p>

<h2>3. Cel i podstawa prawna przetwarzania danych</h2>
<p>Dane osobowe przetwarzane są w następujących celach:</p>
<ul>
<li><strong>Realizacja zamówień i świadczenie usług</strong> - podstawa prawna: art. 6 ust. 1 lit. b RODO (wykonanie umowy)</li>
<li><strong>Marketing bezpośredni</strong> - podstawa prawna: art. 6 ust. 1 lit. f RODO (prawnie uzasadniony interes)</li>
<li><strong>Newsletter</strong> - podstawa prawna: art. 6 ust. 1 lit. a RODO (zgoda)</li>
<li><strong>Wypełnienie obowiązków prawnych</strong> - podstawa prawna: art. 6 ust. 1 lit. c RODO (obowiązek prawny)</li>
<li><strong>Obsługa reklamacji</strong> - podstawa prawna: art. 6 ust. 1 lit. b RODO (wykonanie umowy)</li>
</ul>

<h2>4. Rodzaje przetwarzanych danych</h2>
<p>Przetwarzamy następujące kategorie danych osobowych:</p>
<ul>
<li><strong>Dane identyfikacyjne:</strong> imię, nazwisko</li>
<li><strong>Dane kontaktowe:</strong> adres e-mail, numer telefonu</li>
<li><strong>Dane adresowe:</strong> adres dostawy i fakturowania</li>
<li><strong>Dane o zamówieniach:</strong> historia zakupów, preferencje</li>
<li><strong>Dane płatności:</strong> metoda płatności (bez przechowywania danych karty)</li>
<li><strong>Dane techniczne:</strong> adres IP, dane o przeglądarce, pliki cookies</li>
</ul>

<h2>5. Okres przechowywania danych</h2>
<p>Dane osobowe będą przechowywane przez okres niezbędny do realizacji celów, w tym:</p>
<ul>
<li><strong>Dane do realizacji zamówień:</strong> przez okres przedawnienia roszczeń (3 lata)</li>
<li><strong>Dane do newslettera:</strong> do momentu rezygnacji lub cofnięcia zgody</li>
<li><strong>Dane księgowe:</strong> zgodnie z przepisami prawa (5 lat)</li>
<li><strong>Dane do marketingu bezpośredniego:</strong> do momentu wniesienia sprzeciwu</li>
<li><strong>Dane techniczne:</strong> zgodnie z polityką cookies (maksymalnie 2 lata)</li>
</ul>

<h2>6. Prawa osób, których dane dotyczą</h2>
<p>Zgodnie z RODO, przysługują Ci następujące prawa:</p>
<ul>
<li><strong>Prawo dostępu</strong> - możesz uzyskać informacje o przetwarzanych danych</li>
<li><strong>Prawo sprostowania</strong> - możesz poprawić nieprawidłowe dane</li>
<li><strong>Prawo usunięcia</strong> - możesz żądać usunięcia danych w określonych przypadkach</li>
<li><strong>Prawo ograniczenia przetwarzania</strong> - możesz ograniczyć przetwarzanie danych</li>
<li><strong>Prawo przenoszenia danych</strong> - możesz otrzymać dane w formacie strukturalnym</li>
<li><strong>Prawo sprzeciwu</strong> - możesz sprzeciwić się przetwarzaniu na potrzeby marketingu</li>
<li><strong>Prawo cofnięcia zgody</strong> - możesz cofnąć zgodę w każdym czasie</li>
<li><strong>Prawo wniesienia skargi</strong> - możesz złożyć skargę do organu nadzorczego (UODO)</li>
</ul>

<h2>7. Odbiorcy danych</h2>
<p>Dane osobowe mogą być przekazywane następującym kategoriom odbiorców:</p>
<ul>
<li><strong>Dostawcy usług IT</strong> - hosting, zarządzanie systemem</li>
<li><strong>Firmy kurierskie</strong> - realizacja dostaw</li>
<li><strong>Operatorzy płatności</strong> - obsługa transakcji (Stripe, PayPal)</li>
<li><strong>Firmy księgowe</strong> - prowadzenie księgowości</li>
<li><strong>Organy państwowe</strong> - gdy wynika to z przepisów prawa</li>
<li><strong>Dostawcy usług marketingowych</strong> - po uzyskaniu zgody</li>
</ul>

<h2>8. Przekazywanie danych do państw trzecich</h2>
<p>Niektóre dane mogą być przekazywane do państw trzecich (poza EOG), w szczególności:</p>
<ul>
<li><strong>Stany Zjednoczone</strong> - w przypadku korzystania z usług płatniczych (Stripe) na podstawie standardowych klauzul umownych</li>
<li>Przekazywanie odbywa się wyłącznie do podmiotów zapewniających odpowiedni poziom ochrony danych</li>
</ul>

<h2>9. Bezpieczeństwo danych</h2>
<p>Stosujemy odpowiednie środki techniczne i organizacyjne zapewniające bezpieczeństwo przetwarzanych danych osobowych:</p>
<ul>
<li>Szyfrowanie danych podczas transmisji (SSL/TLS)</li>
<li>Kontrola dostępu do danych osobowych</li>
<li>Regularne kopie zapasowe</li>
<li>Monitorowanie bezpieczeństwa systemów</li>
<li>Szkolenia pracowników w zakresie ochrony danych</li>
</ul>

<h2>10. Pliki cookies i technologie śledzące</h2>
<p>Serwis używa plików cookies w następujących celach:</p>
<ul>
<li><strong>Cookies niezbędne</strong> - zapewnienie prawidłowego funkcjonowania strony</li>
<li><strong>Cookies analityczne</strong> - analiza ruchu i zachowania użytkowników</li>
<li><strong>Cookies marketingowe</strong> - personalizacja reklam (po uzyskaniu zgody)</li>
<li><strong>Cookies preferencji</strong> - zapamiętanie ustawień użytkownika</li>
</ul>
<p>Możesz zarządzać cookies poprzez ustawienia przeglądarki.</p>

<h2>11. Zmiany polityki prywatności</h2>
<p>Zastrzegamy sobie prawo do wprowadzania zmian w niniejszej Polityce Prywatności. O istotnych zmianach będziemy informować:</p>
<ul>
<li>Poprzez publikację nowej wersji na stronie internetowej</li>
<li>Poprzez wysłanie powiadomienia e-mail (jeśli posiadamy Twój adres)</li>
<li>Poprzez wyświetlenie informacji przy następnym logowaniu</li>
</ul>

<h2>12. Podstawy prawne</h2>
<p>Niniejsza polityka prywatności jest zgodna z:</p>
<ul>
<li>Rozporządzeniem Parlamentu Europejskiego i Rady (UE) 2016/679 (RODO)</li>
<li>Ustawą z dnia 10 maja 2018 r. o ochronie danych osobowych</li>
<li>Ustawą z dnia 16 lipca 2004 r. Prawo telekomunikacyjne</li>
</ul>

<h2>13. Kontakt w sprawach ochrony danych</h2>
<p>W sprawach dotyczących ochrony danych osobowych można kontaktować się:</p>
<ul>
<li><strong>E-mail:</strong> hello@dartshop.pl</li>
<li><strong>Telefon:</strong> +48 123 456 789</li>
<li><strong>Adres korespondencyjny:</strong> DartShop, ul. Przykładowa 123, 00-001 Warszawa</li>
</ul>

<p><strong>Data ostatniej aktualizacji:</strong> ' . date('d.m.Y') . '</p>
<p><strong>Data wejścia w życie:</strong> ' . date('d.m.Y') . '</p>
        ';
    }
}
