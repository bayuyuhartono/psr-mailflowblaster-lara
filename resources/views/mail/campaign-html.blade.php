<!DOCTYPE html>
<html>
<head><meta charset="utf-8"></head>
<body style="font-family: Arial, sans-serif; line-height: 1.6;">
    <div style="white-space: pre-wrap;">{{ $personalizedBody }}</div>
    @foreach ($campaign->images as $image)
        <p><img src="{{ $message->embed(Storage::disk('local')->path($image)) }}" alt="Campaign image {{ $loop->iteration }}" style="display: block; max-width: 100%; height: auto;"></p>
    @endforeach
</body>
</html>
