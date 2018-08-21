'use strict';
const converter = new showdown.Converter();
const editors = {

};
const methods = {

    loadDefEditor : function(id){
        CKEDITOR.plugins.addExternal( 'autogrow', autogrow_link, 'plugin.js' );
        let key =  id.replace("_editor", "");
        if(editors[key]){
            editors[key].destroy();
            editors[key] = null
        }
        editors[key] = CKEDITOR.replace(id, {
            toolbarGroups: [
                {name: 'tools'},
                {name: 'basicstyles', groups: ['basicstyles', 'cleanup']},
                {name: 'paragraph',   groups: [ 'blocks']},
                {name: 'others'}
            ],
            extraPlugins : 'autogrow',
            autoGrow_minHeight : 100,
            autoGrow_maxHeight : 200,
            autoGrow_onStartup : true
        })
    },

    uniqueId :function() {
        return Math.round(new Date().getTime() + (Math.random() * 100));
    },

    addNote : function(el){
        let form_group = $(el).closest(".chapter-feedback-item");
        $(".chapter-prompt").hide();
        this.addForm(form_group.find(".full-messages"), "add");
    },

    addReply: function(el, feedback_id) {
        let form_group = $(el).closest(".chapter-feedback-item");
        $(".chapter-prompt").hide();
        this.addReplyForm(form_group.find(".full-messages"), "add", 0, feedback_id);
    },

    addForm : function(form_container, action, published = 0, feedback_id = 0){
        let id = this.uniqueId();
        let submit_text = author_id === current_user ? 'Save Note' : 'Submit Feedback';
        let cancel_button = action === 'add' ?
            '<button class="beta-button danger" onclick="methods.cancelNote(this)"><i class="fa fa-trash"></i> <span>Discard Draft</span></button>'
            : '<button class="beta-button danger"><i class="fa fa-trash"></i> <span>Discard Draft</span></button>';
        /*'<button class="beta-button danger" onclick="methods.cancelNote(this)"><i class="fa fa-trash"></i> <span>Discard Draft</span></button>'
    : (published ? '<button class="beta-button"><i class="fa fa-ban"></i> <span>Cancel</span></button>'
            : '<button class="beta-button danger"><i class="fa fa-trash"></i> <span>Discard Draft</span></button>');*/
        form_container.append(`
                    <div class="form-group ${ action }" id="note_wrapper_${ id }">
                       <input type="hidden" name="feedback_id">
                       <textarea id="note_${ id }_editor"></textarea>
                       <button class="beta-button color success margin-top"  onclick="methods.saveFeedback(this, 1)">
                        <i class="fa fa-check"></i> <span>${submit_text}</span>
                       </button>
                       <button class="beta-button color1 margin-top" onclick="methods.saveFeedback(this, 0)">
                        <i class="fa fa-save"></i> <span>Save Draft</span></button>
                       `+cancel_button+`
                    </div>
                `);
        this.loadDefEditor(`note_${ id }_editor`)
    },

    addReplyForm: function(form_container, action, published = 0, feedback_id = 0) {
        let id = this.uniqueId();
        let submit_text = 'Send Reply';
        let cancel_button = action === 'add' ?
            '<button class="beta-button danger" onclick="methods.cancelNote(this)"><i class="fa fa-trash"></i> <span>Discard Draft</span></button>'
            : '<button class="beta-button danger"><i class="fa fa-trash"></i> <span>Discard Draft</span></button>';
        /*'<button class="beta-button danger" onclick="methods.cancelNote(this)"><i class="fa fa-trash"></i> <span>Discard Draft</span></button>'
            : (published ? '<button class="beta-button"><i class="fa fa-ban"></i> <span>Cancel</span></button>'
                : '<button class="beta-button danger"><i class="fa fa-trash"></i> <span>Discard Draft</span></button>');*/
        form_container.append(`
                    <div class="form-group ${ action }" id="reply_wrapper_${ id }">
                       <input type="hidden" name="feedback_id">
                       <textarea id="reply_${ id }_editor"></textarea>
                       <button class="beta-button color success margin-top"  onclick="methods.saveReply(this, 1, ${feedback_id})">
                        <i class="fa fa-check"></i> <span>${submit_text}</span>
                       </button>
                       <button class="beta-button color1 margin-top" onclick="methods.saveReply(this, 0, ${feedback_id})">
                        <i class="fa fa-save"></i> <span>Save Draft</span></button>
                       `+cancel_button+`
                    </div>
                `);
        this.loadDefEditor(`reply_${ id }_editor`)
    },

    saveFeedback : function(el, published){
        let self = this;
        let elem = this.getElements(el);
        let data = { id : elem.feedback_id, chapter_id : chapter_id, published : published, message : converter.makeHtml(editors[elem.key].getData()) };
        let url = elem.note_wrapper.hasClass("add") ? add_feed_link : update_feed_link;
        let parent_el = $(el).parent('.form-group');
        let prev_el = $(el).parent('.form-group').prev('.note-list-container');
        $.post(url, data)
            .then(function(response){
                if(elem.note_wrapper.hasClass("edit")){
                    //self.editFeedback(el, response.feedback);
                    /*$(el).parent('div').parent(".edit-note-form-container").prev('.message-content').removeClass('hidden')
                        .empty().append(response.feedback.message);

                    $(el).parent('div').parent(".edit-note-form-container").prev('.message-content').parent('.list-group-item')
                        .find('.edit-note-btn').removeClass('hidden');*/
                }
                if (showOtherFeedback) {
                    $(".reader-feedback").removeClass('display-none');
                }
                $(".other-feedback-desc").text("Here's what other have to say");
                self.listFeedback(el, response.feedback, parent_el, prev_el);
                //self.cancelNote(el);
            })
            .catch(function(err){

            });
    },

    saveReply : function(el, published, feedback_id){
        let self = this;
        let elem = this.getElements(el);
        let data = { id : elem.feedback_id, chapter_id : chapter_id, published : published, message : converter.makeHtml(editors[elem.key].getData()),
        is_reply : 1, feedback_id: feedback_id};
        let url = elem.note_wrapper.hasClass("add") ? add_feed_link : update_feed_link;
        let parent_el = $(el).parent('.form-group');
        let prev_el = $(el).parent('.form-group').prev('.feedback-list-container');
        $.post(url, data)
            .then(function(response){
                self.cancelNote(el);
                self.listReply(el, response.feedback, parent_el, prev_el);
            })
            .catch(function(err){

            });
    },

    getElements : function(el){
        let note_wrapper = $(el).closest(".form-group");
        let note_editor = note_wrapper.find("textarea");
        let id = note_editor.get(0).id;
        let key = id.replace("_editor", "");
        let feedback_id = note_wrapper.find("[name='feedback_id']").val() || null;
        return { note_wrapper : note_wrapper, note_editor : note_editor, id : id, key : key, feedback_id : feedback_id }
    },

    cancelNote : function(el){
        let elem = this.getElements(el);
        editors[elem.key].destroy();
        delete editors[elem.key];
        elem.note_wrapper.remove();

        if ($(".chapter-prompt").length) {
            $(".chapter-prompt").show();
        }
    },

    listFeedback: function(el, feedback, parent_el, prev_el) {
        let is_update = 0;
        if (parent_el.hasClass('edit')) {
            prev_el = $("#note-li-"+feedback.id).parent('.note-list-container');
            //parent_el = $("#note-li-"+feedback.id);
            is_update = 1;
        }

        let new_item = `
                        <div class="form-group mb-0 clearfix">
                            <span class="draft-label right-space ${ feedback.published === '0'? '' : 'hidden' }">Draft</span>
                            <span class="text-muted float-left ${ feedback.published === '0'? 'mt-1' : '' }">You ${ feedback.published === '0'? 'saved' : 'posted' } at ${ moment(feedback.created_at).format('MMMM DD hh:mm a') }</span>
                            <span class="feedback-marker unmarked">
                                <select onchange="methods.setMark(this, ${ feedback.id })" class="hidden">
                                </select>
                            </span>
                            <a class="pull-right edit-note-btn">
                                <i class="fa fa-pencil"></i> <span>Edit</span>
                            </a>
                        </div>
                        <div class="${ feedback.published === '0'? 'mt-1' : '' } ml-2 message-content">
                            ${ feedback.message || '' }
                        </div>
                        <div class="form-group edit-note-form-container mt-2">

                        </div>
                `;

        if (is_update) {
            prev_el.find("#note-li-"+feedback.id).empty();
            prev_el.find("#note-li-"+feedback.id).append(new_item);
            this.cancelNote(el);
            parent_el.remove();
        } else {
            if (parent_el.closest(".chapter-feedback-item").find('.chapter-prompt').length) {
                parent_el.closest(".chapter-feedback-item").find('.chapter-prompt').remove();
            }
            prev_el.find("note-li-"+feedback.id).remove();
            prev_el.append(`<li class="list-group-item clearfix" id="note-li-${ feedback.id }"></li>`);
            prev_el.find("#note-li-"+feedback.id).append(new_item);
            this.cancelNote(el);
            parent_el.remove();
        }

        let self = this;

        let marks = {
            'unmarked' : "Unmarked",
            'ignore' : "Ignore",
            'consider' : "Consider",
            'todo' : "Todo",
            'done' : "Done",
            'keep' : "Keep"
        };

        let edit_note_btn = prev_el.find(`#note-li-${ feedback.id } .edit-note-btn`);
        edit_note_btn.click(function(){
            self.editFeedback(this, feedback)
        });

        if(feedback.published === '1' || feedback.published === 1){
            let select_el = prev_el.find(`#note-li-${ feedback.id } select`);
            $.each(marks, function(value , text){
                select_el.append(`
                        <option value="${ value }">${ text }</option>
                        `)
            });
            select_el.val(feedback.mark ? feedback.mark : 'unmarked');
            self.setMark(select_el);
        }
    },

    listReply: function(el, feedback, parent_el, prev_el) {

        let is_update = 0;
        if (parent_el.hasClass('edit')) {
            prev_el = $("#feedback-li-"+feedback.id).parent('.feedback-list-container');
            //parent_el = $("#feedback-li-"+feedback.id);
            is_update = 1;
        }

        let new_item = `
                        <div class="form-group mb-0 clearfix">
                            <span class="draft-label right-space ${ feedback.published === '0'? '' : 'hidden' }">Draft</span>
                            <i class="fa fa-reply right-space text-muted"></i>
                            <span class="text-muted float-left ${ feedback.published === '0'? 'mt-1' : '' }">You ${ feedback.published === '0'? 'saved' : 'posted' } at ${ moment(feedback.created_at).format('MMMM DD hh:mm a') }</span>
                            <span class="feedback-marker unmarked">
                                <select onchange="methods.setMark(this, ${ feedback.id })" class="hidden">
                                </select>
                            </span>
                            <a class="pull-right edit-reply-btn">
                                <i class="fa fa-pencil"></i> <span>Edit</span>
                            </a>
                        </div>
                        <div class="${ feedback.published === '0'? 'mt-1' : '' } ml-2 message-content">
                            ${ feedback.message || '' }
                        </div>
                        <div class="form-group edit-note-form-container mt-2">

                        </div>
                `;

        if (is_update) {
            prev_el.find("#feedback-li-"+feedback.id).empty();
            prev_el.find("#feedback-li-"+feedback.id).append(new_item);
            parent_el.remove();
        } else {
            prev_el.find("feedback-li-"+feedback.id).remove();
            prev_el.append(`<li class="list-group-item clearfix" id="feedback-li-${ feedback.id }"></li>`);
            prev_el.find("#feedback-li-"+feedback.id).append(new_item);
            parent_el.remove();
        }


        let self = this;

        let marks = {
            'unmarked' : "Unmarked",
            'ignore' : "Ignore",
            'consider' : "Consider",
            'todo' : "Todo",
            'done' : "Done",
            'keep' : "Keep"
        };

        let edit_note_btn = prev_el.find(`#feedback-li-${ feedback.id } .edit-reply-btn`);
        edit_note_btn.click(function(){
            self.editReply(this, feedback)
        });

        if(feedback.published === '1' || feedback.published === 1){
            let select_el = prev_el.find(`#feedback-li-${ feedback.id } select`);
            $.each(marks, function(value , text){
                select_el.append(`
                        <option value="${ value }">${ text }</option>
                        `)
            });
            select_el.val(feedback.mark ? feedback.mark : 'unmarked');
            self.setMark(select_el);
        }
    },

    setMark : function(el, id){
        let classes = "pull-right btn btn-sm";
        $(el).removeClass();
        let btnClass = {
            'unmarked' : "select-unmarked",
            'ignore' : "select-ignore",
            'consider' : "select-consider",
            'todo' : "select-todo",
            'done' : "select-done",
            'keep' : "select-keep"
        };
        let mark = $(el).val();
        $(el).addClass(classes + " " + btnClass[mark]);
        if(id){
            $.post(update_feed_link, { id : id, mark : mark})
                .then(function(response){
                    console.log(response);
                })
                .catch(function(err){
                    console.log(err);
                })
        }
    },

    editFeedback: function(el, feedback) {
        let self = this;
        let list_group_item = $(el).closest(`#note-li-${ feedback.id }`);
        let form_container = list_group_item.find(".edit-note-form-container");
        let message_content = list_group_item.find(".message-content");
        this.addForm(form_container, "edit", feedback.published, feedback.id);

        form_container.removeClass("hidden");
        message_content.addClass("hidden");
        $(el).addClass("hidden");
        let note_wrapper = this.getElements(form_container)['note_wrapper'];
        let cancel_button = note_wrapper.find("button:eq(2)");
        let draft_button = note_wrapper.find("button:eq(1)");
        if(feedback.published === 1 || feedback.published === '1'){
            draft_button.addClass("hidden");
        }
        cancel_button[0].onclick = null;
        cancel_button.click(function(){
            if ($(this).hasClass('danger')) {
                self.discardDraft(el, feedback.id);
            } else {
                self.cancelEditFeedback(this);
                self.cancelNote(this);
            }
        });
        let editor = editors[this.getElements(form_container)['key']];
        editor.updateElement();
        editor.setData(feedback.message);
        note_wrapper.find("[name='feedback_id']").val(feedback.id);
    },

    editReply: function(el, feedback) {
        let self = this;
        let list_group_item = $(el).closest(`#feedback-li-${ feedback.id }`);
        let form_container = list_group_item.find(".edit-note-form-container");
        let message_content = list_group_item.find(".message-content");
        this.addReplyForm(form_container, "edit", feedback.published, feedback.feedback_id);

        form_container.removeClass("hidden");
        message_content.addClass("hidden");
        $(el).addClass("hidden");
        let note_wrapper = this.getElements(form_container)['note_wrapper'];
        let cancel_button = note_wrapper.find("button:eq(2)");
        let draft_button = note_wrapper.find("button:eq(1)");
        if(feedback.published === 1 || feedback.published === '1'){
            draft_button.addClass("hidden");
        }
        cancel_button[0].onclick = null;
        cancel_button.click(function(){
            if ($(this).hasClass('danger')) {
                self.discardDraft(el, feedback.id);
            } else {
                self.cancelEditReply(this);
                self.cancelNote(this);
            }
        });
        let editor = editors[this.getElements(form_container)['key']];
        editor.updateElement();
        editor.setData(feedback.message);
        note_wrapper.find("[name='feedback_id']").val(feedback.id);
    },

    cancelEditFeedback : function(el){
        let list_group_item = $(el).closest(".list-group-item");
        list_group_item.find(".edit-note-form-container").addClass("hidden");
        list_group_item.find(".message-content").removeClass("hidden");
        list_group_item.find(".edit-note-btn").removeClass("hidden");
    },

    cancelEditReply : function(el){
        let list_group_item = $(el).closest(".list-group-item");
        list_group_item.find(".edit-note-form-container").addClass("hidden");
        list_group_item.find(".message-content").removeClass("hidden");
        list_group_item.find(".edit-reply-btn").removeClass("hidden");
    },

    discardDraft : function(el, id){
        let self = this;
        $.post(delete_note_link, { id : id})
            .then(function(response){
                if ($(el).closest('.list-group').hasClass('note-list-container')) {
                    $("#note-li-"+id).remove();
                } else {
                    $("#feedback-li-"+id).remove();
                }
            })
            .catch(function(err){

            })
    }

};