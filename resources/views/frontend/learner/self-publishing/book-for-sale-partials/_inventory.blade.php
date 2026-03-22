<div class="panel">
    <div class="panel-body">
        <div class="col-md-6">
            <div class="row">
                <div class="col-3">
                    <div class="form-group">
                        <label class="control-label">Totalt solgte bøker</label>
                    </div>
                </div>
                <div class="col-9">
                    <div class="form-group">
                        <input type="text" class="form-control"
                        value="{{ $totalBookSold }}" disabled>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-3">
                    <div class="form-group">
                        <label class="control-label">Totalt salg</label>
                    </div>
                </div>
                <div class="col-9">
                    <div class="form-group">
                        <input type="text" class="form-control"
                        value="{{ $totalBookSale }}" disabled>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-3">
                    <div class="form-group">
                        <label class="control-label">Lagerstatus</label>
                    </div>
                </div>
                <div class="col-9">
                    <table class="table">
                        <thead>
                            <tr>
                                <td>Totalt</td>
                                <td>Levert</td>
                                <td>Fysiske eksemplarer</td>
                                <td>Returer</td>
                                <td>Saldo</td>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>
                                    {{ $book->inventory->total ?? '' }}
                                </td>
                                <td>
                                    {{ $book->inventory->delivered ?? '' }}
                                </td>
                                <td>
                                    {{ $book->inventory->physical_items ?? '' }}
                                </td>
                                <td>
                                    {{ $book->inventory->returns ?? '' }}
                                </td>
                                <td>
                                    {{ $book->inventory->balance ?? '' }}
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="row">
                <div class="col-3">
                    <div class="form-group">
                        <label class="control-label">Bestilling</label>
                    </div>
                </div>
                <div class="col-9">
                    <div class="form-group">
                        <input type="text" class="form-control"
                        value="{{ $book->inventory->order ?? '' }}" disabled>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-3">
                    <div class="form-group">
                        <label class="control-label">Reservasjoner</label>
                    </div>
                </div>
                <div class="col-9">
                    <div class="form-group">
                        <input type="text" class="form-control"
                        value="{{ $book->inventory->reservations ?? '' }}" disabled>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>