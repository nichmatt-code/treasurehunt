@props(['name'])

@php
$paths = [
    'compass' => '<circle cx="12" cy="12" r="9" /><polygon points="14.2,9.8 12,12 9.8,14.2 12,12" /><polygon points="9.8,9.8 12,12 14.2,14.2 12,12" fill="currentColor" stroke="none" opacity="0.35" />',
    'chat' => '<path d="M4 5.5h16a1 1 0 011 1v9a1 1 0 01-1 1H9.5L5 20.5V16.5H4a1 1 0 01-1-1v-9a1 1 0 011-1Z" />',
    'check-circle' => '<circle cx="12" cy="12" r="9" /><polyline points="8,12.5 11,15.5 16,9" />',
    'search' => '<circle cx="11" cy="11" r="6.5" /><line x1="20" y1="20" x2="15.3" y2="15.3" />',
    'sparkles' => '<path d="M12 3.5v4M12 16.5v4M4.5 12h4M15.5 12h4M6.8 6.8l1.8 1.8M15.4 15.4l1.8 1.8M6.8 17.2l1.8-1.8M15.4 8.6l1.8-1.8" /><circle cx="12" cy="12" r="2.5" />',
    'send' => '<line x1="21" y1="3" x2="10.5" y2="13.5" /><path d="M21 3 14 21l-3.5-7.5L3 10 21 3Z" />',
    'users' => '<circle cx="9" cy="9" r="3" /><path d="M3.5 19a5.5 5.5 0 0111 0" /><circle cx="17" cy="10.5" r="2.5" /><path d="M14.5 19a4 4 0 017.5-1.8" />',
    'academic-cap' => '<path d="M12 4 3 9l9 5 9-5-9-5Z" /><path d="M7 11.5V16c0 1.7 2.7 3 5 3s5-1.3 5-3v-4.5" /><path d="M21 9v5.5" />',
    'gift' => '<rect x="4" y="9.5" width="16" height="3.5" rx="1" /><rect x="5" y="13" width="14" height="7.5" rx="1" /><path d="M12 9.5v11" /><path d="M12 9.5c-1.6-3-5.2-2.6-5.2-.3 0 1.6 2.3 2.1 5.2.3ZM12 9.5c1.6-3 5.2-2.6 5.2-.3 0 1.6-2.3 2.1-5.2.3Z" />',
    'user' => '<circle cx="12" cy="8" r="3.3" /><path d="M5.2 20a6.8 6.8 0 0113.6 0" />',
    'logout' => '<path d="M15 4.5H8a2 2 0 00-2 2v11a2 2 0 002 2h7" /><path d="M9.5 12H20" /><path d="M16.5 8l4 4-4 4" />',
    'close' => '<line x1="6" y1="6" x2="18" y2="18" /><line x1="18" y1="6" x2="6" y2="18" />',
    'shield' => '<path d="M12 3.5 5 6v5.2c0 4.4 3 7.7 7 9.3 4-1.6 7-4.9 7-9.3V6l-7-2.5Z" /><path d="M9 12l2 2 4-4.2" />',
];
$path = $paths[$name] ?? '';
@endphp

<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" {{ $attributes }}>
    {!! $path !!}
</svg>
