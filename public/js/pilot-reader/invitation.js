'use strict';

const settings = {
    data : {
        emails : [],
        form : null,
        tables : {

        },
        invitations : [
            {
                status : 0,
                data : [],
                columns : ['name', 'date', 'send_count', 'action'],
                table_id : 'pending_table',
                order : [ 1, "desc"]
            },
            {
                status : 2,
                data : [],
                columns : ['name', 'date'],
                table_id : 'decline_table',
                order : [ 1, "desc"]
            },
            {
                status : 1,
                data : [],
                columns : ['name', 'role', 'started_at', 'removed_at', 'action'],
                table_id : 'readers_table',
                order : [ 2, "desc"]
            },
            {
                status : 3,
                data : [],
                columns : ['name', 'role', 'finished_at', 'action'],
                table_id : 'finished_table',
                order : [ 1, "desc"]
            },
            {
                status : 4,
                data : [],
                columns : ['name', 'quitted_at', 'action'],
                table_id : 'quitted_table',
                order : [ 1, "desc"]
            }
        ]
    },

    listInvitations : function(tab_index, status){
        let self = this;
        $.get(`/account/book-author/book/${ $("[name='book_id']").val() }/list-invitation/${ status }`)
            .then(function(response){
                const filtered_invites = response.filtered_invites;
                const invitation_list = self.data.invitations;
                let invitations = invitation_list[tab_index];
                if(!_.isEmpty(invitations.data)){
                    let data = JSON.parse(JSON.stringify(invitations.data));
                    _.map(data, function(value, key){
                        return delete value['action']
                    });
                    if(_.isEqual(data, filtered_invites.data)){
                        return
                    }
                }
                invitations.data = filtered_invites.data;
                let table_id = `#${ invitation_list[0].table_id}`;
                let table_data = invitation_list[0].data;
                $(table_id).off('click');
                $.each(table_data, function(key, value){
                    value.action = `
                <button class="btn btn-info btn-sm" type="button" id="reset_btn_${ value.id }">
                    <i class="fa fa-spinner fa-pulse fa-fw hidden"></i> Resend
                </button>
                <button class="btn btn-danger btn-sm" type="button" id="cancel_btn_${ value.id }">
                    Cancel
                </button>`;
                    $(table_id).on('click', `#reset_btn_${ value.id }`, function(){
                        self.resendInvites({ value : value, input : $(this)})
                    });
                    $(table_id).on('click', `#cancel_btn_${ value.id }`, function(){
                        self.cancelInvite(value)
                    });
                });
                table_id = `#${ invitation_list[2].table_id}`;
                table_data = invitation_list[2].data;
                $(table_id).off('click');
                if(! isMyBooks)
                {
                    $(table_id).find(".role").remove();
                    $(table_id).find(".action").remove();
                    invitation_list[2].order[0] = 2
                }
                $.each(table_data, function(key, value){
                    value.action = `
                <button class="btn btn-${ value.removed_at? 'success' : 'danger'} btn-sm" type="button" id="restore_remove_btn_${ value.id }">
                    ${ value.removed_at? 'Restore' : 'Remove'}
                </button>`;

                    if(! isMyBooks)
                    {
                        delete value.role;
                        delete value.action;
                        return true
                    }

                    let role = value.role;
                    value.role = `<select class="form-control" onchange="settings.setReaderRole(${ value.id }, this)">
                                    <option value="reader" ${ role === "reader"? 'selected' : ''}>Reader</option>
                                    <option value="viewer" ${ role === "viewer"? 'selected' : ''}>Viewer</option>
                                    <option value="collaborator" ${ role === "collaborator"? 'selected' : ''}>Collaborator</option>
                              </select>`;

                    $(table_id).on('click', `#restore_remove_btn_${ value.id }`, function(){
                        self.restoreOrRemoveReader(value)
                    })
                });

                table_id = `#${ invitation_list[3].table_id}`;
                table_data = invitation_list[3].data;
                $(table_id).off('click');
                if(! isMyBooks)
                {
                    $(table_id).find(".role").remove();
                    $(table_id).find(".action").remove();
                    invitation_list[3].order[0] = 1
                }
                $.each(table_data, function(key, value){
                    value.action = `
                <button class="btn btn-${ value.removed_at? 'success' : 'danger'} btn-sm" type="button" id="restore_remove_btn_${ value.id }">
                    ${ value.removed_at? 'Restore' : 'Remove'}
                </button>`;

                    if(! isMyBooks)
                    {
                        delete value.role;
                        delete value.action;
                        return true
                    }

                    let role = value.role;
                    value.role = `<select class="form-control" onchange="settings.setReaderRole(${ value.id }, this)">
                                    <option value="reader" ${ role === "reader"? 'selected' : ''}>Reader</option>
                                    <option value="viewer" ${ role === "viewer"? 'selected' : ''}>Viewer</option>
                                    <option value="collaborator" ${ role === "collaborator"? 'selected' : ''}>Collaborator</option>
                              </select>`;

                    $(table_id).on('click', `#restore_remove_btn_${ value.id }`, function(){
                        self.restoreOrRemoveReader(value)
                    })
                });

                table_id = `#${ invitation_list[4].table_id}`;
                table_data = invitation_list[4].data;
                $(table_id).off('click');
                $.each(table_data, function(key, value){
                    value.action = ` <button class="btn btn-outline-info btn-sm" id="view_reason_btn_${ value.id }">View</button>`;

                    $(table_id).on('click', `#restore_remove_btn_${ value.id }`, function(){
                        self.restoreOrRemoveReader(value)
                    });

                    $(table_id).on('click', `#view_reason_btn_${ value.id }`, function(){
                        self.viewReason(value);
                    })
                });

                self.createTable(invitations);
            })
            .catch(function(error){
                console.log(error);
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
            if(! isMyBooks && (value === 'role' || (value === 'action' && target.status === 1)
                || (value === 'action' && target.status === 3)))
            {
                return true
            }
            columns.push({ "data" : value})
        });
        this.data.tables[table_id] = $(`#${table_id}`).DataTable( {
            data: target.data,
            columns: columns,
            responsive: true,
            order : [ target.order ],
            columnDefs: [
                {
                    orderable: table_id !== "pending_table" && table_id !== "readers_table" && table_id !== "finished_table",
                    targets: -1
                },
            ],
            pageLength : 5,
            lengthChange: false
        } )
    },

    cancelInvite : function(value){
        let cancelModal = $("#cancelModal");
            cancelModal.modal();

        // find the modal cancel button
        cancelModal.find(".modal-footer").find('.btn-danger').attr('onclick', `settings.processCancel('${value.id}')`);

    },

    processCancel: function(value) {
        let self = this;
        $.post('/account/book-author/book/invitation/cancel', {
            id : value
        }).then(function(response){
            self.listInvitations(0, 0);
            toastr.success(response.success, "Success");
            $("#cancelModal").modal('hide');
        })
            .catch(function(error){

            });
    },

    resendInvites : function(data){
        let self = this;
        let value = data.value;
        if(value.send_count >= 3){
            toastr.error("You've reached the maximum sending of invitation.", "Error");
            return false
        }
        let input = data.input;
        let spinner = input.find('.fa.fa-spinner');
        spinner.removeClass("hidden");
        input.addClass("disabled");
        input.prop("disabled", true);
        let book_id = $("[name=book_id]").val();
        $.post('/account/book-author/book/'+book_id+'/invitation', {
            emails : [ value.email ],
            msg : null
        })
            .then(function(response){
                input.prop("disabled", false);
                input.removeClass("disabled");
                spinner.addClass("hidden");
                self.listInvitations(0, 0);
                toastr.success(response.success, "Success")
            })
            .catch(function(error){
                console.log(error, "error")
            })
    },

    restoreOrRemoveReader : function(value){
        let removeModal = $("#removeModal"),
            modalTitle = value.removed_at? 'Restore Reader' : 'Remove Reader';
        removeModal.find('.modal-title').text(modalTitle);
        removeModal.find('.modal-body').text('Are you sure to ' + (value.removed_at? 'restore' : 'remove') + ' it?');

        removeModal.modal();
        let removed_at = value.removed_at ? 1 : 0;

        // find the modal cancel button
        removeModal.find(".modal-footer").find('.btn-danger').attr('onclick', `settings.processRemoveRestore('${value.id}', '${removed_at}')`);
    },

    viewReason : function(value)
    {
        let modal = $("#quittedModal");
        modal.modal('show');
        let fullname = value.name.split("<br/>")[0];
        modal.find("#quittedModalLongTitle").html(`${ fullname }'s Reason`);
        modal.find("#reason_pre").html(value.reasons.reasons);
        modal.find("#date_span").html(moment(value.reasons.created_at).format('MMMM DD hh:mm a'));
    },

    setReaderRole : function(id, el)
    {
        $.post('/account/book/settings/reader/role/set', {
            id : id,
            role : $(el).val()
        })
            .then(function(response){
                toastr.success(response.success, "Success")
            })
    },

    processRemoveRestore: function(id, removed) {
        let self = this;
        let action = parseInt(removed) ? 'restore' : 'trashed';

        $.post('/account/book-author/book/reader/restore-remove', {
            id : id,
            action : action
        })
            .then(function(response){
                self.listInvitations(2, 1);
                self.listInvitations(3, 3);
                self.listInvitations(4, 4);
                toastr.success(response.success, "Success");
                $("#removeModal").modal('hide');
            })
            .catch(function(error){

            })
    },

    getLink : function(request, el)
    {
        let self = this;
        let input = $(el);
        let book_id = $("[name='book_id']").val();
        let data = { book_id : book_id };
        if(request === 'toggle'){
            data.enabled = +input.prop('checked')
        }
        $.post('/account/book-author/book/settings/invite/link/get', data)
            .then(function(response){
                response.enabled = parseInt(response.enabled);
                input.prop("checked", response.enabled);
                self.toggleLink(input, response)
            })
    },

    toggleLink : function(input, data)
    {
        let shareable_link_div = $("#shareable_link_div");
        shareable_link_div.css("display", data.enabled? 'block' : 'none');
        shareable_link_div.find(".form-control").val(data.link);
    },

    copyToClipboard : function(el)
    {
        let copyText = $(el).closest('.input-group-global').find('.form-control');
        /* Select the text field */
        copyText.select();
        /* Copy the text inside the text field */
        document.execCommand("copy");
        toastr.success('Copied');
        if (window.getSelection) {
            if (window.getSelection().empty) {  // Chrome
                window.getSelection().empty();
            } else if (window.getSelection().removeAllRanges) {  // Firefox
                window.getSelection().removeAllRanges();
            }
        } else if (document.selection) {  // IE?
            document.selection.empty();
        }
    },
};

$(".invitation-manager .nav-tabs li:first-child>a").trigger('click');