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
            'version' => '1.0',
            'effective_date' => now(),
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
<li><strong>Dane transakcyjne:</strong> historia zamówień, preferencje zakupowe</li>
<li><strong>Dane techniczne:</strong> adres IP, informacje o urządzeniu</li>
</ul>

<h2>5. Okres przechowywania danych</h2>
<p>Dane osobowe przechowujemy przez okres:</p>
<ul>
<li><strong>Dane konta użytkownika:</strong> do momentu usunięcia konta</li>
<li><strong>Dane zamówień:</strong> 5 lat (wymóg prawny)</li>
<li><strong>Dane newslettera:</strong> do momentu rezygnacji</li>
<li><strong>Dane techniczne:</strong> 12 miesięcy</li>
</ul>

<h2>6. Prawa użytkownika</h2>
<p>Użytkownik ma prawo do:</p>
<ul>
<li>Dostępu do swoich danych osobowych</li>
<li>Sprostowania nieprawidłowych danych</li>
<li>Usunięcia danych (prawo do bycia zapomnianym)</li>
<li>Ograniczenia przetwarzania</li>
<li>Przenoszenia danych</li>
<li>Wniesienia sprzeciwu</li>
<li>Cofnięcia zgody</li>
</ul>

<h2>7. Bezpieczeństwo danych</h2>
<p>Stosujemy odpowiednie środki techniczne i organizacyjne, aby chronić dane osobowe przed:</p>
<ul>
<li>Nieuprawnionym dostępem</li>
<li>Utratą danych</li>
<li>Zniszczeniem danych</li>
<li>Zmianą danych</li>
</ul>

<h2>8. Udostępnianie danych</h2>
<p>Dane osobowe mogą być udostępniane:</p>
<ul>
<li>Dostawcom usług płatniczych</li>
<li>Firmom kurierskim</li>
<li>Organom administracji publicznej (na żądanie)</li>
</ul>

<h2>9. Pliki cookies</h2>
<p>Używamy plików cookies w celu:</p>
<ul>
<li>Zapewnienia funkcjonalności sklepu</li>
<li>Analizy ruchu na stronie</li>
<li>Personalizacji treści</li>
</ul>

<h2>10. Zmiany polityki prywatności</h2>
<p>Zastrzegamy sobie prawo do zmiany niniejszej polityki prywatności. O wszelkich zmianach będziemy informować użytkowników poprzez:</p>
<ul>
<li>Powiadomienie na stronie internetowej</li>
<li>Wiadomość e-mail</li>
</ul>

<h2>11. Kontakt</h2>
<p>W sprawach związanych z ochroną danych osobowych można kontaktować się z nami:</p>
<p><strong>Email:</strong> hello@dartshop.pl</p>
<p><strong>Telefon:</strong> +48 123 456 789</p>
<p><strong>Adres:</strong> ul. Przykładowa 123, 00-000 Warszawa</p>

<p><strong>Data ostatniej aktualizacji:</strong> ' . date('d.m.Y') . '</p>';
    }
}
