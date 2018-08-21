@extends('frontend.layout')

@section('title')
<title>Forfatterskolen &rsaquo; FAQ</title>
@stop

@section('content')
<div class="container">
	<div class="courses-hero faq-hero text-center">
		<div class="row">
			<div class="col-sm-12">
				<h2><span class="highlight">FA</span>Q</h2>
			</div>
		</div>
	</div>
</div>

<div class="container faq-container">
    <div class="row">
        <div class="col-sm-12">
    		<h3>SVAR PÅ DINE SPØRSMÅL OM KURSET</h3> <br />
    	</div>
        <div class="col-sm-12">
            <div class="panel-group" id="accordion">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h4 class="panel-title">
                            <a data-toggle="collapse" data-parent="#accordion" href="#collapseOne" class="all-caps">
                        		<span class="text-theme">Q:</span> Må jeg ha noen forkunnskaper før jeg begynner?
                            </a>
                        </h4>
                    </div>
                    <div id="collapseOne" class="panel-collapse collapse in">
                        <div class="panel-body">
							<p class="no-margin-bottom">
							Niks. Kursene våre har elever på alle nivåer. Det eneste du trenger er dedikasjon og vilje til å jobbe. Resten hjelper vi deg med. Når elever vurderer hverandres tekster, forsøker vi å sette sammen forfatterspirer på omtrent samme nivå.
							</p>
                        </div>
                    </div>
                </div>

                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h4 class="panel-title">
                            <a data-toggle="collapse" data-parent="#accordion" href="#collapseTwo" class="all-caps">
                        		<span class="text-theme">Q:</span> Hvordan gjennomføres gruppeoppgavene?
                            </a>
                        </h4>
                    </div>
                    <div id="collapseTwo" class="panel-collapse collapse">
                        <div class="panel-body">
                        	<p>
							Du får to skriveoppgaver i løpet av kurset, en på 1000 ord (ca tre sider) og en på 7500 ord (ca 20 sider). Dette er din personlige tekst. Du vil få tilbakemelding fra oss og to andre elever på teksten. Vi tenker det er lærerikt med en profesjonell tilbakemelding, og to fra "vanlige" lesere. Det er utrolig lærerikt å vurdere andres uferdige tekster, og selvsagt er det utviklende å få tilbakemelding på sin egen.
							</p>
							<p class="no-margin-bottom">
							Alle tekster leses anonymt, også av oss, så det skal være mindre skummelt å komme over levere-fra-seg-manus-terskelen (som forfatter må du over denne likevel, en eller annen gang). Noen ønsker kun tilbakemelding fra oss på tekst, og det er ok.
							</p>
                        </div>
                    </div>
                </div>


                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h4 class="panel-title">
                            <a data-toggle="collapse" data-parent="#accordion" href="#collapseThree" class="all-caps">
                        		<span class="text-theme">Q:</span> Hvordan er moduloppgavene på kurset?
                            </a>
                        </h4>
                    </div>
                    <div id="collapseThree" class="panel-collapse collapse">
                        <div class="panel-body">
                        	<p>
							Du får dryppvis tilgang til hver kursmodul, over 10 uker. Modulene er lagt opp slik at du skal jobbe med ditt personlige manus ved siden av. Den første modulen handler for eksempel om å definere historien i noen få setninger, så du får skarpere fokus og retning før du begynner å skrive - et navigasjonspunkt. Den neste modulen handler om å bygge karakterer, som skal være bærebjelken(e) i historien din. Så handler neste modul om dramaturgi - om å skape en logisk oppbygging og gode spenningskurver, som får leseren til å engasjere seg og henge med hele veien. Deretter kommer modulen om å lage kjøtt på beinet, og skrive drivende gode scener. Til slutt lærer du hvordan du redigerer deg selv, finpusser manus og skriver følgebrev, før du sender inn manuset til et forlag.
							</p>
							<p>
							Hver modul har konkrete oppgaver knyttet til seg, som du kan gjøre for din egen del eller sende inn til oss for tilbakemelding. Kanskje trenger du å vite om ideen din er god nok, eller du vil ha tilbakemelding på karakterskjema? Du får raskt og profesjonelt svar av oss!
							</p>
							<p>
							Hensikten med dette opplegget, som er godt utarbeidet og gjennomprøvd, er at du skal lære skrivehåndverket - språk, dramaturgi, karakterbygging, redigering, litterære virkemidler, samtidig som du utvikler ditt eget manus.
							</p>
							<p class="no-margin-bottom">
							Praktisk, ikke sant? Du vil jo ikke bare lære teori, men skrive boken din også!
							</p>
                        </div>
                    </div>
                </div>


                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h4 class="panel-title">
                            <a data-toggle="collapse" data-parent="#accordion" href="#collapseFour" class="all-caps">
                        		<span class="text-theme">Q:</span> Må jeg begynne helt på nytt med et manus, eller kan jeg bruke noe jeg allerede har?
                            </a>
                        </h4>
                    </div>
                    <div id="collapseFour" class="panel-collapse collapse">
                        <div class="panel-body">
							<p class="no-margin-bottom">
							Ja, takk! Begge deler. Men kanskje ikke på en gang. Så ja, du kan gjerne begynne på noe helt nytt. Men du kan også kvalitetssikre et påbegynt eller halvferdig manus. Er historien godt nok spisset? Trenger karakterene å utvikles mer? Har du nok spenningskurver?
							</p>
                        </div>
                    </div>
                </div>


                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h4 class="panel-title">
                            <a data-toggle="collapse" data-parent="#accordion" href="#collapseFive" class="all-caps">
                        		<span class="text-theme">Q:</span> Hva hvis jeg ikke rekker å gjøre det jeg skal på de ti ukene?
                            </a>
                        </h4>
                    </div>
                    <div id="collapseFive" class="panel-collapse collapse">
                        <div class="panel-body">
							<p class="no-margin-bottom">
							No worries. Vi vet alt om å ikke rekke det man skal, uansett hvor viktig det føles. Derfor har du tilgang på kurset i ett - 1- år. Du kan sende oss moduloppgaver og manus, og få det vurdert, når som helst i denne perioden.
							</p>
                        </div>
                    </div>
                </div>


                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h4 class="panel-title">
                            <a data-toggle="collapse" data-parent="#accordion" href="#collapseSix" class="all-caps">
                        		<span class="text-theme">Q:</span> Hvis jeg ikke rekker webinarene, da?
                            </a>
                        </h4>
                    </div>
                    <div id="collapseSix" class="panel-collapse collapse">
                        <div class="panel-body">
							<p class="no-margin-bottom">
							Du får alltid opptak av dem etterpå. Og du kan sende inn spørsmål på forhånd, så du får svar på webinaret uansett. Eoligere nå?
							</p>
                        </div>
                    </div>
                </div>


                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h4 class="panel-title">
                            <a data-toggle="collapse" data-parent="#accordion" href="#collapseSeven" class="all-caps">
                        		<span class="text-theme">Q:</span> Når kan jeg bli medlem av den lukkede gruppen?
                            </a>
                        </h4>
                    </div>
                    <div id="collapseSeven" class="panel-collapse collapse">
                        <div class="panel-body">
                        	<p>
                        	Med en gang du har betalt kurset, eller deler av det, setter vi deg i gruppe sammen med de andre på kurset. Hvis du vil, selvsagt. Vi anbefaler deg imidlertid å engasjere deg så mye som mulig - sammen med de andre deltakerne - da får du mer ut av kurset. Skriving er ensomt, så du trenger all den støtten og inspirasjonen du kan få.
                        	</p>
							<p class="no-margin-bottom">
							Etter at gruppekurset er over, setter vi deg i det store lukkede skriveforumet vårt (på Facebook).
							</p>
                        </div>
                    </div>
                </div>


                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h4 class="panel-title">
                            <a data-toggle="collapse" data-parent="#accordion" href="#collapseEight" class="all-caps">
                        		<span class="text-theme">Q:</span> Hva innebærer den personlige veiledningen?
                            </a>
                        </h4>
                    </div>
                    <div id="collapseEight" class="panel-collapse collapse">
                        <div class="panel-body">
							<p class="no-margin-bottom">
							Veiledningen innebærer at du kan spørre oss om alt du ønsker på mail, i forumet eller på webinarene. Hvis du kjøper standard- eller propakke får du også manusutvikling(er), der du får 3-4 siders profesjonell veiledning på din personlige tekst (17 000 ord i standardkurset, og 2 x 20 000 ord i prokurset).
							</p>
                        </div>
                    </div>
                </div>


                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h4 class="panel-title">
                            <a data-toggle="collapse" data-parent="#accordion" href="#collapseNine" class="all-caps">
                        		<span class="text-theme">Q:</span> Jeg er forvirret. Hva er forskjell på disse kurspakkene?
                            </a>
                        </h4>
                    </div>
                    <div id="collapseNine" class="panel-collapse collapse">
                        <div class="panel-body">
                        	<p>
                        	Det kan du lese helt konkret på nettsiden vår, før du velger kurspakke. Men kort fortalt:
                        	</p>
                        	<p>
                        	Basickurset gir deg tilgang på kursmodulene, du får tilbakemelding på gruppeoppgavene (3 og 20 sider), webinarer og det lukkede skriveforumet.
                        	</p>
                        	<p>
                        	Standardkurset gir deg alt det overnevnte, og i tillegg får du 3-4 tilbakemelding på en personlig tekst på 17 000 ord.
                        	</p>
                        	Prokurset er det samme som standardkurset, men her får du hele to tilbakemeldinger på personlig tekst (2 x 20 000 ord). I tillegg får du friplass (gratis + førsterett) til en av våre to årlige workshoper i Oslo.
                        	<p>
							<p class="no-margin-bottom">
							Veiledningen innebærer at du kan spørre oss om alt du ønsker på mail, i forumet eller på webinarene. Hvis du kjøper standard- eller propakke får du også manusutvikling(er), der du får 3-4 siders profesjonell veiledning på din personlige tekst (17 000 ord i standardkurset, og 2 x 20 000 ord i prokurset).
							</p>
                        </div>
                    </div>
                </div>


                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h4 class="panel-title">
                            <a data-toggle="collapse" data-parent="#accordion" href="#collapseTen" class="all-caps">
                        		<span class="text-theme">Q:</span> Skjer noe live også, eller foregår alt online?
                            </a>
                        </h4>
                    </div>
                    <div id="collapseTen" class="panel-collapse collapse">
                        <div class="panel-body">
                        	<p>
                        	Alt foregår online, så du kan ta kurset hvor som helst og når som helst. Smart, ikke sant? Vi tror likevel det er viktig med menneskelige møter også. Derfor arrangerer vi innimellom gratistreff, der vi kan møte hverandre live og direkte (jeg er selvsagt med), for eksempel på Litteraturhuset i Oslo (og andre steder i landet, når jeg er der). Vi arrangerer også skrivereiser rundt om i Norge, og verden forøvrig, der du som elev har førsterett på plass!
                        	</p>
							<p class="no-margin-bottom">
							Flere av våre elever har også opprettet skrivegrupper, der de møtes og pilotleser og inspirerer hverandre. Så flott, tenker jeg!
							</p>
                        </div>
                    </div>
                </div>


                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h4 class="panel-title">
                            <a data-toggle="collapse" data-parent="#accordion" href="#collapseEleven" class="all-caps">
                        		<span class="text-theme">Q:</span> Ok, bare for å ha det helt klart: Hva får jeg når jeg betaler for et kurs, helt konkret?
                            </a>
                        </h4>
                    </div>
                    <div id="collapseEleven" class="panel-collapse collapse">
                        <div class="panel-body">
                        	<p>
                        	Ikke noe problem, vi må også ha inn alt med teskjeer. Og vi liker alt som er kort og konkret. So here goes:
                        	</p>
                        	<p>
                        	5 moduler, som skal lære deg det viktiste om skrivehåndverket, samtidig som du jobber med ditt eget manus.
                        	</p>
                        	<p>
                        	Personlig veiledning, via mail, forum og på webinarer
                        	</p>
                        	<p>
                        	Profesjonell tilbakemelding på to skriveoppgaver (1000 ord, 3 sider, og 7500 ord, 20 sider)
                        	</p>
                        	<p>
                        	Evig medlemskap i vårt lukkede skriveforum (Facebook)
                        	</p>
                        	<p>
                        	Webinarer, intensivkurset. Og hele året - med kjente forfattere, dramaturger, redaktører - og oss, selvsagt.
                        	</p>
                        	<p>
                        	Mulighet for å være på på gratistredd meg oss og andre elever, ulike steder i Norge (for eksempel Litteraturhuset i Oslo)
                        	</p>
                        	<p>
                        	Manusutvikling - 3-4 siders profesjonell tilbakemelding på ditt personlige manus (hvis du har kjøpt standard- eller prokurs)
                        	</p>
							<p class="no-margin-bottom">
							1 live workshop (hvis du har kjøpt propakken)
							</p>
                        </div>
                    </div>
                </div>


                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h4 class="panel-title">
                            <a data-toggle="collapse" data-parent="#accordion" href="#collapseTwelve" class="all-caps">
                        		<span class="text-theme">Q:</span> Ok, jeg tror jeg har det nå. Kan jeg betale kurset i flere rater?
                            </a>
                        </h4>
                    </div>
                    <div id="collapseTwelve" class="panel-collapse collapse">
                        <div class="panel-body">
							<p class="no-margin-bottom">
							Selvfølgelig. Vi vet hvordan det er - strømregninga var høyere enn du trodde, bikkja måtte til tannlegen, sånt. Du kan betale i flere rater, vi er fleksible, det er bare å avtale med support: support@forfatterskolen.no
							</p>
                        </div>
                    </div>
                </div>


                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h4 class="panel-title">
                            <a data-toggle="collapse" data-parent="#accordion" href="#collapseThirteen" class="all-caps">
                        		<span class="text-theme">Q:</span> Hva hvis jeg angrer?​
                            </a>
                        </h4>
                    </div>
                    <div id="collapseThirteen" class="panel-collapse collapse">
                        <div class="panel-body">
							<p class="no-margin-bottom">
							Du har to ukers angrefrist etter at kurset har startet. Ombestemmer du deg gir vi deg alle pengene tilbake. Uten et eneste spørsmål, uten en diskusjon. Så sikre er vi på at du vil like og ha nytte av kurset!
							</p>
                        </div>
                    </div>
                </div>


                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h4 class="panel-title">
                            <a data-toggle="collapse" data-parent="#accordion" href="#collapseFourteen" class="all-caps">
                        		<span class="text-theme">Q:</span> Nå ble jeg usikker igjen. Hvis jeg gjennomfører kurset, hva er sjansen for å bli antatt blant tusenvis av andre?
                            </a>
                        </h4>
                    </div>
                    <div id="collapseFourteen" class="panel-collapse collapse">
                        <div class="panel-body">
                        	<p>
                        	Der kom tusenmanusspørsmålet, ja. Vel, vi kan ikke love gullmanus og grønnmisunnelige fans. Men vi kan love dette: Sjansen for å gjennomføre manusløpet, og bli antatt, er langt større hvis du lærer deg skrivehåndverket og har et inspirerende skrivemiljø rundt deg. Flere av våre utgitte elever har sagt at de aldri hadde lykkes, hvis det ikke var det lukkede skriveforumet vårt, der alle støtter og hjelper hverandre frem mot ferdig manus. Innimellom har vi også interne konkurranser, der vi samskriver, og det vanker diverse litterære premier.
                        	</p>
                        	<p>
                        	For ja, vi har mange utgitte elever hos oss. Og det er vi superstolte av! Ingen av dem ble født genier, så vidt jeg vet, de har tatt til seg læring og jobbet hardt. Jeg har selv blitt refusert flere ganger, og bokstavelig talt fått manusene mine revet i stykker. Nå har jeg gitt ut 26 bøker på noen av Norges største forlag, og er solgt til utlandet. Jeg vet endel om veien fra refusjon til suksess, og hva som skal til for å lykkes. Og jeg brenner for å inspirere og lære det videre til forfatterdrømmere!
                        	</p>
							<p class="no-margin-bottom">
							Jobben må du gjøre sjæl. Men vi vil gå med deg, hvert skritt på veien, og heve sjampanjeglasset når du når målet!
							</p>
                        </div>
                    </div>
                </div>



                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h4 class="panel-title">
                            <a data-toggle="collapse" data-parent="#accordion" href="#collapseFifteen" class="all-caps">
                        		<span class="text-theme">Q:</span> ... men hei! Kan jeg ikke bli antatt hos dere også, da?
                            </a>
                        </h4>
                    </div>
                    <div id="collapseFifteen" class="panel-collapse collapse">
                        <div class="panel-body">
                        	<p>
                        	Jo, det kan du! Vi har startet forlaget Forfatterdrøm.​ En gang i året plukker vi ut en elev som vi tenker har utmerket seg. Denne eleven blir årets Drømmeforfatter, og får normalkontrakt med oss. Prosessen videre foregår som hos et hvilket som helst annet forlag, med redaktør, omslag, språkvask, korrektur, trykking, distribusjon og markedsføring. Eleven skal ikke bidra økonomisk, det er Forfatterdrøm som tar alle kostnadene, og gjør en grundig jobb med å skape en helstøpt og selgende bok.
                        	</p>
							<p class="no-margin-bottom">
							Drømmeforfatteren kan skrive innenfor alle typer sjangre voksenroman, barnebok, diktsamling etc.​
							</p>
                        </div>
                    </div>
                </div>


                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h4 class="panel-title">
                            <a data-toggle="collapse" data-parent="#accordion" href="#collapseSixteen" class="all-caps">
                        		<span class="text-theme">Q:</span> Vil du være med nå, da?
                            </a>
                        </h4>
                    </div>
                    <div id="collapseSixteen" class="panel-collapse collapse">
                        <div class="panel-body">
                        	<p>
                        	Krysser tærne, for vi ønsker oss flere dedikerte elever! Her finner du oversikt over våre kurs, med oppstart og mer informasjon:
                        	</p>
							<p class="no-margin-bottom">
							<a href="http://www.forfatterskolen.no/courses-overview/">http://www.forfatterskolen.no/courses-overview/</a>
							</p>
                        </div>
                    </div>
                </div>
            </div>
            Alt godt,<br />
            Rektor Kristine <br />
			<div class="contact-feedback-image faq-rektor margin-top margin-bottom" style="background-image: url({{ asset('images/kristine.png') }}); margin-right: 20px"></div>
			<div class="margin-bottom">
			PS! Lurer du på noe mer, eller noe helt annet? Ikke nøl med å sende meg en mail: <a href="mailto:post@forfatterskolen.no">post@forfatterskolen.no</a>
			</div>
        </div>
    </div>
</div>

@stop