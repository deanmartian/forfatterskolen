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
            $.post(update_note_link, { id : id, mark : mark})
                .then(function(response){
                })
                .catch(function(err){

                })
        }
    },

    uniqueId :function() {
        return Math.round(new Date().getTime() + (Math.random() * 100));
    },

    addNote : function(el){
        let form_group = $(el).closest(".chapter-feedback-item");
        this.addForm(form_group.find(".full-messages"), "add")
    },

    addForm : function(form_container, action, published = 0, note_id = 0){
        let id = this.uniqueId();
        let cancel_button = action === 'add' ?
            '<button class="beta-button danger" onclick="methods.cancelNote(this)"><i class="fa fa-trash"></i> <span>Discard Draft</span></button>'
            : (published ? '<button class="beta-button"><i class="fa fa-ban"></i> <span>Cancel</span></button>'
                : '<button class="beta-button danger"><i class="fa fa-trash"></i> <span>Discard Draft</span></button>');
        form_container.append(`
                    <div class="form-group ${ action }" id="note_wrapper_${ id }">
                       <input type="hidden" name="note_id">
                       <textarea id="note_${ id }_editor"></textarea>
                       <button class="beta-button color success margin-top"  onclick="methods.saveNote(this, 1)">
                        <i class="fa fa-check"></i> <span>Save Note</span>
                       </button>
                       <button class="beta-button color1 margin-top" onclick="methods.saveNote(this, 0)">
                        <i class="fa fa-save"></i> <span>Save Draft</span></button>
                       `+cancel_button+`
                    </div>
                `);
        this.loadDefEditor(`note_${ id }_editor`)
    },

    saveNote : function(el, published){
        let self = this;
        let elem = this.getElements(el);
        let data = { id : elem.note_id, chapter_id : chapter_id, published : published, message : converter.makeHtml(editors[elem.key].getData()) };
        let url = elem.note_wrapper.hasClass("add") ? add_note_link : update_note_link;
        $.post(url, data)
            .then(function(response){
                if(elem.note_wrapper.hasClass("edit")){
                    self.cancelEditNote(el)
                }
                self.cancelNote(el);
                self.listNotes();
            })
            .catch(function(err){

            })
    },

    getElements : function(el){
        let note_wrapper = $(el).closest(".form-group");
        let note_editor = note_wrapper.find("textarea");
        let id = note_editor.get(0).id;
        let key = id.replace("_editor", "");
        let note_id = note_wrapper.find("[name='note_id']").val() || null;
        return { note_wrapper : note_wrapper, note_editor : note_editor, id : id, key : key, note_id : note_id }
    },

    cancelNote : function(el){
        let elem = this.getElements(el);
        editors[elem.key].destroy();
        delete editors[elem.key];
        elem.note_wrapper.remove();
    },

    listNotes : function(){
        let self = this;
        $.get(note_list_link)
            .then(function(response){
                let marks = {
                    'unmarked' : "Unmarked",
                    'ignore' : "Ignore",
                    'consider' : "Consider",
                    'todo' : "Todo",
                    'done' : "Done",
                    'keep' : "Keep"
                };
                let note_list_container = $("#note-list-container");
                note_list_container.html("");
                if (response.length) {
                    $(".chapter-prompt").remove();
                }
                $.each(response, function(key, note){
                    note_list_container.append(`
                    <li class="list-group-item clearfix" id="note-li-${ note.id }">
                        <div class="form-group mb-0 clearfix">
                            <span class="draft-label right-space ${ note.published === 0? '' : 'hidden' }">Draft</span>
                            <span class="text-muted float-left ${ note.published === 0? 'mt-1' : '' }">You ${ note.published === 0? 'saved' : 'posted' } at ${ moment(note.created_at).format('MMMM DD hh:mm a') }</span>
                            <span class="feedback-marker unmarked">
                                <select onchange="methods.setMark(this, ${ note.id })" class="display-none">
                                </select>
                            </span>
                            <a class="pull-right edit-note-btn">
                                <i class="fa fa-pencil"></i> <span>Edit</span>
                            </a>
                        </div>
                        <div class="${ note.published === 0? 'mt-1' : '' } ml-2 message-content">
                            ${ note.message || '' }
                        </div>
                        <div class="form-group edit-note-form-container mt-2">

                        </div>
                    </li>
                `);

                    let edit_note_btn = note_list_container.find(`#note-li-${ note.id } .edit-note-btn`);
                    edit_note_btn.click(function(){
                        self.editNote(this, note)
                    });
                    if(note.published === 1){
                        let select_el = note_list_container.find(`#note-li-${ note.id } select`);
                        $.each(marks, function(value , text){
                            select_el.append(`
                        <option value="${ value }">${ text }</option>
                        `)
                        });
                        select_el.val(note.mark);
                        self.setMark(select_el);
                    }
                })
            })
            .catch(function(err){

            });
    },

    editNote : function(el, note){
        let self = this;
        let list_group_item = $(el).closest(`#note-li-${ note.id }`);
        let form_container = list_group_item.find(".edit-note-form-container");
        let message_content = list_group_item.find(".message-content");
        this.addForm(form_container, "edit", note.published, note.id);
        form_container.removeClass("hidden");
        message_content.addClass("hidden");
        $(el).addClass("hidden");
        let note_wrapper = this.getElements(form_container)['note_wrapper'];
        let testing = note_wrapper.find("button:eq(3)");
        let cancel_button = note_wrapper.find("button:eq(2)");
        let draft_button = note_wrapper.find("button:eq(1)");
        if(note.published === 1){
            draft_button.addClass("hidden");
        }
        cancel_button[0].onclick = null;
        cancel_button.click(function(){
            if ($(this).hasClass('danger')) {
                self.discardDraft(note.id);
            } else {
                self.cancelEditNote(this);
                self.cancelNote(this);
            }
        });
        let editor = editors[this.getElements(form_container)['key']];
        editor.updateElement();
        editor.setData(note.message);
        note_wrapper.find("[name='note_id']").val(note.id)
    },

    cancelEditNote : function(el){
        let list_group_item = $(el).closest(".list-group-item");
        list_group_item.find(".edit-note-form-container").addClass("hidden");
        list_group_item.find(".message-content").removeClass("hidden");
        list_group_item.find(".edit-note-btn").removeClass("hidden");
    },

    discardDraft : function(id){
        let self = this
        $.post(delete_note_link, { id : id})
            .then(function(response){
                self.listNotes()
            })
            .catch(function(err){

            })
    }
};

methods.listNotes();