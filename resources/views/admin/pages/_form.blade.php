@php
  $isEdit = $page->exists;
  $formAction = $isEdit ? route('admin.pages.update', $page) : route('admin.pages.store');
  $blocks = $page->content_blocks ?? [];
@endphp

<form method="POST" action="{{ $formAction }}" class="card-body row g-4" enctype="multipart/form-data" novalidate>
  @csrf
  @if($isEdit)
    @method('PUT')
  @endif

  <div class="col-md-3">
    <label class="form-label" for="section">Section <span class="text-danger">*</span></label>
    <select id="section" name="section" class="form-select @error('section') is-invalid @enderror" required>
      @foreach($sections as $sec)
        <option value="{{ $sec }}" @selected(old('section', $page->section) === $sec)>{{ $sec }}</option>
      @endforeach
    </select>
    @error('section')<div class="invalid-feedback">{{ $message }}</div>@enderror
  </div>

  <div class="col-md-5">
    <label class="form-label" for="slug">Slug <span class="text-danger">*</span></label>
    <input type="text" id="slug" name="slug" maxlength="120" class="form-control @error('slug') is-invalid @enderror" value="{{ old('slug', $page->slug) }}" placeholder="mentions" required>
    @error('slug')<div class="invalid-feedback">{{ $message }}</div>@enderror
    <small class="text-muted">Lettres minuscules, chiffres, tirets (a-z 0-9 -).</small>
  </div>

  <div class="col-md-2">
    <label class="form-label" for="locale">Locale <span class="text-danger">*</span></label>
    <select id="locale" name="locale" class="form-select @error('locale') is-invalid @enderror" required>
      <option value="fr" @selected(old('locale', $page->locale) === 'fr')>FR</option>
      <option value="en" @selected(old('locale', $page->locale) === 'en')>EN</option>
    </select>
    @error('locale')<div class="invalid-feedback">{{ $message }}</div>@enderror
  </div>

  <div class="col-md-2">
    <label class="form-label" for="country_id">Pays</label>
    <select id="country_id" name="country_id" class="form-select @error('country_id') is-invalid @enderror">
      <option value="">Global</option>
      @foreach($countries as $country)
        <option value="{{ $country->id }}" @selected(old('country_id', $page->country_id) == $country->id)>{{ $country->code }}</option>
      @endforeach
    </select>
    @error('country_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
  </div>

  <div class="col-12"><hr class="m-0"></div>

  <div class="col-md-8">
    <label class="form-label" for="title">Titre <span class="text-danger">*</span></label>
    <input type="text" id="title" name="title" maxlength="200" class="form-control @error('title') is-invalid @enderror" value="{{ old('title', $page->title) }}" required>
    @error('title')<div class="invalid-feedback">{{ $message }}</div>@enderror
  </div>

  <div class="col-md-4 d-flex align-items-end">
    <div class="form-check form-switch w-100">
      <input class="form-check-input" type="checkbox" id="is_published" name="is_published" value="1" @checked(old('is_published', $page->is_published))>
      <label class="form-check-label" for="is_published">Publier (visible sur le site public)</label>
    </div>
  </div>

  <div class="col-12">
    <label class="form-label" for="meta_description">Meta description SEO <small class="text-muted">(500 char max)</small></label>
    <textarea id="meta_description" name="meta_description" rows="2" maxlength="500" class="form-control @error('meta_description') is-invalid @enderror">{{ old('meta_description', $page->meta_description) }}</textarea>
    @error('meta_description')<div class="invalid-feedback">{{ $message }}</div>@enderror
  </div>

  <div class="col-12">
    <label class="form-label" for="seo_title">Titre SEO personnalise <small class="text-muted">(optionnel)</small></label>
    <input type="text" id="seo_title" name="seo_title" maxlength="220" class="form-control @error('seo_title') is-invalid @enderror" value="{{ old('seo_title', $blocks['seo_title'] ?? '') }}" placeholder="Titre optimise Google si different du H1">
    @error('seo_title')<div class="invalid-feedback">{{ $message }}</div>@enderror
  </div>

  <div class="col-12"><hr class="m-0"><h5 class="mt-3 mb-0">Contenu (content_blocks)</h5><p class="text-muted mb-0">Champs structures qui alimentent le rendu Blade de la page publique.</p></div>

  <div class="col-md-4">
    <label class="form-label" for="eyebrow">Eyebrow <small class="text-muted">(petit label cyan au-dessus du titre)</small></label>
    <input type="text" id="eyebrow" name="eyebrow" maxlength="200" class="form-control @error('eyebrow') is-invalid @enderror" value="{{ old('eyebrow', $blocks['eyebrow'] ?? '') }}">
    @error('eyebrow')<div class="invalid-feedback">{{ $message }}</div>@enderror
  </div>

  <div class="col-md-4">
    <label class="form-label" for="last_updated">Derniere mise a jour <small class="text-muted">(YYYY-MM-DD)</small></label>
    <input type="text" id="last_updated" name="last_updated" maxlength="30" class="form-control @error('last_updated') is-invalid @enderror" value="{{ old('last_updated', $blocks['last_updated'] ?? '') }}" placeholder="2026-05-12">
    @error('last_updated')<div class="invalid-feedback">{{ $message }}</div>@enderror
  </div>

  <div class="col-md-4">
    <label class="form-label" for="meta_image_path">Image OG / Blog <small class="text-muted">(URL ou chemin public/...)</small></label>
    <input type="text" id="meta_image_path" name="meta_image_path" maxlength="500" class="form-control @error('meta_image_path') is-invalid @enderror" value="{{ old('meta_image_path', $page->meta_image_path) }}" placeholder="/img/og/mentions.png">
    @error('meta_image_path')<div class="invalid-feedback">{{ $message }}</div>@enderror
    @if($page->meta_image_path)
      <small class="text-muted d-block text-truncate mt-1">{{ $page->meta_image_path }}</small>
    @endif
  </div>

  <div class="col-md-4">
    <label class="form-label" for="image_file">Uploader une image locale</label>
    <input type="file" id="image_file" name="image_file" accept="image/jpeg,image/png,image/webp" class="form-control @error('image_file') is-invalid @enderror">
    @error('image_file')<div class="invalid-feedback">{{ $message }}</div>@enderror
    <small class="text-muted">JPG, PNG ou WebP. Remplace le champ image si un fichier est choisi.</small>
  </div>

  <div class="col-md-4">
    <label class="form-label" for="author">Auteur</label>
    <input type="text" id="author" name="author" maxlength="120" class="form-control @error('author') is-invalid @enderror" value="{{ old('author', $blocks['author'] ?? '') }}" placeholder="Dream Digital">
    @error('author')<div class="invalid-feedback">{{ $message }}</div>@enderror
  </div>

  <div class="col-md-4">
    <label class="form-label" for="reading_time">Temps de lecture</label>
    <input type="text" id="reading_time" name="reading_time" maxlength="40" class="form-control @error('reading_time') is-invalid @enderror" value="{{ old('reading_time', $blocks['reading_time'] ?? '') }}" placeholder="5 min">
    @error('reading_time')<div class="invalid-feedback">{{ $message }}</div>@enderror
  </div>

  <div class="col-md-4">
    <label class="form-label" for="tags">Tags <small class="text-muted">(separes par virgule)</small></label>
    <input type="text" id="tags" name="tags" maxlength="500" class="form-control @error('tags') is-invalid @enderror" value="{{ old('tags', implode(', ', $blocks['tags'] ?? [])) }}" placeholder="SMS A2P, OTP, CPaaS">
    @error('tags')<div class="invalid-feedback">{{ $message }}</div>@enderror
  </div>

  <div class="col-md-4">
    <label class="form-label" for="image_alt">Texte alternatif image</label>
    <input type="text" id="image_alt" name="image_alt" maxlength="220" class="form-control @error('image_alt') is-invalid @enderror" value="{{ old('image_alt', $blocks['image_alt'] ?? '') }}">
    @error('image_alt')<div class="invalid-feedback">{{ $message }}</div>@enderror
  </div>

  <div class="col-md-4">
    <label class="form-label" for="image_credit">Credit image</label>
    <input type="text" id="image_credit" name="image_credit" maxlength="220" class="form-control @error('image_credit') is-invalid @enderror" value="{{ old('image_credit', $blocks['image_credit'] ?? '') }}" placeholder="Photo Unsplash / Auteur">
    @error('image_credit')<div class="invalid-feedback">{{ $message }}</div>@enderror
  </div>

  <div class="col-md-4">
    <label class="form-label" for="image_source_url">URL source image</label>
    <input type="url" id="image_source_url" name="image_source_url" maxlength="500" class="form-control @error('image_source_url') is-invalid @enderror" value="{{ old('image_source_url', $blocks['image_source_url'] ?? '') }}">
    @error('image_source_url')<div class="invalid-feedback">{{ $message }}</div>@enderror
  </div>

  <div class="col-12">
    <label class="form-label" for="lead">Lead <small class="text-muted">(paragraphe d'introduction sous le titre)</small></label>
    <textarea id="lead" name="lead" rows="3" class="form-control @error('lead') is-invalid @enderror">{{ old('lead', $blocks['lead'] ?? '') }}</textarea>
    @error('lead')<div class="invalid-feedback">{{ $message }}</div>@enderror
  </div>

  <div class="col-12">
    <label class="form-label" for="sections_json">Sections (JSON) <small class="text-muted">tableau d'objets {heading, body}</small></label>
    <textarea id="sections_json" name="sections_json" rows="14" class="form-control font-monospace small @error('sections_json') is-invalid @enderror">{{ old('sections_json', $sectionsJson) }}</textarea>
    @error('sections_json')<div class="invalid-feedback">{{ $message }}</div>@enderror
    <small class="text-muted">Format attendu : <code>[{"heading":"Titre section","body":"Paragraphe 1.\n\nParagraphe 2."}, ...]</code>. Utilise <code>\n\n</code> pour creer des paragraphes dans le body.</small>
  </div>

  <div class="col-12 d-flex justify-content-end gap-2 pt-2">
    <a href="{{ route('admin.pages.index') }}" class="btn btn-outline-secondary">Annuler</a>
    <button type="submit" class="btn btn-primary">
      <i class="bx {{ $isEdit ? 'bx-save' : 'bx-plus' }} me-1"></i>
      {{ $isEdit ? 'Enregistrer' : 'Creer la page' }}
    </button>
  </div>
</form>
