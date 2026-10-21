@props(['campaign' => null])

<div class="mt-5 grid gap-3">
    <label class="grid gap-2">
        <span class="text-sm font-medium">Images in email body</span>
        <input type="file" name="images[]" multiple accept="image/jpeg,image/png,image/gif" class="w-full rounded-xl border border-slate-300 px-3.5 py-2.5 text-sm file:mr-3 file:rounded-lg file:border-0 file:bg-violet-50 file:px-3 file:py-2 file:font-semibold file:text-violet-700">
        <span class="text-xs text-slate-500">Add up to 5 JPG, PNG or GIF images per save, 2 MB each. Images appear below your message in the selected order. Save to preview.</span>
    </label>
    @foreach ($campaign?->images ?? [] as $index => $image)
        <div class="grid gap-2 rounded-xl border border-slate-200 p-3">
            <img src="{{ route('campaigns.images.show', [$campaign, $index]) }}" alt="Campaign image {{ $loop->iteration }}" class="max-h-64 max-w-full rounded-lg object-contain">
            <label class="flex items-center gap-2 text-sm text-slate-600"><input type="checkbox" name="remove_images[]" value="{{ $index }}" @checked(in_array($index, old('remove_images', [])))> Remove image {{ $loop->iteration }} when saving</label>
        </div>
    @endforeach
</div>
