{{-- Newsletter signup modal (existing functionality) --}}
<div id="writingPlanModal" class="modal fade global-modal" role="dialog">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <div class="modal-title">
                    <h3>{{ trans('site.front.home.free-writing-tips') }}</h3>
                    <p>
                        {{ trans('site.front.home.free-writing-tips-title') }}
                    </p>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Lukk"></button>
            </div>
            <div class="modal-body" style="padding: 30px">

                <div class="form-container">
                    <div class="form-group">
                        <label>
                            {{ trans('site.first-name') }}
                        </label>

                        <input type="text" name="name" class="form-control"
                               required value="{{old('name')}}">
                    </div>

                    <div class="form-group">
                        <label>
                            {{ trans('site.front.form.email') }}
                        </label>

                        <input type="email" name="email"
                               class="form-control" required>
                    </div>

                    <div class="row options-row">
                        <div class="col-md-6">
                            <div class="custom-checkbox">
                                <input type="checkbox" name="terms" id="terms" required>
                                <?php
                                $search_string = [
                                    '[start_link]', '[end_link]'
                                ];
                                $replace_string = [
                                    '<a href="'.route('front.opt-in-terms').'" title="View front page terms">','</a>'
                                ];
                                $terms_link = str_replace($search_string, $replace_string, trans('site.front.accept-terms'))
                                ?>
                                <label for="terms">{!! $terms_link !!}</label>
                            </div>

                            <em>
                                {{ trans('site.front.main-form.note') }}
                            </em>
                        </div>

                        <div class="col-md-6">
                            {!! \Anhskohbo\NoCaptcha\Facades\NoCaptcha::renderJS() !!}
                            {!! \Anhskohbo\NoCaptcha\Facades\NoCaptcha::display(['data-callback' => 'captchaCB']) !!}
                        </div>
                    </div>

                    <input type="hidden" name="captcha" value="">

                    <div class="btn-container text-end" style="margin-top: 20px">
                        <button type="button" class="btn submit-btn w-100" onclick="submitWritingPlan(this)">
                            {{ trans('site.front.main-form.submit-text') }}
                        </button>
                    </div>

                    <div class="alert alert-danger no-bottom-margin mt-3 d-none">
                        <ul>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>

