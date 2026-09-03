<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Preview - {{ $fileAsset->filename }}</title>
    <style>
        :root {
            --bg: #f8fafc;
            --card: #ffffff;
            --text: #0f172a;
            --muted: #64748b;
            --line: #e2e8f0;
            --accent: #0284c7;
            --highlight: #eab308;
        }

        * { box-sizing: border-box; }

        body {
            margin: 0;
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
            color: var(--text);
            background: radial-gradient(circle at top, #e0f2fe, var(--bg) 60%);
            padding: 24px;
            min-height: 100vh;
        }

        .shell {
            max-width: 1280px;
            margin: 0 auto;
        }

        .header {
            background: var(--card);
            border: 1px solid var(--line);
            border-radius: 14px;
            padding: 16px 20px;
            box-shadow: 0 4px 16px rgba(15, 23, 42, 0.05);
            margin-bottom: 20px;
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
        }

        .header-main h1 {
            margin: 0;
            font-size: 18px;
            font-weight: 700;
            color: #0f172a;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .meta {
            margin-top: 4px;
            color: var(--muted);
            font-size: 13px;
        }

        .highlight-alert {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: #fefce8;
            border: 1px solid #fef08a;
            color: #854d0e;
            padding: 6px 12px;
            border-radius: 8px;
            font-size: 13px;
            font-weight: 500;
        }

        .highlight-alert strong {
            color: #713f12;
            background: #fef08a;
            padding: 2px 6px;
            border-radius: 4px;
        }

        .slides {
            display: grid;
            gap: 24px;
        }

        .slide-card {
            background: var(--card);
            border: 1px solid var(--line);
            border-radius: 14px;
            padding: 16px;
            box-shadow: 0 4px 16px rgba(15, 23, 42, 0.05);
            transition: border-color 0.2s, box-shadow 0.2s;
        }

        .slide-card.target {
            border-color: var(--accent);
            box-shadow: 0 0 0 3px rgba(2, 132, 199, 0.15), 0 8px 24px rgba(2, 132, 199, 0.12);
        }

        .slide-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 12px;
        }

        .slide-title {
            font-size: 14px;
            font-weight: 700;
            color: #475569;
            margin: 0;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .slide-stage {
            position: relative;
            width: 100%;
            background: #ffffff;
            border-radius: 10px;
            border: 1px solid #cbd5e1;
            overflow: hidden;
            aspect-ratio: {{ $slideWidth }} / {{ $slideHeight }};
            box-shadow: inset 0 2px 6px rgba(0, 0, 0, 0.02);
        }

        .el {
            position: absolute;
            overflow: hidden;
        }

        .el.text {
            padding: 4px 6px;
            font-size: 14px;
            line-height: 1.3;
            white-space: pre-wrap;
            color: #1e293b;
        }

        .el.text.text-highlighted {
            background: rgba(254, 240, 138, 0.5) !important;
            border: 2.5px solid #eab308 !important;
            border-radius: 6px;
            box-shadow: 0 0 0 3px rgba(234, 179, 8, 0.25), 0 4px 18px rgba(234, 179, 8, 0.35);
            animation: highlight-pulse 2.5s infinite ease-in-out;
            z-index: 40;
        }

        @keyframes highlight-pulse {
            0%, 100% {
                box-shadow: 0 0 0 3px rgba(234, 179, 8, 0.25), 0 4px 18px rgba(234, 179, 8, 0.35);
            }
            50% {
                box-shadow: 0 0 0 6px rgba(234, 179, 8, 0.55), 0 8px 28px rgba(234, 179, 8, 0.5);
            }
        }

        .highlight-badge {
            position: absolute;
            top: -10px;
            left: 2px;
            background: #ca8a04;
            color: #ffffff;
            font-size: 10px;
            font-weight: 700;
            padding: 1px 6px;
            border-radius: 4px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            pointer-events: none;
            box-shadow: 0 2px 4px rgba(0,0,0,0.2);
            z-index: 50;
        }

        mark.matched-phrase {
            background: #fde047;
            color: #1c1917;
            font-weight: 700;
            border-radius: 2px;
            padding: 1px 3px;
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
            padding: 24px;
            color: var(--muted);
            text-align: center;
        }
    </style>
</head>
<body>
<div class="shell">
    <div class="header">
        <div class="header-main">
            <h1>
                <span>📄 {{ $fileAsset->filename }} - Extracted Preview</span>
            </h1>
            <div class="meta">
                Slides : <strong>{{ count($slides) }}</strong> | Dimensions : {{ $slideWidth }} &times; {{ $slideHeight }} EMU
                @if(($requestedSlide ?? 0) > 0)
                    | Diapositive ciblée : <strong>#{{ $requestedSlide }}</strong>
                @endif
            </div>
        </div>

        @if(!empty($requestedHighlight))
            <div class="highlight-alert">
                <span>🎯 Texte ciblé :</span>
                <strong>{{ $requestedHighlight }}</strong>
            </div>
        @endif
    </div>

    @if(count($slides) === 0)
        <div class="empty">Aucune diapositive extraite disponible pour cette présentation.</div>
    @else
        @php
            $targetTerm = !empty($requestedHighlight) ? trim($requestedHighlight) : '';
            $targetTermNormalized = !empty($targetTerm) ? mb_strtolower(preg_replace('/[^\p{L}\p{N}]/u', '', $targetTerm)) : '';
        @endphp

        <div class="slides">
            @foreach($slides as $slide)
                @php
                    $isRequestedSlide = (($requestedSlide ?? 0) === (int) $slide['id']);
                @endphp
                <section id="slide-{{ $slide['id'] }}" class="slide-card{{ $isRequestedSlide ? ' target' : '' }}">
                    <div class="slide-header">
                        <h2 class="slide-title">
                            <span>Slide {{ $slide['id'] }}</span>
                            @if($isRequestedSlide)
                                <span style="font-size: 11px; background: #0284c7; color: #fff; padding: 2px 8px; border-radius: 12px;">Cible</span>
                            @endif
                        </h2>
                    </div>

                    <div class="slide-stage">
                        @foreach($slide['elements'] as $element)
                            @php
                                $isMatch = false;
                                if (!empty($targetTerm) && $element['type'] === 'text') {
                                    $elemContent = $element['content'] ?? '';
                                    if (stripos($elemContent, $targetTerm) !== false) {
                                        $isMatch = true;
                                    } elseif (!empty($targetTermNormalized)) {
                                        $elemNormalized = mb_strtolower(preg_replace('/[^\p{L}\p{N}]/u', '', $elemContent));
                                        if (str_contains($elemNormalized, $targetTermNormalized) || str_contains($targetTermNormalized, $elemNormalized)) {
                                            $isMatch = true;
                                        }
                                    }
                                }
                            @endphp

                            <div
                                class="el {{ $element['type'] }}{{ $isMatch ? ' text-highlighted' : '' }}"
                                style="left: {{ $element['left_pct'] }}%; top: {{ $element['top_pct'] }}%; width: {{ $element['width_pct'] }}%; height: {{ $element['height_pct'] }}%;"
                                @if($isMatch) id="highlighted-element-{{ $slide['id'] }}" data-highlighted="true" @endif
                            >
                                @if($isMatch)
                                    <span class="highlight-badge">Emplacement ciblé</span>
                                @endif

                                @if($element['type'] === 'text')
                                    @if($isMatch && !empty($targetTerm))
                                        {!! preg_replace('/(' . preg_quote($targetTerm, '/') . ')/iu', '<mark class="matched-phrase">$1</mark>', e($element['content'])) !!}
                                    @else
                                        {{ $element['content'] }}
                                    @endif
                                @elseif($element['type'] === 'image')
                                    @if($element['asset_url'])
                                        <img src="{{ $element['asset_url'] }}" alt="{{ $element['description'] }}" loading="lazy">
                                    @else
                                        <div class="missing">Image non trouvée</div>
                                    @endif
                                @elseif($element['type'] === 'video')
                                    @if($element['asset_url'])
                                        <video src="{{ $element['asset_url'] }}" controls preload="metadata"></video>
                                    @else
                                        <div class="missing">Vidéo non trouvée</div>
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

<script>
    (function () {
        const requestedSlide = {{ (int) ($requestedSlide ?? 0) }};
        const highlightedEl = document.querySelector('[data-highlighted="true"]');
        const targetSlide = requestedSlide > 0 ? document.getElementById('slide-' + requestedSlide) : null;

        if (highlightedEl) {
            // Smoothly center the highlighted text emplacement in viewport
            setTimeout(() => {
                highlightedEl.scrollIntoView({ behavior: 'smooth', block: 'center' });
            }, 100);
        } else if (targetSlide) {
            // Smoothly scroll to slide
            setTimeout(() => {
                targetSlide.scrollIntoView({ behavior: 'smooth', block: 'start' });
            }, 100);
        }
    })();
</script>
</body>
</html>
