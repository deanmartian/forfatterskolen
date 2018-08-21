'use strict';

const methods = {
    data : {
        form : null,
        tables : [],
        table_elements : {
            'sent' : {
                table_id : 'sent-queries-table',
                data : [],
                columns : [ 'to', 'book', 'received', 'status', 'action']
            },
            'received' : {
                table_id : 'received-queries-table',
                data : [],
                columns : [ 'from', 'book', 'received', 'status', 'action']
            }
        }
    },

    setSearchMethod : function(value)
    {
        let simpleSearchbox = $("#simpleSearchbox");
        let advancedSearchbox = $("#advancedSearchbox");
        simpleSearchbox.css({ "display" : (value === "simple"? "block" : "none") });
        advancedSearchbox.css({ "display" : (value === "advanced"? "block" : "none") });
    },

    listReaderDirectory : function(url)
    {
        let self = this;
        let current_url = url || '/account/reader-directory/list';
        let data = this.data.form? this.data.form.serialize() : {};
        $.post(current_url, data)
            .then(function(response){
                self.displayReaderProfile(response)
            })
    },

    displayReaderProfile : function(list)
    {
        let resultList = $("#resultList");
        resultList.html("");
        let self = this;
        $.each(list.data, function(key, item){
            let user = item.user;
            let full_name = user.first_name + " " + user.last_name;
            resultList.append(`
            <div class="card mb-3">
                <div class="card-header bg-site-red text-white p-1">
                    <span class="d-block pull-left mt-1 ml-2">${ full_name }</span>
                    <button class="btn btn-outline-light btn-sm pull-right" data-toggle="modal" data-target="#queryReaderModal" data-name="${ full_name }" data-id="${ user.id }">Query This Reader</button>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4">
                            Genre Preferences
                        </div>
                        <div class="col">
                            ${ self.boldString(item.genre_preferences || '', 'genre_preferences') }
                        </div>
                    </div>
                    <hr/>
                    <div class="row">
                        <div class="col-md-4">
                            Doesnt Want To Read...
                        </div>
                        <div class="col">
                            ${ self.boldString(item.dislike_contents || '', 'dislike_contents') }
                        </div>
                    </div>
                    <hr/>
                    <div class="row">
                        <div class="col-md-4">
                            Expertise
                        </div>
                        <div class="col">
                            ${ self.boldString(item.expertise || '', 'expertise') }
                        </div>
                    </div>
                    <hr/>
                    <div class="row">
                        <div class="col-md-4">
                            Favorite Authors
                        </div>
                        <div class="col">
                            ${ self.boldString(item.favourite_author || '', 'favourite_author') }
                        </div>
                    </div>
                </div>
            </div>
            `)
        });
        if(list.data.length)
        {
            this.createPagination(resultList, list)
        }else{
            resultList.append(`<p class="text-center lead"><i class="fa fa-ban"></i> No Record Found.</div>`)
        }
    },

    createPagination : function(container, pagination){
        container.append(`
                    <div class="form-group mt-3">
                    <nav aria-label="Page navigation example">
                        <ul class="pagination justify-content-center">
                            <li class="page-item ${ pagination.prev_page_url? '' : 'disabled' }"><a class="page-link" href="javascript:void(0)" onclick="methods.paginateList('${ pagination.prev_page_url }')"><<</a></li>
                            <li class="page-item disabled"><a class="page-link" href="#">Page ${ pagination.current_page } of ${ pagination.last_page }</a></li>
                            <li class="page-item ${ pagination.next_page_url? '' : 'disabled' }"><a class="page-link" href="javascript:void(0)" onclick="methods.paginateList('${ pagination.next_page_url }')">>></a></li>
                        </ul>
                    </nav>
                    </div>
                `)
    },

    paginateList : function(url)
    {
        this.listReaderDirectory(url)
    },

    boldString : function (str, col){
        if(this.data.form)
        {
            let mode = $("[name='search_mode']:checked").val();
            let input_name = mode === "simple"? 'search' : col;
            let find = $(`[name='${ input_name }']`).val();
            let regex = new RegExp(find, 'g');
            str = str.replace(regex, '<b>'+ find +'</b>');
        }
        return str
    },

    listBook : function(author_id, form)
    {
        $.post('/account/reader-directory/list/book', {
            author_id : author_id
        })
            .then(function(response){
                let select = form.find("[name='book_id']");
                select.html("");
                select.append(`<option value="">---Select---</option>`);
                $.each(response, function(key, book){
                    select.append(`
                    <option value="${ book.id }">${ book.title }</option>
                `)
                })
            })
    },

    queryReader : function(form, modal)
    {
        let self = this;
        $.post('/account/reader-directory/query/sent', form.serialize() )
            .then(function(response){
                self.clearError(form);
                toastr.success(response.success, "Success");
                $(modal).modal('hide');
            })
            .catch(function(err){
                self.clearError(form);
                let error = err.responseJSON;
                let status = err.status;
                if(status === 422)
                {
                    self.setError(form, error)
                }
            })
    },

    setError : function(form, error)
    {
        $.each(error, function(key, err){
            form.find(`[name='${key}']`).after(`<small class="text-danger"><i class="fa fa-exclamation-circle"></i> ${ err[0] }</small>`)
        })
    },

    clearError : function(form){
        form.find('.form-group').each(function(){
            $(this).find("small.text-danger").remove()
        })
    },

    clearInput : function(form)
    {
        form.find('.form-control').each(function(){
            $(this).val('')
        })
    },

    listQuery : function(list)
    {
        let self = this;
        $.post('/account/reader-directory/query/list', {
            list : list
        })
            .then( function(response){
                let table_el = self.data.table_elements[list];
                table_el.data = response.queries.data;
                let table = $(`#${ table_el.table_id }`);
                table.off('click');
                $.each(table_el.data, function(key, val){
                    val.status = self.setStatusColor(val.status);
                    val.action = `<div class="text-center"><button class="btn btn-primary btn-sm btn-block" id="view_btn_${ val.id }">View</button></div>`;
                    table.on('click', `#view_btn_${ val.id }`, function(){
                        self.viewQueryDetail(val, list)
                    })
                });
                self.createTable(table_el)
            })
    },

    createTable : function(target)
    {
        let table_id = target.table_id
        let table = this.data.tables[table_id]
        if(table && table instanceof $.fn.dataTable.Api){
            table.clear()
            table.destroy()
            this.data.tables[table_id] = null
        }
        let columns = [];
        $.each(target.columns, function(key, value){
            columns.push({ "data" : value})
        })
        this.data.tables[table_id] = $(`#${table_id}`).DataTable( {
            data: target.data,
            columns: columns,
            responsive: true,
            order : [[ 2, 'desc']],
            columnDefs: [
                {
                    orderable: false,
                    targets: -1
                },
            ],
            pageLength : 5,
            lengthChange: false
        } )
    },

    setStatusColor : function(status){
        let color = ['info', 'success', 'danger'];
        let text = ['Pending', 'Accepted', 'Declined'];
        return `<span class="badge badge-${ color[status] } p-2">${ text[status]}</span>`;
    },

    viewQueryDetail : function(query, list)
    {
        let self = this;
        let modal = $("#queryReaderDetailModal");
        $("#collapseDiv").collapse('hide');
        modal.modal('show');
        let book_details = query.book_details;
        modal.find("[id*='div']").each(function(){
            let id = $(this).get(0).id;
            let key = id.replace("_div", "");
            let data = query[key];
            if(data)
            {
                $(this).html(data)
            }
            if(key == 'decision')
            {
                let your_decision_div = $("#your_decision_div");
                your_decision_div.addClass("display-none");
                let decision_div = $("#decision_div");
                decision_div.removeClass("display-none");
                if(!data && list == 'received')
                {
                    decision_div.addClass("display-none");
                    your_decision_div.removeClass("display-none");
                    let form = $("#decisionForm");
                    self.clearInput(form)
                    modal.find("input[name='book_id']").val(book_details.id);
                    modal.find("input[name='query_id']").val(query.id);
                    return true
                }
                $(this).html(data? data.decision : `${ query.to } has not decided yet`);
                $(`#${ key }_submitted_date_div`).html(data? data.submitted_date : '')
            }
        });
        modal.find(".preview-btn").off("click");
        modal.find(".preview-btn").click(function(){
            self.bookPreview(book_details, modal, $(this))
        })
    },

    bookPreview : function(details, modal, btn)
    {
        let icon = btn.find("i.fa");
        let book_icon = "fa fa-book";
        let check = icon.hasClass(book_icon);
        book_icon += (check? "-open " : " ") + ("fa-fw");
        icon.removeClass().addClass(book_icon);
        modal.find("#collapseDiv [id*='div']").each(function(){
            let id = $(this).get(0).id;
            let key = id.replace("_div", "");
            let data = details[key];
            if(data)
            {
                $(this).html(data)
            }
            if(key.indexOf('chapter') > -1)
            {
                let chapter = details.chapter;
                if(chapter.id)
                {
                    let check = key.indexOf('chapter_title') > -1;
                    $(this).html(check? chapter.title : chapter.chapter_content)
                }
            }
        })
    },

    submitQueryDecision : function(form, modal)
    {
        let self = this;
        $.post('/account/reader-directory/query/decision/submit',  form.serialize())
            .then(function(response){
                self.clearInput(form);
                modal.modal('hide');
                self.listQuery('received');
                toastr.success(response.success, "Success")
            })
    }

};

methods.listReaderDirectory();

$("input[name='search_mode']").change(function(){
    methods.setSearchMethod($(this).val());
});

$(".searchBoxForm").submit(function(e){
    e.preventDefault();
    methods.data.form = $(this);
    methods.listReaderDirectory();
});

$("#queryReaderModal").on('show.bs.modal', function(e){
    let relatedTarget = $(e.relatedTarget);
    let user_id = relatedTarget.data('id');
    let author_name = relatedTarget.data('name');
    let form = $("#queryReaderForm");
    form.find("span#author_name").html(author_name);
    form.find("input[name='to']").val(user_id);
    methods.listBook(user_id, form);
    methods.clearError(form);
});

$("#queryReaderForm").submit(function(e){
    e.preventDefault();
    methods.queryReader($(this), $("#queryReaderModal"))
});

$("#decisionForm").submit(function(e){
    e.preventDefault();
    methods.submitQueryDecision($(this), $("#queryReaderDetailModal"))
});