{{-- Footer redesign styles --}}
<style>
    @import url('https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;600&family=Source+Sans+3:wght@300;400;500;600&display=swap');

    .footer-newsletter {
        background: var(--secondary-red, #852635);
        padding: 2rem 1.5rem;
    }

    .footer-newsletter__inner {
        max-width: 1140px;
        margin: 0 auto;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 2rem;
        flex-wrap: wrap;
    }

    .footer-newsletter__text {
        flex: 1;
        min-width: 240px;
    }

    .footer-newsletter__heading {
        font-family: 'Playfair Display', Georgia, serif;
        font-size: 1.25rem;
        font-weight: 400;
        color: #fff;
        margin-bottom: 0.25rem;
    }

    .footer-newsletter__sub {
        font-size: 0.85rem;
        color: rgba(255, 255, 255, 0.65);
        font-weight: 300;
    }

    .footer-newsletter__form {
        display: flex;
        gap: 0.5rem;
        flex: 1;
        max-width: 540px;
        min-width: 280px;
    }

    .footer-newsletter__input {
        flex: 1;
        padding: 0.7rem 1rem;
        border: 1px solid rgba(255, 255, 255, 0.2);
        border-radius: 8px;
        background: rgba(255, 255, 255, 0.1);
        color: #fff;
        font-family: 'Source Sans 3', -apple-system, BlinkMacSystemFont, sans-serif;
        font-size: 0.875rem;
        outline: none;
        transition: border-color 0.2s, background 0.2s;
    }

    .footer-newsletter__input::placeholder {
        color: rgba(255, 255, 255, 0.45);
    }

    .footer-newsletter__input:focus {
        border-color: rgba(255, 255, 255, 0.5);
        background: rgba(255, 255, 255, 0.15);
    }

    .footer-newsletter__btn {
        padding: 0.7rem 1.5rem;
        background: #fff;
        color: var(--secondary-red, #852635);
        border: none;
        border-radius: 8px;
        font-family: 'Source Sans 3', -apple-system, BlinkMacSystemFont, sans-serif;
        font-size: 0.875rem;
        font-weight: 600;
        cursor: pointer;
        white-space: nowrap;
        transition: background 0.2s, transform 0.1s;
    }

    .footer-newsletter__btn:hover {
        background: #f5f2ed;
    }

    .footer-newsletter__btn:active {
        transform: scale(0.97);
    }

    .footer-main {
        background: #1e1e1e;
        padding: 3rem 1.5rem 2rem;
    }

    .footer-main__inner {
        max-width: 1140px;
        margin: 0 auto;
    }

    .footer-grid {
        display: grid;
        grid-template-columns: 1.4fr 1fr 1fr 1fr;
        gap: 2.5rem;
        padding-bottom: 2.5rem;
        border-bottom: 1px solid rgba(255, 255, 255, 0.08);
    }

    .footer-brand__logo {
        display: block;
        margin-bottom: 1rem;
        text-decoration: none;
    }

    .footer-brand__img {
        height: 70px;
        width: auto;
    }

    .footer-brand__tagline {
        font-family: 'Source Sans 3', -apple-system, BlinkMacSystemFont, sans-serif;
        font-size: 0.85rem;
        color: #8a8680;
        line-height: 1.6;
        margin-bottom: 1.25rem;
        max-width: 260px;
    }

    .footer-social {
        display: flex;
        gap: 0.6rem;
    }

    .footer-social__link {
        width: 36px;
        height: 36px;
        border-radius: 50%;
        background: #2a2a2a;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: background 0.2s, transform 0.15s;
        text-decoration: none;
    }

    .footer-social__link:hover {
        background: var(--secondary-red, #852635);
        transform: translateY(-2px);
    }

    .footer-social__link svg {
        width: 16px;
        height: 16px;
        fill: #d4d0cb;
        transition: fill 0.2s;
    }

    .footer-social__link:hover svg {
        fill: #fff;
    }

    .footer-col__heading {
        font-family: 'Source Sans 3', -apple-system, BlinkMacSystemFont, sans-serif;
        font-size: 0.7rem;
        font-weight: 600;
        letter-spacing: 1.5px;
        text-transform: uppercase;
        color: #8a8680;
        margin-bottom: 1rem;
    }

    .footer-col__list {
        list-style: none;
        padding: 0;
        margin: 0;
    }

    .footer-col__list li {
        margin-bottom: 0.6rem;
    }

    .footer-col__list a {
        color: #d4d0cb;
        text-decoration: none;
        font-family: 'Source Sans 3', -apple-system, BlinkMacSystemFont, sans-serif;
        font-size: 0.875rem;
        font-weight: 400;
        transition: color 0.15s;
        padding: 0;
    }

    .footer-col__list a:hover {
        color: #f5f2ed;
    }

    .footer-col__list .footer-link--sub {
        font-size: 0.8rem;
        color: #8a8680;
    }

    .footer-col__list .footer-link--sub:hover {
        color: #d4d0cb;
    }

    .footer-contact-item {
        display: flex;
        align-items: flex-start;
        gap: 0.6rem;
        margin-bottom: 0.75rem;
    }

    .footer-contact-item svg {
        width: 16px;
        height: 16px;
        fill: #8a8680;
        flex-shrink: 0;
        margin-top: 2px;
    }

    .footer-contact-item a,
    .footer-contact-item span {
        color: #d4d0cb;
        text-decoration: none;
        font-family: 'Source Sans 3', -apple-system, BlinkMacSystemFont, sans-serif;
        font-size: 0.875rem;
        line-height: 1.4;
        transition: color 0.15s;
    }

    .footer-contact-item a:hover {
        color: #f5f2ed;
    }

    .footer-bottom {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-top: 2rem;
        padding-top: 1.25rem;
        border-top: 1px solid rgba(255,255,255,0.1);
        flex-wrap: wrap;
        gap: 0.5rem;
    }

    .footer-bottom__copy {
        font-family: 'Source Sans 3', -apple-system, BlinkMacSystemFont, sans-serif;
        font-size: 0.75rem;
        color: rgba(255,255,255,0.35);
    }

    .footer-bottom__links {
        display: flex;
        gap: 1.25rem;
    }

    .footer-bottom__links a {
        font-family: 'Source Sans 3', -apple-system, BlinkMacSystemFont, sans-serif;
        font-size: 0.75rem;
        color: rgba(255,255,255,0.35);
        text-decoration: none;
        transition: color 0.15s;
    }

    .footer-bottom__links a:hover {
        color: #d4d0cb;
    }

    @media (max-width: 768px) {
        .footer-grid {
            grid-template-columns: 1fr 1fr;
            gap: 2rem;
        }

        .footer-newsletter__inner {
            flex-direction: column;
            text-align: center;
        }

        .footer-newsletter__form {
            max-width: 100%;
        }
    }

    @media (max-width: 480px) {
        .footer-grid {
            grid-template-columns: 1fr;
            gap: 1.5rem;
        }

        .footer-newsletter__form {
            flex-direction: column;
        }
    }
</style>

{{-- Newsletter strip --}}
<section class="footer-newsletter">
    <div class="footer-newsletter__inner">
        <div class="footer-newsletter__text">
            <h3 class="footer-newsletter__heading">Gratis skrivetips fra rektor</h3>
            <p class="footer-newsletter__sub">66 tips rett i innboksen. Ingen spam, kun inspirasjon.</p>
        </div>
        <div class="footer-newsletter__form">
            <input
                type="text"
                class="footer-newsletter__input"
                placeholder="Fornavn"
                id="footer-newsletter-name"
                aria-label="Fornavn for nyhetsbrev"
            >
            <input
                type="email"
                class="footer-newsletter__input"
                placeholder="Din e-postadresse"
                id="footer-newsletter-email"
                aria-label="E-postadresse for nyhetsbrev"
            >
            <button type="button" class="footer-newsletter__btn" onclick="openNewsletterModal()">Ja, send meg tips!</button>
        </div>
    </div>
</section>

{{-- Main footer --}}
<footer class="footer-main">
    <div class="footer-main__inner">
        <div class="footer-grid">

            {{-- Col 1: Brand --}}
            <div>
                <a href="{{ url('/') }}" class="footer-brand__logo">
                    <img src="https://www.forfatterskolen.no/images-new/home/logo_2.png" alt="Forfatterskolen-logo" class="footer-brand__img">
                </a>
                <p class="footer-brand__tagline">
                    Norges største nettbaserte skriveskole &mdash; for deg som vil gjøre alvor av skrivedrømmen.
                </p>
                <div class="footer-social">
                    <a href="https://www.facebook.com/bliforfatter/" class="footer-social__link" aria-label="Facebook" target="_blank" rel="noopener">
                        <svg viewBox="0 0 24 24"><path d="M18 2h-3a5 5 0 0 0-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 0 1 1-1h3z"/></svg>
                    </a>
                    <a href="https://www.instagram.com/forfatterskolen_norge/" class="footer-social__link" aria-label="Instagram" target="_blank" rel="noopener">
                        <svg viewBox="0 0 24 24"><rect x="2" y="2" width="20" height="20" rx="5"/><path d="M12 8a4 4 0 1 0 0 8 4 4 0 0 0 0-8zm0 6.4A2.4 2.4 0 1 1 12 9.6a2.4 2.4 0 0 1 0 4.8zM17.2 7.6a.96.96 0 1 1-1.92 0 .96.96 0 0 1 1.92 0z"/></svg>
                    </a>
                    <a href="https://twitter.com/Forfatterrektor" class="footer-social__link" aria-label="Twitter" target="_blank" rel="noopener">
                        <svg viewBox="0 0 24 24"><path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/></svg>
                    </a>
                    <a href="https://no.pinterest.com/forfatterskolen_norge/" class="footer-social__link" aria-label="Pinterest" target="_blank" rel="noopener">
                        <svg viewBox="0 0 24 24"><path d="M12 2C6.477 2 2 6.477 2 12c0 4.236 2.636 7.855 6.356 9.312-.088-.791-.167-2.005.035-2.868.182-.78 1.172-4.97 1.172-4.97s-.299-.598-.299-1.482c0-1.388.806-2.425 1.808-2.425.853 0 1.265.64 1.265 1.408 0 .858-.546 2.14-.828 3.33-.236.995.5 1.807 1.48 1.807 1.778 0 3.144-1.874 3.144-4.58 0-2.393-1.72-4.068-4.177-4.068-2.845 0-4.515 2.134-4.515 4.34 0 .859.331 1.781.745 2.282a.3.3 0 0 1 .069.288l-.278 1.133c-.044.183-.145.222-.335.134-1.249-.581-2.03-2.407-2.03-3.874 0-3.154 2.292-6.052 6.608-6.052 3.469 0 6.165 2.473 6.165 5.776 0 3.447-2.173 6.22-5.19 6.22-1.013 0-1.965-.527-2.291-1.148l-.623 2.378c-.226.869-.835 1.958-1.244 2.621.937.29 1.931.446 2.962.446 5.523 0 10-4.477 10-10S17.523 2 12 2z"/></svg>
                    </a>
                </div>
            </div>

            {{-- Col 2: Kurs & tjenester --}}
            <div>
                <h4 class="footer-col__heading">Kurs & tjenester</h4>
                <ul class="footer-col__list">
                    <li><a href="{{ route('front.course.index') }}">{{ trans('site.front.nav.course') }}</a></li>
                    <li><a href="{{ route('front.shop-manuscript.index') }}">{{ trans('site.front.nav.manuscript') }}</a></li>
                    <li><a href="{{ route('front.free-manuscript.index') }}">{{ trans('site.free-feedback') }}</a></li>
                    <li><a href="{{ route('front.course.show', 17) }}">Mentormøter</a></li>
                </ul>
            </div>

            {{-- Col 3: Mer fra oss --}}
            <div>
                <h4 class="footer-col__heading">Mer fra oss</h4>
                <ul class="footer-col__list">
                    <li><a href="{{ route('front.publishing') }}">Utgitte elever</a></li>
                    <li><a href="{{ route('front.contact-us') }}">{{ trans('site.who-are-we') }}</a></li>
                    <li><a href="{{ route('front.blog') }}">Blogg</a></li>
                    <li><a href="https://indiemoon.no" target="_blank" rel="noopener" class="footer-link--sub">Indiemoon Publishing &#8599;</a></li>
                    <li><a href="https://rskolen.no" target="_blank" rel="noopener" class="footer-link--sub">Redaktørskolen &#8599;</a></li>
                </ul>
            </div>

            {{-- Col 4: Kontakt --}}
            <div>
                <h4 class="footer-col__heading">Kontakt</h4>
                <div class="footer-contact-item">
                    <svg viewBox="0 0 24 24"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="2" fill="#2a2a2a"/></svg>
                    <span>{{ trans('site.front.contact-us.address') }}</span>
                </div>
                <div class="footer-contact-item">
                    <svg viewBox="0 0 24 24"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6" fill="none" stroke="#2a2a2a" stroke-width="1.5"/></svg>
                    <a href="mailto:{{ trans('site.front.contact-us.mail') }}">{{ trans('site.front.contact-us.mail') }}</a>
                </div>
                <div class="footer-contact-item">
                    <svg viewBox="0 0 24 24"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72c.127.96.361 1.903.7 2.81a2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45c.907.339 1.85.573 2.81.7A2 2 0 0 1 22 16.92z"/></svg>
                    <a href="tel:+4741123555">{{ trans('site.front.contact-us.company-number') }}</a>
                </div>
            </div>

        </div>

        {{-- Bottom bar --}}
        <div class="footer-bottom">
            <span class="footer-bottom__copy">&copy; {{ date('Y') }} Forfatterskolen. Alle rettigheter reservert.</span>
            <div class="footer-bottom__links">
                <a href="/terms/all">{{ trans('site.terms-and-conditions') }}</a>
            </div>
        </div>
    </div>
</footer>

<script>
function openNewsletterModal() {
    var name = document.getElementById('footer-newsletter-name').value;
    var email = document.getElementById('footer-newsletter-email').value;
    var modal = new bootstrap.Modal(document.getElementById('writingPlanModal'));
    if (name) {
        var nameInput = document.querySelector('#writingPlanModal input[name="name"]');
        if (nameInput) nameInput.value = name;
    }
    if (email) {
        var emailInput = document.querySelector('#writingPlanModal input[name="email"]');
        if (emailInput) emailInput.value = email;
    }
    modal.show();
}
</script>
