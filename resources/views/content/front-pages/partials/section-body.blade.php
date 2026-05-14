@if(!empty($section['body_html']))
  <div class="dd-richtext">{!! $section['body_html'] !!}</div>
@else
  @foreach(preg_split("/\r?\n\r?\n/", trim($section['body'] ?? '')) as $paragraph)
    @if(trim($paragraph) !== '')
      <p>{!! nl2br(e($paragraph)) !!}</p>
    @endif
  @endforeach
@endif
