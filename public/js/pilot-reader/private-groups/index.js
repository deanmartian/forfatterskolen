'use strict';

const methods = {

    data : {
        pathArray : window.location.pathname.split( '/' ),
        converter : new showdown.Converter(),
        editors : {},
        selectedGroup : null,
        tables : []
    },

    loadDefEditor : function(id){
        CKEDITOR.plugins.addExternal( 'autogrow', autogrow_link, 'plugin.js' );
        let key =  id.replace("_editor", "");
        let editors = this.data.editors;
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
            autoGrow_minHeight : 200,
            autoGrow_maxHeight : 300,
            autoGrow_onStartup : true
        })
    },

    createGroup : function(form, modal)
    {
        let self = this;
        $.post('/account/private-groups/create', form.serialize())
            .then(function(response){
                modal.modal('hide');
                toastr.success(response.success, "Success");
                self.addToGroup(response.privateGroup);
            })
            .catch(function(err){
                self.clearError(form);
                let errors = err.responseJSON;
                let status = err.status;
                if(status === 422)
                {
                    self.setError(errors, form)
                }
            });
    },

    addToGroup: function(data){
        let self = this;
        let group_container = $("#group-list-container");
        let list_group = group_container.find('.list-group');

        if(!list_group.find('li').length) {
            group_container.removeClass('display-none');
            $("#no-group-prompt").remove();
        }

        list_group.append(`<li class="list-group-item clearfix">
                                <div class="form-group mb-0 clearfix">
                                    <span class="text-muted font-14">
                                        Member Since: ${data.member_since}
                                    </span>

                                    <div class="ml-2 message-content mt-3">
                                        <p class="mb-0">
                                            <a href="/account/private-groups/${data.id}">${data.group_detail.name}</a>
                                            <span class="badge badge-info color1 py-2">${data.role}</span>
                                        </p>
                                    </div>
                                </div>
                            </li>`);
    },

    setError : function(errors, form){
        $.each(errors, function(key, error){
            form.find(`[name='${ key }']`).closest('.form-group').append(`<small class='text-danger'><i class='fa fa-exclamation-circle'></i> ${ error[0] }</small>`)
        })
    },

    clearError : function(form){
        form.find('.form-group').each(function(){
            $(this).find('.text-danger').remove()
        })
    },

    clearInputs : function(form){
        form.find('.form-group').each(function(){
            let input = $(this).find('input');
            if(input.length > 1)
            {
                $(input[0]).prop('checked', true)
            }else if(input.prop('type') === 'checkbox'){
                input.prop('checked', false)
            }else{
                input.val('')
            }
        })
    },

    inlineEdit : function(el)
    {
        let self = this;
        let parent = $(el).parent();
        parent.addClass("display-none");
        parent.next().removeClass("display-none");
        methods.loadDefEditor("welcome_msg_editor");

        let element = $("#welcome_msg");
        let key = element.get(0).id;
        element.html($("#welcome_msg").html());
        let editors = self.data.editors;
        // check if the editor exists and set a value to the editor
        if(editors[key])
        {
            let editor = editors[key];
            editor.updateElement();
            editor.setData($("#welcome_msg").html())
        }
    },

    inlineSave : function(el)
    {
        let self = this;
        let parent = $(el).parent().parent();
        let group_id = $("[name='group_id']").val();
        let data = { id : group_id };
        let editors = this.data.editors;
        parent.find('textarea').each(function(){
            let key = $(this).prop('name');
            if(editors[key]){
                let editor = editors[key];
                data[key] = self.data.converter.makeHtml(editor.getData())
            }
        });
        $.post('/account/private-groups/update', data)
            .then(function(response){
                toastr.success(response.success, "Success");
                self.reinitializeData(parent);
                $("#welcome_msg").html('');
                $("#welcome_msg").append(response.data.welcome_msg);
            })
    },

    inlineCancel : function(el)
    {
        let parent = $(el).parent().parent();
        this.reinitializeData(parent);
    },

    reinitializeData : function(parent)
    {
        parent.addClass("display-none");
        parent.prev().removeClass("display-none");
        let instance;
        for ( instance in CKEDITOR.instances ){
            CKEDITOR.instances[instance].updateElement();
            CKEDITOR.instances[instance].setData('');
        }
    },

    showDiscussionDivForm : function()
    {

        let self = this;
        let form = $("#discussionForm");
        let collapse = $("#discussionDivForm");
        if(collapse.is(':visible'))
        {
            return
        }
        this.clearInputs(form);
        this.clearError(form);
        form.find('textarea').each(function(){
            self.loadDefEditor($(this).get(0).id)
        });
        collapse.collapse('show');
    },

    closeDiscussionDivForm : function()
    {
        let collapse = $("#discussionDivForm");
        collapse.collapse('hide');
        let form = $("#discussionForm");
        let editors = this.data.editors
        form.find('textarea').each(function(){
            let key = $(this).get(0).name;
            if(editors[key])
            {
                let editor = editors[key];
                editor.updateElement();
                editor.setData('');
            }
        })

    },

    saveDiscussion : function(form)
    {
        let self = this;
        let data = this.data;
        let editors = data.editors;
        form.find('textarea').each(function(){
            let key = $(this).get(0).name;
            if(editors[key])
            {
                let editor = editors[key];
                $(this).val(data.converter.makeHtml(editor.getData()))
            }
        });
        let group_id = $("[name='group_id']").val();
        let form_data = form.serialize() + `&private_group_id=${ group_id }`;
        if(form_data.indexOf("is_announcement=on") > -1)
        {
            form_data = form_data.replace("is_announcement=on", "is_announcement=1")
        }
        $.post('/account/private-groups/discussions/create', form_data)
            .then(function(response){
                self.listDiscussion();
                self.closeDiscussionDivForm();
                toastr.success(response.success, "Success");
            })
            .catch(function(err){
                self.clearError(form);
                let errors = err.responseJSON;
                let status = err.status;
                if(status === 422)
                {
                    self.setError(errors, form)
                }
            })
    },

    listDiscussion : function()
    {
        let self = this;
        let group_id = $("[name='group_id']").val();
        $.get(`/account/private-groups/discussions/list/${ group_id }`)
            .then(function(response){
                self.createTable({
                    table_id : "discussion-table",
                    columns : [ 'subject', 'posts', 'started', 'last_post'],
                    data : response.discussions.data,
                    order : [ 2, 'desc']
                })
            })
    },

    createTable : function(target){
        let table_id = target.table_id;
        let table = this.data.tables[table_id];
        if(table && table instanceof $.fn.dataTable.Api){
            table.clear();
            table.destroy();
            this.data.tables[table_id] = null
        }
        let columns = [];
        $.each(target.columns, function(key, value){
            columns.push({ "data" : value})
        });
        this.data.tables[table_id] = $(`#${table_id}`).DataTable( {
            data: target.data,
            columns: columns,
            responsive: true,
            order : [ target.order ],
            columnDefs: [
                {
                    orderable: table_id !== "pending-table" && table_id !== "members-table"  && table_id !== "books-table",
                    targets: -1
                },
            ],
            pageLength : 5,
            lengthChange: false
        } )
    },

    shareBook : function()
    {
        let self = this;
        let form = $(".add-book-div");
        let group_id = $("[name='group_id']").val();
        let book_id =  $("[name='book_id']").val();
        $.post('/account/private-groups/shared-book/share', {
            private_group_id : group_id,
            book_id : book_id
        })
            .then(function(response){
                self.clearInputs(form);
                self.clearError(form);
                toastr.success(response.success, "Success");
                $("#add_a_book_select").find("option[value="+book_id+"]").remove();
                self.loadSharedBooks()
            })
            .catch(function(err){
                self.clearError(form);
                let errors = err.responseJSON;
                let status = err.status;
                if(status === 422)
                {
                    self.setError(errors, form)
                }
            })
    },

    loadSharedBooks : function()
    {
        let self = this;
        let role = member_role;
        let group_id = $("[name='group_id']").val();
        $.get('/account/private-groups/shared-book/list/' + group_id)
            .then(function(response){
                let book_shared = response.book_shared.data;
                let table_id = "books-table"
                let table = $(`#${ table_id }`);
                table.off('click');
                $.each(book_shared, function(key, book){
                    let book_title = book.title;
                    let visibility = book.visibility;
                    book.title = `<a class="font-weight-bold no-underline" href="/account/book-author/book/${ book.book_id }">` + (visibility === 1? `<h5 class="d-inline-block pb-0 mb-0 mr-2"><span class="badge badge-success right-space">Featured</span></h5>` : ``) + book_title + '</a>'
                    if(role !== "manager")
                    {
                        delete book.visibility;
                        $(".manager-th").addClass("display-none")
                    }else{
                        book.visibility = `<select class="form-control" onchange="methods.setVisibility(this, ${ book.id })">
                                          <option value="1" ${ visibility === 1? 'selected' : ''}>Featured</option>
                                          <option value="2" ${ visibility === 2? 'selected' : ''}>Available</option>  
                                          <option value="0" ${ visibility === 0? 'selected' : ''}>Hidden</option>  
                                       </select>`;
                        book.action = `<button class="btn btn-outline-danger btn-sm" id="remove_btn_${  book.id }">Remove</button>`;
                        table.on('click', `#remove_btn_${  book.id }`, function(){
                            self.removeSharedBook(book, this)
                        })
                        $(".manager-th").removeClass("display-none")
                    }
                })
                let columns = ['title', 'author', 'shared_on'];
                if(role === "manager")
                {
                    columns.push('visibility', 'action')
                }
                self.createTable({
                    table_id : table_id,
                    columns : columns,
                    data : book_shared,
                    order : [ 2, 'desc']
                })
            })
    },

    setVisibility : function(el, id)
    {
        let self = this;
        let element = $(el);
        let visibility = parseInt(element.val());
        $.post('/account/private-groups/shared-book/update', {
            visibility : visibility,
            id : id
        })
            .then(function(response){
                self.updateVisibilityLabel(element.closest('tr').find('td:first-child'), visibility)
            })
    },

    updateVisibilityLabel : function(td, visibility)
    {
        let a = td.find('a.no-underline');
        if(visibility === 1)
        {
            let title = a.html();
            a.html(`<h5 class="d-inline-block pb-0 mb-0 mr-2"><span class="badge badge-success right-space">Featured</span></h5>` + title)
        }else{
            a.find('h5.d-inline-block').remove()
        }
    },

    removeSharedBook : function(book, btn)
    {
        let self = this;
        let visibility = parseInt($(btn).parent().prev().find('select').val());
        $.confirm({
            title: 'Remove Shared Book',
            content: 'Are you sure to remove it?',
            type: 'red',
            typeAnimated: true,
            buttons: {
                tryAgain: {
                    text: 'Ok',
                    btnClass: 'btn-red',
                    action: function(){
                        $.post('/account/private-groups/shared-book/remove', {
                            id : book.id
                        })
                            .then(function(response){
                                toastr.success(response.success, "Success");
                                self.loadSharedBooks();
                            })
                    }
                },
                close: function () {
                }
            }
        })
    },

    viewPreference : function()
    {
        let group_id = $("[name='group_id']").val();
        $.get('/account/private-groups/preferences/get/' + group_id)
            .then(function(response){
                $("[name=email_notifications_option][value="+response.email_notifications_option+"]").prop('checked', true);
            })
    },

    setPreference : function()
    {
        let group_id = $("[name='group_id']").val();
        let email_notifications_option = $("[name='email_notifications_option']:checked").val();
        $.post('/account/private-groups/preferences/set', {
            private_group_id : group_id,
            email_notifications_option : email_notifications_option
        })
            .then(function(response){
                toastr.success(response.success, "Success")
            })
    },

};

$("#createPrivateGroupForm").submit(function(e){
    e.preventDefault();
    methods.createGroup($(this), $("#createPrivateGroupModal"));
});

$("#createPrivateGroupModal").on('hidden.bs.modal', function(){
    methods.clearInputs($("#createPrivateGroupForm"));
    methods.clearError($("#createPrivateGroupForm"));
});

$("#discussionForm").submit(function(e){
    e.preventDefault();
    methods.saveDiscussion($(this));
});

let url = window.location.pathname;
let current_page = url.substr(url.lastIndexOf('/') + 1);
if (current_page === "discussions") {
    methods.listDiscussion();
}

if (current_page === "books") {
    methods.loadSharedBooks();
}

if (current_page === "preferences") {
    methods.viewPreference();
}
