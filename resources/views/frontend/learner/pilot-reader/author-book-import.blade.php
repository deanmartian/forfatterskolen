@extends('frontend.layout')

@section('title')
    <title>{{ $book->title }}, Chapter &rsaquo; Forfatterskolen</title>
@stop

@section('styles')
    <link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">
@stop

@section('content')

    <div class="account-container">

        @include('frontend.partials.learner-menu')

        <div class="col-sm-12 col-md-10 sub-right-content chapter-import fff-bg">
            <div class="col-sm-12">

                <div class="col-md-8 col-sm-offset-2 col-sm-12 margin-top">
                    <header>
                        <h1>Book Importer</h1>
                        <hr>
                    </header>

                    <section class="book-importer sans margin-top-16">
                        <p class="big">
                            Please read the <a href="#">Book Import Guide</a> before your first import.
                        </p>

                        <h2 class="group-label">Supported Formats</h2>

                        <div class="quick-instructions small">
                            <p>We support importing manuscripts in two formats:
                                <strong>HTML</strong> (Microsoft Word, Scrivener, and others), and <strong>Markdown</strong>
                                (Ulysses and others).
                            </p>
                        </div> <!-- quick-instructions small -->

                        <div class="import-forms">
                            <!--<h2 class="group-label">Select Your File Format</h2>-->
                            <div class="import-form html">
                                <div class="group-label small">
                                    <i class="fa fa-file-code-o"></i>
                                    <span>HTML (MS Word and others)</span>
                                </div>

                                <form action="" accept-charset="UTF-8" enctype="multipart/form-data" method="POST">
                                    {{ csrf_field() }}
                                    <div class="margin-top-16">
                                        <input accept=".htm,.html" name="book_file" type="file" class="book-file">
                                        <div class="warning hidden color danger margin-top">
                                            <i class="fa fa-exclamation-triangle"></i>
                                            <span>
                                                Invalid file type selected. You must provide one of the following:
                                                .htm, .html
                                            </span>
                                        </div>
                                    </div>

                                    <label class="check small margin-top-16" style="font-weight: normal">
                                        <input name="guide" type="checkbox" value="true">
                                        <span>
                                            I've read the HTML Import Guide (<a target="_blank" href="#">MS Word Version</a>)
                                        </span>
                                    </label>

                                    <button class="beta-button color success margin-top" disabled="">
                                        <i class="fa fa-download"></i>
                                        <span>Import</span>
                                    </button>
                                </form>

                            </div>
                        </div>
                    </section> <!-- end book-importer sans margin-top-16 -->
                </div> <!-- end col-md-8 col-sm-offset-2 col-sm-12 margin-top -->
            </div> <!-- end col-sm-12 -->
        </div> <!-- end col-sm-12 col-md-10 sub-right-content -->

        <div class="clearfix"></div>

    </div>
@stop

@section('scripts')
    <script>
        $(document).ready(function(){
            $("input[name=book_file]").change(function(){

                if (!$(".warning").hasClass('hidden')) {
                    $(".warning").addClass('hidden');
                }

                if ($(this).val()) {
                    var fileExtension = ['html', 'htm'];
                    if ($.inArray($(this).val().split('.').pop().toLowerCase(), fileExtension) === -1) {
                        $(".warning").removeClass('hidden');
                        $("button").attr('disabled', true);
                    } else {
                        if ($("input[name=guide]").prop('checked')){
                            $("button").removeAttr('disabled');
                        }
                    }
                } else {
                    $("button").attr('disabled', true);
                }
            });

            $("input[name=guide]").click(function(){

               if($(this).prop('checked')) {
                   if ($("input[name=book_file]").val()) {
                       var fileExtension = ['html', 'htm'];
                       if ($.inArray($("input[name=book_file]").val().split('.').pop().toLowerCase(), fileExtension) === -1) {
                           $("button").attr('disabled', true);
                       } else {
                           $("button").removeAttr('disabled');
                       }
                   }

               } else {
                   $("button").attr('disabled', true);
               }
            });
        });
    </script>
@stop