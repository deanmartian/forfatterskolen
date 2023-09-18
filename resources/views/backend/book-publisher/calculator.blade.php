@extends('backend.layout')

@section('title')
    <title>Book Publisher &rsaquo; Forfatterskolen Admin</title>
@stop

@section('styles')
    <style>
        .top15 {
            margin-top: 15px;
        }

        .btn.beregnbutton {
            width: 200px;
            padding: 10px 14px;
            font: 900 18px/21px "Sans", sans-serif;
            color: black;
            background: white;
            text-transform: uppercase;
            white-space: normal;
            border: 1px solid #d6d4d4;
        }

        .markup {
            padding: 10px;
            font: 600 1.5rem "Roboto", sans-serif;
            color: black;
            background: white;
            text-transform: uppercase;
            border: 1px solid #d6d4d4;
            white-space: normal;
            max-width: 400px;
            display: inline-block;
        }

        .free-freight {
            color: #ad1380;
        }

        .table-dark {
            width: 100%;
            max-width: 100%;
            margin-bottom: 1rem;
            font-size: 1.4rem;
            border-collapse: collapse;
        }

        .table-dark tr {
            background: #fff;
        }

        .table-dark th {
            padding: 12px 5px;
            color: #fff;
            background-color: #373a3c;
            border: 1px solid #373a3c;
            min-width: 100px;
            vertical-align: middle;
        }

        .table-dark td:first-child, .table-dark th:first-child {
            padding: 5px 0px 5px 5px;
            text-align: left;
        }

        table td[class*="col-"], table th[class*="col-"] {
            float: none;
            display: table-cell;
        }

        .table-dark td {
            border: 1px solid #e0e0e0;
        }

        .table-dark td, .table-dark th {
            min-width: 100px;
            vertical-align: middle;
            text-align: center;
        }

        .table-responsive {
            overflow-x: hidden;
        }

        .table-dark td span {
            vertical-align: super;
            font-size: 1rem;
            font-weight: bold;
        }

        .font18 {
            font-size: 18px;
        }
    </style>
@stop

