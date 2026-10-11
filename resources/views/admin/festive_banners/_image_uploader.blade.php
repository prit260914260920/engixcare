{{--
  Reusable image uploader slot for festive banner form.
  Variables:
    $fieldName   — hidden input name & ID suffix (e.g. 'product_image_path')
    $previewId   — unique ID suffix for preview img (e.g. 'prev_product')
    $currentPath — existing storage path (nullable)
    $currentUrl  — resolved public URL (nullable)
    $label       — alt text / aria label
--}}
<div class="flex items-start gap-md">

    {{-- Current image preview box --}}
    <div class="relative w-28 h-20 rounded-xl overflow-hidden border border-outline-variant/30 bg-surface-container flex-shrink-0">
        <img id="img-{{ $previewId }}"
             src="{{ $currentUrl ?? '' }}"
             alt="{{ $label }}"
             class="{{ $currentUrl ? '' : 'hidden' }} w-full h-full object-contain" />
        <div id="spinner-{{ $previewId }}" class="hidden absolute inset-0 bg-black/40 flex items-center justify-center">
            <span class="material-symbols-outlined text-white text-[28px] animate-spin">progress_activity</span>
        </div>
        @if(!$currentUrl)
        <div class="flex items-center justify-center h-full text-on-surface-variant/40">
            <span class="material-symbols-outlined text-[32px]">add_photo_alternate</span>
        </div>
        @endif
    </div>

    {{-- Drop zone --}}
    <div class="flex-1">
        <div class="relative border-2 border-dashed border-outline-variant/60 rounded-xl bg-surface hover:border-primary hover:bg-primary/5 transition-colors cursor-pointer py-4 px-md flex flex-col items-center gap-1"
             onclick="document.getElementById('file-{{ $previewId }}').click()"
             ondragover="event.preventDefault(); this.classList.add('border-primary','bg-primary/10')"
             ondragleave="this.classList.remove('border-primary','bg-primary/10')"
             ondrop="event.preventDefault(); this.classList.remove('border-primary','bg-primary/10'); uploadBannerImage(event.dataTransfer.files[0], '{{ $fieldName }}', '{{ $previewId }}')">
            <span class="material-symbols-outlined text-[28px] text-on-surface-variant/50">cloud_upload</span>
            <span class="text-body-sm text-on-surface-variant text-center">
                Drop image here or <span class="text-primary font-semibold">click to browse</span>
            </span>
            <span class="text-label-sm text-on-surface-variant/60">PNG, JPG, WEBP — max 8 MB</span>
            <input type="file" id="file-{{ $previewId }}" accept="image/*" class="hidden"
                   onchange="uploadBannerImage(this.files[0], '{{ $fieldName }}', '{{ $previewId }}')" />
        </div>
        <button type="button" id="remove-{{ $fieldName }}"
                onclick="removeImage('{{ $fieldName }}', '{{ $previewId }}')"
                class="{{ ($currentUrl && $currentUrl !== '') ? '' : 'hidden' }} mt-sm flex items-center gap-xs text-error text-label-sm hover:underline">
            <span class="material-symbols-outlined text-[16px]">delete</span> Remove image
        </button>
    </div>

    {{-- Hidden field carries the path to the server --}}
    <input type="hidden" name="{{ $fieldName }}" id="hidden-{{ $fieldName }}" value="{{ $currentPath }}" />
</div>
