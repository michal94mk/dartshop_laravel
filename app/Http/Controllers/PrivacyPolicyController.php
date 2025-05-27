<?php

namespace App\Http\Controllers;

use App\Models\PrivacyPolicy;
use Illuminate\Http\Request;

class PrivacyPolicyController extends Controller
{
    /**
     * Display the current privacy policy.
     */
    public function show()
    {
        $privacyPolicy = PrivacyPolicy::getActive();
        
        if (!$privacyPolicy) {
            // Return default policy if none exists
            $privacyPolicy = $this->getDefaultPrivacyPolicy();
        }
        
        return response()->json([
            'privacy_policy' => $privacyPolicy
        ]);
    }

    /**
     * Get default privacy policy content.
     */
    private function getDefaultPrivacyPolicy()
    {
        return (object) [
            'id' => null,
            'title' => 'Polityka Prywatności DartShop',
            'version' => '1.0',
            'effective_date' => now(),
            'content' => $this->getDefaultContent(),
        ];
    }

    /**
     * Get default privacy policy content.
     */
    private function getDefaultContent()
    {
        return '
<h2>1. Informacje ogólne</h2>
<p>Niniejsza Polityka Prywatności określa zasady przetwarzania i ochrony danych osobowych przekazanych przez Użytkowników w związku z korzystaniem z serwisu DartShop.</p>

<h2>2. Administrator danych</h2>
<p>Administratorem danych osobowych jest DartShop z siedzibą w Polsce.</p>
<p>Kontakt: hello@dartshop.pl</p>

<h2>3. Cel i podstawa prawna przetwarzania danych</h2>
<p>Dane osobowe przetwarzane są w następujących celach:</p>
<ul>
<li>Realizacja zamówień i świadczenie usług (art. 6 ust. 1 lit. b RODO)</li>
<li>Marketing bezpośredni (art. 6 ust. 1 lit. f RODO)</li>
<li>Newsletter (art. 6 ust. 1 lit. a RODO)</li>
<li>Wypełnienie obowiązków prawnych (art. 6 ust. 1 lit. c RODO)</li>
</ul>

<h2>4. Rodzaje przetwarzanych danych</h2>
<p>Przetwarzamy następujące kategorie danych osobowych:</p>
<ul>
<li>Dane identyfikacyjne (imię, nazwisko)</li>
<li>Dane kontaktowe (adres e-mail, telefon)</li>
<li>Dane adresowe (adres dostawy)</li>
<li>Dane o zamówieniach i płatnościach</li>
</ul>

<h2>5. Okres przechowywania danych</h2>
<p>Dane osobowe będą przechowywane przez okres niezbędny do realizacji celów, w tym:</p>
<ul>
<li>Dane do realizacji zamówień: przez okres przedawnienia roszczeń</li>
<li>Dane do newslettera: do momentu rezygnacji</li>
<li>Dane księgowe: zgodnie z przepisami prawa</li>
</ul>

<h2>6. Prawa osób, których dane dotyczą</h2>
<p>Użytkownik ma prawo do:</p>
<ul>
<li>Dostępu do swoich danych osobowych</li>
<li>Sprostowania danych osobowych</li>
<li>Usunięcia danych osobowych</li>
<li>Ograniczenia przetwarzania</li>
<li>Przenoszenia danych</li>
<li>Wniesienia sprzeciwu wobec przetwarzania</li>
<li>Cofnięcia zgody</li>
</ul>

<h2>7. Odbiorcy danych</h2>
<p>Dane osobowe mogą być przekazywane:</p>
<ul>
<li>Dostawcom usług IT</li>
<li>Firmom kurierskim</li>
<li>Operatorom płatności</li>
<li>Organom państwowym (gdy wynika to z przepisów prawa)</li>
</ul>

<h2>8. Bezpieczeństwo danych</h2>
<p>Stosujemy odpowiednie środki techniczne i organizacyjne zapewniające bezpieczeństwo przetwarzanych danych osobowych.</p>

<h2>9. Pliki cookies</h2>
<p>Serwis używa plików cookies w celu zapewnienia prawidłowego funkcjonowania strony i poprawy komfortu użytkowania.</p>

<h2>10. Zmiany polityki prywatności</h2>
<p>Zastrzegamy sobie prawo do wprowadzania zmian w niniejszej Polityce Prywatności. O zmianach będziemy informować na stronie internetowej.</p>

<h2>11. Kontakt</h2>
<p>W sprawach dotyczących ochrony danych osobowych można kontaktować się pod adresem: hello@dartshop.pl</p>

<p><strong>Data ostatniej aktualizacji:</strong> ' . date('d.m.Y') . '</p>
        ';
    }
}
