'use strict';

const methods = {

    data : {
        converter : new showdown.Converter(),
        editors : {},
        discussion : null
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

    inlineEdit : function(el)
    {
        let parent = $(el).parent();
        parent.addClass("display-none");
        parent.next().removeClass("display-none");
    },
    inlineSave : function(el)
    {
        let self = this;
        let parent = $(el).parent().parent();
        let data = { id : discussion_id };
        let editors = this.data.editors;
        parent.find('textarea').each(function(){
            let key = $(this).prop('name');
            if(editors[key]){
                let editor = editors[key];
                data[key] = self.data.converter.makeHtml(editor.getData())
            }
        });
        $.post('/account/private-groups/discussion/update', data)
            .then(function(response){
                self.data.discussion = response.data;
                self.reinitializeData(parent)
            })
    },
    inlineCancel : function(el)
    {
        let parent = $(el).parent().parent();
        this.reinitializeData(parent)
    },
    reinitializeData : function(parent)
    {
        parent.addClass("display-none");
        let discussion = this.data.discussion;
        let editors = this.data.editors;
        parent.find('textarea').each(function(){
            let element = $(this);
            let key = element.get(0).name;
            if(editors[key])
            {
                let editor = editors[key];
                editor.updateElement();
                editor.setData(discussion[key])
            }
            $(`[data-id=${ key }]`).html(discussion[key])
        });
        parent.prev().removeClass("display-none");
    },

    loadReplies : function(replies)
    {
        let discussion_replies_ul = $("#discussion-replies-ul");
        discussion_replies_ul.html('');
        $.each(replies, function(key, reply){
            let author = reply.user;
            discussion_replies_ul.append(`
                <li class="list-group-item clearfix">
                    <div class="clearfix font-weight-light text-muted">
                        <span class="pull-left">${ author.first_name + ' ' + author.last_name }</span>
                        <span class="pull-right">${ reply.date }</span>
                    </div>
                    <div class="form-group clearfix mb-0 pb-0">
                         <div class="lead-17 mb-0">${ reply.message }</div>
                         <button class="btn btn-outline-primary btn-sm pull-right ${ !reply.is_owner? 'display-none' : ''}" onclick="methods.addForm(this)">Edit</button>
                    </div>
                    <div class="form-group edit display-none mb-0 pb-0 mt-2" data-id="${ reply.id }">
                    </div>
                </li>
            `)
        })
    },

    getDiscussionReplies: function() {
        let self = this;
        $.get(`/account/private-groups/discussion/replies/get/${ discussion_id}`)
            .then(function(response){
                let discussion = response.discussion.data[0];
                self.loadReplies(discussion.replies);
            })
    },

    uniqueId :function() {
        return Math.round(new Date().getTime() + (Math.random() * 100));
    },

    addForm : function(el)
    {
        let parent = $(el).parent();
        parent.addClass('display-none');
        let next = parent.next();
        next.removeClass('display-none');
        let isAdding = next.hasClass('add');
        let name = 'textarea_' + this.uniqueId();
        next.html(`
            ${ isAdding? '<label class="label-control">Your Reply</label>' : '' }
            <textarea class="form-control" id="${ name }_editor" name="${ name }"></textarea>
            <div class="form-group clearfix mt-2 mb-0 pb-0">
                <button class="btn btn-primary btn-sm pull-right" onclick="methods.saveItem(this)">${ isAdding? 'Submit' : 'Save' }</button>
                <button class="btn btn-danger btn-sm pull-right mr-1" onclick="methods.cancelItem(this)">Cancel</button>
            </div>
       `);
        this.loadDefEditor(`${ name }_editor`);
        if(!isAdding)
        {
            let editor = this.data.editors[name];
            editor.updateElement();
            editor.setData($(el).prev().html())
        }
    },

    cancelItem : function(el)
    {
        let parent = $(el).parent().parent();
        parent.addClass('display-none');
        parent.html('');
        parent.prev().removeClass('display-none');
    },

    saveItem : function(el)
    {
        let self = this;
        let data = { disc_id : discussion_id};
        let parent = $(el).parent().parent();
        let isAdding = parent.hasClass('add');
        let key = parent.find('textarea').get(0).name;
        let editor = this.data.editors[key];
        data.message = this.data.converter.makeHtml(editor.getData());
        if(!isAdding)
        {
            data.id = parent.data('id')
        }
        $.post(`/account/private-groups/discussion/reply/${ isAdding? 'create' : 'update'}`, data)
            .then(function(response){
                self.cancelItem(el);
                self.getDiscussionReplies();
            })
    }
};

methods.getDiscussionReplies();

let key = 'message';
let value = $("#message").html();
methods.loadDefEditor(`message_editor`);
let editor = methods.data.editors[key];
editor.updateElement();
editor.setData(value);