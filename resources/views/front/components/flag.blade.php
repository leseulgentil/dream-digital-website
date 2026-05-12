@props(['id' => '', 'label' => ''])

@switch($id)
  @case('kinshasa')
    <svg viewBox="0 0 30 20" preserveAspectRatio="none" aria-label="{{ $label }}" role="img" xmlns="http://www.w3.org/2000/svg"><rect width="30" height="20" fill="#007fff"/><polygon points="0,7 30,11 30,13 0,9" fill="#fcd116"/><polygon points="0,7.8 30,11.8 30,12.2 0,8.2" fill="#ce1126"/></svg>
    @break
  @case('abidjan')
    <svg viewBox="0 0 30 20" preserveAspectRatio="none" aria-label="{{ $label }}" role="img" xmlns="http://www.w3.org/2000/svg"><rect width="10" height="20" fill="#ff8200"/><rect x="10" width="10" height="20" fill="#fff"/><rect x="20" width="10" height="20" fill="#009a44"/></svg>
    @break
  @case('brazzaville')
    <svg viewBox="0 0 30 20" preserveAspectRatio="none" aria-label="{{ $label }}" role="img" xmlns="http://www.w3.org/2000/svg"><polygon points="0,0 30,0 0,20" fill="#009543"/><polygon points="30,0 30,20 0,20" fill="#dc241f"/><polygon points="0,12 30,4 30,8 0,16" fill="#fbde4a"/></svg>
    @break
  @case('nairobi')
    <svg viewBox="0 0 30 20" preserveAspectRatio="none" aria-label="{{ $label }}" role="img" xmlns="http://www.w3.org/2000/svg"><rect width="30" height="6.4" fill="#000"/><rect y="6.4" width="30" height=".8" fill="#fff"/><rect y="7.2" width="30" height="5.6" fill="#bb0000"/><rect y="12.8" width="30" height=".8" fill="#fff"/><rect y="13.6" width="30" height="6.4" fill="#006600"/></svg>
    @break
  @case('gentilly')
    <svg viewBox="0 0 30 20" preserveAspectRatio="none" aria-label="{{ $label }}" role="img" xmlns="http://www.w3.org/2000/svg"><rect width="10" height="20" fill="#002395"/><rect x="10" width="10" height="20" fill="#fff"/><rect x="20" width="10" height="20" fill="#ed2939"/></svg>
    @break
  @default
    <span aria-label="{{ $label }}"></span>
@endswitch
