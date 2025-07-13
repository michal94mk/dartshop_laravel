<?php

namespace Database\Seeders;

use App\Models\TermsOfService;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TermsOfServiceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        TermsOfService::create([
            'title' => 'Regulamin Sklepu Internetowego DartShop',
            'version' => '1.0',
            'effective_date' => now(),
            'is_active' => true,
            'content' => '
<h2>§ 1. Postanowienia ogólne</h2>
<p><strong>1.1.</strong> Niniejszy Regulamin określa zasady korzystania ze sklepu internetowego DartShop prowadzonego pod adresem www.dartshop.pl oraz zasady świadczenia usług drogą elektroniczną przez ten sklep.</p>
<p><strong>1.2.</strong> Sklep internetowy prowadzony jest przez DartShop z siedzibą w Polsce, wpisaną do rejestru przedsiębiorców prowadzonego przez Sąd Rejonowy.</p>
<p><strong>1.3.</strong> Niniejszy Regulamin jest regulaminem, o którym mowa w art. 8 ustawy z dnia 18 lipca 2002 r. o świadczeniu usług drogą elektroniczną oraz stanowi integralną część umowy sprzedaży zawieranej przez Sklep.</p>
<p><strong>1.4.</strong> Każdy Klient zobowiązany jest do zapoznania się z niniejszym Regulaminem przed złożeniem pierwszego zamówienia.</p>

<h2>§ 2. Definicje</h2>
<p>Użyte w Regulaminie pojęcia oznaczają:</p>
<ul>
<li><strong>Sklep</strong> - sklep internetowy DartShop działający pod adresem www.dartshop.pl</li>
<li><strong>Sprzedawca</strong> - DartShop prowadzący Sklep internetowy</li>
<li><strong>Klient</strong> - osoba fizyczna, osoba prawna lub jednostka organizacyjna nieposiadająca osobowości prawnej, dokonująca zakupu w Sklepie</li>
<li><strong>Konsument</strong> - Klient będący osobą fizyczną dokonującą czynności prawnej niezwiązanej bezpośrednio z jej działalnością gospodarczą lub zawodową</li>
<li><strong>Produkt</strong> - przedmiot sprzedaży dostępny w Sklepie</li>
<li><strong>Zamówienie</strong> - oświadczenie woli Klienta składające się z wyboru Produktów i określenia warunków sprzedaży</li>
<li><strong>Umowa sprzedaży</strong> - umowa zawierana między Sprzedawcą a Klientem</li>
</ul>

<h2>§ 3. Informacje o Sprzedawcy</h2>
<p><strong>Dane Sprzedawcy:</strong></p>
<p>DartShop<br>
Adres: ul. Przykładowa 123, 00-001 Warszawa<br>
Email: hello@dartshop.pl<br>
Telefon: +48 123 456 789<br>
NIP: 1234567890<br>
REGON: 123456789</p>

<h2>§ 4. Zasady korzystania ze Sklepu</h2>
<p><strong>4.1.</strong> Sklep jest dostępny 24 godziny na dobę, 7 dni w tygodniu.</p>
<p><strong>4.2.</strong> Korzystanie ze Sklepu wymaga posiadania urządzenia z dostępem do Internetu oraz przeglądarki internetowej.</p>
<p><strong>4.3.</strong> Sklep zastrzega sobie prawo do czasowego zawieszenia działania Sklepu w celu przeprowadzenia prac technicznych.</p>
<p><strong>4.4.</strong> Sklep nie ponosi odpowiedzialności za problemy techniczne po stronie Klienta.</p>

<h2>§ 5. Zawieranie umowy sprzedaży</h2>
<p><strong>5.1.</strong> Umowa sprzedaży zawierana jest w drodze elektronicznej poprzez złożenie Zamówienia przez Klienta i potwierdzenie przyjęcia zamówienia przez Sprzedawcę.</p>
<p><strong>5.2.</strong> Zamówienie składa się poprzez dodanie Produktów do koszyka, wypełnienie formularza zamówienia i potwierdzenie zamówienia.</p>
<p><strong>5.3.</strong> Po złożeniu zamówienia Klient otrzymuje potwierdzenie przyjęcia zamówienia na podany adres email.</p>
<p><strong>5.4.</strong> Łączna wartość zamówienia zawiera cenę za Produkty oraz koszty dostawy, które są uzależnione od wybranego sposobu dostawy i podane przed złożeniem zamówienia.</p>
<p><strong>5.5.</strong> Po złożeniu zamówienia Klient otrzymuje automatyczne potwierdzenie przyjęcia zamówienia na podany adres email.</p>

<h2>§ 6. Dostępne sposoby płatności</h2>
<p><strong>6.1.</strong> Sklep umożliwia dokonanie płatności następującymi sposobami:</p>
<ul>
<li><strong>Płatności elektroniczne:</strong> karty płatnicze (Visa, MasterCard), przelewy online, BLIK, płatności mobilne</li>
<li><strong>Przelew tradycyjny:</strong> na rachunek bankowy Sprzedawcy</li>
<li><strong>Płatność przy odbiorze:</strong> gotówką lub kartą u kuriera (za pobraniem)</li>
<li><strong>Raty:</strong> w wybranych instytucjach finansowych (dla kwot powyżej 300 PLN)</li>
</ul>
<p><strong>6.2.</strong> W przypadku płatności przelewem tradycyjnym, Produkt zostanie wysłany po zaksięgowaniu wpłaty na rachunku Sprzedawcy.</p>
<p><strong>6.3.</strong> W przypadku płatności elektronicznych, Klient zostanie przekierowany na bezpieczną stronę operatora płatności.</p>
<p><strong>6.4.</strong> Sprzedawca nie przechowuje danych karty płatniczej Klienta - płatności obsługiwane są przez zewnętrznych, licencjonowanych operatorów płatności.</p>

<h2>§ 7. Dostawa</h2>
<p><strong>7.1.</strong> Dostawa Produktów odbywa się wyłącznie na terenie Rzeczypospolitej Polskiej.</p>
<p><strong>7.2.</strong> Dostępne są następujące sposoby dostawy:</p>
<ul>
<li><strong>Kurier DPD/UPS:</strong> dostawa pod wskazany adres w dni robocze</li>
<li><strong>Paczkomaty InPost:</strong> odbiór w wybranym punkcie całodobowo</li>
<li><strong>Poczta Polska:</strong> przesyłka priorytetowa lub ekonomiczna</li>
<li><strong>Odbiór osobisty:</strong> w siedzibie Sprzedawcy (po wcześniejszym umówieniu)</li>
</ul>
<p><strong>7.3.</strong> Koszty dostawy są podane przy każdym sposobie dostawy i doliczane do wartości zamówienia.</p>
<p><strong>7.4.</strong> Darmowa dostawa przysługuje przy zamówieniach o wartości powyżej 200 PLN.</p>
<p><strong>7.5.</strong> Czas realizacji zamówienia wynosi 1-3 dni robocze od momentu potwierdzenia płatności.</p>

<h2>§ 8. Prawo odstąpienia od umowy</h2>
<p><strong>8.1.</strong> Konsument ma prawo odstąpienia od umowy w terminie 14 dni bez podania przyczyny.</p>
<p><strong>8.2.</strong> Termin do odstąpienia od umowy rozpoczyna się od dnia objęcia w posiadanie Produktu.</p>
<p><strong>8.3.</strong> Oświadczenie o odstąpieniu od umowy może być złożone w formie pisemnej lub elektronicznej.</p>
<p><strong>8.4.</strong> W przypadku odstąpienia od umowy, Sprzedawca zwróci wszystkie otrzymane od Konsumenta płatności w terminie 14 dni.</p>
<p><strong>8.5.</strong> Prawo odstąpienia nie przysługuje w przypadku Produktów przygotowanych na indywidualne zamówienie Konsumenta.</p>

<h2>§ 9. Reklamacje</h2>
<p><strong>9.1.</strong> Klient ma prawo do złożenia reklamacji w przypadku wady Produktu.</p>
<p><strong>9.2.</strong> Reklamację można złożyć w formie pisemnej, elektronicznej lub ustnie.</p>
<p><strong>9.3.</strong> Sprzedawca rozpatrzy reklamację w terminie 14 dni od jej otrzymania.</p>
<p><strong>9.4.</strong> W przypadku uznania reklamacji, Sprzedawca naprawi wadę, wymieni Produkt lub zwróci zapłaconą cenę.</p>

<h2>§ 10. Ochrona danych osobowych</h2>
<p><strong>10.1.</strong> Administratorem danych osobowych Klientów jest Sprzedawca.</p>
<p><strong>10.2.</strong> Dane osobowe przetwarzane są w celu realizacji zamówień i świadczenia usług.</p>
<p><strong>10.3.</strong> Klient ma prawo dostępu do swoich danych osobowych oraz ich poprawiania.</p>
<p><strong>10.4.</strong> Szczegółowe informacje o przetwarzaniu danych osobowych zawarte są w Polityce Prywatności.</p>

<h2>§ 11. Własność intelektualna</h2>
<p><strong>11.1.</strong> Wszystkie prawa własności intelektualnej związane ze Sklepem należą do Sprzedawcy.</p>
<p><strong>11.2.</strong> Zabronione jest kopiowanie, modyfikowanie i rozpowszechnianie treści Sklepu bez zgody Sprzedawcy.</p>

<h2>§ 12. Odpowiedzialność</h2>
<p><strong>12.1.</strong> Sprzedawca odpowiada za wady Produktów zgodnie z przepisami prawa.</p>
<p><strong>12.2.</strong> Sprzedawca nie ponosi odpowiedzialności za szkody pośrednie wynikające z korzystania ze Sklepu.</p>
<p><strong>12.3.</strong> Odpowiedzialność Sprzedawcy jest ograniczona do wysokości zapłaconej ceny.</p>

<h2>§ 13. Zmiany Regulaminu</h2>
<p><strong>13.1.</strong> Sprzedawca zastrzega sobie prawo do zmiany Regulaminu.</p>
<p><strong>13.2.</strong> Sprzedawca zastrzega sobie prawo do wprowadzania zmian w Regulaminie z ważnych przyczyn, takich jak zmiana przepisów prawa, zmiana sposobu płatności i dostawy - w zakresie, w jakim te zmiany wpływają na realizację postanowień niniejszego Regulaminu.</p>
<p><strong>13.3.</strong> O każdej zmianie Regulaminu Sprzedawca poinformuje Klientów poprzez publikację na stronie internetowej oraz wysłanie wiadomości email do zarejestrowanych Klientów.</p>
<p><strong>13.4.</strong> Zmieniony Regulamin obowiązuje od momentu jego publikacji, przy czym nie dotyczy zamówień złożonych przed tą datą.</p>
<p><strong>13.5.</strong> W sprawach nieuregulowanych niniejszym Regulaminem zastosowanie mają przepisy prawa polskiego, w szczególności: Kodeksu cywilnego, ustawy o prawach konsumenta, ustawy o świadczeniu usług drogą elektroniczną.</p>
<p><strong>13.6.</strong> Ewentualne spory będą rozstrzygane przez sąd właściwy zgodnie z przepisami Kodeksu postępowania cywilnego.</p>

<h2>§ 14. Kontakt</h2>
<p><strong>Dane kontaktowe Sprzedawcy:</strong></p>
<p>DartShop<br>
Adres: ul. Przykładowa 123, 00-001 Warszawa<br>
Email: hello@dartshop.pl<br>
Telefon: +48 123 456 789<br>
NIP: 1234567890<br>
REGON: 123456789</p>

<p><strong>Godziny obsługi klienta:</strong><br>
Poniedziałek - Piątek: 9:00 - 17:00<br>
Sobota: 10:00 - 14:00<br>
Niedziela: dzień wolny</p>

<p><strong>Data ostatniej aktualizacji:</strong> ' . date('d.m.Y') . '</p>
            '
        ]);
    }
}
