<script type="text/javascript" src="{{asset('js/jquery-3.2.1.min.js')}}"></script>
<?php
$newDesignPages = ['front.shop-manuscript.index', 'front.publishing', 'front.blog', 'front.shop.thankyou', 'front.thank-you',
    'front.course.index', 'front.course.show', 'front.opt-in.thanks', 'front.opt-in.referral', 'front.contact-us',
    'front.faq', 'front.read-blog', 'front.coaching-timer', 'front.support', 'front.support-articles', 'front.support-article',
    'front.course.checkout', 'front.home', 'front.free-manuscript.success', 'front.workshop.index', 'front.workshop.show',
    'front.course.apply-discount', 'front.shop-manuscript.checkout', 'front.workshop.checkout', 'front.copy-editing',
    'front.correction', 'front.other-service-checkout', 'front.coaching-timer-checkout', 'front.opt-in', 'learner.dashboard',
    'front.webinar-thanks', 'front.free-manuscript.index', 'front.course.claim-reward', 'front.free-webinar',
    'auth.login.show', 'front.henrik', 'front.free-webinar-thanks', 'front.terms', 'front.opt-in-terms', 'learner.account.search',
    'learner.course', 'learner.course.show', 'learner.shop-manuscript', 'learner.shop-manuscript.show',
    'learner.workshop', 'learner.webinar', 'learner.course-webinar', 'learner.assignment',
    'learner.assignment.group.show', 'learner.calendar', 'learner.invoice', 'learner.upgrade',
    'learner.get-upgrade-manuscript', 'learner.get-upgrade-assignment', 'learner.get-upgrade-course',
    'learner.competition', 'learner.profile', 'front.poems', 'learner.survey', 'front.gro-dahle', 'front.gift.shop-manuscript',
    'learner.project.show', 'learner.project.marketing-plan', 'learner.project.graphic-work', 'learner.project.registration',
    'learner.project.marketing', 'learner.project.contract', 'learner.project.invoice', 'learner.book-sale',
    'learner.coaching-time', 'learner.coaching-time.available']
?>
@if(in_array(Route::currentRouteName(), $newDesignPages))
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.11.0/umd/popper.min.js"
            integrity="sha384-b/U6ypiBEHpOf/4+1nzFpr53nxSS+GLCkfwBdFNTxtclqqenISfwAzpKaMNFNmj4" crossorigin="anonymous"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js"
            integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl"
            crossorigin="anonymous"></script>
@else
    <script type="text/javascript" src="{{asset('js/vendor.js')}}"></script>
@endif
<script type="text/javascript" src="{{asset('js/frontend.min.js?v=1')}}"></script>