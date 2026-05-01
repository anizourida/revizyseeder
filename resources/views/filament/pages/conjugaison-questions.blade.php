<x-filament-panels::page>
    <div class="space-y-6">
        {{-- Info Bar --}}
        <div class="flex flex-wrap items-center gap-4 p-4 bg-white rounded-xl shadow-sm border border-gray-200 dark:bg-gray-800 dark:border-gray-700">
            <div class="flex gap-2">
                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-primary-100 text-primary-700 dark:bg-primary-900 dark:text-primary-300">{{ $this->conjugaison->n }}</span>
                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-300">{{ $this->conjugaison->p }}</span>
                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-300">{{ $this->conjugaison->sem }}</span>
            </div>
            @if($this->conjugaison->verbe)
                <span class="font-bold text-gray-900 dark:text-white">{{ $this->conjugaison->verbe }}</span>
            @endif
            @if($this->conjugaison->tense)
                <span class="text-sm text-gray-500 dark:text-gray-400">{{ $this->conjugaison->tense }}</span>
            @endif
            @if($this->conjugaison->source_file_asset_id)
                <a
                    href="{{ route('admin.files.preview', ['fileAsset' => $this->conjugaison->source_file_asset_id, 'slide' => $this->conjugaison->source_slide_id ?: null]) }}{{ $this->conjugaison->source_slide_id ? '#slide-' . $this->conjugaison->source_slide_id : '' }}"
                    target="_blank"
                    class="text-xs text-primary-600 hover:text-primary-500 dark:text-primary-400"
                >
                    Preview source{{ $this->conjugaison->source_slide_id ? ' (slide ' . $this->conjugaison->source_slide_id . ')' : '' }}
                </a>
            @endif

            <div id="concept-badge" class="ml-auto">
                @if($this->conjugaison->concept_id)
                    <span class="inline-flex items-center gap-1 px-3 py-1.5 rounded-full text-sm font-semibold bg-success-100 text-success-700 dark:bg-success-900/40 dark:text-success-400">
                        ✓ Concept #<span id="concept-id-display">{{ $this->conjugaison->concept_id }}</span>
                    </span>
                @else
                    <span id="no-concept-badge" class="inline-flex items-center gap-1 px-3 py-1.5 rounded-full text-sm font-medium bg-warning-100 text-warning-700 dark:bg-warning-900/40 dark:text-warning-400">
                        ⚠ No concept linked
                    </span>
                    <span id="has-concept-badge" class="hidden inline-flex items-center gap-1 px-3 py-1.5 rounded-full text-sm font-semibold bg-success-100 text-success-700 dark:bg-success-900/40 dark:text-success-400">
                        ✓ Concept #<span id="concept-id-display">—</span>
                    </span>
                @endif
            </div>
            <a href="{{ \App\Filament\Resources\ConjugaisonResource::getUrl() }}"
                class="text-sm text-primary-600 hover:text-primary-500 dark:text-primary-400">← Back to list</a>
        </div>

        {{-- Concept Management --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 dark:bg-gray-800 dark:border-gray-700 p-6">
            <h3 class="text-base font-semibold text-gray-900 dark:text-white mb-5">
                <x-heroicon-o-link class="w-5 h-5 inline mr-1" /> Concept
            </h3>

            <div x-data="{ mode: '{{ $this->conjugaison->concept_id ? 'linked' : 'none' }}' }" class="space-y-5">

                {{-- Tabs --}}
                <div class="flex gap-2 border-b border-gray-200 dark:border-gray-700 pb-0">
                    <button @click="mode = 'select'"
                        :class="mode === 'select' ? 'border-b-2 border-primary-500 text-primary-600 dark:text-primary-400' : 'text-gray-500 dark:text-gray-400 hover:text-gray-700'"
                        class="px-4 py-2.5 text-sm font-medium transition">
                        Select existing
                    </button>
                    <button @click="mode = 'create'"
                        :class="mode === 'create' ? 'border-b-2 border-primary-500 text-primary-600 dark:text-primary-400' : 'text-gray-500 dark:text-gray-400 hover:text-gray-700'"
                        class="px-4 py-2.5 text-sm font-medium transition">
                        Create new concept
                    </button>
                </div>

                {{-- Select existing --}}
                <div x-show="mode === 'select'" class="space-y-4 pt-6">
                    <div class="flex gap-3 items-center">
                        <input type="number" id="existing-concept-id"
                            value="{{ $this->conjugaison->concept_id ?? '' }}"
                            placeholder="Paste concept ID..."
                            class="w-48 rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-gray-300 text-sm focus:border-primary-500 focus:ring-primary-500">
                        <button id="btn-verify-concept"
                            class="px-4 py-2 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 text-sm font-medium rounded-lg hover:bg-gray-200 dark:hover:bg-gray-600 transition">
                            Verify
                        </button>
                    </div>
                    <div id="verify-result" class="hidden text-sm p-3 rounded-lg bg-gray-50 dark:bg-gray-900 text-gray-700 dark:text-gray-300"></div>
                    <button id="btn-link-concept"
                        class="hidden px-4 py-2 bg-success-600 text-white text-sm font-semibold rounded-lg hover:bg-success-500 transition">
                        ✓ Link this concept
                    </button>
                </div>

                {{-- Create new --}}
                <div x-show="mode === 'create'" class="space-y-4 pt-6">
                    @php
                        $gradeSkillMap = ['N1'=>11,'N2'=>10,'N3'=>12,'N4'=>2,'N5'=>13,'N6'=>14];
                        $n = $this->conjugaison->n ?? 'N1';
                        $verbe = $this->conjugaison->verbe ?? '';
                        $tense = $this->conjugaison->tense ?? 'présent';
                        $sem = $this->conjugaison->sem ?? 'SEM1';
                        $weekNum = (int) preg_replace('/\D+/', '', $sem);
                        $skillId = $gradeSkillMap[$n] ?? '';
                        $autoName = 'Le verbe ' . ($verbe ?: '[verbe]') . ' au ' . ($tense ?: '[tense]') . '.';
                        $description = trim((string)($this->conjugaison->name ?? ''));
                    @endphp

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="md:col-span-2">
                            <label class="block text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wide mb-1.5">Concept Name</label>
                            <input type="text" id="create-concept-name" value="{{ $autoName }}"
                                class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-gray-300 text-sm focus:border-primary-500 focus:ring-primary-500">
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wide mb-1.5">Description (lesson name)</label>
                            <input type="text" id="create-concept-description" value="{{ $description }}"
                                class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-gray-300 text-sm focus:border-primary-500 focus:ring-primary-500">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wide mb-1.5">
                                Skill ID <span class="text-gray-400 normal-case font-normal">(Grade {{ $n }})</span>
                            </label>
                            <input type="number" id="create-concept-skill-id" value="{{ $skillId }}"
                                placeholder="e.g. 2"
                                class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-gray-300 text-sm focus:border-primary-500 focus:ring-primary-500">
                            <p class="text-xs text-gray-400 mt-1">N1→11 · N2→10 · N3→12 · N4→2 · N5→13 · N6→14</p>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wide mb-1.5">
                                Week <span class="text-gray-400 normal-case font-normal">(from {{ $sem }})</span>
                            </label>
                            <input type="number" id="create-concept-week" value="{{ $weekNum }}" min="1" max="52"
                                class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-gray-300 text-sm focus:border-primary-500 focus:ring-primary-500">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wide mb-1.5">Unite ID</label>
                            <input type="number" id="create-concept-unite-id" value="0" placeholder="0"
                                class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-gray-300 text-sm focus:border-primary-500 focus:ring-primary-500">
                            <p class="text-xs text-gray-400 mt-1">TODO: map to Revizy unit IDs</p>
                        </div>
                    </div>

                    <button id="btn-create-concept"
                        class="px-5 py-2.5 bg-primary-600 text-white text-sm font-semibold rounded-lg hover:bg-primary-500 transition flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                        Create Concept in Revizy
                    </button>
                    <p id="create-concept-status" class="text-sm text-gray-500 dark:text-gray-400 hidden"></p>
                </div>
            </div>
        </div>

        {{-- Raw Data --}}
        @php
            $relatedRawDecoded = json_decode((string) ($this->conjugaison->related_raw_data ?? ''), true);
            $topExamples = is_array($relatedRawDecoded) && is_array($relatedRawDecoded['top_examples'] ?? null)
                ? array_values(array_filter(array_map(static fn ($row) => is_array($row) ? trim((string) ($row['sentence'] ?? '')) : '', $relatedRawDecoded['top_examples'])))
                : [];
        @endphp
        @if($this->conjugaison->raw_data || count($topExamples) > 0)
        <div x-data="{ showRaw: false }" class="bg-white rounded-xl shadow-sm border border-gray-200 dark:bg-gray-800 dark:border-gray-700">
            <button @click="showRaw = !showRaw" class="w-full flex items-center justify-between p-4 text-left">
                <span class="text-sm font-medium text-gray-700 dark:text-gray-300">
                    <x-heroicon-o-document-text class="w-4 h-4 inline mr-1" /> Extracted Raw Data
                </span>
                <x-heroicon-o-chevron-down class="w-4 h-4 text-gray-400 transition-transform" x-bind:class="showRaw && 'rotate-180'" />
            </button>
            <div x-show="showRaw" x-collapse class="px-4 pb-4">
                <div class="bg-gray-50 dark:bg-gray-900 rounded-lg p-3 max-h-56 overflow-y-auto text-sm space-y-1 font-mono">
                    @foreach(preg_split('/\r?\n/', $this->conjugaison->raw_data ?? '') as $line)
                        @if(trim($line) !== '')
                        <div class="text-gray-700 dark:text-gray-300 py-0.5">{{ $line }}</div>
                        @endif
                    @endforeach

                    @if(count($topExamples) > 0)
                        <div class="pt-2 mt-2 border-t border-gray-200 dark:border-gray-700 text-xs uppercase tracking-wide text-gray-500 dark:text-gray-400">
                            Extracted examples
                        </div>
                        @foreach($topExamples as $exampleLine)
                            <div class="text-gray-700 dark:text-gray-300 py-0.5">{{ $exampleLine }}</div>
                        @endforeach
                    @endif
                </div>
            </div>
        </div>
        @endif

        {{-- Questions JSON --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 dark:bg-gray-800 dark:border-gray-700 p-6">
            <h3 class="text-base font-semibold text-gray-900 dark:text-white mb-4">
                <x-heroicon-o-code-bracket class="w-5 h-5 inline mr-1" /> Questions JSON
            </h3>
            <textarea id="conj-json-input" rows="8"
                placeholder='Paste JSON array here... e.g. [{"name":"Q1","concept_id":"123","data":{"instruction":"...","body":"...","answers":[{"body":"...","is_correct":true}]}}]'
                class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-gray-300 font-mono text-xs focus:border-primary-500 focus:ring-primary-500 mb-4"></textarea>
            <div class="flex items-center gap-3">
                <button id="btn-parse-json"
                    class="inline-flex items-center px-4 py-2 bg-primary-600 text-white text-sm font-semibold rounded-lg hover:bg-primary-500 transition">
                    Parse &amp; Preview
                </button>
                <span id="parse-status" class="text-sm text-gray-500 dark:text-gray-400"></span>
            </div>
        </div>

        {{-- Preview --}}
        <div id="questions-preview" class="hidden space-y-4">
            <h3 class="text-base font-semibold text-gray-900 dark:text-white">
                Preview (<span id="q-count">0</span> questions)
            </h3>
            <div id="questions-grid" class="grid grid-cols-1 md:grid-cols-2 gap-5"></div>
        </div>
    </div>

    <script>
    document.addEventListener('DOMContentLoaded', function () {
        const conjugaisonId = {{ $this->conjugaison->id }};
        let currentConceptId = '{{ $this->conjugaison->concept_id ?? '' }}';
        let parsedQuestions = [];

        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || '';

        // ─── Concept: Verify existing ────────────────────────────────────────
        const btnVerify = document.getElementById('btn-verify-concept');
        const verifyResult = document.getElementById('verify-result');
        const btnLink = document.getElementById('btn-link-concept');
        let verifiedConceptId = null;

        if (btnVerify) {
            btnVerify.addEventListener('click', async function () {
                const id = document.getElementById('existing-concept-id').value.trim();
                if (!id) return;
                btnVerify.disabled = true;
                btnVerify.textContent = 'Verifying...';
                verifyResult.classList.remove('hidden');
                verifyResult.textContent = 'Looking up concept...';
                btnLink.classList.add('hidden');

                try {
                    const res = await fetch(`/api/proxy/concepts/${id}`, {
                        headers: { 'Accept': 'application/json' }
                    });
                    if (res.ok) {
                        const data = await res.json();
                        const concept = data.data || data;
                        
                        const skillName = concept.skill ? concept.skill.name : 'Unknown Skill';
                        const isConjugaison = skillName.toLowerCase().includes('conjugaison');
                        const uniteName = concept.unite ? `${concept.unite.index}- ${concept.unite.name}` : 'No Unit';
                        const week = concept.week || 'N/A';

                        const skillHtml = isConjugaison 
                            ? `<span class="text-sm font-medium text-gray-700 dark:text-gray-300">${skillName}</span>`
                            : `<span class="text-sm font-bold text-danger-600 dark:text-danger-400 flex items-center gap-1">
                                <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path></svg>
                                ${skillName}
                               </span>`;

                        verifyResult.innerHTML = `
                            <div class="flex flex-col gap-2">
                                <div class="font-bold text-gray-900 dark:text-white text-base">✓ ${concept.name}</div>
                                <div class="grid grid-cols-1 md:grid-cols-3 gap-3 mt-1">
                                    <div class="flex flex-col">
                                        <span class="text-[10px] uppercase font-bold text-gray-400 tracking-wider">Skill</span>
                                        ${skillHtml}
                                    </div>
                                    <div class="flex flex-col">
                                        <span class="text-[10px] uppercase font-bold text-gray-400 tracking-wider">Unite</span>
                                        <span class="text-sm font-medium text-gray-700 dark:text-gray-300">${uniteName}</span>
                                    </div>
                                    <div class="flex flex-col">
                                        <span class="text-[10px] uppercase font-bold text-gray-400 tracking-wider">Week</span>
                                        <span class="text-sm font-medium text-gray-700 dark:text-gray-300">${week}</span>
                                    </div>
                                </div>
                                ${!isConjugaison ? '<div class="text-[10px] text-danger-500 font-bold mt-1 uppercase tracking-tighter">⚠ Warning: This concept is not linked to a Conjugaison skill.</div>' : ''}
                            </div>
                        `;
                        verifyResult.classList.add('bg-success-50', 'text-success-700', 'border', 'border-success-200');
                        verifyResult.classList.remove('bg-danger-50', 'text-danger-700', 'bg-warning-50', 'text-warning-700', 'hidden');
                        verifiedConceptId = String(id);
                        btnLink.classList.remove('hidden');
                    } else {
                        const data = await res.json().catch(() => ({}));
                        const detail = data.detail || data.message || `Status ${res.status}`;
                        verifyResult.innerHTML = `⚠ Verification failed: ${detail} <br><small class="text-gray-400">Concept #${id} will be accepted as-is if you link it.</small>`;
                        verifyResult.classList.remove('bg-danger-50', 'text-danger-700');
                        verifyResult.classList.add('bg-warning-50', 'text-warning-700');
                        verifiedConceptId = String(id);
                        btnLink.classList.remove('hidden');
                    }
                } catch (e) {
                    verifyResult.textContent = 'Error verifying concept: ' + e.message;
                } finally {
                    btnVerify.disabled = false;
                    btnVerify.textContent = 'Verify';
                }
            });

            btnLink.addEventListener('click', async function () {
                if (!verifiedConceptId) return;
                btnLink.disabled = true;
                btnLink.textContent = 'Saving...';
                await saveConcept(verifiedConceptId, null, null);
                btnLink.disabled = false;
                btnLink.textContent = '✓ Link this concept';
            });
        }

        // ─── Concept: Create new ─────────────────────────────────────────────
        const btnCreate = document.getElementById('btn-create-concept');
        const createStatus = document.getElementById('create-concept-status');

        if (btnCreate) {
            btnCreate.addEventListener('click', async function () {
                const name     = document.getElementById('create-concept-name').value.trim();
                const desc     = document.getElementById('create-concept-description').value.trim();
                const skillId  = parseInt(document.getElementById('create-concept-skill-id').value);
                const uniteId  = parseInt(document.getElementById('create-concept-unite-id').value) || 0;
                const week     = parseInt(document.getElementById('create-concept-week').value);

                if (!name || !skillId) { alert('Name and Skill ID are required.'); return; }

                btnCreate.disabled = true;
                btnCreate.textContent = '⏳ Creating...';
                createStatus.classList.remove('hidden');
                createStatus.textContent = 'Sending to Revizy...';

                try {
                    const res = await fetch('/api/concepts', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': csrfToken },
                        body: JSON.stringify({ name, description: desc, skill_id: skillId, unite_id: uniteId, week, status: 'published', is_active: true })
                    });
                    const data = await res.json();

                    if (res.ok && data.concept_id) {
                        createStatus.textContent = `✓ Concept created! ID: ${data.concept_id}`;
                        createStatus.classList.add('text-success-600');
                        await saveConcept(data.concept_id, skillId, uniteId);
                    } else {
                        createStatus.textContent = '✗ Failed: ' + (data.detail || JSON.stringify(data));
                        createStatus.classList.add('text-danger-600');
                    }
                } catch (e) {
                    createStatus.textContent = '✗ Error: ' + e.message;
                } finally {
                    btnCreate.disabled = false;
                    btnCreate.textContent = 'Create Concept in Revizy';
                }
            });
        }

        // ─── Save concept_id to conjugaison record ───────────────────────────
        async function saveConcept(conceptId, skillId, uniteId) {
            try {
                const res = await fetch(`/api/conjugaison/${conjugaisonId}/concept`, {
                    method: 'PATCH',
                    headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': csrfToken },
                    body: JSON.stringify({ concept_id: String(conceptId), revizy_skill_id: skillId, revizy_unite_id: uniteId })
                });
                if (res.ok) {
                    currentConceptId = String(conceptId);
                    // Update badge
                    const noB = document.getElementById('no-concept-badge');
                    const hasB = document.getElementById('has-concept-badge');
                    const display = document.getElementById('concept-id-display');
                    if (noB) noB.classList.add('hidden');
                    if (hasB) { hasB.classList.remove('hidden'); }
                    if (display) display.textContent = conceptId;
                }
            } catch (e) {
                console.error('Failed to save concept_id', e);
            }
        }

        // ─── Questions JSON Parser ────────────────────────────────────────────
        const parseBtn  = document.getElementById('btn-parse-json');
        const parseStatus = document.getElementById('parse-status');
        const previewSection = document.getElementById('questions-preview');
        const questionsGrid  = document.getElementById('questions-grid');
        const qCount = document.getElementById('q-count');

        parseBtn.addEventListener('click', function () {
            const raw = document.getElementById('conj-json-input').value.trim();
            if (!raw) { alert('Please paste JSON first'); return; }

            try {
                let cleaned = raw.replace(/[\u201C\u201D]/g, '"').replace(/[\u2018\u2019]/g, "'");
                if (cleaned.includes('```')) {
                    cleaned = cleaned.replace(/```json\s*[\n\r]+/, '').replace(/```\s*[\n\r]+/, '').replace(/```\s*$/, '');
                }
                const first = cleaned.indexOf('['); const last = cleaned.lastIndexOf(']');
                if (first !== -1 && last !== -1) cleaned = cleaned.substring(first, last + 1);
                cleaned = cleaned.replace(/,\s*([\]}])/g, '$1');

                parsedQuestions = JSON.parse(cleaned);
                if (!Array.isArray(parsedQuestions)) { alert('JSON must be an array'); return; }

                qCount.textContent = parsedQuestions.length;
                questionsGrid.innerHTML = '';
                previewSection.classList.remove('hidden');
                parseStatus.textContent = `✓ ${parsedQuestions.length} questions parsed`;

                parsedQuestions.forEach((q, idx) => {
                    const data     = q.data || {};
                    const instruction = data.instruction || '';
                    const body     = data.body || '';
                    const answers  = data.answers || [];
                    const cid      = q.concept_id || currentConceptId;

                    let answersHtml = '';
                    answers.forEach(a => {
                        const isCorrect = a.is_correct;
                        const bg = isCorrect ? 'bg-success-50 border-success-300 dark:bg-success-900/20 dark:border-success-700' : 'bg-gray-50 border-gray-200 dark:bg-gray-700/50 dark:border-gray-600';
                        const icon = isCorrect ? '✓ ' : '';
                        answersHtml += `<div class="px-3 py-2 rounded-lg border text-sm font-medium ${bg}">${icon}${a.body || a.text || '—'}</div>`;
                    });

                    const card = document.createElement('div');
                    card.className = 'bg-white dark:bg-gray-800 rounded-xl border-2 border-gray-200 dark:border-gray-700 overflow-hidden shadow-sm flex flex-col';
                    card.dataset.index = idx;
                    card.innerHTML = `
                        <div class="p-4 bg-gray-50 dark:bg-gray-900/50 border-b border-gray-200 dark:border-gray-700">
                            <div class="flex justify-between items-start mb-2">
                                <span class="text-xs font-semibold text-gray-400 dark:text-gray-500">Q${idx+1} of ${parsedQuestions.length}</span>
                                <span class="text-xs text-gray-400">${cid ? 'Concept #'+cid : '⚠ No concept'}</span>
                            </div>
                            ${instruction ? `<div class="text-sm font-semibold text-gray-800 dark:text-gray-200" dir="rtl">${instruction}</div>` : ''}
                        </div>
                        <div class="p-4 flex-1">
                            ${body ? `<div class="text-center text-base font-medium text-gray-900 dark:text-white mb-4 py-2 px-3 bg-gray-50 dark:bg-gray-900/30 rounded-lg">${body}</div>` : ''}
                            <div class="grid grid-cols-2 gap-2">${answersHtml}</div>
                        </div>
                        <div class="p-4 bg-gray-50 dark:bg-gray-900/50 border-t border-gray-200 dark:border-gray-700 flex gap-3">
                            <button class="btn-publish flex-1 py-2 bg-success-600 text-white text-sm font-semibold rounded-lg hover:bg-success-500 transition" data-index="${idx}">✓ Publish</button>
                            <button class="btn-unaccept flex-1 py-2 bg-danger-500 text-white text-sm font-semibold rounded-lg hover:bg-danger-400 transition" data-index="${idx}">✕ Unaccept</button>
                        </div>
                    `;
                    questionsGrid.appendChild(card);
                });
            } catch (e) {
                alert('Invalid JSON: ' + e.message);
            }
        });

        // ─── Publish ─────────────────────────────────────────────────────────
        questionsGrid.addEventListener('click', function (e) {
            const btn = e.target.closest('.btn-publish');
            if (!btn || btn.disabled) return;

            const idx = parseInt(btn.dataset.index);
            const q = parsedQuestions[idx];
            if (!q) return;

            const cid = q.concept_id || currentConceptId;
            if (!cid) { alert('No concept linked yet. Please create or select a concept first.'); return; }

            btn.disabled = true;
            btn.textContent = '⏳ Publishing...';

            const dataToSend = JSON.parse(JSON.stringify(q.data || {}));
            delete dataToSend.type;

            fetch(`/api/questions/${idx}/publish`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': csrfToken },
                body: JSON.stringify({
                    local_question_id: idx,
                    concept_id: String(cid),
                    name: q.name || `Conjugaison Q${idx+1}`,
                    type: q.type || q.data?.type || 'universal',
                    status: 'published',
                    data: dataToSend
                })
            })
            .then(r => r.json().then(d => ({ ok: r.ok, data: d })))
            .then(({ ok, data }) => {
                const card = btn.closest('[data-index]');
                if (ok) {
                    btn.textContent = '✓ Published';
                    btn.classList.replace('bg-success-600', 'bg-gray-400');
                    btn.classList.replace('hover:bg-success-500', 'hover:bg-gray-400');
                    card.style.borderColor = '#86efac';
                    card.querySelector('.btn-unaccept').disabled = true;
                    card.querySelector('.btn-unaccept').className = 'btn-unaccept flex-1 py-2 bg-gray-300 text-white text-sm font-semibold rounded-lg cursor-not-allowed';
                } else {
                    btn.disabled = false;
                    btn.textContent = '✓ Publish';
                    alert('Failed: ' + (data.detail || 'Unknown error'));
                }
            })
            .catch(err => { btn.disabled = false; btn.textContent = '✓ Publish'; alert('Error: ' + err.message); });
        });

        // ─── Unaccept ─────────────────────────────────────────────────────────
        questionsGrid.addEventListener('click', function (e) {
            const btn = e.target.closest('.btn-unaccept');
            if (!btn || btn.disabled) return;

            const idx = parseInt(btn.dataset.index);
            const q = parsedQuestions[idx];
            if (!q || !confirm('Mark as unaccepted?')) return;

            const cid = q.concept_id || currentConceptId;
            btn.disabled = true;
            btn.textContent = '⏳...';

            fetch(`/api/questions/${idx}/unaccept`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': csrfToken },
                body: JSON.stringify({
                    local_question_id: idx,
                    concept_id: String(cid || '0'),
                    name: q.name || `Conjugaison Q${idx+1}`,
                    data: q.data || {}
                })
            })
            .then(r => r.json().then(d => ({ ok: r.ok, data: d })))
            .then(({ ok, data }) => {
                const card = btn.closest('[data-index]');
                if (ok) {
                    btn.textContent = '✕ Unaccepted';
                    btn.classList.replace('bg-danger-500', 'bg-gray-400');
                    card.style.borderColor = '#FCA5A5';
                    card.querySelector('.btn-publish').disabled = true;
                } else {
                    btn.disabled = false;
                    btn.textContent = '✕ Unaccept';
                    alert('Failed: ' + (data.detail || 'Unknown error'));
                }
            })
            .catch(err => { btn.disabled = false; btn.textContent = '✕ Unaccept'; alert('Error: ' + err.message); });
        });
    });
    </script>
</x-filament-panels::page>
