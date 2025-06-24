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
            'title' => 'Regulamin sklepu DartShop',
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
<li><strong>Przedsiębiorca</strong> - Klient będący osobą fizyczną, prawną lub jednostką organizacyjną nieposiadającą osobowości prawnej, prowadzący działalność gospodarczą lub zawodową</li>
<li><strong>Produkt</strong> - rzecz ruchoma będąca przedmiotem umowy sprzedaży</li>
<li><strong>Umowa na odległość</strong> - umowa zawarta z Klientem w ramach zorganizowanego systemu zawierania umów na odległość, bez jednoczesnej fizycznej obecności stron</li>
<li><strong>Konto</strong> - indywidualne konto Klienta w Sklepie, zawierające dane podane przez Klienta</li>
</ul>

<h2>§ 3. Warunki korzystania ze Sklepu</h2>
<p><strong>3.1.</strong> Korzystanie ze Sklepu jest możliwe przy spełnieniu następujących minimalnych wymagań technicznych:</p>
<ul>
<li>Komputer, laptop, tablet lub inne urządzenie mobilne z dostępem do sieci Internet</li>
<li>Przeglądarka internetowa obsługująca JavaScript, pliki cookies oraz protokół szyfrowania SSL</li>
<li>Aktywne konto poczty elektronicznej</li>
<li>Dla płatności kartą: możliwość dokonywania płatności online</li>
</ul>
<p><strong>3.2.</strong> Zakazane jest dostarczanie przez Klienta treści o charakterze bezprawnym, w szczególności treści propagujących przemoc, zniesławiających lub naruszających dobra osobiste i prawa osób trzecich.</p>
<p><strong>3.3.</strong> Klient zobowiązuje się do korzystania ze Sklepu w sposób zgodny z prawem, niniejszym Regulaminem, dobrymi obyczajami, poszanowaniem dóbr osobistych i praw własności intelektualnej osób trzecich.</p>
<p><strong>3.4.</strong> Zabrania się podejmowania przez Klienta działań mogących zakłócić funkcjonowanie Sklepu, w szczególności poprzez użycie określonego oprogramowania lub urządzeń.</p>

<h2>§ 4. Zakładanie i usuwanie Konta</h2>
<p><strong>4.1.</strong> Założenie Konta w Sklepie jest dobrowolne, ale umożliwia korzystanie z dodatkowych funkcjonalności.</p>
<p><strong>4.2.</strong> Rejestracja odbywa się poprzez wypełnienie formularza rejestracyjnego dostępnego na stronie Sklepu oraz akceptację niniejszego Regulaminu.</p>
<p><strong>4.3.</strong> Klient zobowiązuje się do podania prawdziwych, aktualnych i kompletnych danych osobowych w formularzu rejestracyjnym oraz do aktualizowania tych danych w przypadku ich zmiany.</p>
<p><strong>4.4.</strong> Klient zobowiązuje się do zachowania poufności danych umożliwiających dostęp do Konta, w szczególności loginu i hasła.</p>
<p><strong>4.5.</strong> Klient zobowiązuje się niezwłocznie poinformować Sprzedawcę o nieautoryzowanym dostępie do swojego Konta lub o naruszeniu poufności hasła.</p>
<p><strong>4.6.</strong> Klient może w każdej chwili, bez podania przyczyny, usunąć swoje Konto przesyłając odpowiednią wiadomość na adres email Sprzedawcy.</p>
<p><strong>4.7.</strong> Sprzedawca może usunąć Konto Klienta w przypadku naruszenia przez Klienta postanowień niniejszego Regulaminu, po uprzednim wysłaniu ostrzeżenia i wyznaczeniu 7-dniowego terminu na zaprzestanie naruszeń.</p>

<h2>§ 5. Zasady składania zamówienia</h2>
<p><strong>5.1.</strong> Złożenie zamówienia w Sklepie następuje poprzez:</p>
<ol>
<li>Wybór Produktu i dodanie go do koszyka</li>
<li>Przejście do koszyka i sprawdzenie zawartości</li>
<li>Wybranie sposobu dostawy i płatności</li>
<li>Uzupełnienie lub weryfikację danych osobowych i adresowych</li>
<li>Zapoznanie się z niniejszym Regulaminem</li>
<li>Złożenie zamówienia poprzez kliknięcie przycisku "Zamawiam i płacę"</li>
</ol>
<p><strong>5.2.</strong> Warunkiem złożenia zamówienia przez Klienta niebędącego Konsumentem jest dodatkowa akceptacja wzorca umowy.</p>
<p><strong>5.3.</strong> Ceny Produktów podane są w złotych polskich (PLN) i zawierają wszystkie składniki, w tym podatek VAT oraz cła.</p>
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
<p><strong>7.3.</strong> Czas realizacji zamówienia wynosi od 1 do 5 dni roboczych od momentu zaksięgowania płatności.</p>
<p><strong>7.4.</strong> W przypadku produktów dostępnych na zamówienie, czas realizacji może zostać wydłużony do 14 dni roboczych.</p>
<p><strong>7.5.</strong> Klient zostanie poinformowany o przekazaniu przesyłki do dostawy oraz otrzyma numer przesyłki umożliwiający śledzenie.</p>
<p><strong>7.6.</strong> Ryzyko przypadkowej utraty lub uszkodzenia Produktu przechodzi na Klienta z chwilą wydania przesyłki przez Sprzedawcę przewoźnikowi.</p>

