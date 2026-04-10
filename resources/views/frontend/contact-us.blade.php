@extends('frontend.layout')

@section('page_title', 'Forfatterskolen &rsaquo; Om oss')
@section('meta_desc', 'Ta kontakt med Forfatterskolen. Vi hjelper deg med spørsmål om kurs, manusutvikling og publisering.')

@section('content')
<link rel="stylesheet" href="{{ asset('css/pages/contact.css') }}">

<div class="om-redesign">

    {{-- ═══════════ HERO ═══════════ --}}
    <section class="om-hero">
        <div>
            <p class="om-hero__eyebrow">Om Forfatterskolen</p>
            <h1 class="om-hero__heading">Din litterære familie <em>siden 2015</em></h1>
            <div class="om-hero__desc">
                <p>Grunnlegger og rektor for skolen er Kristine Storli Henningsen — en erfaren forfatter med flere titalls bøker utgitt på Gyldendal, Cappelen Damm, Vigmostad &amp; Bjørke og Flux. Debutromanen hennes ble solgt til flere land.</p>
                <p>Med bakgrunn som journalist, spaltist og redaktør i over 15 år, bygget Kristine Forfatterskolen for å gi aspirerende forfattere det samme verktøysettet hun selv brukte.</p>
            </div>
            <div class="om-hero__social">
                <a href="https://www.facebook.com/bliforfatter/" class="om-social-link" aria-label="Facebook" target="_blank" rel="noopener">
                    <svg viewBox="0 0 24 24"><path d="M18 2h-3a5 5 0 0 0-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 0 1 1-1h3z"/></svg>
                </a>
                <a href="https://www.instagram.com/forfatterskolen_norge/" class="om-social-link" aria-label="Instagram" target="_blank" rel="noopener">
                    <svg viewBox="0 0 24 24"><rect x="2" y="2" width="20" height="20" rx="5"/><path d="M12 8a4 4 0 1 0 0 8 4 4 0 0 0 0-8zm0 6.4A2.4 2.4 0 1 1 12 9.6a2.4 2.4 0 0 1 0 4.8zM17.2 7.6a.96.96 0 1 1-1.92 0 .96.96 0 0 1 1.92 0z"/></svg>
                </a>
                <a href="https://no.pinterest.com/forfatterskolen_norge/" class="om-social-link" aria-label="Pinterest" target="_blank" rel="noopener">
                    <svg viewBox="0 0 24 24"><path d="M12 2C6.477 2 2 6.477 2 12c0 4.236 2.636 7.855 6.356 9.312-.088-.791-.167-2.005.035-2.868.182-.78 1.172-4.97 1.172-4.97s-.299-.598-.299-1.482c0-1.388.806-2.425 1.808-2.425.853 0 1.265.64 1.265 1.408 0 .858-.546 2.14-.828 3.33-.236.995.5 1.807 1.48 1.807 1.778 0 3.144-1.874 3.144-4.58 0-2.393-1.72-4.068-4.177-4.068-2.845 0-4.515 2.134-4.515 4.34 0 .859.331 1.781.745 2.282a.3.3 0 0 1 .069.288l-.278 1.133c-.044.183-.145.222-.335.134-1.249-.581-2.03-2.407-2.03-3.874 0-3.154 2.292-6.052 6.608-6.052 3.469 0 6.165 2.473 6.165 5.776 0 3.447-2.173 6.22-5.19 6.22-1.013 0-1.965-.527-2.291-1.148l-.623 2.378c-.226.869-.835 1.958-1.244 2.621.937.29 1.931.446 2.962.446 5.523 0 10-4.477 10-10S17.523 2 12 2z"/></svg>
                </a>
            </div>
        </div>

        <div>
            <div class="om-hero__image-frame">
                <img src="https://www.forfatterskolen.no/images-new/kristine1.png" alt="Kristine Storli Henningsen">
            </div>
            <p class="om-hero__image-caption">Foto: Angel Carlos</p>
        </div>
    </section>

    {{-- Advisory notice --}}
    @if($hasAdvisory)
        <div class="om-advisory">
            <div class="om-advisory__box">
                {!! $advisory->message ?? 'Advisory' !!}
            </div>
        </div>
    @endif

    {{-- ═══════════ VÅR HISTORIE ═══════════ --}}
    <section class="om-story">
        <div class="om-story__inner">
            <h2 class="om-section-heading">Vår historie</h2>
            <p class="om-section-sub">Fra en idé til Norges største nettbaserte skriveskole.</p>

            <div class="om-story__text">
                <p>
                    Forfatterskolen startet med en enkel tanke: at alle som har en historie å fortelle, fortjener profesjonell hjelp til å fortelle den. Kristine Storli Henningsen visste fra egen erfaring som forfatter og redaktør at veien fra idé til ferdig bok er lang — og at riktig veiledning gjør hele forskjellen.
                </p>

                <div class="om-story__quote">
                    «Kan jeg, kan du.»
                </div>

                <p>
                    I dag teller Forfatterskolen flere tusen elever og et knippe erfarne redaktører. Vi har fulgt mange av dem helt fra første utkast til utgivelse — på alt fra store forlag til eget forlag. Skolen drives nettbasert, noe som betyr at du kan delta uansett hvor du bor, og i ditt eget tempo.
                </p>

                <p>
                    Hver uke samler vi elever til mentormøter med kjente forfattere og redaktører — live og direkte. Det er her magien skjer: du lærer håndverket fra folk som lever av det, og du blir en del av et fellesskap av skriveglade mennesker.
                </p>
            </div>
        </div>
    </section>

    {{-- ═══════════ VERDIER ═══════════ --}}
    <section class="om-values">
        <div class="om-values__inner">
            <h2 class="om-section-heading">Hva vi tror på</h2>
            <p class="om-section-sub">Tre pilarer som driver alt vi gjør.</p>

            <div class="om-values__grid">
                <div class="om-value-card">
                    <div class="om-value-card__icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="#862736" stroke-width="1.5" stroke-linecap="round">
                            <path d="M12 2L2 7l10 5 10-5-10-5z"/>
                            <path d="M2 17l10 5 10-5"/>
                            <path d="M2 12l10 5 10-5"/>
                        </svg>
                    </div>
                    <div class="om-value-card__title">Håndverk</div>
                    <div class="om-value-card__desc">Skriving er et håndverk som kan læres. Vi gir deg verktøyene og teknikkene som erfarne forfattere bruker — ikke vage råd, men konkret, praktisk kunnskap.</div>
                </div>
                <div class="om-value-card">
                    <div class="om-value-card__icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="#862736" stroke-width="1.5" stroke-linecap="round">
                            <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
                            <circle cx="9" cy="7" r="4"/>
                            <path d="M23 21v-2a4 4 0 0 0-3-3.87M16 3.13a4 4 0 0 1 0 7.75"/>
                        </svg>
                    </div>
                    <div class="om-value-card__title">Fellesskap</div>
                    <div class="om-value-card__desc">Å skrive trenger ikke være ensomt. Våre mentormøter, kursgrupper og redaktørsamtaler gir deg et støttende nettverk av likesinnede.</div>
                </div>
                <div class="om-value-card">
                    <div class="om-value-card__icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="#862736" stroke-width="1.5" stroke-linecap="round">
                            <polyline points="22 12 18 12 15 21 9 3 6 12 2 12"/>
                        </svg>
                    </div>
                    <div class="om-value-card__title">Fremdrift</div>
                    <div class="om-value-card__desc">Vi dytter deg videre — med ærlige tilbakemeldinger, tydelige mål og en struktur som gjør at manuset faktisk blir ferdig.</div>
                </div>
            </div>
        </div>
    </section>

    {{-- ═══════════ TEAM ═══════════ --}}
    <section class="om-team">
        <div class="om-team__inner">
            <h2 class="om-section-heading">Team Forfatterskolen</h2>
            <p class="om-section-sub">Menneskene bak skolen.</p>

            <div class="om-team__grid">
                <div class="om-team-card">
                    <div class="om-team-card__avatar">
                        <img src="https://www.forfatterskolen.no/images-new/kristine1.png" alt="Kristine Storli Henningsen">
                    </div>
                    <div class="om-team-card__name">Kristine Storli Henningsen</div>
                    <div class="om-team-card__role">Grunnlegger &amp; rektor</div>
                    <div class="om-team-card__bio">Forfatter med titalls utgivelser på Gyldendal, Cappelen Damm og Vigmostad &amp; Bjørke. 15 års erfaring som journalist og redaktør.</div>
                </div>

                <div class="om-team-card">
                    <div class="om-team-card__avatar">
                        <span class="om-team-card__avatar-initials">MR</span>
                    </div>
                    <div class="om-team-card__name">Marit Reiersgård</div>
                    <div class="om-team-card__role">Mentormøter &amp; webinarer</div>
                    <div class="om-team-card__bio">Forfatter med bøker oversatt til flere språk, nominert til Rivertonprisen. Ansvarlig for å booke forfattere og drifte mentormøtene.</div>
                    <a href="mailto:marit@forfatterskolen.no" class="om-team-card__email">marit@forfatterskolen.no</a>
                </div>

                <div class="om-team-card">
                    <div class="om-team-card__avatar">
                        <span class="om-team-card__avatar-initials">AF</span>
                    </div>
                    <div class="om-team-card__name">Annina Forsblom</div>
                    <div class="om-team-card__role">Kursansvarlig EasyWrite &amp; support</div>
                    <div class="om-team-card__bio">Kursansvarlig for EasyWrite Sverige og ansvarlig for support til kursdeltagere.</div>
                </div>

                <div class="om-team-card">
                    <div class="om-team-card__avatar">
                        <span class="om-team-card__avatar-initials">SH</span>
                    </div>
                    <div class="om-team-card__name">Sven Inge Henningsen</div>
                    <div class="om-team-card__role">Webutvikling &amp; publisering</div>
                    <div class="om-team-card__bio">Ansvarlig for nettportal, redaksjonell støtte og publiseringstjenester. Utvikler og vedlikeholder skolens digitale plattform.</div>
                </div>
            </div>
        </div>
    </section>

    {{-- ═══════════ KONTAKT ═══════════ --}}
    <section class="om-contact">
        <div class="om-contact__inner">

            {{-- Contact form --}}
            <div class="om-contact-form">
                <h2 class="om-contact-form__heading">Kontakt oss</h2>
                <p class="om-contact-form__sub">Har du spørsmål? Vi svarer gjerne!</p>

                <form method="POST" action="" onsubmit="disableSubmit(this)">
                    {{ csrf_field() }}
                    <div class="om-form-group">
                        <label for="om-fullname">Navn</label>
                        <input type="text" id="om-fullname" name="fullname" placeholder="Ditt fulle navn" required value="{{ old('fullname') }}">
                    </div>
                    <div class="om-form-group">
                        <label for="om-email">E-postadresse</label>
                        <input type="email" id="om-email" name="email" placeholder="din@epost.no" required value="{{ old('email') }}">
                    </div>
                    <div class="om-form-group">
                        <label for="om-message">Melding</label>
                        <textarea id="om-message" name="message" placeholder="Hva kan vi hjelpe deg med?" required>{{ old('message') }}</textarea>
                    </div>
                    <div class="om-form-checkbox">
                        <input type="checkbox" name="terms" required id="om-terms">
                        {!! str_replace(
                            ['_start_label_', '_end_label_', '_start_link_','_end_link_'],
                            ['<label for="om-terms">','</label>','<a href="'.url('/opt-in-terms').'" target="_blank">','</a>'],
                            trans('site.front.contact-us.accept-terms')
                        ) !!}
                    </div>

                    <p class="om-form-note" style="margin-bottom: 0.75rem;">
                        {{ trans('site.front.contact-us.note') }}
                    </p>

                    {!! \Anhskohbo\NoCaptcha\Facades\NoCaptcha::display() !!}

                    <div style="margin-top: 1rem;">
                        <button type="submit" class="om-form-submit">Send melding</button>
                    </div>
                </form>

                @if ($errors->any())
                    @php
                        $alert_type = session('alert_type', 'danger');
                    @endphp
                    <div class="om-form-alert om-form-alert--{{ $alert_type }}">
                        <button type="button" class="om-alert-close" onclick="this.parentElement.remove()">&times;</button>
                        <ul>
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
            </div>

            {{-- Contact info sidebar --}}
            <div class="om-contact-info">
                <h2 class="om-contact-info__heading">Finn oss</h2>

                <div class="om-contact-info-item">
                    <div class="om-contact-info-item__icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="#862736" stroke-width="1.5" stroke-linecap="round">
                            <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/>
                            <polyline points="22,6 12,13 2,6"/>
                        </svg>
                    </div>
                    <div>
                        <div class="om-contact-info-item__label">E-post</div>
                        <div class="om-contact-info-item__value"><a href="mailto:post@forfatterskolen.no">post@forfatterskolen.no</a></div>
                    </div>
                </div>

                <div class="om-contact-info-item">
                    <div class="om-contact-info-item__icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="#862736" stroke-width="1.5" stroke-linecap="round">
                            <path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72c.127.96.361 1.903.7 2.81a2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45c.907.339 1.85.573 2.81.7A2 2 0 0 1 22 16.92z"/>
                        </svg>
                    </div>
                    <div>
                        <div class="om-contact-info-item__label">Telefon</div>
                        <div class="om-contact-info-item__value"><a href="tel:+4741123555">+47 411 23 555</a></div>
                    </div>
                </div>

                <div class="om-contact-info-item">
                    <div class="om-contact-info-item__icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="#862736" stroke-width="1.5" stroke-linecap="round">
                            <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/>
                            <circle cx="12" cy="10" r="3"/>
                        </svg>
                    </div>
                    <div>
                        <div class="om-contact-info-item__label">Adresse</div>
                        <div class="om-contact-info-item__value">Lihagen 21, 3029 Drammen</div>
                    </div>
                </div>

                <div class="om-response-badge">
                    <span class="om-response-badge__dot"></span>
                    <span class="om-response-badge__text">Vi svarer vanligvis innen 24 timer</span>
                </div>
            </div>
        </div>
    </section>

</div>
@stop
