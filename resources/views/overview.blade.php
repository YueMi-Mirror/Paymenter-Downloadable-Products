@php
    $expiryDays = isset($settings['download_expiry']) ? (int)$settings['download_expiry'] : 0;
    $expiryDate = $expiryDays > 0 ? $service->created_at->addDays($expiryDays) : null;
@endphp

<div class="bg-background-secondary border border-neutral p-6 rounded-lg mt-2">
    <h1 class="text-2xl font-semibold mb-4">Download Info</h1>

    <div class="grid md:grid-cols-2 gap-6">
        <div class="flex flex-col gap-3">
            <div class="flex items-center text-base">
                <span class="mr-2 font-medium text-base/70">Download Count:</span>
                <span class="text-base/50 font-semibold">{{ $service->download_count }} /
                    {{ $settings['download_limit'] > 0 ? $settings['download_limit'] : '∞' }}</span>
            </div>

            @if($expiryDate)
            <div class="flex items-center text-base">
                <span class="mr-2 font-medium text-base/70">Expires on:</span>
                <span class="text-base/50">{{ $expiryDate->format('M d, Y H:i:s') }}</span>
            </div>
            <div class="flex items-center text-base">
                <span class="mr-2 font-medium text-base/70">Time remaining:</span>
                <span id="download-timer" class="text-red-500 font-bold"></span>
            </div>
            @endif
        </div>

        @if(isset($settings['file_checksum']))
        <div x-data="{ showToast: false }" class="flex flex-col gap-2">
            <span class="text-sm font-medium text-base/70">File Checksum (SHA256):</span>
            <div class="flex flex-row gap-2 items-center bg-background/50 p-2 rounded border border-neutral/30">
                <span class="text-xs font-mono break-all text-base/50 flex-1">{{ $settings['file_checksum'] }}</span>
                <button
                    class="px-3 py-1 bg-blue-600/10 text-blue-500 border border-blue-600/30 rounded hover:bg-blue-600 hover:text-white transition text-xs font-semibold"
                    @click="
                        navigator.clipboard.writeText('{{ $settings['file_checksum'] }}');
                        showToast = true;
                        setTimeout(() => showToast = false, 3000);
                    "
                >
                    Copy
                </button>
            </div>

            <div
                x-show="showToast"
                x-transition
                class="fixed bottom-4 right-4 bg-green-500 text-white px-4 py-2 rounded shadow-lg z-50"
            >
                Checksum copied!
            </div>
        </div>
        @endif
    </div>
</div>

@if($versions->count() > 0)
<div class="bg-background-secondary border border-neutral p-6 rounded-lg mt-4">
    <div class="flex items-center gap-3 mb-6">
        <div class="w-10 h-10 rounded-full bg-blue-600/10 flex items-center justify-center">
            <i class="ri-history-line text-xl text-blue-500"></i>
        </div>
        <div>
            <h2 class="text-xl font-semibold">Available Versions</h2>
            <p class="text-xs text-base/40">Download previous releases and updates</p>
        </div>
    </div>
    
    <div class="overflow-x-auto">
        <table class="w-full text-left">
            <thead>
                <tr class="border-b border-neutral/50 text-base/40 text-[10px] uppercase font-bold tracking-[0.1em]">
                    <th class="py-3 px-4">Version</th>
                    <th class="py-3 px-4">Release Date</th>
                    <th class="py-3 px-4">Release Notes</th>
                    <th class="py-3 px-4 text-right">Action</th>
                </tr>
            </thead>
            <tbody>
                @foreach($versions as $version)
                <tr class="border-b border-neutral/20 hover:bg-neutral/5 transition-colors group">
                    <td class="py-4 px-4">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-blue-600 text-white shadow-sm shadow-blue-600/20">
                            {{ $version->version }}
                        </span>
                    </td>
                    <td class="py-4 px-4 text-sm text-base/50 font-medium">{{ $version->created_at->format('M d, Y') }}</td>
                    <td class="py-4 px-4 text-sm text-base/60">
                        @if($version->release_notes)
                            <div class="flex items-center gap-2">
                                <i class="ri-information-line text-blue-500/50"></i>
                                <span class="italic">{{ Str::limit($version->release_notes, 100) }}</span>
                            </div>
                        @else
                            <span class="text-base/20 italic">No release notes provided</span>
                        @endif
                    </td>
                    <td class="py-4 px-4 text-right">
                        <a href="{{ route('service.download', ['service' => $service->id, 'version' => $version->id]) }}" 
                           class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg transition-all text-xs font-bold shadow-lg shadow-blue-600/30 hover:-translate-y-0.5 active:translate-y-0">
                            <i class="ri-download-cloud-2-line"></i>
                            Download
                        </a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endif

@if($expiryDate)
<script>
const expiry = new Date("{{ $expiryDate->toIsoString() }}").getTime();
const timerEl = document.querySelector('#download-timer');

setInterval(() => {
    const now = new Date().getTime();
    const distance = expiry - now;

    if (distance < 0) {
        timerEl.innerText = 'Expired';
        return;
    }

    const d = Math.floor(distance / (1000 * 60 * 60 * 24));
    const h = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
    const m = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
    const s = Math.floor((distance % (1000 * 60)) / 1000);

    timerEl.innerText = `${d}d ${h}h ${m}m ${s}s`;
}, 1000);
</script>
@endif