<h2>§ 8. Prawo odstąpienia od umowy (dla Konsumentów)</h2>
<p><strong>8.1.</strong> Konsument ma prawo do odstąpienia od umowy sprzedaży w terminie 14 dni bez podania przyczyny.</p>
<p><strong>8.2.</strong> Termin do odstąpienia od umowy wygasa po upływie 14 dni od dnia objęcia rzeczy w posiadanie przez Konsumenta lub wskazaną przez niego osobę trzecią inną niż przewoźnik.</p>
<p><strong>8.3.</strong> Aby skorzystać z prawa odstąpienia od umowy, Konsument musi poinformować Sprzedawcę o swojej decyzji poprzez jednoznaczne oświadczenie.</p>
<p><strong>8.4.</strong> Konsument może skorzystać z wzoru formularza odstąpienia od umowy, dostępnego na stronie Sklepu, lecz nie jest to obowiązkowe.</p>
<p><strong>8.5.</strong> Aby dotrzymać terminu do odstąpienia od umowy, wystarczy, aby Konsument wysłał informację dotyczącą wykonania prawa odstąpienia od umowy przed upływem terminu do odstąpienia od umowy.</p>
<p><strong>8.6.</strong> W przypadku odstąpienia od umowy, zwracamy Konsumentowi wszystkie otrzymane od niego płatności niezwłocznie, a w każdym przypadku nie później niż w terminie 14 dni od dnia, w którym zostaliśmy poinformowani o decyzji o wykonaniu prawa odstąpienia od umowy.</p>
<p><strong>8.7.</strong> Zwrot płatności dokonujemy przy użyciu takich samych sposobów płatności, jakie zostały przez Konsumenta użyte w pierwotnej transakcji, chyba że Konsument wyraźnie zgodził się na inne rozwiązanie.</p>
<p><strong>8.8.</strong> Możemy wstrzymać się ze zwrotem płatności do otrzymania rzeczy z powrotem lub do przedłożenia przez Konsumenta dowodu odesłania rzeczy.</p>

<h2>§ 9. Zwracanie Produktów</h2>
<p><strong>9.1.</strong> Konsument powinien odesłać rzecz niezwłocznie, a w każdym przypadku nie później niż w terminie 14 dni od dnia, w którym poinformował Sprzedawcę o odstąpieniu od umowy.</p>
<p><strong>9.2.</strong> Konsument ponosi bezpośrednie koszty zwrotu rzeczy, chyba że Sprzedawca zgodził się je ponieść lub nie poinformował Konsumenta o tym, że Konsument ma je ponosić.</p>
<p><strong>9.3.</strong> Konsument odpowiada tylko za zmniejszenie wartości rzeczy wynikające z korzystania z niej w sposób inny niż było to konieczne do stwierdzenia charakteru, cech i funkcjonowania rzeczy.</p>
<p><strong>9.4.</strong> Produkty należy zwrócić w oryginalnym opakowaniu, z zachowaniem oryginalnych etykiet i metek, w stanie umożliwiającym ponowną sprzedaż.</p>

<h2>§ 10. Rękojmia i gwarancja</h2>
<p><strong>10.1.</strong> Sprzedawca odpowiada wobec Klienta za zgodność towaru z umową na zasadach określonych w przepisach Kodeksu cywilnego.</p>
<p><strong>10.2.</strong> Reklamację można zgłosić pisemnie na adres email: reklamacje@dartshop.pl lub na adres pocztowy Sprzedawcy.</p>
<p><strong>10.3.</strong> Zaleca się dołączenie do reklamacji zdjęć przedstawiających wadę oraz kopii dowodu zakupu.</p>
<p><strong>10.4.</strong> Sprzedawca ustosunkuje się do reklamacji w terminie 14 dni od jej otrzymania.</p>
<p><strong>10.5.</strong> W przypadku uznania reklamacji, Sprzedawca dokona naprawy, wymiany towaru, obniżenia ceny lub odstąpi od umowy zgodnie z życzeniem Klienta i przepisami prawa.</p>
<p><strong>10.6.</strong> Na niektóre Produkty producent udziela dodatkowo gwarancji, której warunki określone są w karcie gwarancyjnej dołączonej do Produktu.</p>

<h2>§ 11. Ochrona danych osobowych</h2>
<p><strong>11.1.</strong> Szczegółowe zasady przetwarzania danych osobowych określa Polityka Prywatności dostępna na stronie Sklepu.</p>
<p><strong>11.2.</strong> Podanie danych osobowych jest dobrowolne, ale niezbędne do realizacji zamówienia i świadczenia usług.</p>
<p><strong>11.3.</strong> Klient ma prawo dostępu, sprostowania, przenoszenia, ograniczenia przetwarzania oraz usunięcia swoich danych osobowych.</p>
<p><strong>11.4.</strong> Klient może w każdej chwili wycofać zgodę na przetwarzanie danych osobowych w celach marketingowych.</p>

<h2>§ 12. Pozasądowe sposoby rozpatrywania reklamacji i dochodzenia roszczeń</h2>
<p><strong>12.1.</strong> Szczegółowe informacje o możliwości skorzystania przez Konsumenta z pozasądowych sposobów rozpatrywania reklamacji i dochodzenia roszczeń oraz zasady dostępu do tych procedur dostępne są w siedzibach oraz na stronach internetowych powiatowych (miejskich) rzeczników konsumentów, organizacji społecznych, o których mowa w art. 61 ustawy z dnia 16 lutego 2007 r. o ochronie konkurencji i konsumentów, Wojewódzkich Inspektoratów Inspekcji Handlowej oraz pod adresem internetowym UOKiK.</p>
<p><strong>12.2.</strong> Konsument ma możliwość skorzystania z platformy ODR (Online Dispute Resolution), która dostępna jest pod adresem: http://ec.europa.eu/consumers/odr/</p>

<h2>§ 13. Postanowienia końcowe</h2>
<p><strong>13.1.</strong> Niniejszy Regulamin wchodzi w życie z dniem publikacji w Sklepie internetowym.</p>
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
