@props(['id' => 'mermaid-' . uniqid(), 'definition', 'title' => null, 'description' => null])

<div class="w-full">
    @if ($title)
        <h3 class="text-lg font-semibold text-primary mb-4">{{ $title }}</h3>
    @endif

    <div class="card bg-base-100 shadow-lg">
        <div class="card-body">
            <div class="flex justify-between items-center mb-4">
                <div class="flex-1">
                    <h4 class="card-title text-sm">Diagramme</h4>
                    @if ($description)
                        <p class="text-sm text-base-content/70 text-wrap">{{ $description }}</p>
                    @endif
                </div>
                <div class="flex-1 flex items-start justify-end gap-2">
                    <button onclick="downloadDiagram('{{ $id }}')" class="btn btn-primary rounded-full">
                        <x-heroicon-s-photo class="size-4" />
                        {{ __('Download SVG') }}
                    </button>
                    <button onclick="downloadDiagram('{{ $id }}', 'png')"
                        class="btn btn-secondary rounded-full">
                        <x-heroicon-s-photo class="size-4" />
                        {{ __('Download PNG') }}
                    </button>
                </div>
            </div>

            <div class="bg-base-200 rounded-lg p-4 overflow-auto">
                <div id="{{ $id }}" class="mermaid flex items-center justify-center">
                    {{ $definition }}
                </div>
            </div>
        </div>
    </div>
</div>

@once
    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/mermaid@10.6.1/dist/mermaid.min.js"></script>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                mermaid.initialize({
                    startOnLoad: true,
                    theme: 'forest',
                    securityLevel: 'loose'
                });
            });

            function downloadDiagram(id, format = 'svg') {
                const element = document.getElementById(id);
                const svg = element.querySelector('svg');

                if (!svg) {
                    console.error('SVG not found in the diagram');
                    return;
                }

                // Get component props title if available from blade php
                const componentTitle = @json($title ?? '');

                // Clone the SVG to avoid modifying the original
                const clonedSvg = svg.cloneNode(true);

                // Set width and height attributes explicitly
                const rect = svg.getBoundingClientRect();
                const width = rect.width || 800;
                const height = rect.height || 600;

                clonedSvg.setAttribute('width', width);
                clonedSvg.setAttribute('height', height);

                // Add white background
                const background = document.createElementNS('http://www.w3.org/2000/svg', 'rect');
                background.setAttribute('width', '100%');
                background.setAttribute('height', '100%');
                background.setAttribute('fill', 'white');
                clonedSvg.insertBefore(background, clonedSvg.firstChild);

                // Generate filename
                let filename;
                if (componentTitle) {
                    filename = `${componentTitle.replace(/[^a-z0-9]/gi, '_').toLowerCase()}`;
                } else {
                    const cardTitle = document.querySelector(`#${id}`).closest('.card').querySelector('.card-title');
                    const title = cardTitle ? cardTitle.textContent.trim() : '';
                    filename = title ?
                        `${title.replace(/[^a-z0-9]/gi, '_').toLowerCase()}` :
                        `diagram-${id}`;
                }

                if (format === 'png') {
                    // Convert SVG to PNG using canvas
                    const data = new XMLSerializer().serializeToString(clonedSvg);
                    const svgDataUrl = 'data:image/svg+xml;base64,' + btoa(unescape(encodeURIComponent(data)));

                    const canvas = document.createElement('canvas');
                    const ctx = canvas.getContext('2d');

                    // Set canvas size with higher resolution for better quality
                    const scale = 2;
                    canvas.width = width * scale;
                    canvas.height = height * scale;
                    ctx.scale(scale, scale);

                    const img = new Image();
                    img.onload = function() {
                        // Fill white background
                        ctx.fillStyle = 'white';
                        ctx.fillRect(0, 0, width, height);

                        // Draw the SVG
                        ctx.drawImage(img, 0, 0, width, height);

                        // Convert to PNG and download
                        canvas.toBlob(function(blob) {
                            const url = URL.createObjectURL(blob);
                            const downloadLink = document.createElement('a');
                            downloadLink.href = url;
                            downloadLink.download = filename + '.png';

                            document.body.appendChild(downloadLink);
                            downloadLink.click();
                            document.body.removeChild(downloadLink);

                            // Clean up
                            setTimeout(() => {
                                URL.revokeObjectURL(url);
                            }, 100);
                        }, 'image/png', 1.0);
                    };

                    img.onerror = function() {
                        console.error('Failed to load SVG for PNG conversion');
                        alert('Erreur lors de la conversion en PNG. Veuillez essayer le format SVG.');
                    };

                    img.src = svgDataUrl;
                } else {
                    // SVG download (existing functionality)
                    const data = new XMLSerializer().serializeToString(clonedSvg);
                    const svgBlob = new Blob([data], {
                        type: 'image/svg+xml;charset=utf-8'
                    });
                    const url = URL.createObjectURL(svgBlob);

                    const downloadLink = document.createElement('a');
                    downloadLink.href = url;
                    downloadLink.download = filename + '.svg';

                    document.body.appendChild(downloadLink);
                    downloadLink.click();
                    document.body.removeChild(downloadLink);

                    // Clean up
                    setTimeout(() => {
                        URL.revokeObjectURL(url);
                    }, 100);
                }
            }
        </script>
    @endpush
@endonce
