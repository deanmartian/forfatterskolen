<?php

require __DIR__ . '/../../vendor/autoload.php';
$app = require_once __DIR__ . '/../../bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$html = <<<'HTML'
<h2 style="font-family:Georgia,serif;color:#862736;">Velkommen til Romankurs i gruppe!</h2>

<p>Vi er <strong>så glade</strong> for at du er med! De neste 10 ukene skal bli skikkelig spennende — og vi gleder oss til å følge deg på veien fra idé til ferdig førsteutkast.</p>

<p>Du er nå del av en gruppe skrivende som alle deler det samme målet: å skrive en roman. Sammen med noen av Norges mest erfarne forfattere skal vi hjelpe deg dit.</p>

<h3 style="color:#862736;">Dine kurslærere:</h3>
<ul>
<li><strong>Trude Marstein</strong> — prisbelønt romanforfatter</li>
<li><strong>Gro Dahle</strong> — en av Norges mest elskede forfattere</li>
<li><strong>Bjarte Breiteig</strong> — mester i den korte formen og romanen</li>
<li><strong>Rolf Enger</strong> — erfaren redaktør og skrivekurslærer</li>
</ul>

<h3 style="color:#862736;">Hva du kan forvente:</h3>
<ul>
<li>📚 <strong>10 kursmoduler</strong> — ny modul hver uke fra 20. april</li>
<li>📹 <strong>Ukentlige live-webinarer</strong> — still spørsmål direkte til forfatterne</li>
<li>📝 <strong>Tilbakemelding fra redaktør</strong> — profesjonell vurdering av teksten din</li>
<li>💬 <strong>Skriveforum i portalen</strong> — del tekst, få respons, og bli kjent med medstudentene dine</li>
<li>🎓 <strong>Mentormøter</strong> — ukentlige nettmøter med kjente forfattere og redaktører</li>
</ul>

<h3 style="color:#862736;">Før kursstart 20. april:</h3>
<p>Du trenger ikke forberede noe spesielt. Ha gjerne en romanidé klar — men det er helt OK om den fortsatt er vag. Første modul handler nettopp om å finne og spisse ideen din.</p>

<p>Du finner alt kursmateriell, webinarer og forumet inne på <strong>Min side</strong> i portalen. Vi sender deg en påminnelse når første modul åpner.</p>

<p>Lurer du på noe i mellomtiden? Bare svar på denne e-posten — ingen spørsmål er for dumme.</p>

<p style="font-style:italic;color:#555;">Vi gleder oss enormt til å lese det du skriver!</p>

<p>
Skriveglad hilsen,<br>
<strong>Rektor Kristine</strong><br>
<a href="mailto:kristine@forfatterskolen.no" style="color:#862736;">kristine@forfatterskolen.no</a>
</p>
HTML;

DB::table('courses')->where('id', 121)->update(['email' => $html]);
echo "Velkomstmail oppdatert for Romankurs i gruppe!\n";
