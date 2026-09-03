<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Preview - {{ $fileAsset->filename }}</title>
    <style>
        :root {
            --bg: #f1f5f9;
            --card: #ffffff;
            --text: #0f172a;
            --muted: #64748b;
            --line: #cbd5e1;
            --accent: #0ea5e9;
        }

        * { box-sizing: border-box; }

        body {
            margin: 0;
            font-family: "SF Pro Text", "Segoe UI", Arial, sans-serif;
            color: var(--text);
            background: radial-gradient(circle at top, #e0f2fe, var(--bg) 50%);
            padding: 24px;
        }

        .shell {
            max-width: 1200px;
            margin: 0 auto;
        }

        .header {
            background: var(--card);
            border: 1px solid var(--line);
            border-radius: 14px;
            padding: 16px 18px;
            box-shadow: 0 8px 24px rgba(15, 23, 42, 0.06);
            margin-bottom: 20px;
        }

        .header h1 {
            margin: 0;
            font-size: 20px;
            font-weight: 700;
        }

        .meta {
            margin-top: 6px;
            color: var(--muted);
            font-size: 13px;
        }

        .slides {
            display: grid;
            gap: 18px;
        }

        .slide-card {
            background: var(--card);
            border: 1px solid var(--line);
            border-radius: 14px;
            padding: 14px;
            box-shadow: 0 8px 24px rgba(15, 23, 42, 0.06);
        }

        .slide-card.target {
            border-color: var(--accent);
            box-shadow: 0 0 0 2px rgba(14, 165, 233, 0.2), 0 10px 28px rgba(2, 132, 199, 0.15);
        }

        .slide-title {
            font-size: 14px;
            font-weight: 700;
            color: var(--muted);
            margin: 0 0 10px;
        }

        .slide-stage {
            position: relative;
            width: 100%;
            background: #fff;
            border-radius: 10px;
            border: 1px solid #e2e8f0;
            overflow: hidden;
            aspect-ratio: {{ $slideWidth }} / {{ $slideHeight }};
        }

        .el {
            position: absolute;
            overflow: hidden;
        }

        .el.text {
            padding: 4px 6px;
            font-size: 14px;
            line-height: 1.25;
            white-space: pre-wrap;
            color: #111827;
        }

        .el.image img,
        .el.video video {
            width: 100%;
            height: 100%;
            display: block;
            object-fit: contain;
            background: #f8fafc;
        }

        .missing {
            width: 100%;
            height: 100%;
            display: grid;
            place-items: center;
            border: 1px dashed #ef4444;
            color: #b91c1c;
            font-size: 12px;
            background: #fef2f2;
        }

        .empty {
            background: var(--card);
            border: 1px solid var(--line);
            border-radius: 14px;
            padding: 20px;
            color: var(--muted);
        }
    </style>
</head>
<body>
<div class="shell">
    <div class="header">
        <h1>{{ $fileAsset->filename }} - Extracted Preview</h1>
        <div class="meta">Slides: {{ count($slides) }} | Layout: {{ $slideWidth }} x {{ $slideHeight }} EMU</div>
        @if(($requestedSlide ?? 0) > 0 && !($requestedSlideExists ?? false))
            <div class="meta" style="color:#b45309;">Requested slide {{ $requestedSlide }} was not found in extracted data.</div>
        @endif
    </div>

    @if(count($slides) === 0)
        <div class="empty">No extracted slides available in this presentation JSON.</div>
    @else
        <div class="slides">
            @foreach($slides as $slide)
                <section id="slide-{{ $slide['id'] }}" class="slide-card{{ (($requestedSlide ?? 0) === (int) $slide['id']) ? ' target' : '' }}">
                    <h2 class="slide-title">Slide {{ $slide['id'] }}</h2>
                    <div class="slide-stage">
                        @foreach($slide['elements'] as $element)
                            <div
                                class="el {{ $element['type'] }}"
                                style="left: {{ $element['left_pct'] }}%; top: {{ $element['top_pct'] }}%; width: {{ $element['width_pct'] }}%; height: {{ $element['height_pct'] }}%;"
                            >
                                @if($element['type'] === 'text')
                                    {{ $element['content'] }}
                                @elseif($element['type'] === 'image')
                                    @if($element['asset_url'])
                                        <img src="{{ $element['asset_url'] }}" alt="{{ $element['description'] }}" loading="lazy">
                                    @else
                                        <div class="missing">Missing image path</div>
                                    @endif
                                @elseif($element['type'] === 'video')
                                    @if($element['asset_url'])
                                        <video src="{{ $element['asset_url'] }}" controls preload="metadata"></video>
                                    @else
                                        <div class="missing">Missing video path</div>
                                    @endif
                                @endif
                            </div>
                        @endforeach
                    </div>
                </section>
            @endforeach
        </div>
    @endif
</div>
@if(($requestedSlide ?? 0) > 0 && ($requestedSlideExists ?? false))
<script>
    (function () {
        const target = document.getElementById('slide-{{ $requestedSlide }}');
        if (target) {
            target.scrollIntoView({ behavior: 'smooth', block: 'start' });
        }
    })();
</script>
@endif
</body>
</html>
