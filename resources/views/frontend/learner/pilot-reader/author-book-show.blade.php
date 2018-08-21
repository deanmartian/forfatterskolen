@extends('frontend.layout')

@section('title')
    <title>{{ $book->title }} &rsaquo; Forfatterskolen</title>
@stop

@section('heading') My Books @stop

@section('content')
    <div class="account-container">

        @include('frontend.partials.learner-menu')

        <div class="col-sm-12 col-md-10 sub-right-content show-book white-background">
            <div class="col-sm-12">
                <div class="col-md-8 col-sm-offset-2 col-sm-12 margin-top">
                    @include('frontend.learner.pilot-reader.partials.nav')
                    @if ($book->user_id == Auth::user()->id || ($reader && $reader->role == 'collaborator'))
                        @include('frontend.learner.pilot-reader.partials.author-book-details')
                    @else
                        @include('frontend.learner.pilot-reader.partials.reader-book-details')
                    @endif

                </div><!-- col-xs-offset-2 col-xs-8 margin-top -->

            </div>
        </div>

        <div class="clearfix"></div>
    </div>

    <div id="deleteChapterModal" class="modal fade" role="dialog" data-backdrop="static" data-keyboard="false">
        <div class="modal-dialog modal-sm">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Delete Chapter</h4>
                </div>
                <div class="modal-body">
                    <form method="POST" action="">
                        {{ csrf_field() }}
                        {{ method_field('delete') }}

                        <p>Are you sure you want to delete this chapter?</p>
                        <div class="text-right margin-top">
                            <button type="submit" class="btn btn-primary">ja</button>
                            <button type="button" class="btn btn-danger" data-dismiss="modal">Nei</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="bulkImportsChaptersModal" tabindex="-1" role="dialog" aria-labelledby="bulkImportsChaptersModalTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Import Chapters</h4>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <div class="custom-file">
                        <input type="file" class="custom-file-input" id="customFile" onchange="methods.browseHtmlFile(this)">
                        <label class="custom-file-label" for="customFile">Choose file</label>
                        <small class="text-danger ml-2 hidden"><i class="fa fa-exclamation-circle"></i> Invalid File</small>
                    </div>
                    <div class="hidden" id="no-chapters-div" style="margin-top: 20px;">
                        <small class="text-danger ml-2"><i class="fa fa-exclamation-circle"></i> No Chapter Found</small>
                        <small class="text-muted ml-2 d-block">Please make sure you added an heading for every chapter and try again.</small>
                    </div>
                    <div class="clearfix" style="margin-top: 15px;"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary disabled" id="import-btn" onclick="methods.extractChapters()" disabled>Import</button>
                </div>
            </div>
        </div>
    </div>

    <input type="hidden" name="book_id" value="{{ $book->id }}">
@stop

