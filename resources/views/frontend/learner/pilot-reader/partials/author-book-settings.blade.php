<div class="global-card" id="settings">
    <div class="card-body">
        <h4 class="card-title">
            Book Settings
        </h4>
        <div class="form-group mt-1">
            <p class="lead mb-0">
                Reader Management
            </p>
            <small class="d-block text-muted">These settings help you direct your readers through the beta process.</small>
            <p class="font-weight-bold margin-top">Reading Reminders</p>
            <small class="d-block text-muted mb-2">You can enable this feature to have reminding reminders sent to your readers if they have not read in more than <input type="number" onkeypress="authorSettings.numberOnly(event)" name="days_of_reminder" class="form-control d-inline-block col-1" disabled onblur="authorSettings.setDaysOfReminder(this.value)"> days. This feature is currently <span class="reminder-status font-weight-bold">disabled</span>.</small>

            {{--<div class="form-group margin-top">
                <input type="checkbox" data-toggle="toggle" data-on=""
                       class="link-toggle" data-off="" data-style="ios"
                       name="is_reading_reminder_on" onchange="authorSettings.toggleReminders(this, 'toggle')">
                <label class="switch-label">Enable Reading Reminders</label>
            </div>--}}

            <div class="switch-container mt-3">
                <label class="switch">
                    <input type="checkbox" name="is_reading_reminder_on" onchange="authorSettings.toggleReminders(this, 'toggle')">
                    <span class="slider round"></span>
                </label>
                <label class="switch-label">Enable Reading Reminders</label>
            </div>

            <div class="clear-fix"></div>
        </div>
        <div id="settingsDiv">
            <div class="form-group mt-1">
                <p class="lead mb-0">
                    Feedback Options
                </p>
                <small class="d-block text-muted">These settings control how feedback collection will work. Most users can stick with the defaults.</small>
                <div class="custom-control custom-checkbox mt-3 no-left-padding">
                    <input type="checkbox" name="will_receive_a_feedback_email" class="custom-control-input" id="customCheck1">
                    <label class="custom-control-label" for="customCheck1">Receive email notifications for feedback?</label>
                </div>
                <small class="d-block text-muted mt-1">By default you will receive an email whenever someone leaves feedback or a review of your book. If you uncheck this box you will only receive email notifications on feedback you reply to.</small>
                <div class="custom-control custom-checkbox mt-3 no-left-padding">
                    <input type="checkbox" name="is_feedback_shared" class="custom-control-input" id="customCheck2">
                    <label class="custom-control-label" for="customCheck2">Share Feedback Among Readers?</label>
                </div>
                <small class="d-block text-muted mt-1">By default readers can only see their own feedback. Checking this box allows readers to see each other's feedback after they've left their own feedback.</small>
                <div class="custom-control custom-checkbox mt-3 no-left-padding">
                    <input type="checkbox" name="is_inline_commenting_allowed" class="custom-control-input" id="customCheck3">
                    <label class="custom-control-label" for="customCheck3">Allow Inline Commenting? (Highlighter Mode)</label>
                </div>
                <small class="d-block text-muted mt-1">{{ "If enabled, readers will be able to highlight text in the book and mark it with their reaction. Each reader's highlights will be displayed along with the rest of their feedback." }}</small>
                <div class="form-group text-muted mt-1">
                    <small class="font-weight-bold">Please Note Two Gotchas:</small>
                    <ul>
                        <li>
                            <small>{{ "If you enable inline coments your chapters will be locked for editing when any comments are left, because changing the text after highlights have been placed on the chapter will break the highlights. If you want to edit a chapter after inline feedback has been given you'll need to save a new version of the chapter." }}</small>
                        </li>
                        <li>
                            <small>{{ "Most readers choose to read on tablets and smartphones. Text selection is not easy on touch screens, and there's nothing BetaBooks can do about that. Our stats indicate that when authors ask for inline comments fewer readers finish the book." }}</small>
                        </li>
                    </ul>
                </div>
            </div>
            <div class="form-group mt-1">
                <p class="lead">
                    Customization Options
                </p>
                <small class="d-block text-muted">These settings let you tweak the way the book is displayed. Most users can stick with the defaults.</small>
                <div class="form-group mt-3">
                    <label class="label-control">What do you call the units of your book? (singular, defaults to "Chapter")</label>
                    <div class="row">
                        <div class="col-md-6">
                            <input type="text" name="book_units" class="form-control">
                        </div>
                    </div>
                    <small class="d-block text-muted mt-1">A book is typically divided into Chapters, thus each unit is a "Chapter". However sometimes authors want to post a different kind of work, such as a Novella divided into "Parts" or a collection divided into "Stories" etc. This setting does not change the functionality of the app, but does change how the units of a story are displayed.</small>
                </div>
            </div>
            <div class="form-group mt-1">
                <p class="lead">
                    Book Access
                </p>
                <small class="d-block text-muted">Enable or disable reader access to this book.</small>
                <div class="custom-control custom-checkbox mt-3 no-left-padding">
                    <input type="checkbox" name="is_deactivated" class="custom-control-input" id="customCheck5">
                    <label class="custom-control-label" for="customCheck5">Deactivate this Book?</label>
                </div>
                <small class="d-block text-muted mt-1">{{ "If you deactivate this book, only you will be able to see it. It will be hidden from readers dashboards etc. and if they try to visit bookmarked pages they'll be told the book is no longer available to read. You will not lose any reader data, and you can re-activate the book later." }}</small>
                <div class="form-group mt-3">
                    <button class="btn btn-success btn-sm" onclick="authorSettings.prepareBookSettingData()">Save All Changes</button>
                </div>
                <div class="jumbotron jumbotron-fluid">
                    <small class="text-muted d-inline-block mr-2">{{ "If you're looking to fully delete a book, not just deactivate it, use this button." }}</small>
                    <button class="btn btn-danger btn-sm" onclick="authorSettings.deleteBook()">Delete this book</button>
                </div>
            </div>
        </div>
    </div>
</div>