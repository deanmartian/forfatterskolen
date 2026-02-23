<table class="table">
    <thead>
        <tr>
            <th>Cover</th>
            <th>Description</th>
            <th>Format</th>
            <th>ISBN</th>
            <th>Backside Text</th>
            <th>Backside Image</th>
            <th>Instruction</th>
            <th>Print Ready</th>
            <th></th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td>
                @php
                    $coverFiles = explode(',', $cover->value);
                @endphp
                @foreach ($coverFiles as $coverFile)
                    @if (strpos($coverFile, 'Forfatterskolen_app'))
                        <a href="/dropbox/download/{{ trim($coverFile) }}">
                            <i class="fa fa-download" aria-hidden="true"></i>
                        </a>&nbsp;
                        <a href="/dropbox/shared-link/{{ trim($coverFile) }}" target="_blank" 
                        style="margin-right: 5px">
                            {{ basename($coverFile) }}
                        </a>
                    @else
                        @if ($coverFile)
                            <a href="{{ $coverFile }}" class="btn btn-success btn-xs" download>
                                <i class="fa fa-download"></i>
                            </a>
                            <a href="{{ asset($coverFile) }}" target="_blank" style="margin-right: 5px">
                                {{ basename($coverFile) }}
                            </a>
                        @endif
                    @endif
                @endforeach
            </td>
            <td>
                {{ $cover->description }}
            </td>
            <td>
                {{ !is_array(AdminHelpers::projectFormats($cover->format)) ?
                    AdminHelpers::projectFormats($cover->format) 
                    : $cover->format . ' mm' }}
            </td>
            <td>
                {{ $cover->isbn?->value }}
            </td>
            <td>
                @if ($cover->backside_type == 'text')
                    {{ $cover->backside_text }}
                @else
                    <a href="/dropbox/download/{{ trim($cover->backside_text) }}">
                        <i class="fa fa-download" aria-hidden="true"></i>
                    </a>&nbsp;
                    <a href="/dropbox/shared-link/{{ $cover->backside_text }}" target="_blank">
                        {{ basename($cover->backside_text) }}
                    </a>
                @endif
            </td>
            <td>
                @if ($cover->backside_image)
                    @php
                        $backsideImages = explode(',', $cover->backside_image);
                    @endphp
                    @foreach ($backsideImages as $backsideImage)
                        <a href="/dropbox/download/{{ trim($backsideImage) }}">
                            <i class="fa fa-download" aria-hidden="true"></i>
                        </a>&nbsp;
                        <span>{{ basename($backsideImage) }}</span>
                    @endforeach
                @endif
            </td>
            <td>
                {{ $cover->instruction }}
            </td>
            <td>
                @if ($cover->print_ready)
                    <a href="/dropbox/download/{{ trim($cover->print_ready) }}">
                        <i class="fa fa-download" aria-hidden="true"></i>
                    </a>&nbsp;
                    {!! basename($cover->print_ready) !!}
                @endif
            </td>
        </tr>
    </tbody>
</table>