@section('scripts')
    <script src="https://cdn.tinymce.com/4/tinymce.min.js"></script>
    <script src="//ajax.googleapis.com/ajax/libs/jqueryui/1.10.4/jquery-ui.min.js"></script>
    <script src="{{ asset('js/showdown/dist/showdown.min.js') }}"></script>
    <script>

        $(document).ready(function(){
            // Sortable rows
            $(".draggable-table tbody").sortable({
                items: "tr",
                cursor: 'move',
                opacity: 0.6,
                update: function() {
                    sendOrderToServer(this);
                }
            });

            $(".deleteChapterBtn").click(function(){
               var action = $(this).data('action');
               $("#deleteChapterModal").find('form').attr('action', action);
            });

            // toggle hide/unhide
            $(".toggle-hide").click(function(){
                var t = $(this);
                var tr = $(this).closest('tr'),
                    trID = tr.attr('id').split('chapter_')[1],
                    is_hidden = 0;

                if ($(this).find('span').text() === 'Hide') {
                    is_hidden = 1;
                }

                var data = { field: 'is_hidden', value: is_hidden, _token: '{{ csrf_token() }}'};

                $.ajax({
                   type: "POST",
                   dataType: "json",
                   url: "/account/book/chapter/"+trID+"/update-field",
                   data: data,
                   success: function(response) {
                       if (response.success) {
                           if (t.find('span').text() === 'Hide') {
                               t.find('.fa').removeClass('fa-lock').addClass('fa-unlock');
                               t.find('span').text('Unhide');
                               tr.addClass('is-hidden');
                               tr.find('td.title').append('<span class="hide-indicator">(Hidden)</span>');
                           } else {
                               t.find('.fa').removeClass('fa-unlock').addClass('fa-lock');
                               t.find('span').text('Hide');
                               tr.removeClass();
                               tr.find('td.title').find('.hide-indicator').remove();
                           }
                       }
                   }
                });
            });

        });

        function processTitle(t) {
            var tr = $(t).closest('tr'),
                td = tr.find('td.title'),
                current_title = td.find('.title').text() === 'Untitled' ? '' : td.find('.title').text();

            if (!td.hasClass('editing')) {
                td.addClass('editing');
                td.find('.title').remove();
                td.prepend('<input type="text" class="title-input" value="'+current_title+'">');
                titleInputBlur();
            }
        }

        function titleInputBlur() {
            $(".title-input").focus()
                .blur(function(){
                processTitleInput(this);
            }).on('keyup', function (e) {
                if (e.keyCode === 13) {
                    processTitleInput(this);
                }
            });
        }

        function processTitleInput(t) {
            var tr = $(t).closest('tr'),
                trID = tr.attr('id').split('chapter_')[1],
                td = tr.find('td.title'),
                text = $(t).val();

            var data = { field: 'title', value: text, _token: '{{ csrf_token() }}'};
            $.ajax({
                type: "POST",
                dataType: "json",
                url: "/account/book/chapter/"+trID+"/update-field",
                data: data,
                success: function(response) {
                    if (response.success) {
                        $(t).remove();
                        text = text ? text : "Untitled";
                        td.prepend('<span class="title" onclick="processTitle(this)">'+text+'</span>');
                        td.removeClass('editing');
                    }
                }
            });


        }

        function sendOrderToServer(t) {
            var order = $(t).sortable('serialize');
            order = order+'&_token={{ csrf_token() }}';
            //console.log(order);
            $.ajax({
                type: "POST", dataType: "json", url: "{{ route('learner.book-author-book-sort-chapter', $book->id) }}",
                data: order,
                success: function(response) {
                }
            });
        }


        function editAuthorTitle(t) {
            var book_title_editor_wrapper = $(".book-title-editor");
            book_title_editor_wrapper.empty();

            var display_name = $(t).data('display-name');
            var fields = $(t).data('fields');
            var author_placeholder = $(t).data('author-placeholder');
            var form = '<form action="{{ route('learner.book-author-book-update', $book->id) }}" method="POST" style="margin-top: 20px">';
                form += '{{ csrf_field() }}';
                form += '{{ method_field('PUT') }}';
                form += '<div class="form-group" style="margin-bottom: 10px;">';
                    form += '<input type="text" class="form-control" value="'+fields.title+'" name="title" required>';
                form += '</div>';

                form += '<div class="form-group">';
                    form += '<span class="right-space">By</span> <input type="text" class="form-control" ' +
                    'style="width: 40%;display: inline" value="'+display_name+'" placeholder="'+author_placeholder+'"' +
                    ' name="display_name">';
                    form += '<div class="edit-toggle-buttons pull-right" style="color: #3a3023">';
                        form += '<button class="beta-button" style="margin-right: 4px" type="submit"><i class="fa fa-check text-color-success"></i><span>Save</span></button>';
                        form += '<a class="beta-button" onclick="cancelAuthorTitle()"><i class="fa fa-remove text-color-danger"></i><span>Cancel</span></a>';
                    form += '</div>';
                form += '</div>';
            form += '</form>';

            book_title_editor_wrapper.append(form);
        }

        function cancelAuthorTitle() {
            var book_title_editor_wrapper = $(".book-title-editor");
            book_title_editor_wrapper.empty();

            var display = '<h1>{{ $book->title }}</h1>';
            display += '<div class="subhead">';
            display += 'By {{ $book->display_name ? $book->display_name : Auth::user()->full_name }}';
            display += '<div class="edit-toggle-buttons pull-right" style="color: #3a3023">' +
                '<button class="beta-button" data-fields="{{ json_encode($book) }}" '
                + 'data-display-name="{{ $book->display_name}}" data-author-placeholder="{{ Auth::user()->full_name }}" '
                + 'onclick="editAuthorTitle(this)"> <i class="fa fa-pencil"></i> <span>Edit</span> </button> </div>';
            display += '</div>';

            book_title_editor_wrapper.append(display);
        }

        function editAboutBook(t) {
            var book_description_wrapper = $(".book-description");
            book_description_wrapper.find('.edit-toggle-buttons').remove();
            book_description_wrapper.find('#description-container').remove();

            var form = '<form action="{{ route('learner.book-author-book-update', $book->id) }}" method="POST" style="margin-top: 20px" novalidate>';
                form += '{{ csrf_field() }}';
                form += '{{ method_field('PUT') }}';

                form += '<div class="form-group" style="margin-bottom: 10px;">';
                    form += '<textarea name="about_book" cols="10" rows="5" class="form-control" required></textarea>';
                form += '</div>';

                form += '<div class="edit-toggle-buttons pull-right" style="color: #3a3023">';
                    form += '<button class="beta-button" style="margin-right: 4px" type="submit"><i class="fa fa-check text-color-success"></i><span>Save</span></button>';
                    form += '<a class="beta-button" onclick="cancelAboutBook()"><i class="fa fa-remove text-color-danger"></i><span>Cancel</span></a>';
                form += '</div>';

                form += '<div class="clearfix"></div>';

                form += '</form>';

            book_description_wrapper.append(form);

            $('textarea[name=about_book]').html('{{ $book->about_book }}');
            tinymce.init({
                selector:'textarea',
                height : "150",
                menubar: false,
                toolbar: 'undo redo |  formatselect | bold italic backcolor  | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | removeformat | help'
            });
        }

        function cancelAboutBook() {
            var book_description_wrapper = $(".book-description");
            book_description_wrapper.empty();

            var display = '<h2>About the Book';
            display += '<div class="edit-toggle-buttons pull-right" style="color: #3a3023">' +
                '<button class="beta-button" data-fields="{{ json_encode($book) }}" '
                + 'onclick="editAboutBook(this)"> <i class="fa fa-pencil"></i> <span>Edit</span> </button> </div>';
            display += '</div> </h2>';

            display += '<div id="description-container"> {!! $book->about_book !!}</div>';

            book_description_wrapper.append(display);
        }

        function editCritique(t) {
            var book_critique_wrapper = $(".book-critique-guidance");
            book_critique_wrapper.find('.edit-toggle-buttons').remove();
            book_critique_wrapper.find('#description-container').remove();

            var form = '<form action="{{ route('learner.book-author-book-update', $book->id) }}" method="POST" style="margin-top: 20px" novalidate>';
                form += '{{ csrf_field() }}';
                form += '{{ method_field('PUT') }}';

                    form += '<div class="form-group" style="margin-bottom: 10px;">';
                        form += '<textarea name="critique_guidance" cols="10" rows="5" class="form-control" required></textarea>';
                    form += '</div>';

                    form += '<div class="edit-toggle-buttons pull-right" style="color: #3a3023">';
                        form += '<button class="beta-button" style="margin-right: 4px" type="submit"><i class="fa fa-check text-color-success"></i><span>Save</span></button>';
                        form += '<a class="beta-button" onclick="cancelCritique()"><i class="fa fa-remove text-color-danger"></i><span>Cancel</span></a>';
                    form += '</div>';

                    form += '<div class="clearfix"></div>';

                form += '</form>';

            book_critique_wrapper.append(form);

            $('textarea[name=critique_guidance]').html('{{ $book->critique_guidance }}');
            tinymce.init({
                selector:'textarea',
                height : "150",
                menubar: false,
                toolbar: 'undo redo |  formatselect | bold italic backcolor  | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | removeformat | help'
            });
        }

        function cancelCritique() {
            var book_critique_wrapper = $(".book-critique-guidance");
            book_critique_wrapper.empty();

            var display = '<h2>Critique Guidance';
            display += '<div class="edit-toggle-buttons pull-right" style="color: #3a3023">' +
                '<button class="beta-button" data-fields="{{ json_encode($book) }}" '
                + 'onclick="editCritique(this)"> <i class="fa fa-pencil"></i> <span>Edit</span> </button> </div>';
            display += '</div> </h2>';

            display += '<div id="description-container"> {!! $book->critique_guidance !!}</div>';

            book_critique_wrapper.append(display);
        }

        // for bulk import
        const converter = new showdown.Converter();
        const methods = {
            browseHtmlFile : function(input){
                //get file
                const files = input.files;
                //check if has a file
                $("#no-chapters-div").addClass('hidden');
                if(files.length){
                    var allowedExtensions = /(\.htm|\.html|\.md)$/i;
                    let custom_file_label = $(input).next(".custom-file-label");
                    let error_msg = custom_file_label.next(".text-danger");
                    let import_btn = $("#import-btn");
                    //check if extension is htm or html
                    if(!allowedExtensions.exec(input.value)){
                        error_msg.removeClass("hidden");
                        import_btn.addClass("disabled").prop("disabled", true);
                        custom_file_label.text("Choose File");
                        return
                    }
                    const file = files[0];
                    var reader = new FileReader();
                    reader.onload = function(e) {
                        //save the resul to varible text
                        text = reader.result;
                        import_btn.removeClass("disabled").prop("disabled", false);
                        custom_file_label.text(file.name);
                        error_msg.addClass("hidden")
                    };
                    //convert file content to text
                    reader.readAsText(file)
                }
            },

            extractChapters : function(){
                //convert text to html
                const html = $.parseHTML(converter.makeHtml(text));
                let chapters = [];
                //find and get only the div which has a class 'WordSection1' which where the content is located
                let div = $(html).filter(function(){
                    return this.nodeName && this.nodeName.toLowerCase() == "div" && $(this).hasClass("WordSection1")
                });
                //check if ms word container div found
                if(div.length){
                    //remove ms word paragraph tag
                    $(div).find("o\\:p").remove();
                    //remove all span inside of each elements like h1, p and etc and replace each them with their text/innerHtml
                    $(div).find("* > span").each(function () {
                        $(this).replaceWith($(this).text())
                    })
                }
                //execute/attached removeAttribute fnc
                let content_container = div.length ? div : html;
                $(content_container).find("*").removeAttributes();
                /*
                          Assigning value to contents, if div not found, most likely the hmtl file is exported from ms word.
                          Can be a markdown file.
                        */
                let contents = div.length? $.parseHTML(div.get(0).innerHTML) : html;
                //filter for removing #text node
                contents = $(contents).filter(function(){
                    return this.nodeType === 1
                });
                //loop all nodes
                $.each(contents, function(i, node){
                    //get note outerHtml
                    const outerHTML = $(node).get(0).outerHTML;
                    //check if node is h1
                    if($.inArray(node.nodeName.toLowerCase(), ["h1"]) > -1){
                        //push chapter details like title, content, book_id every time h1 node is found
                        chapters.push({ title : $(node).get(0).innerHTML, content : "" , book_id : $("[name='book_id']").val() });
                        return true
                    }
                    if(chapters[chapters.length - 1] !== undefined){
                        //exclude element with &nsbp; content
                        if($(node).html() === "&nbsp;"){
                            return true
                        }
                        //concat all element outerHtml/text after h1
                        chapters[chapters.length - 1]['content'] += outerHTML
                    }
                });

                //check if there chapters
                if(chapters.length){
                    $("#no-chapters-div").addClass("hidden");
                    let self = this
                    //pass the array of chapters in backend for saving
                    $.post('{{ route('learner.bulk-import-chapter') }}', {chapters : chapters})
                        .then(function(response){
                            window.location.reload();
                        })
                        .catch(function(err){
                            console.log(err, "err")
                        })
                }else{
                    //show message if no chapters found
                    $("#no-chapters-div").removeClass("hidden")
                }
            },
        };

        //function for removing all attributes like class, styles and etc
        jQuery.fn.removeAttributes = function() {
            return this.each(function() {
                //get all attributes for each element
                var attributes = $.map(this.attributes, function(item) {
                    return item.name;
                });
                var el = $(this);
                //remove each attributes in each element
                $.each(attributes, function(i, item) {
                    el.removeAttr(item);
                });
            });
        }
    </script>
@stop