@section('content')
    <div class="page-toolbar">
        <h3><i class="fa fa-file"></i> Book Publisher</h3>
        
        <a href="{{ route('admin.project.index') }}" class="btn btn-default" style="margin-left: 10px">
            Back
        </a>
        <div class="clearfix"></div>
    </div>

    <div class="col-md-12 margin-top">
        <div class="container" style="background-color: #fff; padding: 20px">
            <div class="col-md-12">
                <div class="row">
                    <div class="col-sm-4 col-md-1 col-lg-1">
                        <span class="left3"><label for="Antall">Antall</label></span>
                        <input class="form-control" id="txtAmount" name="impression" type="text" value="500" tabindex="1">
                    </div>
    
                    <div class="col-sm-4 col-md-1 col-lg-1">
                        <span class="left3"><label for="Sider">Sider</label></span>
                        <input class="form-control" id="Pages" name="pages" type="text" value="200" tabindex="2">
                    </div>
    
                    <div class="col-sm-4 col-md-2 col-lg-2">
                        <span class="left3"><label for="St_rrelse">Størrelse</label></span>
                        <select class="form-control" id="ddlFormat" name="format"  tabindex="3">
                            <option selected="selected" value="999">Valgfri størrelse</option>
                            <option value="125x200">125x200 mm (liten roman)</option>
                            <option value="140x220">140x220 mm (roman)</option>
                            <option value="148x210">148x210 mm (A5)</option>
                            <option value="155x230">155x230 mm (stor roman)</option>
                            <option value="170x240">170x240 mm (B5 - lærebok)</option>
                            <option value="210x210">210x210 mm (kvadratisk)</option>
                            <option value="210x297">210x297 mm (A4)</option>
                        </select>
                    </div>
    
                    <div class="col-sm-4 col-md-2 col-lg-1">
                        <span class="left3"><label for="Bredde__mm_">Bredde (mm)</label></span>
                        <input value="210" class="form-control" id="txtWidth" name="width" type="text" tabindex="4">
                    </div>
    
                    <div class="col-sm-4 col-md-2 col-lg-1">
                        <span class="left3"><label for="H_yde__mm_">Høyde (mm)</label></span>
                        <input value="297" class="form-control" id="txtHeight" name="height" type="text" tabindex="5">
                    </div>
    
                    <div class="col-sm-4 col-md-2 col-lg-2">
                        <span class="left3"><label for="Antall_titler">Antall titler</label></span>
                        <select class="form-control"id="Originals" name="originals"tabindex="6">
                            <option selected="selected" value="1">1</option>
                            <option value="2">2</option>
                            <option value="3">3</option>
                            <option value="4">4</option>
                            <option value="5">5</option>
                            <option value="6">6</option>
                            <option value="7">7</option>
                            <option value="8">8</option>
                            <option value="9">9</option>
                            <option value="10">10</option>
                            <option value="11">11</option>
                            <option value="12">12</option>
                            <option value="13">13</option>
                            <option value="14">14</option>
                            <option value="15">15</option>
                            <option value="16">16</option>
                            <option value="17">17</option>
                            <option value="18">18</option>
                            <option value="19">19</option>
                            <option value="20">20</option>
                        </select>
                    </div>
    
                    <div class="col-sm-4 col-md-2 col-lg-2">
                        <div class="top7">
                            <label for="Bokblokk_tykkelse:">Bokblokk tykkelse:</label> 
                            <label for="">15,0 mm</label>
                        </div>
    
                        <div>
                            <label for="Ryggbredde:">Ryggbredde:</label> 
                            <label for="">15,8 mm</label>
                        </div>
                    </div>
                </div> <!-- end row -->
    
                <div class="row top15">
                    <div class="col-sm-4 col-md-2 col-lg-2">
                        <span class="left3"><label for="Innbinding">Innbinding</label></span>
                        <select class="form-control" id="Binding" name="binding" tabindex="7">
                            <option selected="selected" value="1">Paperback/softcover</option>
                            <option value="2">Paperback/softcover m. flappomslag</option>
                            <option value="20">Spiralbok</option>
                            <option value="4">Helbind/hardcover</option>
                        </select>
                    </div>
    
                    <div class="col-sm-4 col-md-2 col-lg-2">
                        <span class="left3"><label for="Garnhefting">Garnhefting</label></span>
                        <select class="form-control" id="YarnStapling" name="YarnStapling" tabindex="8">
                            <option selected="selected" value="False">Nei</option>
                            <option value="True">Ja</option>
                        </select>
                    </div>
                </div> <!-- end row -->
    
                <div class="row top15">
                    <div class="col-sm-4 col-md-2 col-lg-2">
                        <span class="left3"><label for="Papirtype_innhold">Papirtype innhold</label></span>
                        <select class="form-control" id="Media" name="media" tabindex="9">
                            <option value="1">90g Offsetpapir</option>
                            <option value="2">120g Offsetpapir</option>
                            <option value="183">140g Offsetpapir</option>
                            <option value="3">130g Silk-papir</option>
                            <option value="4">170g Silk-papir</option>
                            <option value="34">80g Munken Cream</option>
                            <option selected="selected" value="36">100g Munken Cream</option>
                            <option value="163">115g G Print</option>
                            <option value="349">135g Resirkulert papir</option>
                        </select>
                    </div>
    
                    <div class="col-sm-4 col-md-2 col-lg-2">
                        <span class="left3"><label for="Trykk_av_innhold">Trykk av innhold</label></span>
                        <select class="form-control" id="PrintMethod" name="printMethod" tabindex="10">
                            <option selected="selected" value="99">Billigste</option>
                            <option value="1">Digitaltrykk</option>
                            <option value="2">Offsettrykk</option>
                        </select>
                    </div>
                    
                    <div class="col-sm-4 col-md-2 col-lg-2">
                        <span class="left3"><label for="Innhold_trykkes_med">Innhold trykkes med</label></span>
                        <select class="form-control" id="Color" name="Color" tabindex="11">
                            <option value="3">4+4 (fargetrykk på begge sider)</option>
                            <option selected="selected" value="4">1+1 (trykk med 1 farge på 2 sider)</option>
                        </select>
                    </div>
    
                    <div class="col-sm-4 col-md-2 col-lg-2">
                        <span class="left3"><label for="Antall_fargesider">Antall fargesider</label></span>
                        <input class="form-control" id="NumberOfColorPages" name="numberOfColorPages" type="text" 
                        value="0" tabindex="12">
                    </div>
                </div> <!-- end row -->
    
                <div class="row top15">
                    <div class="col-sm-4 col-md-2 col-lg-2">
                        <span class="left3"><label for="Papirtype_omslag">Papirtype omslag</label></span>
                        <select class="form-control" id="CoverMedia" name="CoverMedia" tabindex="13">
                            <option selected="selected" value="11">260g Chromo-kartong</option>
                            <option value="185">300g Munken Cream</option>
                            <option value="90">300g Offsetpapir</option>
                            <option value="19">350g Silk-papir</option>
                            <option value="350">250g Resirkulert papir</option>
                        </select>
                    </div>
                    <div class="col-sm-4 col-md-2 col-lg-2">
                        <span class="left3"><label for="Omslag_trykkes_med">Omslag trykkes med</label></span>
                        <select class="form-control" id="CoverColorFront" name="CoverColorFront" tabindex="14">
                            <option selected="selected" value="2">4+0 (fargetrykk på én side)</option>
                            <option value="3">4+4 (fargetrykk på begge sider)</option>
                        </select>
                    </div>
                    <div class="col-sm-4 col-md-2 col-lg-2">
                        <span class="left3"><label for="Kachering_p__omslag">Kachering på omslag</label></span>
                        <select class="form-control" id="Kachering" name="Kachering" tabindex="15">
                            <option value="0">Ingen kasjering, fare for fargesmitte</option>
                            <option selected="selected" value="10">Matt kachering</option>
                            <option value="11">Blank kachering</option>
                            <option value="12">Soft touch</option>
                            <option value="22">Ripebestandig, matt (scratch free)</option>
                        </select>
                    </div>
                </div> <!-- end row -->
    
                <div class="row top15">
                    <div class="col-sm-4 col-md-2 col-lg-2">
                        <span class="left3"><label for="Omslag__delvis_3D_lakk">Omslag, delvis 3D lakk</label></span>
                        <select class="form-control" id="PartialLacquer" name="PartialLacquer" tabindex="16">
                            <option selected="selected" value="0">Nei</option>
                            <option value="1">Partiell 3D lakk</option>
                        </select>
                    </div>
                    
                    <div class="col-sm-4 col-md-2 col-lg-2">
                        <span class="left3"><label for="Omslag__delvis_folie">Omslag, delvis folie</label></span>
                        <select class="form-control" id="FoliePreag" name="FoliePreag" tabindex="17">
                            <option selected="selected" value="0">Nej</option>
                            <option value="1">Blank gullfolie</option>
                            <option value="2">Matt gullfolie</option>
                            <option value="3">Blank sølvfolie</option>
                            <option value="4">Matt sølvfolie</option>
                            <option value="5">Blindpreg</option>
                            <option value="6">3D gullfolie</option>
                            <option value="7">3D sølvfolie</option>
                        </select>
                    </div>
                </div> <!-- end row -->
    
                <div class="row top15">
                    <div class="col-sm-4 col-md-2 col-lg-2">
                        <span class="left3"><label for="Pr_vetrykk">Prøvetrykk</label></span>
                        <select class="form-control" id="SampleType" name="SampleType" tabindex="18">
                            <option selected="selected" value="0">Nei</option>
                            <option value="1">Prøvetrykk</option>
                            <option value="2">Prøvebok</option>
                        </select>
                    </div>
    
                    <div class="col-sm-4 col-md-2 col-lg-2">
                        <label for="Foliering">Foliering</label>
                        <select class="form-control" id="Foliering" name="Foliering" tabindex="19">
                            <option selected="selected" value="0">Nei</option>
                            <option value="1">Individuell foliering</option>
                            <option value="2">Foliert i pakker av</option>
                        </select>
                    </div>
                </div> <!-- end row -->
    
                <div class="row top15">
                    <div class="col-sm-4 col-md-2 col-lg-2">
                        <span class="left3"><label for="Tittel">Tittel</label></span>
                        <input class="form-control" id="Titel" name="Titel" type="text" value="" tabindex="20">
                    </div>
                    <div class="col-sm-4 col-md-2 col-lg-2">
                        <span class="left3"><label for="ISBN_p__fakturaen">ISBN på fakturaen</label></span>
                        <input class="form-control" id="ISBN" name="ISBN" type="text" value="" tabindex="21">
                    </div>
                </div> <!-- end row -->
    
                <div class="row top15">
                    <div id="ErrorSum" class="col-md-12">
                        <div class="text-danger">
                            Bemerk: Sidetall skal være delelig med 4. Kan sidetallet ikke deles med 4 vil vi
                             legge blanke sider til inntil boken ha et acceptabelt sidetall.
                        </div>
                    </div>
                </div>
            </div> <!-- end col-md-12 -->

            <div class="row top15">
                <div class="col-md-3">
                    <input type="submit" name="calcBtn" value="BEREGN" class="beregnbutton btn btn-default" tabindex="22">
                </div>

                <div class="clearfix">
                    <div class="pull-right">
                        <div class="markup">
                            Alle priser er oppgitt uten mva
                        </div>
                        <div class="markup free-freight">
                            Gratis Frakt
                            <i class="fa fa-truck"></i>
                        </div>
                    </div>
                </div>
            </div> <!-- end row -->

            <div class="row">
                <div class="col-md-12 top15">
                    <strong class="font18">Din pris</strong>
                </div>
            </div> <!-- end row -->

            <div class="row">
                <div class="col-xs-12 top15">
                    <div class="table-responsive">
                        <table class="table-dark">
                            <thead>
                                <tr>
                                    <th class="col-xs-3">Papirtype/Innhold</th>
                                    <th>
                                        <div class="row">
                                            <div class="col-md-2 pivot-arrow">&lt;&lt;</div>
                                            <div class="col-md-6">300 stk.</div>
                                            <div class="col-md-2"></div>
                                        </div>
                                    </th>
                                    <th>
                                        <div>400 stk.</div>
                                    </th>
                                    <th>
                                        <div>500 stk.</div>
                                    </th>
                                    <th>
                                        <div>600 stk.</div>
                                    </th>
                                    <th>
                                        <div class="row">
                                            <div class="col-md-2"></div>
                                            <div class="col-md-6">700 stk.</div>
                                            <div class="col-md-2 pivot-arrow">&gt;&gt;</div>
                                        </div>
                                    </th>
            
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td><b>100g Munken Cream</b> (rygg: 15,8 mm)</td>
        
                                        <td class="custom-column" data-uniqueid="1][1][36][300">
                                            23.885                                    
                                            <span>1</span>
                                        </td>
                                        <td class="custom-column" data-uniqueid="1][1][36][400">
                                            30.661                                    
                                            <span>1</span>
                                        </td>
                                        <td class="custom-column" data-uniqueid="1][1][36][500">
                                            37.428                                    
                                            <span>1</span>
                                        </td>
                                        <td class="custom-column" data-uniqueid="1][1][36][600">
                                            44.200                                    
                                            <span>1</span>
                                        </td>
                                        <td class="custom-column" data-uniqueid="1][1][36][700">
                                            50.969                                    
                                            <span>1</span>
                                        </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div> <!-- end col-xs-12 -->
            </div> <!-- end row -->

            <div class="row">
                <div class="col-md-12 top15">
                    <strong class="font18">Pris ved inkjettrykk</strong>
                </div>
            </div> <!-- end row -->

            <div class="row">
                <div class="col-xs-12 top15">
                    <div class="table-responsive">
                        <table class="table-dark">
                            <thead>
                                <tr>
                                    <th class="col-xs-3">Papirtype/Innhold</th>
                                    <th>
                                        <div class="row">
                                            <div class="col-md-2 pivot-arrow">&lt;&lt;</div>
                                            <div class="col-md-6">300 stk.</div>
                                            <div class="col-md-2"></div>
                                        </div>
                                    </th>
                                    <th>
                                        <div>400 stk.</div>
                                    </th>
                                    <th>
                                        <div>500 stk.</div>
                                    </th>
                                    <th>
                                        <div>600 stk.</div>
                                    </th>
                                    <th>
                                        <div class="row">
                                            <div class="col-md-2"></div>
                                            <div class="col-md-6">700 stk.</div>
                                            <div class="col-md-2 pivot-arrow">&gt;&gt;</div>
                                        </div>
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                    <tr>
                                        <td>
                                            <b>80g Munken Cream</b> (rygg: 12,8 mm)
                                        </td>
                                        <td class="custom-column" data-uniqueid="6][1][34][300">
                                            19.550                                    
                                            <span>6</span>
                                        </td>
                                        <td class="custom-column" data-uniqueid="6][1][34][400">
                                            24.599                                    
                                            <span>6</span>
                                        </td>
                                        <td class="custom-column" data-uniqueid="6][1][34][500">
                                            29.612                                    
                                            <span>6</span>
                                        </td>
                                        <td class="custom-column" data-uniqueid="6][1][34][600">
                                            33.353                                    
                                            <span>6</span>
                                        </td>
                                        <td class="custom-column" data-uniqueid="6][1][34][700">
                                            37.012                                    
                                            <span>6</span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <b>100g Munken Cream</b> (rygg: 15,8 mm)
                                        </td>
                                        <td class="custom-column" data-uniqueid="6][1][36][300">
                                            21.219                                    
                                            <span>6</span>
                                        </td>
                                        <td class="custom-column" data-uniqueid="6][1][36][400">
                                            26.782                                    
                                            <span>6</span>
                                        </td>
                                        <td class="custom-column" data-uniqueid="6][1][36][500">
                                            32.342                                    
                                            <span>6</span>
                                        </td>
                                        <td class="custom-column" data-uniqueid="6][1][36][600">
                                            36.595                                    
                                            <span>6</span>
                                        </td>
                                        <td class="custom-column" data-uniqueid="6][1][36][700">
                                            40.761                                    
                                            <span>6</span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <b>100g Munken Print White</b> (rygg: 15,8 mm)
                                        </td>
                                        <td class="custom-column" data-uniqueid="6][1][40][300">
                                            21.219                                    
                                            <span>6</span>
                                        </td>
                                        <td class="custom-column" data-uniqueid="6][1][40][400">
                                            26.782                                    
                                            <span>6</span>
                                        </td>
                                        <td class="custom-column" data-uniqueid="6][1][40][500">
                                            32.342                                    
                                            <span>6</span>
                                        </td>
                                        <td class="custom-column" data-uniqueid="6][1][40][600">
                                            36.595                                    
                                            <span>6</span>
                                        </td>
                                        <td class="custom-column" data-uniqueid="6][1][40][700">
                                            40.761                                    
                                            <span>6</span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <b>90g Offsetpapir</b> (rygg: 12,2 mm)
                                        </td>
                                        <td class="custom-column" data-uniqueid="6][1][1][300">
                                            19.186                                    
                                            <span>6</span>
                                        </td>
                                        <td class="custom-column" data-uniqueid="6][1][1][400">
                                            24.073                                    
                                            <span>6</span>
                                        </td>
                                        <td class="custom-column" data-uniqueid="6][1][1][500">
                                            28.948                                    
                                            <span>6</span>
                                        </td>
                                        <td class="custom-column" data-uniqueid="6][1][1][600">
                                            32.568                                    
                                            <span>6</span>
                                        </td>
                                        <td class="custom-column" data-uniqueid="6][1][1][700">
                                            36.105                                    
                                            <span>6</span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <b>90g G Print</b> (rygg: 9,8 mm)
                                        </td>
                                        <td class="custom-column" data-uniqueid="6][1][177][300">
                                            20.314                                    
                                            <span>6</span>
                                        </td>
                                        <td class="custom-column" data-uniqueid="6][1][177][400">
                                            25.576                                    
                                            <span>6</span>
                                        </td>
                                        <td class="custom-column" data-uniqueid="6][1][177][500">
                                            30.837                                   
                                            <span>6</span>
                                        </td>
                                        <td class="custom-column" data-uniqueid="6][1][177][600">
                                            34.806                                    
                                            <span>6</span>
                                        </td>
                                        <td class="custom-column" data-uniqueid="6][1][177][700">
                                            38.694                                    
                                            <span>6</span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <b>115g G Print</b> (rygg: 12,1 mm)
                                        </td>
                                        <td class="custom-column" data-uniqueid="6][1][163][300">
                                            22.339                                    
                                            <span>6</span>
                                        </td>
                                        <td class="custom-column" data-uniqueid="6][1][163][400">
                                            28.276                                    
                                            <span>6</span>
                                        </td>
                                        <td class="custom-column" data-uniqueid="6][1][163][500">
                                            34.207                                    
                                            <span>6</span>
                                        </td>
                                        <td class="custom-column" data-uniqueid="6][1][163][600">
                                            38.814                                    
                                            <span>6</span>
                                        </td>
                                        <td class="custom-column" data-uniqueid="6][1][163][700">
                                            43.324                                    
                                            <span>6</span>
                                        </td>
                                    </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div> <!-- end row -->

            <div class="row">
                <div class="col-xs-10">
                    <div class="col-xs-10 col-md-6">
                        <div class="checkbox-inline">
                            <label>
                                <input id="ShowUnitPrice" name="ShowUnitPrice" type="checkbox" value="true">
                                <input name="ShowUnitPrice" type="hidden" value="false">
                                Vis stk. pris
                                <input id="ShowUnitPrice" name="ShowUnitPrice" type="hidden" value="False">
                            </label>
                        </div>
                    </div>
                    <div class="col-xs-10 col-md-6 text-right">
                        1:Digitaltrykk, 
                        2:Offsettrykk, 
                        3:Puljetrykk, 
                        4:Storformat, 
                        5:Rotasjonstrykk, 
                        6:Inkjet
                    </div>
                </div>
            </div> <!-- end row-->
        </div> <!-- end container -->
    </div>
@stop