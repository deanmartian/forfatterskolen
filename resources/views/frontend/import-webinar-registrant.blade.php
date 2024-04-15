<form action="{{ route('process-import-webinar-registrants') }}" method="post" enctype="multipart/form-data">
    @csrf
    <input type="text" name="link"> <br>
    <input type="file" name="file"> <br>
    <button type="submit">Import</button>
</form>