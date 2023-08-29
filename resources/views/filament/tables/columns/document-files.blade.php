@if (is_array($getState()))
    @foreach ($getState() as $file)
        <a href="{{ Storage::url($file) }}" target="_blank"
            class="inline-flex items-center justify-center space-x-1 rtl:space-x-reverse min-h-6 px-2 py-0.5 text-sm font-medium tracking-tight rounded-xl whitespace-nowrap text-{{ $getColor() }}-700 bg-{{ $getColor() }}-500/10 dark:text-{{ $getColor() }}--300 dark:bg-{{ $getColor() }}--500/20">
            {{ basename($file) }}
        </a>
    @endforeach
@endif
