$(document).ready(function () {

    // ============================================
    // API Helpers
    // ============================================
    const API = {
        getStats: () => $.get('/stats'),
        getTree: () => $.get('/tree'),
        getFiles: () => $.get('/files'),
        sync: () => $.post('/sync'),
        inspect: () => $.post('/inspect'),
        getVocabulary: (params) => $.get('/vocabulary' + (params ? '?' + params : '')),
        extractVocabulary: () => $.post('/extract-vocabulary'),
        getAudios: () => $.get('/audios'),
        getVocabularyAssets: (params) => $.get('/vocabulary-assets' + (params ? '?' + params : '')),
        syncVocabularyAssets: () => $.post('/sync-assets'),
        uploadAssetImage: (id) => $.post(`/vocabulary-assets/${id}/upload-image`),
        uploadAssetAudio: (id) => $.post(`/vocabulary-assets/${id}/upload-audio`),
        uploadAssetWalidio: (id) => $.post(`/vocabulary-assets/${id}/upload-walidio`),
        generateAudioNext: () => $.post('/generate-audio-next'),
        checkCategory: (id) => $.get(`/proxy/flashcard-categories/${id}`),
        checkSkill: (id) => $.get(`/proxy/skills/${id}`),
        checkUnit: (id) => $.get(`/proxy/units/${id}`),
        createConcept: (id, payload) => $.ajax({
            url: `/vocabulary-assets/${id}/create-concept`,
            type: 'POST',
            contentType: 'application/json',
            data: JSON.stringify(payload)
        }),
        checkQuestionDuplicate: (payload) => $.ajax({
            url: '/questions/check-duplicates',
            type: 'POST',
            contentType: 'application/json',
            data: JSON.stringify(payload)
        }),
        getRoadmap: () => $.get('/api/roadmap'),
        getGrammaire: () => $.get('/api/grammaire'),
        getConjugaison: (params) => $.get('/api/conjugaison' + (params ? '?' + params : '')),
        createGenericConcept: (payload) => $.ajax({
            url: '/api/concepts',
            type: 'POST',
            contentType: 'application/json',
            data: JSON.stringify(payload)
        }),
        checkConcept: (id) => $.get(`/proxy/concepts/${id}`)
    };

    // ============================================
    // Logging
    // ============================================
    function log(message, type = 'info') {
        const timestamp = new Date().toLocaleTimeString();
        $('#activity-log').prepend(`<div class="log-item ${type}">[${timestamp}] ${message}</div>`);
    }

    // ============================================
    // Dashboard Stats
    // ============================================
    function loadStats() {
        API.getStats().done(function (data) {
            $('#stat-total').text(data.total_files);
            $('#stat-downloaded').text(data.downloaded_files);
            $('#stat-percent').text(data.completion_percentage.toFixed(1) + '%');
            $('#stat-size').text(data.total_size_gb.toFixed(2) + ' GB');
            $('#stat-corrupt').text(data.corrupt_files);
            if (data.corrupt_files > 0) {
                $('#stat-corrupt').addClass('text-error');
            } else {
                $('#stat-corrupt').removeClass('text-error');
            }
        });
    }

    // ============================================
    // Dashboard Actions
    // ============================================
    $('#btn-sync').click(function () {
        $(this).prop('disabled', true).html('<i class="fa-solid fa-spinner fa-spin"></i> Sync...');
        log('Synchronisation démarrée...', 'info');
        API.sync().done(function () {
            log('Synchronisation lancée en arrière-plan.', 'success');
        }).fail(function () {
            log('Erreur lors du lancement de la synchro.', 'error');
        }).always(function () {
            setTimeout(() => {
                $('#btn-sync').prop('disabled', false).html('<i class="fa-solid fa-sync"></i> Synchroniser');
            }, 2000);
        });
    });

    $('#btn-inspect').click(function () {
        log('Vérification d\'intégrité démarrée...', 'info');
        API.inspect().done(function () {
            log('Inspection lancée en arrière-plan.', 'success');
        });
    });

    // ============================================
    // File Tree (Browser View)
    // ============================================
    function loadTree() {
        $('#file-tree').empty().append('<div class="loading"><i class="fa-solid fa-spinner fa-spin"></i> Chargement...</div>');
        API.getTree().done(function (data) {
            $('#file-tree').empty();
            renderTree(data, $('#file-tree'));
        }).fail(function () {
            $('#file-tree').html('<div class="text-error">Erreur de chargement.</div>');
        });
    }

    function renderTree(nodes, container) {
        if (!nodes || nodes.length === 0) return;
        const ul = $('<div class="tree-node open"></div>');
        nodes.forEach(node => {
            const isFile = node.type === 'file';
            const iconClass = isFile ? 'fa-file-powerpoint' : 'fa-folder';
            const toggleIcon = isFile ? '' : '<i class="fa-solid fa-chevron-right tree-toggle"></i>';
            const corruptClass = node.is_corrupt ? 'corrupt' : '';
            const item = $(`
                <div class="tree-wrapper">
                    <div class="tree-item type-${node.type} ${corruptClass}">
                        ${toggleIcon}
                        <i class="fa-solid ${iconClass} item-icon"></i>
                        <span class="item-name">${node.name}</span>
                        ${isFile ? renderFileMeta(node) : ''}
                    </div>
                </div>
            `);
            if (node.children && node.children.length > 0) {
                const childrenContainer = $('<div class="tree-node"></div>');
                item.find('.tree-item').click(function () {
                    if (isFile) return;
                    $(this).find('.tree-toggle').toggleClass('rotated');
                    childrenContainer.slideToggle(150);
                });
                renderTree(node.children, childrenContainer);
                item.append(childrenContainer);
            }
            ul.append(item);
        });
        container.append(ul);
    }

    function renderFileMeta(node) {
        const sizeMB = (node.size / (1024 * 1024)).toFixed(2);
        const downloadedIcon = node.is_downloaded ? '<i class="fa-solid fa-check status-badge"></i>' : '';
        return `<div class="file-meta"><span>${sizeMB} MB</span>${downloadedIcon}</div>`;
    }

    // ============================================
    // Files Table (Fichiers View)
    // ============================================
    let currentFiles = [];
    let currentSort = { column: 'id', order: 'asc' };

    function loadFilesTable() {
        $('#files-table-body').empty();
        $('#table-loading').show();
        API.getFiles().done(function (data) {
            $('#table-loading').hide();
            currentFiles = data;
            populateFilters(data);
            applyFilters();
        }).fail(function () {
            $('#table-loading').html('<div class="text-error">Erreur de chargement.</div>');
        });
    }

    function populateFilters(files) {
        const grades = [...new Set(files.map(f => f.grade))].sort();
        const subjects = [...new Set(files.map(f => f.subject))].sort();
        const periods = [...new Set(files.map(f => f.period))].sort();
        const weeks = [...new Set(files.map(f => f.week))].sort();

        const fill = (id, items) => {
            const select = $(id);
            const current = select.val();
            const placeholder = select.find('option:first');
            select.empty().append(placeholder);
            items.forEach(item => select.append(`<option value="${item}">${item}</option>`));
            if (current) select.val(current);
        };

        fill('#filter-grade', grades);
        fill('#filter-subject', subjects);
        fill('#filter-period', periods);
        fill('#filter-week', weeks);

        // After populating, load and apply stored filters
        loadFilterPrefs();
    }

    function applyFilters() {
        const grade = $('#filter-grade').val();
        const subject = $('#filter-subject').val();
        const period = $('#filter-period').val();
        const week = $('#filter-week').val();
        const status = $('#filter-status').val();
        const search = $('#table-search').val().toLowerCase();

        let filtered = currentFiles.filter(file => {
            const matchGrade = !grade || file.grade === grade;
            const matchSubject = !subject || file.subject === subject;
            const matchPeriod = !period || file.period === period;
            const matchWeek = !week || file.week === week;
            let matchStatus = true;
            if (status === 'downloaded') matchStatus = file.is_downloaded;
            if (status === 'missing') matchStatus = !file.is_downloaded;
            if (status === 'corrupt') matchStatus = file.is_corrupt;
            const matchSearch = !search || file.filename.toLowerCase().includes(search);
            return matchGrade && matchSubject && matchPeriod && matchWeek && matchStatus && matchSearch;
        });

        // Sort
        filtered.sort((a, b) => {
            let valA = a[currentSort.column];
            let valB = b[currentSort.column];
            if (currentSort.column === 'status') {
                valA = a.is_downloaded ? 1 : 0;
                valB = b.is_downloaded ? 1 : 0;
            }
            if (currentSort.column === 'size' || currentSort.column === 'id') {
                valA = parseInt(valA) || 0;
                valB = parseInt(valB) || 0;
            }
            if (valA < valB) return currentSort.order === 'asc' ? -1 : 1;
            if (valA > valB) return currentSort.order === 'asc' ? 1 : -1;
            return 0;
        });

        updateSortIcons();
        renderTable(filtered);
        saveFilterPrefs();
    }

    function updateSortIcons() {
        $('.data-table th').find('i').removeClass('fa-arrow-up fa-arrow-down').addClass('fa-sort');
        const activeHeader = $(`.data-table th[data-sort="${currentSort.column}"]`);
        activeHeader.find('i').removeClass('fa-sort').addClass(currentSort.order === 'asc' ? 'fa-arrow-up' : 'fa-arrow-down');
    }

    // Sort Click Handler
    $(document).on('click', '.data-table th[data-sort]', function () {
        const column = $(this).data('sort');
        if (currentSort.column === column) {
            currentSort.order = currentSort.order === 'asc' ? 'desc' : 'asc';
        } else {
            currentSort.column = column;
            currentSort.order = 'asc';
        }
        applyFilters();
    });

    // ============================================
    // Table Rendering (with Grouping)
    // ============================================
    function renderTable(files) {
        const tbody = $('#files-table-body');
        tbody.empty();
        const groupBy = $('#group-by').val();

        if (groupBy) {
            const groups = {};
            files.forEach(file => {
                const key = file[groupBy] || 'Autre';
                if (!groups[key]) groups[key] = [];
                groups[key].push(file);
            });
            Object.keys(groups).sort().forEach((groupKey, index) => {
                const groupId = `group-${index}`;
                const count = groups[groupKey].length;
                const groupHeader = `
                    <tr class="group-header" data-group="${groupId}">
                        <td colspan="11">
                            <i class="fa-solid fa-chevron-down group-toggle-icon"></i>
                            <span class="group-title">${groupKey}</span>
                            <span class="group-count">(${count} fichiers)</span>
                        </td>
                    </tr>
                `;
                tbody.append(groupHeader);
                renderRows(groups[groupKey], tbody, groupId);
            });
            // Collapsible Handler
            $('.group-header').off('click').on('click', function () {
                const groupId = $(this).data('group');
                $(this).find('.group-toggle-icon').toggleClass('rotated');
                $(`tr[data-parent="${groupId}"]`).toggleClass('hidden-row');
            });
        } else {
            renderRows(files, tbody);
        }
        applyColumnVisibility(tbody);
    }

    function renderRows(files, tbody, groupId = null) {
        files.forEach(file => {
            const sizeMB = (file.size / (1024 * 1024)).toFixed(2);
            const statusIcon = file.is_downloaded
                ? '<span class="status-badge"><i class="fa-solid fa-check"></i></span>'
                : '<span class="text-secondary"><i class="fa-regular fa-circle"></i></span>';
            const corruptIcon = file.is_corrupt
                ? '<span class="text-error" title="Corrompu"><i class="fa-solid fa-triangle-exclamation"></i></span> '
                : '';
            let vocabStatus = '<span class="text-secondary" title="Non extrait"><i class="fa-regular fa-circle"></i></span>';
            if (file.is_vocab_extracted) {
                if (file.vocab_count > 0) {
                    vocabStatus = `<span class="vocab-status-badge success" title="${file.vocab_count} items"><i class="fa-solid fa-book"></i> Extrait (${file.vocab_count})</span>`;
                } else {
                    vocabStatus = `<span class="vocab-status-badge warning" title="Aucun vocabulaire trouvé"><i class="fa-solid fa-ghost"></i> Néant</span>`;
                }
            }
            const parentAttr = groupId ? `data-parent="${groupId}"` : '';
            const row = `
                <tr ${parentAttr}>
                    <td class="col-id">${file.id}</td>
                    <td>${corruptIcon}<i class="fa-solid fa-file-powerpoint text-primary"></i> ${file.filename}</td>
                    <td><span class="vocab-badge">N${file.grade}</span></td>
                    <td><span class="vocab-badge">${file.subject}</span></td>
                    <td><span class="vocab-badge">P${file.period}</span></td>
                    <td><span class="vocab-badge">SEM${file.week}</span></td>
                    <td>${sizeMB} MB</td>
                    <td class="text-center">${statusIcon}</td>
                    <td class="text-center">${vocabStatus}</td>
                    <td><span class="vocab-badge">${file.session || '-'}</span></td>
                    <td class="text-center">
                        <button class="btn btn-sm btn-outline btn-primary btn-extract-file" data-id="${file.id}">
                            <i class="fa-solid fa-wand-magic-sparkles"></i> Extraire
                        </button>
                    </td>
                </tr>
            `;
            tbody.append(row);
        });
    }

    // ============================================
    // Column Selection & Persistence
    // ============================================
    $('#columnDropdown').click(function (e) {
        e.stopPropagation();
        $(this).parent('.dropdown').toggleClass('active');
    });
    $(document).click(() => $('.dropdown').removeClass('active'));
    $('.dropdown-menu').click(e => e.stopPropagation());

    $('.dropdown-menu input[type="checkbox"]').change(function () {
        const col = $(this).data('col');
        const isVisible = $(this).is(':checked');
        toggleColumn(col, isVisible);
        saveColumnPrefs();
    });

    function toggleColumn(col, isVisible) {
        $(`th[data-sort="${col}"]`).toggleClass('hidden-col', !isVisible);
        $(`#filter-${col}`).toggleClass('hidden-col', !isVisible);
        $(`#files-table-body tr:not(.group-header)`).each(function () {
            const index = $(`th[data-sort="${col}"]`).index();
            $(this).find('td').eq(index).toggleClass('hidden-col', !isVisible);
        });
    }

    function applyColumnVisibility(tbody) {
        $('.dropdown-menu input[type="checkbox"]').each(function () {
            const col = $(this).data('col');
            const isVisible = $(this).is(':checked');
            if (!isVisible) {
                const index = $(`th[data-sort="${col}"]`).index();
                tbody.find('tr:not(.group-header)').each(function () {
                    $(this).find('td').eq(index).addClass('hidden-col');
                });
                $(`th[data-sort="${col}"]`).addClass('hidden-col');
                $(`#filter-${col}`).addClass('hidden-col');
            }
        });
    }

    function saveColumnPrefs() {
        const prefs = {};
        $('.dropdown-menu input[type="checkbox"]').each(function () {
            prefs[$(this).data('col')] = $(this).is(':checked');
        });
        localStorage.setItem('raiida_column_prefs', JSON.stringify(prefs));
    }

    function loadColumnPrefs() {
        const prefs = JSON.parse(localStorage.getItem('raiida_column_prefs'));
        if (prefs) {
            for (const [col, isVisible] of Object.entries(prefs)) {
                const checkbox = $(`.dropdown-menu input[data-col="${col}"]`);
                if (checkbox.length) {
                    checkbox.prop('checked', isVisible);
                    toggleColumn(col, isVisible);
                }
            }
        }
    }

    // ============================================
    // Filter Persistence
    // ============================================
    function saveFilterPrefs() {
        const prefs = {
            files: {
                grade: $('#filter-grade').val(),
                subject: $('#filter-subject').val(),
                period: $('#filter-period').val(),
                week: $('#filter-week').val(),
                status: $('#filter-status').val(),
                search: $('#table-search').val(),
                groupBy: $('#group-by').val()
            },
            vocab: {
                grade: $('#vocab-filter-grade').val(),
                period: $('#vocab-filter-period').val(),
                week: $('#vocab-filter-week').val(),
                search: $('#vocab-search').val(),
                viewMode: currentVocabView
            },
            questions: {
                grade: $('#questions-grade').val(),
                period: $('#questions-period').val(),
                week: $('#questions-week').val()
            }
        };
        localStorage.setItem('raiida_filter_prefs', JSON.stringify(prefs));
    }

    function loadFilterPrefs() {
        const prefs = JSON.parse(localStorage.getItem('raiida_filter_prefs'));
        if (!prefs) return;

        if (prefs.files) {
            $('#filter-grade').val(prefs.files.grade);
            $('#filter-subject').val(prefs.files.subject);
            $('#filter-period').val(prefs.files.period);
            $('#filter-week').val(prefs.files.week);
            $('#filter-status').val(prefs.files.status);
            $('#table-search').val(prefs.files.search);
            $('#group-by').val(prefs.files.groupBy);
        }

        if (prefs.vocab) {
            $('#vocab-filter-grade').val(prefs.vocab.grade);
            $('#vocab-filter-period').val(prefs.vocab.period);
            $('#vocab-filter-week').val(prefs.vocab.week);
            $('#vocab-search').val(prefs.vocab.search);
            if (prefs.vocab.viewMode) {
                currentVocabView = prefs.vocab.viewMode;
            }
        }

        if (prefs.questions) {
            $('#questions-grade').val(prefs.questions.grade);
            $('#questions-period').val(prefs.questions.period);
            $('#questions-week').val(prefs.questions.week);
        }
    }

    // ============================================
    // Event Listeners for Filters
    // ============================================
    $('.filter-select').on('change', applyFilters);
    $('#table-search').on('keyup', applyFilters);
    $('#group-by').on('change', applyFilters);

    // ============================================
    // Routing
    // ============================================
    const initialView = window.RAIIDA_INITIAL_VIEW
        || $('#raiida-root').data('initial-view')
        || $('body').data('initial-view')
        || 'dashboard';

    function handleRouting() {
        const hash = window.location.hash.substring(1) || initialView;
        const activeView = $(`#${hash}-view`).length ? hash : initialView;

        $('.nav-item').removeClass('active');
        $('.view').removeClass('active');
        $(`.nav-item[data-view="${activeView}"]`).addClass('active');
        $(`#${activeView}-view`).addClass('active');
        if (activeView === 'files') loadFilesTable();
        else if (activeView === 'browser') loadTree();
        else if (activeView === 'vocab') loadVocab();
        else if (activeView === 'audios') loadAudios();
        else if (activeView === 'assets') loadAssets();
        else if (activeView === 'flashcards-uploader') loadFlashcardsUploader();
        else if (activeView === 'concept-creator') loadConceptCreator();
        else if (activeView === 'conjugaison') {
            loadConjugaison();
            // Auto-open questions modal if coming from Filament with query params
            const urlParams = new URLSearchParams(window.location.search);
            const conceptId = urlParams.get('concept_id');
            if (conceptId) {
                setTimeout(() => {
                    selectedConjForQuestions = { concept_id: conceptId };
                    $('#conj-q-concept-id').val(conceptId);
                    $('#conj-q-concept-name').val('').css('color', '');
                    $('#btn-check-conj-q-concept').removeClass('btn-success btn-error').addClass('btn-secondary').html('<i class="fa-solid fa-check-double"></i>');
                    isConjQConceptVerified = false;
                    $('#conj-questions-json').val('');
                    $('#conj-questions-preview-section').hide();
                    $('#conj-questions-preview-section .question-cards-grid').remove();
                    $('#conj-questions-count').text('0');
                    window.parsedConjQuestions = [];
                    $('#conj-questions-modal').show();
                    if (conceptId) $('#btn-check-conj-q-concept').trigger('click');
                    // Clean up URL params
                    window.history.replaceState({}, '', window.location.pathname + window.location.hash);
                }, 500);
            }
        }
        else if (activeView === 'roadmap') loadRoadmap();
        else if (activeView === 'grammaire') loadGrammaire();
        else if (activeView === 'questions-studio') {
            loadQuestions();
            const grade = $('#questions-grade').val();
            const period = $('#questions-period').val();
            const week = $('#questions-week').val();
            if (grade && period && week) {
                loadQuestionsVocab();
            }
        }

    }

    function loadRoadmap() {
        const tbody = $('#roadmap-body');
        tbody.html('<tr><td colspan="4" class="text-center text-secondary"><i class="fa-solid fa-spinner fa-spin"></i> Chargement...</td></tr>');

        API.getRoadmap().done(function (data) {
            tbody.empty();

            if (!data || data.length === 0) {
                tbody.html('<tr><td colspan="4" class="text-center text-secondary">Aucune donnée roadmap.</td></tr>');
                return;
            }

            data.forEach(item => {
                const row = `
                    <tr>
                        <td>
                            <span class="vocab-badge">${item.n || '-'}</span>
                            <small class="text-secondary" style="margin-left: 6px;">${item.p || '-'} • ${item.sem || '-'}</small>
                        </td>
                        <td>${item.vocab_count > 0 ? `${item.vocab_count} mots extraits` : '<span class="text-secondary">Aucun mot</span>'}</td>
                        <td>${item.conjugaison && item.conjugaison !== '-' ? item.conjugaison : '<span class="text-secondary">Non définie</span>'}</td>
                        <td>${item.grammaire && item.grammaire !== '-' ? item.grammaire : '<span class="text-secondary">Non définie</span>'}</td>
                    </tr>
                `;
                tbody.append(row);
            });
        }).fail(function () {
            tbody.html('<tr><td colspan="4" class="text-center text-error">Erreur de chargement de la roadmap.</td></tr>');
        });
    }

    function loadGrammaire() {
        const n = $('#filter-n').val();
        const search = ($('#search-grammar').val() || '').toLowerCase();
        const tbody = $('#grammaire-body');

        tbody.html('<tr><td colspan="4" class="text-center text-secondary"><i class="fa-solid fa-spinner fa-spin"></i> Chargement...</td></tr>');

        API.getGrammaire().done(function (data) {
            tbody.empty();

            const filtered = (data || []).filter(item => {
                if (n && item.n !== n) return false;
                if (search && !(item.objectif || '').toLowerCase().includes(search)) return false;
                return true;
            });

            if (filtered.length === 0) {
                tbody.html('<tr><td colspan="4" class="text-center text-secondary">Aucun résultat.</td></tr>');
                return;
            }

            filtered.forEach(item => {
                const row = `
                    <tr>
                        <td>
                            <span class="vocab-badge">${item.n || '-'}</span>
                            <small class="text-secondary" style="margin-left: 6px;">${item.p || '-'} • ${item.sem || '-'}</small>
                        </td>
                        <td><strong>${item.objectif || '-'}</strong></td>
                        <td class="text-secondary">${item.lesson_title || 'Grammaire'}</td>
                        <td class="text-secondary">${item.raw_data || '-'}</td>
                    </tr>
                `;
                tbody.append(row);
            });
        }).fail(function () {
            tbody.html('<tr><td colspan="4" class="text-center text-error">Erreur de chargement de la grammaire.</td></tr>');
        });
    }

    $('#roadmap-refresh-btn').on('click', loadRoadmap);
    $('#grammaire-refresh-btn').on('click', loadGrammaire);
    $('#filter-n, #search-grammar').on('change keyup', loadGrammaire);

    $('.nav-item').click(function (e) {
        const href = $(this).attr('href');
        if (href && href !== '#') {
            return;
        }

        e.preventDefault();
        const targetView = $(this).data('view');
        if (targetView) {
            window.location.hash = targetView;
        }
    });

    if (window.location.hash) {
        $(window).on('hashchange', handleRouting);
    }


    // ============================================
    // Assets Logic
    // ============================================
    // ============================================
    // Assets Logic
    // ============================================
    function loadAssets() {
        $('#assets-table-body').empty();
        $('#assets-loading').removeClass('hidden');
        $('#assets-pagination').empty(); // Clear pagination if any

        // Fetch all (high limit)
        const params = `offset=0&limit=10000`;

        API.getVocabularyAssets(params).done(function (data) {
            $('#assets-loading').addClass('hidden');
            renderAssetsTable(data.items);
            $('#assets-pagination').html(`<div class="pagination-info">Total: ${data.total} assets</div>`);
        }).fail(function () {
            $('#assets-loading').html('<div class="text-error">Erreur de chargement.</div>');
        });
    }

    function renderPagination(totalCount) {
        const container = $('#assets-pagination');
        container.empty();

        const totalPages = Math.ceil(totalCount / assetsLimit);

        if (totalPages <= 1) return;

        // Info
        const start = (currentAssetsPage - 1) * assetsLimit + 1;
        const end = Math.min(currentAssetsPage * assetsLimit, totalCount);
        container.append(`<div class="pagination-info">Affichage ${start}-${end} sur ${totalCount} assets (Page ${currentAssetsPage}/${totalPages})</div>`);

        // Prev Button
        const prevBtn = $(`<button class="pagination-btn" ${currentAssetsPage === 1 ? 'disabled' : ''}><i class="fa-solid fa-chevron-left"></i></button>`);
        prevBtn.click(() => {
            if (currentAssetsPage > 1) {
                currentAssetsPage--;
                loadAssets();
            }
        });
        container.append(prevBtn);

        // Page Numbers Logic (Smart Logic for many pages)
        // Always show 1, Last, and window around current
        const range = 2; // Neighbors

        for (let i = 1; i <= totalPages; i++) {
            // Show first, last, current, and neighbors
            if (i === 1 || i === totalPages || (i >= currentAssetsPage - range && i <= currentAssetsPage + range)) {
                const btn = $(`<button class="pagination-btn ${i === currentAssetsPage ? 'active' : ''}">${i}</button>`);
                btn.click(() => {
                    currentAssetsPage = i;
                    loadAssets();
                });
                container.append(btn);
            } else if (i === currentAssetsPage - range - 1 || i === currentAssetsPage + range + 1) {
                container.append('<span class="pagination-dots">...</span>');
            }
        }

        // Next Button
        const nextBtn = $(`<button class="pagination-btn" ${currentAssetsPage === totalPages ? 'disabled' : ''}><i class="fa-solid fa-chevron-right"></i></button>`);
        nextBtn.click(() => {
            if (currentAssetsPage < totalPages) {
                currentAssetsPage++;
                loadAssets();
            }
        });
        container.append(nextBtn);
    }

    $('#btn-refresh-assets').click(function () {
        currentAssetsPage = 1;
        loadAssets();
    });

    // Audio Playback Logic
    $(document).on('click', '.btn-audio-play', function () {
        const btn = $(this);
        const src = btn.data('src');

        // Stop any currently playing audio
        if (window.currentAudio) {
            window.currentAudio.pause();
            $('.btn-audio-play').removeClass('playing').find('i').removeClass('fa-pause').addClass('fa-volume-high');
            if (window.currentAudio.src.endsWith(src) || window.currentAudio.paused) {
                window.currentAudio = null;
                return; // Toggle behavior (stop if clicked again)
            }
        }

        const audio = new Audio(src);
        window.currentAudio = audio;

        btn.addClass('playing').find('i').removeClass('fa-volume-high').addClass('fa-pause');

        audio.play().catch(e => {
            console.error("Audio playback failed", e);
            btn.removeClass('playing').find('i').removeClass('fa-pause').addClass('fa-triangle-exclamation');
        });

        audio.onended = function () {
            btn.removeClass('playing').find('i').removeClass('fa-pause').addClass('fa-volume-high');
            window.currentAudio = null;
        };

        audio.onpause = function () {
            // Handle manual pause if needed, but mainly driven by toggle above
        };
    });

    $('#btn-sync-assets').click(function () {
        const btn = $(this);
        btn.prop('disabled', true).html('<i class="fa-solid fa-spinner fa-spin"></i> Syncing...');
        API.syncVocabularyAssets().done(function (res) {
            alert(`Sync Finished: Added ${res.added}, Updated ${res.updated}`);
            loadAssets();
        }).fail(function () {
            alert('Failed to sync assets');
        }).always(function () {
            btn.prop('disabled', false).html('<i class="fa-solid fa-database"></i> Sync Data');
        });
    });

    function renderAssetsTable(items) {
        const tbody = $('#assets-table-body');
        tbody.empty();
        if (!items || items.length === 0) {
            tbody.html('<tr><td colspan="9" class="text-center">Aucun asset trouvé.</td></tr>');
            return;
        }
        items.forEach(item => {
            const imageUrl = item.image ? (item.image.startsWith('vocab_assets') ? `/${item.image}` : `/taalim-data/${item.image}`) : '';
            const audioUrl = item.audio ? `/audios/${item.audio}` : '';

            const imageDisplay = imageUrl ? `<img src="${imageUrl}" class="vocab-thumbnail" loading="lazy" style="max-height: 50px;">` : '-';
            const audioDisplay = audioUrl ? `
                <button class="btn-audio-play" data-src="${audioUrl}" title="Play Audio">
                    <i class="fa-solid fa-volume-high"></i>
                </button>
            ` : '-';

            const row = `
                <tr>
                    <td>${item.id}</td>
                    <td><span class="vocab-badge">${item.grade || '-'}</span></td>
                    <td>${imageDisplay}</td>
                    <td>${audioDisplay}</td>
                    <td>${item.name || '-'}</td>
                    <td>${item.name_ar || '-'}</td>
                    <td><small>${item.revizy_image_file_id || '-'}</small></td>
                    <td><small>${item.revizy_audio_file_id || '-'}</small></td>
                    <td><small>${item.walidio_image_id || '-'}</small></td>
                    <td>
                        <div class="btn-group-actions">
                            <button class="btn-icon-round btn-action-upload-image" data-id="${item.id}" title="${item.revizy_image_file_id ? 'Already uploaded' : 'Upload Image'}" ${item.revizy_image_file_id ? 'disabled style="opacity: 0.5; cursor: not-allowed;"' : ''}>
                                <i class="fa-solid ${item.revizy_image_file_id ? 'fa-check text-success' : 'fa-image'}"></i>
                            </button>
                            <button class="btn-icon-round btn-action-upload-audio" data-id="${item.id}" title="${item.revizy_audio_file_id ? 'Already uploaded' : 'Upload Audio'}" ${item.revizy_audio_file_id ? 'disabled style="opacity: 0.5; cursor: not-allowed;"' : ''}>
                                <i class="fa-solid ${item.revizy_audio_file_id ? 'fa-check text-success' : 'fa-microphone'}"></i>
                            </button>
                            <button class="btn-icon-round btn-action-upload-walidio" data-id="${item.id}" title="${item.walidio_image_id ? 'Already uploaded' : 'Upload to Walidio'}" ${item.walidio_image_id ? 'disabled style="opacity: 0.5; cursor: not-allowed;"' : ''}>
                                <i class="fa-solid ${item.walidio_image_id ? 'fa-check text-secondary' : 'fa-cloud-arrow-up'}"></i>
                            </button>
                        </div>
                    </td>
                </tr>
            `;
            tbody.append(row);
        });
    }

    // ============================================
    // Upload Handlers
    // ============================================
    // ============================================
    // Upload Handlers (Sync to Revizy/Walidoi)
    // ============================================

    $(document).on('click', '.btn-action-upload-image', function () {
        const id = $(this).data('id');
        const btn = $(this);
        console.log("Sync Image Clicked for ID:", id);
        btn.prop('disabled', true).html('<i class="fa-solid fa-spinner fa-spin"></i>');

        API.uploadAssetImage(id).done(function (data) {
            console.log('Image synced to Revizy successfully!');
            // Update button in-place instead of reloading entire table
            btn.html('<i class="fa-solid fa-check text-success"></i>').attr('title', 'Already uploaded').css({ 'opacity': '0.5', 'cursor': 'not-allowed' });
            // Update the Revizy Image ID cell in the same row
            if (data && data.revizy_image_file_id) {
                const row = btn.closest('tr');
                row.find('td:nth-child(8) small').text(data.revizy_image_file_id);
            }
        }).fail(function (err) {
            console.error(err);
            // alert('Image sync failed: ' + (err.responseJSON ? err.responseJSON.detail : 'Unknown error'));
            btn.prop('disabled', false).html('<i class="fa-solid fa-image"></i>');
        });
    });

    $(document).on('click', '.btn-action-upload-audio', function () {
        const id = $(this).data('id');
        const btn = $(this);
        console.log("Sync Audio Clicked for ID:", id);
        btn.prop('disabled', true).html('<i class="fa-solid fa-spinner fa-spin"></i>');

        API.uploadAssetAudio(id).done(function (data) {
            console.log('Audio synced to Revizy successfully!');
            // Update button in-place instead of reloading entire table
            btn.html('<i class="fa-solid fa-check text-success"></i>').attr('title', 'Already uploaded').css({ 'opacity': '0.5', 'cursor': 'not-allowed' });
            // Update the Revizy Audio ID cell in the same row
            if (data && data.revizy_audio_file_id) {
                const row = btn.closest('tr');
                row.find('td:nth-child(9) small').text(data.revizy_audio_file_id);
            }
        }).fail(function (err) {
            console.error(err);
            // alert('Audio sync failed: ' + (err.responseJSON ? err.responseJSON.detail : 'Unknown error'));
            btn.prop('disabled', false).html('<i class="fa-solid fa-microphone"></i>');
        });
    });

    $(document).on('click', '.btn-action-upload-walidio', function () {
        const id = $(this).data('id');
        const btn = $(this);
        console.log("DEBUG: Sync Walidio Clicked for ID:", id);

        if (btn.prop('disabled')) {
            console.log("DEBUG: Button is disabled, ignoring click.");
            return;
        }

        btn.prop('disabled', true).html('<i class="fa-solid fa-spinner fa-spin"></i>');

        API.uploadAssetWalidio(id).done(function (data) {
            console.log('Synced to Walidio successfully!', data);
            // Update button in-place instead of reloading entire table
            btn.html('<i class="fa-solid fa-check text-secondary"></i>').attr('title', 'Already uploaded').css({ 'opacity': '0.5', 'cursor': 'not-allowed' });
            // Update the Walidio Image ID cell in the same row
            if (data && data.walidio_image_id) {
                const row = btn.closest('tr');
                row.find('td:nth-child(10) small').text(data.walidio_image_id);
            }
        }).fail(function (err) {
            console.error("Walidio Sync Error:", err);
            // alert('Walidio sync failed: ' + (err.responseJSON ? err.responseJSON.detail : 'Unknown error'));
            console.log("Error details:", err.responseJSON);
            btn.prop('disabled', false).html('<i class="fa-solid fa-cloud-arrow-up"></i>');
        });
    });

    // ============================================
    // Auto-Sync Logic
    // ============================================
    let isAutoSyncRunning = false;
    let autoSyncStopRequested = false;

    function syncLog(msg) {
        console.log(msg);
        $('#auto-sync-log').text(msg);
    }

    $(document).on('click', '#btn-auto-sync', function () {
        console.log("Auto-Sync Button Clicked");
        if (isAutoSyncRunning) {
            // Stop requested
            console.log("Requesting Stop...");
            autoSyncStopRequested = true;
            $(this).html('<i class="fa-solid fa-spinner fa-spin"></i> Stopping...').prop('disabled', true);
        } else {
            // Start requested
            console.log("Requesting Start...");
            isAutoSyncRunning = true;
            autoSyncStopRequested = false;
            $(this).removeClass('btn-secondary').addClass('btn-danger').html('<i class="fa-solid fa-stop"></i> Stop Auto-Sync');
            startAutoSyncLoop();
        }
    });

    async function startAutoSyncLoop() {
        syncLog("Starting Auto-Sync Loop...");

        // Get all rows
        const rows = $('#assets-table-body tr').toArray();
        let processedCount = 0;

        for (const row of rows) {
            if (autoSyncStopRequested) break;

            const $row = $(row);
            const id = $row.find('.btn-action-upload-image').data('id');
            const btnImage = $row.find('.btn-action-upload-image');
            const btnAudio = $row.find('.btn-action-upload-audio');
            const btnWalidio = $row.find('.btn-action-upload-walidio');

            // Scroll to row
            row.scrollIntoView({ behavior: 'smooth', block: 'center' });

            // Check Grade (skip N4)
            const gradeText = $row.find('td:nth-child(3) .vocab-badge').text().trim();
            if (gradeText === 'N4') {
                syncLog(`[${id}] Skipping N4`);
                continue;
            }

            // 1. Upload Image?
            if (!btnImage.prop('disabled')) {
                syncLog(`[${id}] Uploading Image...`);
                await processAutoUpload(btnImage, () => API.uploadAssetImage(id));
                if (autoSyncStopRequested) break;
            }

            // 2. Upload Audio?
            if (!btnAudio.prop('disabled')) {
                syncLog(`[${id}] Uploading Audio...`);
                await processAutoUpload(btnAudio, () => API.uploadAssetAudio(id));
                if (autoSyncStopRequested) break;
            }

            // 3. Upload Walidio?
            // Only if image is uploaded (disabled means uploaded or uploading, but we check if we just did it)
            // Ideally we check the data attributes or the button state.
            // If btnWalidio is NOT disabled, we try.
            if (!btnWalidio.prop('disabled')) {
                // Double check prereq: Image must be uploaded (btnImage disabled)
                if (btnImage.prop('disabled') || btnImage.find('.fa-check').length > 0) {
                    syncLog(`[${id}] Uploading Walidio...`);
                    await processAutoUpload(btnWalidio, () => API.uploadAssetWalidio(id));
                    if (autoSyncStopRequested) break;
                } else {
                    syncLog(`[${id}] Skipping Walidio (no image)`);
                }
            }

            processedCount++;
        }

        isAutoSyncRunning = false;
        $('#btn-auto-sync').removeClass('btn-danger').addClass('btn-secondary').html('<i class="fa-solid fa-robot"></i> Start Auto-Sync').prop('disabled', false);

        if (autoSyncStopRequested) {
            syncLog(`Stopped. Processed ${processedCount} rows.`);
        } else {
            syncLog(`Done! Processed ${processedCount} rows.`);
        }
        autoSyncStopRequested = false;
    }

    async function processAutoUpload(btn, apiCall) {
        if (btn.prop('disabled')) return; // Check again

        btn.prop('disabled', true).html('<i class="fa-solid fa-spinner fa-spin"></i>');
        try {
            await apiCall();
            // Success - disabled state is kept, icon updated by loadAssets usually? 
            // Wait, loadAssets() REFRESHES the whole table, which destroys our 'rows' reference!
            // CRITICAL: We cannot call loadAssets() inside the loop or we lose our place.
            // We must update the UI manually here to reflect success, OR we just let the button state persist as "disabled/success" 
            // and blindly continue.
            // BUT: app.js api calls usually call loadAssets() on success. 
            // We need to modify the API calls or the handlers to NOT call loadAssets() if we are in auto mode?
            // Actually, the click handlers call `loadAssets()`. 
            // Here we are calling `API.upload...` directly. The API function just returns a promise.
            // So we are safe from table refresh unless we call it.

            // Manually mark success
            btn.html('<i class="fa-solid fa-check text-secondary"></i>');
        } catch (err) {
            console.error("Auto-Sync Error:", err);
            // Re-enable on failure
            btn.prop('disabled', false).html('<i class="fa-solid fa-exclamation-triangle"></i>');
        }
    }

    function randomCooldown() {
        const min = 6000;
        const max = 12000;
        const delay = Math.floor(Math.random() * (max - min + 1)) + min;
        console.log(`Cooldown: ${delay / 1000}s`);
        syncLog(`Cooldown ${Math.round(delay / 1000)}s...`);
        return new Promise(resolve => setTimeout(resolve, delay));
    }



    // ============================================
    // Vocabulary Logic
    // ============================================
    let currentVocab = [];
    let currentVocabView = 'table'; // 'grid' or 'table'

    function loadVocab() {
        if (currentVocabView === 'grid') {
            $('#vocab-grid').empty().append('<div class="loading"><i class="fa-solid fa-spinner fa-spin"></i> Chargement...</div>');
        } else {
            $('#vocab-table-body').empty().append('<tr><td colspan="7" class="text-center">Chargement...</td></tr>');
        }

        // Build params from filters
        let params = new URLSearchParams();
        const grade = $('#vocab-filter-grade').val();
        const period = $('#vocab-filter-period').val();
        const week = $('#vocab-filter-week').val();

        if (grade) params.append('grade', 'N' + grade);
        if (period) params.append('period', 'P' + period);
        if (week) params.append('week', 'SEM' + week);
        params.append('limit', '2000'); // Load more data

        API.getVocabulary(params.toString()).done(function (data) {
            currentVocab = data;
            renderVocabView();
        }).fail(function () {
            const errorMsg = '<div class="text-error">Erreur de chargement du vocabulaire.</div>';
            if (currentVocabView === 'grid') $('#vocab-grid').html(errorMsg);
            else $('#vocab-table-body').html(`<tr><td colspan="7">${errorMsg}</td></tr>`);
        });
    }

    function renderVocabView() {
        if (currentVocabView === 'grid') {
            $('#vocab-grid').removeClass('hidden');
            $('#vocab-table-container').addClass('hidden');
            $('#btn-view-grid').addClass('active');
            $('#btn-view-table').removeClass('active');
            renderVocabGrid(currentVocab);
        } else {
            $('#vocab-grid').addClass('hidden');
            $('#vocab-table-container').removeClass('hidden');
            $('#btn-view-grid').removeClass('active');
            $('#btn-view-table').addClass('active');
            renderVocabTable(currentVocab);
        }
        saveFilterPrefs();
    }

    function renderVocabGrid(items) {
        const grid = $('#vocab-grid');
        grid.empty();

        if (!items || items.length === 0) {
            grid.html('<div class="loading">Aucun vocabulaire trouvé.</div>');
            return;
        }

        const searchTerm = $('#vocab-search').val().toLowerCase();

        items.forEach(item => {
            // Client-side search filter
            if (searchTerm && !item.word.toLowerCase().includes(searchTerm)) return;

            const imageUrl = item.image_path.startsWith('vocab_assets') ? `/${item.image_path}` : `/taalim-data/${item.image_path}`;
            const card = `
                <div class="vocab-card">
                    <div class="vocab-image-container">
                        <img src="${imageUrl}" alt="${item.word}" class="vocab-image" loading="lazy" onerror="this.src='data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHZpZXdCb3g9IjAgMCAyNCAyNCIgZmlsbD0ibm9uZSIgc3Ryb2tlPSIjY2NjIiBzdHJva2Utd2lkdGg9IjIiPjxyZWN0IHg9IjMiIHk9IjMiIHdpZHRoPSIxOCIgaGVpZ2h0PSIxOCIgcng9IjIiIHJ5PSIyIi8+PGNpcmNsZSBjeD0iOC41IiBjeT0iOC41IiByPSIxLjUiLz48cG9seWxpbmUgcG9pbnRzPSIyMSAxNSAxNiAxMCA1IDIxIi8+PC9zdmc+'">
                    </div>
                    <div class="vocab-content">
                        <div class="vocab-word">${item.word}</div>
                        ${item.ar_translation ? `<div class="vocab-translation" style="color: var(--text-muted); margin-top: 4px; font-size: 1.1em;" dir="rtl">${item.ar_translation}</div>` : ''}
                        <div class="vocab-meta">
                            <span class="vocab-badge">${item.grade}</span>
                            <span class="vocab-badge">${item.period}</span>
                            <span class="vocab-badge">${item.week}</span>
                        </div>
                    </div>
                </div>
             `;
            grid.append(card);
        });
    }

    function renderVocabTable(items) {
        const tbody = $('#vocab-table-body');
        tbody.empty();

        if (!items || items.length === 0) {
            tbody.html('<tr><td colspan="7" class="text-center">Aucun vocabulaire trouvé.</td></tr>');
            return;
        }

        const searchTerm = $('#vocab-search').val().toLowerCase();

        items.forEach(item => {
            // Client-side search filter
            if (searchTerm && !item.word.toLowerCase().includes(searchTerm)) return;

            const imageUrl = item.image_path.startsWith('vocab_assets') ? `/${item.image_path}` : `/taalim-data/${item.image_path}`;
            const translation = item.ar_translation || '';
            const row = `
                <tr>
                    <td>${item.id}</td>
                    <td><img src="${imageUrl}" class="vocab-thumbnail" loading="lazy" onerror="this.src='data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHZpZXdCb3g9IjAgMCAyNCAyNCIgZmlsbD0ibm9uZSIgc3Ryb2tlPSIjY2NjIiBzdHJva2Utd2lkdGg9IjIiPjxyZWN0IHg9IjMiIHk9IjMiIHdpZHRoPSIxOCIgaGVpZ2h0PSIxOCIgcng9IjIiIHJ5PSIyIi8+PGNpcmNsZSBjeD0iOC41IiBjeT0iOC41IiByPSIxLjUiLz48cG9seWxpbmUgcG9pbnRzPSIyMSAxNSAxNiAxMCA1IDIxIi8+PC9zdmc+'"></td>
                    <td><strong>${item.word}</strong></td>
                    <td class="text-right" dir="rtl">${translation}</td>
                    <td><span class="vocab-badge">${item.grade}</span></td>
                    <td><span class="vocab-badge">${item.period}</span></td>
                    <td><span class="vocab-badge">${item.week}</span></td>
                    <td><small class="text-secondary">${item.lesson_id}</small></td>
                </tr>
             `;
            tbody.append(row);
        });
    }

    // Vocab Filters
    $('#vocab-filter-grade, #vocab-filter-period, #vocab-filter-week').change(function () {
        loadVocab(); // Reload from server
    });

    $('#vocab-search').on('keyup', function () {
        renderVocabView(); // Filter client-side
    });

    // View Toggles
    $('#btn-view-grid').click(() => {
        currentVocabView = 'grid';
        renderVocabView();
    });
    $('#btn-view-table').click(() => {
        currentVocabView = 'table';
        renderVocabView();
    });

    // Extract Button
    $('#btn-extract-vocab').click(function () {
        if (!confirm("Lancer l'extraction du vocabulaire ? Cela peut prendre du temps.")) return;

        $(this).prop('disabled', true).html('<i class="fa-solid fa-spinner fa-spin"></i> Extraction...');
        log("Extraction du vocabulaire lancée...", "info");

        API.extractVocabulary().done(function () {
            log("Extraction terminée (ou lancée en arrière-plan).", "success");
            setTimeout(loadVocab, 2000);
        });
    });

    // Individual file extraction
    $(document).on('click', '.btn-extract-file', function () {
        const btn = $(this);
        const fileId = btn.data('id');
        const originalHtml = btn.html();

        btn.prop('disabled', true).html('<i class="fa-solid fa-spinner fa-spin"></i>...');

        $.ajax({
            url: `/extract-vocabulary/${fileId}`,
            method: 'POST',
            success: function (response) {
                if (response.error) {
                    log(response.error, 'error');
                    btn.prop('disabled', false).html(originalHtml);
                } else {
                    const count = response.count || 0;
                    log(`Extraction terminée : ${count} items trouvés pour ${response.lesson}`, 'success');

                    // Update the row status visually
                    const row = btn.closest('tr');
                    const vocabCell = row.find('td:nth-child(9)');

                    if (count > 0) {
                        vocabCell.html(`<span class="vocab-status-badge success"><i class="fa-solid fa-book"></i> Extrait (${count})</span>`);
                        btn.html('<i class="fa-solid fa-check"></i> Fait').removeClass('btn-primary').addClass('btn-success');
                    } else {
                        vocabCell.html('<span class="vocab-status-badge warning"><i class="fa-solid fa-ghost"></i> Néant</span>');
                        btn.html('<i class="fa-solid fa-check"></i> Fait').removeClass('btn-primary').addClass('btn-warning');
                    }
                }
            },
            error: function () {
                log('Erreur lors de l\'extraction', 'error');
                btn.prop('disabled', false).html(originalHtml);
            }
        });
    });

    // ============================================
    // Audio Logic
    // ============================================
    function loadAudios(autoPlayFile = null) {
        $('#audios-table-body').empty();
        $('#audios-loading').removeClass('hidden');
        API.getAudios().done(function (data) {
            $('#audios-loading').addClass('hidden');
            renderAudiosTable(data, autoPlayFile);
        }).fail(function () {
            $('#audios-loading').html('<div class="text-error">Erreur de chargement.</div>');
        });
    }

    function renderAudiosTable(items, autoPlayFile = null) {
        const tbody = $('#audios-table-body');
        tbody.empty();
        if (!items || items.length === 0) {
            tbody.html('<tr><td colspan="6" class="text-center">Aucun audio généré.</td></tr>');
            return;
        }
        items.forEach(item => {
            const imageUrl = item.image.startsWith('vocab_assets') ? `/${item.image}` : `/taalim-data/${item.image}`;

            // Audio player
            const audioUrl = `/audios/${item.audio_file}`;

            const row = `
                <tr>
                    <td>${item.id}</td>
                    <td><img src="${imageUrl}" class="vocab-thumbnail" loading="lazy"></td>
                    <td><strong>${item.word}</strong></td>
                    <td>
                        <audio controls src="${audioUrl}" preload="none" data-filename="${item.audio_file}"></audio>
                    </td>
                    <td>${new Date(item.created_at).toLocaleString()}</td>
                </tr>
            `;
            tbody.append(row);
        });

        if (autoPlayFile) {
            const audio = $(`audio[data-filename="${autoPlayFile}"]`)[0];
            if (audio) {
                console.log('Autoplaying:', autoPlayFile);
                audio.play().catch(e => console.warn('Autoplay failed:', e));
            }
        }
    }

    let isGeneratingAudio = false;

    window.API = API; // Expose for debugging

    $('#btn-toggle-audio').click(function () {
        if (!isGeneratingAudio) {
            // Start
            console.log('Starting Audio Generation');
            isGeneratingAudio = true;
            updateAudioUI('running');
            processNextAudio();
        } else {
            // Stop
            console.log('Stopping Audio Generation');
            isGeneratingAudio = false;
            updateAudioUI('stopped');
            $('#audio-status-text').text('Arrêté.');
        }
    });

    function updateAudioUI(state) {
        const btn = $('#btn-toggle-audio');
        const spinner = $('#audio-progress .fa-spinner');

        if (state === 'running') {
            btn.removeClass('btn-success').addClass('btn-danger').html('<i class="fa-solid fa-stop"></i> Stop');
            $('#audio-progress').removeClass('hidden');
            spinner.removeClass('hidden');
        } else {
            // Idle or Stopped
            btn.removeClass('btn-danger').addClass('btn-success').html('<i class="fa-solid fa-play"></i> Start');

            if (state === 'idle') {
                $('#audio-progress').addClass('hidden');
            } else {
                // Stopped: Keep box visible, hide spinner
                spinner.addClass('hidden');
            }
        }
    }

    function processNextAudio() {
        if (!isGeneratingAudio) return;

        API.generateAudioNext().done(function (response) {
            if (!isGeneratingAudio) return;

            if (response.status === 'success') {
                // Reload list to show new item
                loadAudios(response.file);

                // Countdown Logic
                let waitTime = response.wait || 10;
                const updateStatus = () => {
                    $('#audio-status-text').html(`Généré: <strong>${response.item}</strong> (Attente <strong>${waitTime}s</strong>...)`);
                };

                updateStatus();

                const timer = setInterval(() => {
                    if (!isGeneratingAudio) {
                        clearInterval(timer);
                        return;
                    }
                    waitTime--;
                    if (waitTime <= 0) {
                        clearInterval(timer);
                        processNextAudio();
                    } else {
                        updateStatus();
                    }
                }, 1000);

            } else if (response.status === 'retry') {
                // Retry Logic
                let waitTime = response.wait || 5;
                const updateStatus = () => {
                    $('#audio-status-text').html(`Erreur temporaire. Réessai dans <strong>${waitTime}s</strong>...`);
                };

                updateStatus();

                const timer = setInterval(() => {
                    if (!isGeneratingAudio) {
                        clearInterval(timer);
                        return;
                    }
                    waitTime--;
                    if (waitTime <= 0) {
                        clearInterval(timer);
                        processNextAudio();
                    } else {
                        updateStatus();
                    }
                }, 1000);

            } else if (response.status === 'complete') {
                isGeneratingAudio = false;
                updateAudioUI('idle');
                alert("Génération terminée ! Tous les items ont un audio.");
                $('#audio-status-text').text('Terminé.');

            } else if (response.status === 'error') {
                isGeneratingAudio = false;
                updateAudioUI('stopped');
                alert(`Erreur: ${response.message}`);
                $('#audio-status-text').text('Erreur.');
            }
        }).fail(function () {
            isGeneratingAudio = false;
            updateAudioUI('stopped');
            alert("Erreur de communication avec le serveur.");
            $('#audio-status-text').text('Erreur réseau.');
        });
    }



    // ============================================
    // Flashcards Uploader Logic
    // ============================================
    let isCategoryVerified = false;

    function loadFlashcardsUploader() {
        // Only load if all 3 filters are selected
        const grade = $('#uploader-grade').val();
        const period = $('#uploader-period').val();
        const week = $('#uploader-week').val();

        if (!grade || !period || !week) {
            $('#uploader-table-body').html(`<tr><td colspan="8" class="text-center text-secondary"><i class="fa-solid fa-arrow-up"></i> Please select N, P, and SEM to view flashcards.</td></tr>`);
            updateUploadButtonState();
            return;
        }

        $('#uploader-table-body').empty();
        $('#uploader-loading').removeClass('hidden');

        // Reuse getVocabularyAssets but with filters
        const params = `grade=N${grade}&period=P${period}&week=SEM${week}&limit=1000`;

        API.getVocabularyAssets(params).done(function (data) {
            $('#uploader-loading').addClass('hidden');
            renderFlashcardsTable(data.items);
            updateUploadButtonState();
        }).fail(function () {
            $('#uploader-loading').html('<div class="text-error">Error loading flashcards.</div>');
        });
    }

    function renderFlashcardsTable(items) {
        const tbody = $('#uploader-table-body');
        tbody.empty();

        if (!items || items.length === 0) {
            tbody.html('<tr><td colspan="9" class="text-center">No flashcards found for this selection.</td></tr>');
            return;
        }

        items.forEach(item => {
            const imageUrl = item.image ? (item.image.startsWith('vocab_assets') ? `/${item.image}` : `/taalim-data/${item.image}`) : '';
            const audioUrl = item.audio ? `/audios/${item.audio}` : '';

            const imageDisplay = imageUrl ? `<img src="${imageUrl}" class="vocab-thumbnail" loading="lazy" style="max-height: 50px;">` : '-';
            const audioDisplay = audioUrl ? `
                <button class="btn-audio-play" data-src="${audioUrl}" title="Play Audio">
                    <i class="fa-solid fa-volume-high"></i>
                </button>
            ` : '-';

            // Check status
            const isUploaded = !!item.flashcard_id;
            const statusHtml = isUploaded
                ? `<span class="text-success" title="ID: ${item.flashcard_id}"><i class="fa-solid fa-check"></i> Uploaded</span>`
                : '<span class="text-secondary"><i class="fa-regular fa-circle"></i> Pending</span>';

            const rowClass = isUploaded ? 'uploaded' : '';
            const rowStyle = isUploaded ? 'background-color: #f0fff4;' : '';

            const row = `
                <tr class="${rowClass}" style="${rowStyle}" data-id="${item.id}" data-uploaded="${isUploaded}">
                    <td>${item.id}</td>
                    <td>${imageDisplay}</td>
                    <td>${audioDisplay}</td>
                    <td>${item.name || '-'}</td>
                    <td>${item.name_ar || '-'}</td>
                    <td><span class="vocab-badge">${item.grade || '-'}</span></td>
                    <td><span class="vocab-badge">${item.period || '-'}</span></td>
                    <td><span class="vocab-badge">${item.week || '-'}</span></td>
                    <td class="col-status">${statusHtml}</td>
                </tr>
            `;
            tbody.append(row);
        });
    }

    function updateUploadButtonState() {
        if (isCategoryVerified) {
            $('#btn-uploader-upload').prop('disabled', false);
        } else {
            $('#btn-uploader-upload').prop('disabled', true);
        }
    }

    // Flashcards Uploader Event Listeners
    $('#uploader-grade, #uploader-period, #uploader-week').change(function () {
        loadFlashcardsUploader();
    });

    // Check Category Logic
    $('#btn-check-category').click(function () {
        const id = $('#uploader-category-id').val();
        const btn = $(this);
        const nameInput = $('#uploader-category-name');

        // Remove old stats if any
        $('.category-stats').remove();

        if (!id) {
            alert("Please enter a Category ID");
            return;
        }

        btn.prop('disabled', true).html('<i class="fa-solid fa-spinner fa-spin"></i>');
        nameInput.val('Checking...');

        API.checkCategory(id).done(function (data) {
            console.log("Category Info:", data);

            // Handle different response structures
            let name = "Category Found";
            let childrenCount = 0;
            let flashcardsCount = 0;

            const category = data.data || data;

            if (category.name) name = category.name;
            else if (category.title) name = category.title;

            if (category.children_count !== undefined) childrenCount = category.children_count;
            if (category.flashcards_count !== undefined) flashcardsCount = category.flashcards_count;

            nameInput.val(name);

            // Validation: Must not have children
            if (childrenCount > 0) {
                nameInput.css('background-color', '#fff3e0'); // Warning Orange
                btn.html('<i class="fa-solid fa-triangle-exclamation"></i>').removeClass('btn-secondary').addClass('btn-warning');
                isCategoryVerified = false; // Invalid for upload

                // Show error
                const errorHtml = `<div class="category-stats text-error" style="width: 100%; margin-top: 5px; font-size: 0.9em;">
                    <i class="fa-solid fa-ban"></i> Error: This is a parent category (${childrenCount} subcategories). Cannot add flashcards.
                 </div>`;
                nameInput.parent().parent().append(errorHtml);

            } else {
                nameInput.css('background-color', '#e8f5e9'); // Light green
                btn.html('<i class="fa-solid fa-check"></i>').removeClass('btn-secondary').addClass('btn-success');
                isCategoryVerified = true;

                // Show stats
                let parentInfo = '';
                if (category.parent && category.parent.name) {
                    parentInfo = ` | <i class="fa-solid fa-turn-up"></i> Parent: <strong>${category.parent.name}</strong>`;
                } else if (category.parent_id) {
                    parentInfo = ` | <i class="fa-solid fa-turn-up"></i> Parent ID: ${category.parent_id}`;
                }

                const statsHtml = `<div class="category-stats text-secondary" style="width: 100%; margin-top: 5px; font-size: 0.9em;">
                    <i class="fa-solid fa-layer-group"></i> Subcategories: ${childrenCount} | <i class="fa-solid fa-clone"></i> Existing Flashcards: ${flashcardsCount}${parentInfo}
                 </div>`;
                nameInput.parent().parent().append(statsHtml);
            }

            updateUploadButtonState();
            setTimeout(() => { btn.prop('disabled', false); }, 500);

        }).fail(function (err) {
            console.error("Check Failed:", err);
            let errorMsg = 'Error: Not Found';
            if (err.responseJSON && err.responseJSON.detail) errorMsg = err.responseJSON.detail;

            nameInput.val(errorMsg);
            nameInput.css('background-color', '#ffebee'); // Light red
            btn.html('<i class="fa-solid fa-xmark"></i>').removeClass('btn-success').addClass('btn-danger');

            isCategoryVerified = false;
            updateUploadButtonState();

            setTimeout(() => {
                btn.html('<i class="fa-solid fa-check"></i>').removeClass('btn-danger').addClass('btn-secondary').prop('disabled', false);
            }, 2000);
        });
    });

    // Reset verification on ID change
    $('#uploader-category-id').on('input', function () {
        if (isCategoryVerified || $('#uploader-category-name').val() !== '') {
            isCategoryVerified = false;
            $('#uploader-category-name').val('').css('background-color', '#f5f5f5');
            $('#btn-check-category').html('<i class="fa-solid fa-check"></i>').removeClass('btn-success').removeClass('btn-warning').addClass('btn-secondary');
            $('.category-stats').remove();
            updateUploadButtonState();
        }
    });

    let isUploading = false;

    $('#btn-uploader-upload').click(async function () {
        const btn = $(this);

        // STOP Logic
        if (isUploading) {
            isUploading = false;
            btn.html('<i class="fa-solid fa-spinner fa-spin"></i> Stopping...');
            return;
        }

        // START Logic
        const id = $('#uploader-category-id').val();
        if (!isCategoryVerified || !id) return;

        const originalHtml = '<i class="fa-solid fa-cloud-arrow-up"></i> Upload';

        // Confirm
        const count = $('#uploader-table-body tr').not('.uploaded').length;
        if (!confirm(`Are you sure you want to upload all pending items to Category ID ${id}?`)) return;

        isUploading = true;
        btn.removeClass('btn-primary').addClass('btn-danger').html('<i class="fa-solid fa-stop"></i> Stop');
        btn.prop('disabled', false); // Ensure it's clickable to stop

        // Iterate table rows
        const rows = $('#uploader-table-body tr').toArray();
        let successCount = 0;
        let failCount = 0;
        let processedCount = 0;

        for (const row of rows) {
            if (!isUploading) break; // Stop loop if flag is false

            const $row = $(row);
            const assetId = $row.data('id');
            const isUploaded = $row.data('uploaded');

            if (isUploaded) continue;

            processedCount++;
            const statusCell = $row.find('.col-status');
            statusCell.html('<i class="fa-solid fa-spinner fa-spin text-primary"></i>');

            try {
                const response = await $.ajax({
                    url: `/vocabulary-assets/${assetId}/upload-flashcard?flashcard_category_id=${id}`,
                    method: 'POST'
                });

                if (response.success) {
                    successCount++;
                    statusCell.html(`<i class="fa-solid fa-check text-success" title="ID: ${response.flashcard_id}"></i> Created`);
                    $row.addClass('uploaded').data('uploaded', true).css('background-color', '#f0fff4');
                } else {
                    failCount++;
                    statusCell.html('<i class="fa-solid fa-xmark text-danger"></i> Failed');
                }

            } catch (err) {
                console.error(err);
                failCount++;
                statusCell.html('<i class="fa-solid fa-xmark text-danger"></i> Error');
            }

            // Small delay
            await new Promise(r => setTimeout(r, 200));
        }

        isUploading = false;
        btn.removeClass('btn-danger').addClass('btn-primary').html(originalHtml);

        if (processedCount > 0) {
            alert(`Process Finished.\nSuccess: ${successCount}\nFailed: ${failCount}`);
        } else {
            alert("No items were processed.");
        }
    });



    // ============================================
    // Concept Creator Logic
    // ============================================
    let isSkillVerified = false;
    let isUnitVerified = false;
    let isCreatingConcept = false;
    let isConjugaisonSkillVerified = false;
    let isConjugaisonUnitVerified = false;

    function loadConceptCreator() {
        const grade = $('#concept-grade').val();
        const period = $('#concept-period').val();
        const week = $('#concept-week').val();

        if (!grade || !period || !week) {
            $('#concept-table-body').html(`<tr><td colspan="9" class="text-center text-secondary"><i class="fa-solid fa-arrow-up"></i> Please select N, P, and SEM to view flashcards.</td></tr>`);
            updateConceptCreateButtonState();
            return;
        }

        $('#concept-table-body').empty();
        $('#concept-loading').removeClass('hidden');

        const params = `grade=N${grade}&period=P${period}&week=SEM${week}&limit=1000`;

        API.getVocabularyAssets(params).done(function (data) {
            $('#concept-loading').addClass('hidden');
            renderConceptsTable(data.items);
            updateConceptCreateButtonState();
        }).fail(function () {
            $('#concept-loading').html('<div class="text-error">Error loading vocabulary.</div>');
        });
    }

    function renderConceptsTable(items) {
        const tbody = $('#concept-table-body');
        tbody.empty();

        if (!items || items.length === 0) {
            tbody.html('<tr><td colspan="9" class="text-center">No vocabulary found for this selection.</td></tr>');
            return;
        }

        items.forEach(item => {
            const imageUrl = item.image ? (item.image.startsWith('vocab_assets') ? `/${item.image}` : `/taalim-data/${item.image}`) : '';
            const audioUrl = item.audio ? `/audios/${item.audio}` : '';

            const imageDisplay = imageUrl ? `<img src="${imageUrl}" class="vocab-thumbnail" loading="lazy" style="max-height: 50px;">` : '-';
            const audioDisplay = audioUrl ? `
                <button class="btn-audio-play" data-src="${audioUrl}" title="Play Audio">
                    <i class="fa-solid fa-volume-high"></i>
                </button>
            ` : '-';

            // Check status
            const isCreated = !!item.concept_id;
            const statusHtml = isCreated
                ? `<span class="text-success" title="ID: ${item.concept_id}"><i class="fa-solid fa-check"></i> Created</span>`
                : '<span class="text-secondary"><i class="fa-regular fa-circle"></i> Pending</span>';

            const rowClass = isCreated ? 'uploaded' : '';
            const rowStyle = isCreated ? 'background-color: #f0fff4;' : '';

            const row = `
                <tr class="${rowClass}" style="${rowStyle}" data-id="${item.id}" data-name="${item.name}" data-created="${isCreated}">
                    <td>${item.id}</td>
                    <td>${imageDisplay}</td>
                    <td>${audioDisplay}</td>
                    <td>${item.name || '-'}</td>
                    <td>${item.name_ar || '-'}</td>
                    <td><span class="vocab-badge">${item.grade || '-'}</span></td>
                    <td><span class="vocab-badge">${item.period || '-'}</span></td>
                    <td><span class="vocab-badge">${item.week || '-'}</span></td>
                    <td class="col-status">${statusHtml}</td>
                </tr>
            `;
            tbody.append(row);
        });
    }

    function updateConceptCreateButtonState() {
        if (isSkillVerified && isUnitVerified) {
            $('#btn-concept-create').prop('disabled', false);
        } else {
            $('#btn-concept-create').prop('disabled', true);
        }
    }

    // Concept Creator Event Listeners
    $('#concept-grade, #concept-period, #concept-week').change(function () {
        loadConceptCreator();
    });

    // Check Skill Logic
    $('#btn-check-skill').click(function () {
        const id = $('#concept-skill-id').val();
        const btn = $(this);
        const nameInput = $('#concept-skill-name');

        if (!id) {
            alert("Please enter a Skill ID");
            return;
        }

        btn.prop('disabled', true).html('<i class="fa-solid fa-spinner fa-spin"></i>');
        nameInput.val('Checking...');

        API.checkSkill(id).done(function (data) {
            console.log("Skill Info:", data);

            // Expected info: data.name (subject.grade.name)
            const skill = data.data || data;
            let info = skill.name || "Skill Found";
            if (skill.subject && skill.subject.name) {
                const gradeName = (skill.subject.grade && skill.subject.grade.name) ? skill.subject.grade.name : '';
                info = `${skill.subject.name} ${gradeName ? '(' + gradeName + ')' : ''} - ${skill.name}`;
            }

            nameInput.val(info).css('background-color', '#e8f5e9');
            btn.html('<i class="fa-solid fa-check"></i>').removeClass('btn-secondary').addClass('btn-success');
            isSkillVerified = true;
            updateConceptCreateButtonState();
            setTimeout(() => { btn.prop('disabled', false); }, 500);
        }).fail(function (err) {
            console.error("Skill Check Failed:", err);
            nameInput.val('Error: Not Found').css('background-color', '#ffebee');
            btn.html('<i class="fa-solid fa-xmark"></i>').removeClass('btn-success').addClass('btn-danger');
            isSkillVerified = false;
            updateConceptCreateButtonState();
            setTimeout(() => {
                btn.html('<i class="fa-solid fa-check"></i>').removeClass('btn-danger').addClass('btn-secondary').prop('disabled', false);
            }, 2000);
        });
    });

    // Check Unit Logic
    $('#btn-check-unit').click(function () {
        const id = $('#concept-unit-id').val();
        const btn = $(this);
        const nameInput = $('#concept-unit-name');

        if (!id) {
            alert("Please enter a Unit ID");
            return;
        }

        btn.prop('disabled', true).html('<i class="fa-solid fa-spinner fa-spin"></i>');
        nameInput.val('Checking...');

        API.checkUnit(id).done(function (data) {
            console.log("Unit Info:", data);

            const unit = data.data || data;
            let info = unit.name || "Unit Found";
            if (unit.subject && unit.subject.name) {
                const gradeName = (unit.subject.grade && unit.subject.grade.name) ? unit.subject.grade.name : '';
                const unitIndex = unit.index !== undefined ? `Periode ${unit.index}: ` : '';
                info = `${unit.subject.name} ${gradeName ? '(' + gradeName + ')' : ''} - ${unitIndex}${unit.name}`;
            }

            nameInput.val(info).css('background-color', '#e8f5e9');
            btn.html('<i class="fa-solid fa-check"></i>').removeClass('btn-secondary').addClass('btn-success');
            isUnitVerified = true;
            updateConceptCreateButtonState();
            setTimeout(() => { btn.prop('disabled', false); }, 500);
        }).fail(function (err) {
            console.error("Unit Check Failed:", err);
            nameInput.val('Error: Not Found').css('background-color', '#ffebee');
            btn.html('<i class="fa-solid fa-xmark"></i>').removeClass('btn-success').addClass('btn-danger');
            isUnitVerified = false;
            updateConceptCreateButtonState();
            setTimeout(() => {
                btn.html('<i class="fa-solid fa-check"></i>').removeClass('btn-danger').addClass('btn-secondary').prop('disabled', false);
            }, 2000);
        });
    });

    // Reset verification on ID change
    $('#concept-skill-id').on('input', function () {
        if (isSkillVerified) {
            isSkillVerified = false;
            $('#concept-skill-name').val('').css('background-color', '#f5f5f5');
            $('#btn-check-skill').html('<i class="fa-solid fa-check"></i>').removeClass('btn-success').addClass('btn-secondary');
            updateConceptCreateButtonState();
        }
    });
    $('#concept-unit-id').on('input', function () {
        if (isUnitVerified) {
            isUnitVerified = false;
            $('#concept-unit-name').val('').css('background-color', '#f5f5f5');
            $('#btn-check-unit').html('<i class="fa-solid fa-check"></i>').removeClass('btn-success').addClass('btn-secondary');
            updateConceptCreateButtonState();
        }
    });

    // ============================================
    // Conjugaison Concept Creation Logic
    // ============================================
    let isConjSkillVerified = false;
    let isConjUnitVerified = false;

    $('#btn-check-conj-skill').click(function () {
        const id = $('#create-conj-skill-id').val();
        const btn = $(this);
        const nameInput = $('#create-conj-skill-name');

        if (!id) {
            alert("Please enter a Skill ID");
            return;
        }

        btn.prop('disabled', true).html('<i class="fa-solid fa-spinner fa-spin"></i>');
        nameInput.val('Checking...');

        API.checkSkill(id).done(function (data) {
            const skill = data.data || data;
            let info = skill.name || "Skill Found";
            if (skill.subject && skill.subject.name) {
                const gradeName = (skill.subject.grade && skill.subject.grade.name) ? skill.subject.grade.name : '';
                info = `${skill.subject.name} ${gradeName ? '(' + gradeName + ')' : ''} - ${skill.name}`;
            }

            nameInput.val(info).css('background-color', '#e8f5e9');
            btn.html('<i class="fa-solid fa-check"></i>').removeClass('btn-secondary').addClass('btn-success');
            isConjSkillVerified = true;
            setTimeout(() => { btn.prop('disabled', false); }, 500);
        }).fail(function (err) {
            nameInput.val('Error: Not Found').css('background-color', '#ffebee');
            btn.html('<i class="fa-solid fa-xmark"></i>').removeClass('btn-success').addClass('btn-danger');
            isConjSkillVerified = false;
            setTimeout(() => {
                btn.html('<i class="fa-solid fa-check"></i>').removeClass('btn-danger').addClass('btn-secondary').prop('disabled', false);
            }, 2000);
        });
    });

    $('#btn-check-conj-unit').click(function () {
        const id = $('#create-conj-unit-id').val();
        const btn = $(this);
        const nameInput = $('#create-conj-unit-name');

        if (!id) {
            alert("Please enter a Unit ID");
            return;
        }

        btn.prop('disabled', true).html('<i class="fa-solid fa-spinner fa-spin"></i>');
        nameInput.val('Checking...');

        API.checkUnit(id).done(function (data) {
            const unit = data.data || data;
            let info = unit.name || "Unit Found";
            if (unit.subject && unit.subject.name) {
                const gradeName = (unit.subject.grade && unit.subject.grade.name) ? unit.subject.grade.name : '';
                const unitIndex = unit.index !== undefined ? `Periode ${unit.index}: ` : '';
                info = `${unit.subject.name} ${gradeName ? '(' + gradeName + ')' : ''} - ${unitIndex}${unit.name}`;
            }

            nameInput.val(info).css('background-color', '#e8f5e9');
            btn.html('<i class="fa-solid fa-check"></i>').removeClass('btn-secondary').addClass('btn-success');
            isConjUnitVerified = true;
            setTimeout(() => { btn.prop('disabled', false); }, 500);
        }).fail(function (err) {
            nameInput.val('Error: Not Found').css('background-color', '#ffebee');
            btn.html('<i class="fa-solid fa-xmark"></i>').removeClass('btn-success').addClass('btn-danger');
            isConjUnitVerified = false;
            setTimeout(() => {
                btn.html('<i class="fa-solid fa-check"></i>').removeClass('btn-danger').addClass('btn-secondary').prop('disabled', false);
            }, 2000);
        });
    });

    $('#create-conj-skill-id').on('input', function () {
        if (isConjSkillVerified) {
            isConjSkillVerified = false;
            $('#create-conj-skill-name').val('').css('background-color', '#fff');
            $('#btn-check-conj-skill').html('<i class="fa-solid fa-check"></i>').removeClass('btn-success').addClass('btn-secondary');
        }
    });

    $('#create-conj-unit-id').on('input', function () {
        if (isConjUnitVerified) {
            isConjUnitVerified = false;
            $('#create-conj-unit-name').val('').css('background-color', '#fff');
            $('#btn-check-conj-unit').html('<i class="fa-solid fa-check"></i>').removeClass('btn-success').addClass('btn-secondary');
        }
    });

    $('#btn-create-conj-concept').click(function () {
        const skillId = $('#create-conj-skill-id').val();
        const unitId = $('#create-conj-unit-id').val();
        const name = $('#create-conj-name').val();
        const week = $('#create-conj-week').val();
        const btn = $(this);

        if (!skillId || !unitId || !name) {
            alert("Please fill in all fields (Skill ID, Unit ID, and Concept Name)");
            return;
        }

        if (!isConjSkillVerified || !isConjUnitVerified) {
            alert("Please verify Skill and Unit IDs first");
            return;
        }

        btn.prop('disabled', true).html('<i class="fa-solid fa-spinner fa-spin"></i> Creating...');

        const payload = {
            skill_id: parseInt(skillId),
            unite_id: parseInt(unitId),
            name: name,
            description: `Concept de conjugaison: ${name}`,
            week: parseInt(week),
            status: "published",
            is_active: true
        };

        API.createGenericConcept(payload).done(function (data) {
            alert("Successfully created concept ID: " + data.concept_id);
            btn.html('<i class="fa-solid fa-check"></i> Created').addClass('btn-success');
            setTimeout(() => {
                btn.prop('disabled', false).html('<i class="fa-solid fa-magic"></i> Create Concept').removeClass('btn-success');
            }, 3000);
        }).fail(function (err) {
            const errorMsg = (err.responseJSON && err.responseJSON.detail) ? err.responseJSON.detail : 'Unknown error';
            alert("Failed to create concept: " + errorMsg);
            btn.prop('disabled', false).html('<i class="fa-solid fa-magic"></i> Create Concept');
        });
    });

    // Conjugaison Table Loading
    function loadConjugaison() {
        const grade = $('#conj-grade-filter').val();
        const search = $('#conj-search').val();
        let params = [];
        if (grade) params.push(`n=${grade}`);

        $('#conjugaison-table-body').html('<tr><td colspan="7" class="text-center text-secondary"><i class="fa-solid fa-spinner fa-spin"></i> Loading...</td></tr>');

        API.getConjugaison(params.join('&')).done(function (data) {
            let filtered = data;
            if (search) {
                const s = search.toLowerCase();
                filtered = data.filter(c =>
                    (c.verbe && c.verbe.toLowerCase().includes(s)) ||
                    (c.tense && c.tense.toLowerCase().includes(s)) ||
                    (c.raw_data && c.raw_data.toLowerCase().includes(s))
                );
            }
            renderConjugaisonTable(filtered);
        });
    }

    function renderConjugaisonTable(items) {
        const tbody = $('#conjugaison-table-body');
        tbody.empty();

        if (items.length === 0) {
            tbody.append('<tr><td colspan="7" class="text-center text-secondary">No matching conjugaison items found.</td></tr>');
            return;
        }

        items.forEach(item => {
            const createQuestionsBtn = `<button class="btn btn-sm btn-success btn-create-conj-questions" data-concept-id="${item.concept_id || ''}" data-n="${item.n}" data-p="${item.p}" data-sem="${item.sem}" data-verbe="${item.verbe || ''}" title="Create Questions">
                     <i class="fa-solid fa-pen-to-square"></i> Questions
                   </button>`;

            const row = `
                <tr>
                    <td>${item.id}</td>
                    <td><strong>${item.verbe || '-'}</strong></td>
                    <td><span class="badge badge-secondary">${item.tense || '-'}</span></td>
                    <td><small>${item.n} | ${item.p} | ${item.sem}</small></td>
                    <td>${item.concept_id ? `<span class="text-success">#${item.concept_id}</span>` : '<span class="text-secondary">None</span>'}</td>
                    <td>0</td>
                    <td style="display: flex; gap: 6px;">
                        ${createQuestionsBtn}
                        <button class="btn btn-sm btn-secondary btn-copy-raw" data-raw='${JSON.stringify(item.raw_data)}' title="Copy Raw Data">
                            <i class="fa-solid fa-copy"></i>
                        </button>
                    </td>
                </tr>
            `;
            tbody.append(row);
        });
    }

    $('#conj-grade-filter, #conj-search').on('change input', loadConjugaison);

    // Initial load when view is activated
    $('.nav-item[data-view="conjugaison"], button[data-view="conjugaison"]').click(function () {
        loadConjugaison();
    });

    // ============================================
    // Conjugaison Questions Creator
    // ============================================
    let isConjQConceptVerified = false;
    let selectedConjForQuestions = null;

    // Open modal when "Create Questions" button is clicked on a conjugaison row
    $(document).on('click', '.btn-create-conj-questions', function () {
        const conceptId = $(this).data('concept-id');
        const n = $(this).data('n');
        const p = $(this).data('p');
        const sem = $(this).data('sem');
        const verbe = $(this).data('verbe');

        selectedConjForQuestions = { concept_id: conceptId, n, p, sem, verbe };

        // Auto-fill concept ID and clear previous state
        $('#conj-q-concept-id').val(conceptId);
        $('#conj-q-concept-name').val('').css('color', '');
        $('#btn-check-conj-q-concept').removeClass('btn-success btn-error').addClass('btn-secondary').html('<i class="fa-solid fa-check-double"></i>');
        isConjQConceptVerified = false;

        // Clear previous questions
        $('#conj-questions-json').val('');
        $('#conj-questions-preview-section').hide();
        $('#conj-questions-preview-section .question-cards-grid').remove();
        $('#conj-questions-count').text('0');
        window.parsedConjQuestions = [];

        // Show modal
        $('#conj-questions-modal').show();

        // Auto-verify the concept
        $('#btn-check-conj-q-concept').trigger('click');
    });

    // Close modal
    $('#btn-close-conj-questions').click(function () {
        $('#conj-questions-modal').hide();
        selectedConjForQuestions = null;
        window.parsedConjQuestions = [];
    });

    // Concept verification for conjugaison questions
    $('#btn-check-conj-q-concept').click(function () {
        const conceptId = $('#conj-q-concept-id').val();
        if (!conceptId) return;

        const btn = $(this);
        btn.prop('disabled', true).html('<i class="fa-solid fa-spinner fa-spin"></i>');

        API.checkConcept(conceptId)
            .done(function (data) {
                isConjQConceptVerified = true;
                const concept = data.data || data;
                const skillName = concept.skill ? concept.skill.name : 'N/A';
                const unitName = concept.unite ? concept.unite.name : 'N/A';

                $('#conj-q-concept-name').val(`${concept.name} | Skill: ${skillName} | Unit: ${unitName}`).css('color', 'var(--success)');
                btn.removeClass('btn-secondary').addClass('btn-success').html('<i class="fa-solid fa-check"></i>');
            })
            .fail(function () {
                isConjQConceptVerified = false;
                $('#conj-q-concept-name').val('Concept not found').css('color', 'var(--error)');
                btn.removeClass('btn-secondary').addClass('btn-error').html('<i class="fa-solid fa-xmark"></i>');
            })
            .always(function () {
                btn.prop('disabled', false);
            });
    });

    $('#conj-q-concept-id').on('input', function () {
        isConjQConceptVerified = false;
        $('#conj-q-concept-name').val('').css('color', '');
        $('#btn-check-conj-q-concept').removeClass('btn-success btn-error').addClass('btn-secondary').html('<i class="fa-solid fa-check-double"></i>');
    });

    // Parse Conjugaison JSON — full preview cards with Publish/Unaccept
    $('#btn-parse-conj-questions').click(async function () {
        const jsonText = $('#conj-questions-json').val().trim();
        if (!jsonText) {
            alert('Please paste JSON data');
            return;
        }

        try {
            let cleanedJson = jsonText;
            // Replace smart quotes
            cleanedJson = cleanedJson.replace(/[\u201C\u201D]/g, '"').replace(/[\u2018\u2019]/g, "'");
            // Remove markdown code blocks
            if (cleanedJson.includes('```')) {
                cleanedJson = cleanedJson.replace(/```json\s*[\n\r]+/, '')
                    .replace(/```\s*[\n\r]+/, '')
                    .replace(/```\s*$/, '');
            }
            // Extract array
            const firstBracket = cleanedJson.indexOf('[');
            const lastBracket = cleanedJson.lastIndexOf(']');
            if (firstBracket !== -1 && lastBracket !== -1) {
                cleanedJson = cleanedJson.substring(firstBracket, lastBracket + 1);
            }
            // Remove trailing commas
            cleanedJson = cleanedJson.replace(/,\s*([\]}])/g, '$1');

            const questions = JSON.parse(cleanedJson);
            if (!Array.isArray(questions)) {
                alert('JSON must be an array of questions');
                return;
            }

            // Store parsed questions globally for publish/unaccept
            window.parsedConjQuestions = questions;

            // Show loading
            const section = $('#conj-questions-preview-section');
            section.find('.question-cards-grid').remove();
            section.append('<div id="conj-loading-indicator" class="loading" style="padding: 20px;"><i class="fa-solid fa-spinner fa-spin"></i> Processing...</div>');
            section.show();

            // No media lookups needed for conjugaison (text-only questions)
            const mediaCache = {};

            // Check for duplicates
            const questionsCheckPayload = questions.map((q, idx) => ({
                index: idx,
                concept_id: String(q.concept_id || $('#conj-q-concept-id').val() || ''),
                data: q.data || {}
            }));

            let duplicateStatus = {};
            try {
                const checkResponse = await $.ajax({
                    url: '/questions/check-duplicates',
                    type: 'POST',
                    contentType: 'application/json',
                    data: JSON.stringify({ questions: questionsCheckPayload })
                });
                if (checkResponse.duplicates) {
                    checkResponse.duplicates.forEach(dup => {
                        duplicateStatus[dup.index] = dup;
                    });
                }
            } catch (e) {
                console.error('Failed to check duplicates:', e);
            }

            // Remove loading
            $('#conj-loading-indicator').remove();

            // Create grid container
            const gridContainer = $(`<div class="question-cards-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(350px, 1fr)); gap: 20px; padding: 20px 0; align-items: stretch;"></div>`);
            section.append(gridContainer);

            // Render question cards
            questions.forEach((q, index) => {
                const cardHtml = renderQuestionPreviewCard(q, index, questions.length, mediaCache, duplicateStatus[index]);
                gridContainer.append(cardHtml);
            });

            $('#conj-questions-count').text(questions.length);

        } catch (e) {
            alert('Invalid JSON format: ' + e.message);
        }
    });

    // Publish conjugaison question
    $(document).on('click', '#conj-questions-preview-section .btn-validate-publish-question', function () {
        const index = $(this).data('index');
        const question = window.parsedConjQuestions[index];
        if (!question) { alert('Question not found'); return; }

        const btn = $(this);
        const card = btn.closest('[data-index]');
        btn.prop('disabled', true).html('<i class="fa-solid fa-spinner fa-spin"></i> Publishing...');

        const conceptId = question.concept_id || $('#conj-q-concept-id').val();
        const dataToSend = JSON.parse(JSON.stringify(question.data || {}));
        if (dataToSend.type) delete dataToSend.type;

        $.ajax({
            url: `/questions/${index}/publish`,
            type: 'POST',
            contentType: 'application/json',
            data: JSON.stringify({
                local_question_id: index,
                concept_id: String(conceptId),
                name: question.name || `Conjugaison Q${index + 1}`,
                type: question.type || question.data?.type || 'universal',
                status: 'published',
                data: dataToSend
            })
        }).done(function (response) {
            btn.html('<i class="fa-solid fa-check"></i> Published').removeClass('btn-success').addClass('btn-secondary');
            card.css({ 'border-color': '#86efac', 'background': '#f0fdf4' });
            card.find('.btn-unaccept-question').prop('disabled', true);
            alert(`Question "${question.name || 'Q' + (index + 1)}" published!\nRevizy ID: ${response.revizy_question_id}`);
        }).fail(function (xhr) {
            btn.prop('disabled', false).html('<i class="fa-solid fa-check-circle"></i> Publish');
            alert(`Failed to publish: ${xhr.responseJSON?.detail || 'Unknown error'}`);
        });
    });

    // Unaccept conjugaison question
    $(document).on('click', '#conj-questions-preview-section .btn-unaccept-question', function () {
        const index = $(this).data('index');
        const question = window.parsedConjQuestions[index];
        if (!question) { alert('Question not found'); return; }

        if (!confirm('Mark this question as unaccepted?')) return;

        const btn = $(this);
        const card = btn.closest('[data-index]');
        btn.prop('disabled', true).html('<i class="fa-solid fa-spinner fa-spin"></i> Processing...');

        const conceptId = question.concept_id || $('#conj-q-concept-id').val();

        $.ajax({
            url: `/questions/${index}/unaccept`,
            type: 'POST',
            contentType: 'application/json',
            data: JSON.stringify({
                local_question_id: index,
                concept_id: String(conceptId),
                name: question.name || `Conjugaison Q${index + 1}`,
                data: question.data || {}
            })
        }).done(function () {
            btn.html('<i class="fa-solid fa-xmark"></i> Unaccepted').css('background', '#9CA3AF');
            card.css({ 'border-color': '#FCA5A5', 'background': '#FEF2F2' });
            card.find('.btn-validate-publish-question').prop('disabled', true);
            alert(`Question marked as unaccepted.`);
        }).fail(function (xhr) {
            btn.prop('disabled', false).html('<i class="fa-solid fa-xmark"></i> Unaccept');
            alert(`Failed: ${xhr.responseJSON?.detail || 'Unknown error'}`);
        });
    });

    $(document).on('click', '.btn-copy-raw', function () {
        const raw = $(this).data('raw');
        const textToCopy = typeof raw === 'string' ? raw : JSON.stringify(raw);
        navigator.clipboard.writeText(textToCopy).then(() => {
            alert("Raw data copied to clipboard!");
        });
    });

    // Create Concept Loop
    $('#btn-concept-create').click(async function () {
        const btn = $(this);

        if (isCreatingConcept) {
            isCreatingConcept = false;
            btn.html('<i class="fa-solid fa-spinner fa-spin"></i> Stopping...');
            return;
        }

        const skillId = $('#concept-skill-id').val();
        const unitId = $('#concept-unit-id').val();
        const weekVal = $('#concept-week').val();

        if (!isSkillVerified || !isUnitVerified || !skillId || !unitId) return;

        if (!confirm(`Are you sure you want to create concepts for the items in the table below?`)) return;

        isCreatingConcept = true;
        btn.removeClass('btn-primary').addClass('btn-danger').html('<i class="fa-solid fa-stop"></i> Stop');

        const rows = $('#concept-table-body tr').toArray();
        let count = 0;

        for (const row of rows) {
            if (!isCreatingConcept) break;

            const $row = $(row);
            const assetId = $row.data('id');
            const assetName = $row.data('name');
            const isCreated = $row.data('created');

            if (!assetId || isCreated) continue;

            const statusCell = $row.find('.col-status');
            statusCell.html('<i class="fa-solid fa-spinner fa-spin text-primary"></i> Creating...');

            // Construct Payload
            const payload = {
                "skill_id": parseInt(skillId),
                "unite_id": parseInt(unitId),
                "name": assetName,
                "description": `Le mot de vocabulaire ${assetName}`,
                "week": parseInt(weekVal),
                "status": "published",
                "is_active": true
            };

            try {
                const response = await API.createConcept(assetId, payload);
                console.log(`[Concept Creator] Asset ID ${assetId} Created:`, response);
                statusCell.html(`<span class="text-success" title="ID: ${response.concept_id}"><i class="fa-solid fa-check"></i> Created</span>`);
                $row.css('background-color', '#f0fff4');
                $row.data('created', true); // Prevent re-creation in the same session
                count++;
            } catch (err) {
                console.error(`[Concept Creator] Asset ID ${assetId} Failed:`, err);
                const errorMsg = (err.responseJSON && err.responseJSON.detail) ? err.responseJSON.detail : 'Failed';
                statusCell.html(`<span class="text-error" title="${errorMsg}"><i class="fa-solid fa-triangle-exclamation"></i> Error</span>`);
                $row.css('background-color', '#fff5f5');
            }

            await new Promise(r => setTimeout(r, 200)); // Small delay to avoid hammering
        }

        isCreatingConcept = false;
        btn.removeClass('btn-danger').addClass('btn-primary').html('<i class="fa-solid fa-plus"></i> Create');
        alert(`Process finished. ${count} item(s) created.`);
    });
    loadColumnPrefs();
    loadFilterPrefs(); // Load vocab prefs before loading views
    setInterval(loadStats, 5000);
    loadStats();
    // handleRouting moved to end of file to avoid TDZ issues

    // ============================================
    // Questions Studio
    // ============================================
    let selectedAssetForQuestion = null;
    let currentVocabItems = [];

    function loadQuestionsVocab() {
        const grade = $('#questions-grade').val();
        const period = $('#questions-period').val();
        const week = $('#questions-week').val();

        if (!grade || !period || !week) {
            $('#questions-vocab-table-body').html(`<tr><td colspan="11" class="text-center text-secondary"><i class="fa-solid fa-arrow-up"></i> Please select N, P, and SEM to view vocabulary.</td></tr>`);
            $('#btn-export-csv').prop('disabled', true);
            currentVocabItems = [];
            return;
        }

        $('#questions-vocab-table-body').empty();
        $('#questions-vocab-loading').removeClass('hidden');

        const params = `grade=N${grade}&period=P${period}&week=SEM${week}&limit=1000`;

        // Fetch vocab items and question counts in parallel
        Promise.all([
            $.get(`/vocabulary-assets?${params}`),
            $.get('/questions/counts')
        ]).then(function ([data, counts]) {
            $('#questions-vocab-loading').addClass('hidden');
            currentVocabItems = data.items || [];
            $('#btn-export-csv').prop('disabled', currentVocabItems.length === 0);
            renderQuestionsVocabTable(data.items, counts);
        }).catch(function () {
            $('#questions-vocab-loading').addClass('hidden');
            $('#questions-vocab-table-body').html('<tr><td colspan="11" class="text-center text-error">Error loading vocabulary.</td></tr>');
            $('#btn-export-csv').prop('disabled', true);
            currentVocabItems = [];
        });
    }

    function renderQuestionsVocabTable(items, questionCounts) {
        const tbody = $('#questions-vocab-table-body');
        tbody.empty();
        const counts = questionCounts || {};

        if (!items || items.length === 0) {
            tbody.html('<tr><td colspan="11" class="text-center">No vocabulary found for this selection.</td></tr>');
            return;
        }

        items.forEach(item => {
            const imageUrl = item.image ? (item.image.startsWith('vocab_assets') ? `/${item.image}` : `/taalim-data/${item.image}`) : '';
            const audioUrl = item.audio ? `/audios/${item.audio}` : '';

            const imageDisplay = imageUrl ? `<img src="${imageUrl}" class="vocab-thumbnail" loading="lazy" style="max-height: 50px;">` : '-';
            const audioDisplay = audioUrl ? `
                <button class="btn-audio-play" data-src="${audioUrl}" title="Play Audio">
                    <i class="fa-solid fa-volume-high"></i>
                </button>
            ` : '-';

            // Concept ID status
            const hasConceptId = !!item.concept_id;
            const conceptIdHtml = hasConceptId
                ? `<code style="font-size: 11px;">${item.concept_id}</code>`
                : `<span class="text-error" title="No concept ID assigned"><i class="fa-solid fa-triangle-exclamation"></i> Missing</span>`;

            // Question count
            const qCount = hasConceptId ? (counts[String(item.concept_id)] || 0) : 0;
            const qCountHtml = qCount > 0
                ? `<span class="badge" style="background: var(--success); color: #fff; padding: 2px 8px; border-radius: 12px; font-size: 12px; font-weight: 600;">${qCount}</span>`
                : `<span class="badge" style="background: var(--bg-secondary); color: var(--text-secondary); padding: 2px 8px; border-radius: 12px; font-size: 12px;">0</span>`;

            // Actions - only allow creating questions if concept_id exists
            const actionsHtml = hasConceptId
                ? `<button class="btn btn-sm btn-success btn-generate-questions-for-asset" data-id="${item.id}" data-name="${item.name || ''}" data-concept-id="${item.concept_id}"><i class="fa-solid fa-robot"></i> Generate Questions</button>`
                : `<span class="text-secondary" style="font-size: 12px;">Need concept ID</span>`;

            const row = `
                <tr data-id="${item.id}">
                    <td>${item.id}</td>
                    <td><span class="vocab-badge">${item.grade || '-'}</span></td>
                    <td>${imageDisplay}</td>
                    <td>${audioDisplay}</td>
                    <td>${item.name || '-'}</td>
                    <td style="text-align: right;">${item.name_ar || '-'}</td>
                    <td>${item.period || '-'}</td>
                    <td>${item.week || '-'}</td>
                    <td>${conceptIdHtml}</td>
                    <td style="text-align: center;">${qCountHtml}</td>
                    <td>${actionsHtml}</td>
                </tr>
            `;
            tbody.append(row);
        });
    }

    function loadQuestions() {
        console.log('Loading questions...');
        $.get('/questions').done(function (questions) {
            console.log('Questions loaded:', questions.length, questions);
            const tbody = $('#questions-table-body');
            tbody.empty();

            if (questions.length === 0) {
                console.log('No questions to display');
                $('#questions-empty').show();
                $('#questions-table-container').hide();
                return;
            }

            $('#questions-empty').hide();
            $('#questions-table-container').show();

            questions.forEach(function (q) {
                const date = new Date(q.created_at).toLocaleDateString();

                // Determine status badge
                let statusBadge = '';
                let statusClass = '';
                if (q.status === 'published') {
                    statusBadge = '<span class="vocab-badge" style="background: #10B981; color: white;">Published</span>';
                    statusClass = 'success';
                } else if (q.status === 'unaccepted') {
                    statusBadge = '<span class="vocab-badge" style="background: #EF4444; color: white;">Unaccepted</span>';
                    statusClass = 'error';
                } else if (q.status === 'failed') {
                    statusBadge = '<span class="vocab-badge" style="background: #F59E0B; color: white;">Failed</span>';
                    statusClass = 'warning';
                } else {
                    statusBadge = '<span class="vocab-badge">Pending</span>';
                    statusClass = '';
                }

                tbody.append(`
                    <tr data-id="${q.id}" class="${statusClass}">
                        <td>${q.id}</td>
                        <td><strong>${q.name}</strong></td>
                        <td><code style="font-size: 11px;">${q.concept_id}</code></td>
                        <td>${statusBadge}</td>
                        <td>${q.revizy_question_id ? `<code style="font-size: 11px;">${q.revizy_question_id}</code>` : '-'}</td>
                        <td class="text-secondary">${date}</td>
                        <td>
                            <button class="btn btn-sm btn-primary btn-preview-question" data-id="${q.id}">
                                <i class="fa-solid fa-eye"></i> Preview
                            </button>
                        </td>
                    </tr>
                `);
            });
        }).fail(function () {
            console.error('Failed to load questions');
        });
    }

    // Preview question button click
    $(document).on('click', '.btn-preview-question', async function () {
        const attemptId = $(this).data('id');
        const btn = $(this);
        console.log('Preview clicked for attempt ID:', attemptId);

        // Show loading state on button
        btn.prop('disabled', true).html('<i class="fa-solid fa-spinner fa-spin"></i> Loading...');

        try {
            // Fetch the question data from the API
            const questions = await $.get('/questions');
            const question = questions.find(q => q.id === attemptId);
            if (!question) {
                console.error('Question not found for ID:', attemptId);
                btn.prop('disabled', false).html('<i class="fa-solid fa-eye"></i> Preview');
                return;
            }

            console.log('Found question:', question);

            // Parse the question_data field which is a JSON string
            let questionData;
            try {
                questionData = typeof question.question_data === 'string'
                    ? JSON.parse(question.question_data)
                    : question.question_data;
            } catch (e) {
                console.error('Failed to parse question_data:', e);
                alert('Failed to parse question data');
                btn.prop('disabled', false).html('<i class="fa-solid fa-eye"></i> Preview');
                return;
            }

            console.log('Parsed question data:', questionData);

            // Collect all unique secret IDs from question and answer media
            const secretIdsToLookup = new Set();

            if (questionData.media?.image) secretIdsToLookup.add(questionData.media.image);
            if (questionData.media?.audio) secretIdsToLookup.add(questionData.media.audio);

            if (questionData.answers) {
                questionData.answers.forEach(ans => {
                    if (ans.media?.image) secretIdsToLookup.add(ans.media.image);
                    if (ans.media?.audio) secretIdsToLookup.add(ans.media.audio);
                });
            }

            // Resolve all secret IDs to actual asset data
            const mediaCache = {};
            const lookupPromises = Array.from(secretIdsToLookup).map(async (secretId) => {
                const asset = await lookupMediaBySecretId(secretId);
                if (asset) {
                    mediaCache[secretId] = asset;
                }
            });
            await Promise.all(lookupPromises);

            console.log('Media cache resolved:', mediaCache);

            const modal = $('#preview-modal');
            const content = $('#preview-modal-content');

            // Use the same render function (totalQuestions = null hides Publish/Unaccept buttons)
            const cardHtml = renderQuestionPreviewCard(questionData, 0, null, mediaCache);

            content.html(cardHtml);
            modal.css('display', 'flex');
            console.log('Modal displayed');
        } catch (err) {
            console.error('Failed to load question preview:', err);
            alert('Failed to load question preview');
        } finally {
            btn.prop('disabled', false).html('<i class="fa-solid fa-eye"></i> Preview');
        }
    });

    // Close modal when clicking outside
    $('#preview-modal').click(function (e) {
        if (e.target === this) {
            closePreviewModal();
        }
    });

    // Filter button click
    $('#btn-load-questions-vocab').click(function () {
        loadQuestionsVocab();
    });

    // Filter dropdown changes - auto reload if all selected
    $('#questions-grade, #questions-period, #questions-week').change(function () {
        saveFilterPrefs(); // Save to localStorage
        const grade = $('#questions-grade').val();
        const period = $('#questions-period').val();
        const week = $('#questions-week').val();
        if (grade && period && week) {
            loadQuestionsVocab();
        }
    });

    // Generate questions button click (from table)
    $(document).on('click', '.btn-generate-questions-for-asset', async function () {
        const btn = $(this);
        const assetId = btn.data('id');
        const assetName = btn.data('name');
        const conceptId = btn.data('concept-id');

        selectedAssetForQuestion = {
            id: assetId,
            name: assetName,
            concept_id: conceptId
        };

        // Show loading state on the button
        const originalBtnHtml = btn.html();
        btn.prop('disabled', true).html('<i class="fa-solid fa-spinner fa-spin"></i> Generating...');

        try {
            // Call the deterministic generation endpoint
            const generatedQuestions = await $.get(`/generate-questions/${assetId}`);

            if (!generatedQuestions || generatedQuestions.length === 0) {
                alert('No questions could be generated for this item. Check that distractors exist.');
                return;
            }

            // Reset modal
            $('#ai-questions-preview-section').hide();
            $('#ai-questions-preview-section .question-card').remove();
            $('#ai-questions-count').text('0');

            // Populate the JSON textarea with pretty-printed generated JSON
            const jsonString = JSON.stringify(generatedQuestions, null, 2);
            $('#ai-questions-json').val(jsonString);

            // Show modal
            $('#generate-questions-modal').show();

            // Auto-trigger the Parse JSON button
            $('#btn-parse-ai-questions').trigger('click');

        } catch (err) {
            console.error('Question generation failed:', err);
            const errorMsg = err.responseJSON?.detail || err.statusText || 'Unknown error';
            alert(`Failed to generate questions: ${errorMsg}`);

            // Still open the modal for manual paste if generation fails
            $('#ai-questions-json').val('');
            $('#ai-questions-preview-section').hide();
            $('#ai-questions-preview-section .question-card').remove();
            $('#ai-questions-count').text('0');
            $('#generate-questions-modal').show();
            $('#ai-questions-json').focus();
        } finally {
            btn.prop('disabled', false).html(originalBtnHtml);
        }
    });

    // Helper function to lookup media by secret ID
    async function lookupMediaBySecretId(secretId) {
        if (!secretId) return null;
        try {
            const response = await $.get(`/vocabulary-assets/by-secret-id/${secretId}`);
            return response;
        } catch (e) {
            return null;
        }
    }

    // Helper function to render media HTML from asset data
    function renderMediaFromAsset(asset) {
        let mediaHtml = '';

        if (asset && asset.image) {
            const imageUrl = asset.image.startsWith('vocab_assets') ? `/${asset.image}` : `/taalim-data/${asset.image}`;
            mediaHtml += `<div style="margin-bottom: 5px;"><img src="${imageUrl}" style="max-width: 100px; max-height: 100px; border-radius: 4px;"></div>`;
        }

        if (asset && asset.audio) {
            mediaHtml += `
            <button class="btn-audio-play-small" data-src="/audios/${asset.audio}" title="Play Audio" style="background: #eef2ff; border: 1px solid #c7d2fe; border-radius: 50%; width: 24px; height: 24px; display: inline-flex; items-align: center; justify-content: center; cursor: pointer; color: #4f46e5;">
                <i class="fa-solid fa-volume-high" style="font-size: 10px;"></i>
            </button>`;
        }

        return mediaHtml || '<span class="text-secondary" style="font-size: 11px;">No media</span>';
    }



    // Parse AI JSON
    $('#btn-parse-ai-questions').click(async function () {
        const jsonText = $('#ai-questions-json').val().trim();
        if (!jsonText) {
            alert('Please paste JSON data');
            return;
        }

        try {
            // Clean JSON string
            let cleanedJson = jsonText;

            // Replace smart quotes from ChatGPT
            cleanedJson = cleanedJson.replace(/[\u201C\u201D]/g, '"').replace(/[\u2018\u2019]/g, "'");

            // Remove markdown code blocks if present
            if (cleanedJson.includes('```')) {
                cleanedJson = cleanedJson.replace(/```json\s*[\n\r]+/, '')
                    .replace(/```\s*[\n\r]+/, '')
                    .replace(/```\s*$/, '');
            }

            // Only keep content between first [ and last ]
            const firstBracket = cleanedJson.indexOf('[');
            const lastBracket = cleanedJson.lastIndexOf(']');
            if (firstBracket !== -1 && lastBracket !== -1) {
                cleanedJson = cleanedJson.substring(firstBracket, lastBracket + 1);
            }

            // Remove trailing commas (simple heuristic)
            cleanedJson = cleanedJson.replace(/,\s*([\]}])/g, '$1');

            const questions = JSON.parse(cleanedJson);
            if (!Array.isArray(questions)) {
                alert('JSON must be an array of questions');
                return;
            }

            // Store parsed questions
            window.parsedAIQuestions = questions;

            // Collect all unique secret IDs from questions and answers
            const secretIdsToLookup = new Set();

            questions.forEach(q => {
                // Question media
                if (q.data?.media?.image) secretIdsToLookup.add(q.data.media.image);
                if (q.data?.media?.audio) secretIdsToLookup.add(q.data.media.audio);

                // Answer media
                if (q.data?.answers) {
                    q.data.answers.forEach(ans => {
                        if (ans.media?.image) secretIdsToLookup.add(ans.media.image);
                        if (ans.media?.audio) secretIdsToLookup.add(ans.media.audio);
                    });
                }
            });

            // Show loading
            const section = $('#ai-questions-preview-section');
            section.find('.question-card').remove();
            section.append('<div id="ai-loading-indicator" class="loading" style="padding: 20px;"><i class="fa-solid fa-spinner fa-spin"></i> Looking up media...</div>');
            section.show();

            // Lookup all secret IDs in parallel
            const mediaCache = {};
            const lookupPromises = Array.from(secretIdsToLookup).map(async (secretId) => {
                const asset = await lookupMediaBySecretId(secretId);
                if (asset) {
                    mediaCache[secretId] = asset;
                }
            });

            await Promise.all(lookupPromises);

            // Remove loading indicator
            $('#ai-loading-indicator').remove();

            // Check for duplicates in backend
            const questionsCheckPayload = questions.map((q, idx) => ({
                index: idx,
                concept_id: String(q.concept_id || ''),
                data: processQuestionDataForUpload(q.data, q.data?.type)
            }));

            let duplicateStatus = {};
            try {
                const checkResponse = await $.ajax({
                    url: '/questions/check-duplicates',
                    type: 'POST',
                    contentType: 'application/json',
                    data: JSON.stringify({ questions: questionsCheckPayload })
                });

                if (checkResponse.duplicates) {
                    checkResponse.duplicates.forEach(dup => {
                        duplicateStatus[dup.index] = dup;
                    });
                }
            } catch (e) {
                console.error('Failed to check duplicates:', e);
            }

            // Remove existing question cards but keep the header
            section.find('.question-cards-grid').remove();

            // Create grid container for question cards (2 per row on desktop, 1 on mobile)
            const gridContainer = $(`<div class="question-cards-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(350px, 1fr)); gap: 20px; padding: 20px 0; align-items: stretch;"></div>`);
            section.append(gridContainer);

            questions.forEach((q, index) => {
                const instruction = q.data?.instruction || '';
                const body = q.data?.body || '';
                const type = q.data?.type || 'universal';
                const answers = q.data?.answers || [];

                // Lookup question media by secret ID from JSON
                const qImageId = q.data?.media?.image;
                const qAudioId = q.data?.media?.audio;
                const qImageAsset = qImageId ? mediaCache[qImageId] : null;
                const qAudioAsset = qAudioId ? mediaCache[qAudioId] : null;

                // Get question image URL
                let questionImageUrl = '';
                if (qImageAsset && qImageAsset.image) {
                    questionImageUrl = qImageAsset.image.startsWith('vocab_assets/') ?
                        '/' + qImageAsset.image : '/taalim-data/' + qImageAsset.image;
                }

                // Get question audio URL
                let questionAudioUrl = '';
                if (qAudioAsset && qAudioAsset.audio) {
                    questionAudioUrl = '/audios/' + qAudioAsset.audio;
                }

                // Build options/answers HTML using mobile quiz template style
                let optionsHtml = '';

                // Smart layout detection for answers
                // Default to 2 per row, but use 1 per row if any text is long (>40 chars)
                // Images are allowed in 2-column layout - they'll just be smaller
                let answersUseTwoColumns = true;
                if (answers.length > 0) {
                    // Check if any answer has very long text that would wrap badly in 2-col
                    const hasVeryLongText = answers.some(a => a.body && a.body.length > 40);
                    if (hasVeryLongText) {
                        answersUseTwoColumns = false;
                    }
                }

                if (answers.length > 0) {
                    answers.forEach((ans, ansIndex) => {
                        const isCorrect = ans.is_correct;
                        const answerBody = ans.body || '';

                        // Lookup answer media by secret ID from JSON
                        const aImageId = ans.media?.image;
                        const aAudioId = ans.media?.audio;
                        const aImageAsset = aImageId ? mediaCache[aImageId] : null;
                        const aAudioAsset = aAudioId ? mediaCache[aAudioId] : null;

                        // Get answer image
                        let answerImageUrl = '';
                        if (aImageAsset && aImageAsset.image) {
                            answerImageUrl = aImageAsset.image.startsWith('vocab_assets/') ?
                                '/' + aImageAsset.image : '/taalim-data/' + aImageAsset.image;
                        }

                        // Determine shadow and face classes based on correctness
                        let shadowClass = '';
                        let faceClass = '';
                        if (isCorrect) {
                            shadowClass = 'correct';
                            faceClass = 'correct';
                        }

                        const hasText = answerBody ? 'has-text' : '';

                        // Render styled text with color tags (skip for fill_text and letter_by_letter)
                        const skipStyling = type.includes('fill_text') || type.includes('letter_by_letter');
                        const styledText = skipStyling
                            ? answerBody.replace(/\[(BLUE|PINK|RED|GREEN)\]([\s\S]*?)\[\/\1\]/gi, '$2')
                            : renderStyledText(answerBody);

                        // Get answer audio
                        let answerAudioUrl = '';
                        if (aAudioAsset && aAudioAsset.audio) {
                            answerAudioUrl = '/audios/' + aAudioAsset.audio;
                        }

                        // Calculate width: 2 per row by default, 1 per row if long text detected
                        const itemWidth = answersUseTwoColumns ? 'calc(50% - 8px)' : '100%';

                        optionsHtml += `
                            <div class="option-btn-container preview-answer-btn" 
                                 data-audio="${answerAudioUrl || ''}"
                                 data-correct="${isCorrect}"
                                 style="position: relative; width: ${itemWidth}; min-height: 60px; cursor: pointer;"
                                 onclick="handlePreviewAnswerClick(this, '${answerAudioUrl || ''}', ${isCorrect})">
                                <div class="option-btn-shadow ${shadowClass}" style="position: absolute; top: 4px; left: 0; right: 0; bottom: -4px; border-radius: 16px; background: ${isCorrect ? '#10B981' : '#e2e8f0'}; transition: background 0.3s ease;"></div>
                                 <div class="option-btn-face ${faceClass} ${hasText}" style="position: relative; background: ${isCorrect ? '#f0fdf4' : '#ffffff'}; border-radius: 16px; border: 2px solid ${isCorrect ? '#10B981' : '#e2e8f0'}; min-height: 60px; padding: 12px; display: flex; flex-direction: column; align-items: center; justify-content: center; transition: all 0.1s ease; overflow: hidden;">
                                     ${answerImageUrl ? `<img src="${answerImageUrl}" style="width: 50px; height: 50px; object-fit: contain; margin-bottom: 6px; border-radius: 6px;">` : ''}
                                     ${answerBody ? `<div style="font-size: 15px; color: #334155; text-align: center; line-height: 1.4; font-family: 'Rubik', sans-serif;">${styledText}</div>` : ''}
                                    ${answerAudioUrl ? `<div class="audio-indicator" style="width: 32px; height: 32px; border-radius: 50%; background: #e8f4f8; display: flex; align-items: center; justify-content: center; margin-top: ${answerImageUrl || answerBody ? '6px' : '0'};"><i class="fa-solid fa-volume-high" style="color: #2E76B6; font-size: 14px;"></i></div>` : ''}
                                </div>
                            </div>
                        `;
                    });
                }

                // Render mobile quiz style card with fixed height using the helper function
                const cardHtml = renderQuestionPreviewCard(q, index, questions.length, mediaCache, duplicateStatus[index]);
                gridContainer.append(cardHtml);
            });

            $('#ai-questions-count').text(questions.length);
            $('#ai-questions-preview-section').show();

        } catch (e) {
            alert('Invalid JSON format: ' + e.message);
        }
    });

    // Helper function to auto-color French articles
    function autoColorArticles(text) {
        if (!text) return '';
        if (/\[(BLUE|PINK|RED|GREEN|YELLOW|PURPLE|ORANGE)\]/i.test(text)) return text;
        // Le, Un → BLUE (masculine)
        // La, Une → PINK (feminine)
        return text
            .replace(/\b(Le|Un)\b/gi, '[BLUE]$1[/BLUE]')
            .replace(/\b(La|Une)\b/gi, '[PINK]$1[/PINK]');
    }

    // Helper function to process question data recursively for upload
    function processQuestionDataForUpload(data, questionType) {
        if (!data) return data;

        const skipAnswerStyling = questionType &&
            (questionType.includes('fill_text') || questionType.includes('letter_by_letter'));

        if (typeof data === 'string') {
            return data;
        }

        if (Array.isArray(data)) {
            return data.map(item => processQuestionDataForUpload(item, questionType));
        }

        if (typeof data === 'object') {
            const processed = {};
            for (const key in data) {
                // Process only body fields
                if (key === 'body') {
                    if (typeof data[key] === 'string') {
                        processed[key] = autoColorArticles(data[key]);
                    } else {
                        processed[key] = data[key];
                    }
                } else if (key === 'answers' && skipAnswerStyling) {
                    // For fill_text/letter_by_letter: strip color tags from answer text
                    processed[key] = data[key].map(answer => {
                        const cleanAnswer = { ...answer };
                        if (cleanAnswer.body) {
                            cleanAnswer.body = cleanAnswer.body
                                .replace(/\[(BLUE|PINK|RED|GREEN)\]([\s\S]*?)\[\/\1\]/gi, '$2');
                        }
                        if (cleanAnswer.text) {
                            cleanAnswer.text = cleanAnswer.text
                                .replace(/\[(BLUE|PINK|RED|GREEN)\]([\s\S]*?)\[\/\1\]/gi, '$2');
                        }
                        return cleanAnswer;
                    });
                } else {
                    processed[key] = processQuestionDataForUpload(data[key], questionType);
                }
            }
            return processed;
        }

        return data;
    }

    // Helper function to render styled text with color tags
    function renderStyledText(text) {
        if (!text) return '';
        // Only auto-color if text doesn't already contain color tags (prevents double-tagging)
        let processedText = text;
        if (!/\[(BLUE|PINK|RED|GREEN)\]/i.test(text)) {
            processedText = autoColorArticles(text);
        }
        return processedText
            .replace(/\[BLUE\]([\s\S]*?)\[\/BLUE\]/gi, '<span style="color: #2E76B6;">$1</span>')
            .replace(/\[PINK\]([\s\S]*?)\[\/PINK\]/gi, '<span style="color: #DC03A2;">$1</span>')
            .replace(/\[RED\]([\s\S]*?)\[\/RED\]/gi, '<span style="color: #AF0A54;">$1</span>')
            .replace(/\[GREEN\]([\s\S]*?)\[\/GREEN\]/gi, '<span style="color: #00AAA4;">$1</span>')
            .replace(/(_+)/g, '<span style="color: #00AAA4;">$1</span>');
    }

    // Global function to close preview modal
    window.closePreviewModal = function () {
        $('#preview-modal').css('display', 'none');
    };

    // Helper function to render a question preview card
    function renderQuestionPreviewCard(q, index, totalQuestions, mediaCache, duplicateInfo) {
        const instruction = q.data?.instruction || q.instruction || '';
        const body = q.data?.body || q.body || '';
        const type = q.data?.type || q.type || 'universal';
        const answers = q.data?.answers || q.answers || [];

        // Lookup question media by secret ID from JSON
        const qImageId = q.data?.media?.image || q.media?.image;
        const qAudioId = q.data?.media?.audio || q.media?.audio;
        const qImageAsset = qImageId ? mediaCache[qImageId] : null;
        const qAudioAsset = qAudioId ? mediaCache[qAudioId] : null;

        // Get question image URL
        let questionImageUrl = '';
        if (qImageAsset && qImageAsset.image) {
            questionImageUrl = qImageAsset.image.startsWith('vocab_assets/') ?
                '/' + qImageAsset.image : '/taalim-data/' + qImageAsset.image;
        }

        // Get question audio URL
        let questionAudioUrl = '';
        if (qAudioAsset && qAudioAsset.audio) {
            questionAudioUrl = '/audios/' + qAudioAsset.audio;
        }

        // Build options/answers HTML using mobile quiz template style
        let optionsHtml = '';

        // Smart layout detection for answers
        let answersUseTwoColumns = true;
        if (answers.length > 0) {
            const hasVeryLongText = answers.some(a => (a.body || a.text) && (a.body || a.text).length > 40);
            if (hasVeryLongText) {
                answersUseTwoColumns = false;
            }
        }

        if (answers.length > 0) {
            answers.forEach((ans, ansIndex) => {
                const isCorrect = ans.is_correct;
                const answerBody = ans.body || ans.text || '';

                // Lookup answer media by secret ID from JSON
                const aImageId = ans.media?.image;
                const aAudioId = ans.media?.audio;
                const aImageAsset = aImageId ? mediaCache[aImageId] : null;
                const aAudioAsset = aAudioId ? mediaCache[aAudioId] : null;

                // Get answer image
                let answerImageUrl = '';
                if (aImageAsset && aImageAsset.image) {
                    answerImageUrl = aImageAsset.image.startsWith('vocab_assets/') ?
                        '/' + aImageAsset.image : '/taalim-data/' + aImageAsset.image;
                }

                // Determine shadow and face classes based on correctness
                let shadowClass = '';
                let faceClass = '';
                if (isCorrect) {
                    shadowClass = 'correct';
                    faceClass = 'correct';
                }

                const hasText = answerBody ? 'has-text' : '';

                // Render styled text with color tags (skip for fill_text and letter_by_letter)
                const skipStyling = type.includes('fill_text') || type.includes('letter_by_letter');
                const styledText = skipStyling
                    ? answerBody.replace(/\[(BLUE|PINK|RED|GREEN)\]([\s\S]*?)\[\/\1\]/gi, '$2')
                    : renderStyledText(answerBody);

                // Get answer audio
                let answerAudioUrl = '';
                if (aAudioAsset && aAudioAsset.audio) {
                    answerAudioUrl = '/audios/' + aAudioAsset.audio;
                }

                const itemWidth = answersUseTwoColumns ? 'calc(50% - 8px)' : '100%';

                optionsHtml += `
                    <div class="option-btn-container preview-answer-btn" 
                         data-audio="${answerAudioUrl || ''}"
                         data-correct="${isCorrect}"
                         style="position: relative; width: ${itemWidth}; min-height: 60px; cursor: pointer;"
                         onclick="handlePreviewAnswerClick(this, '${answerAudioUrl || ''}', ${isCorrect})">
                        <div class="option-btn-shadow ${shadowClass}" style="position: absolute; top: 4px; left: 0; right: 0; bottom: -4px; border-radius: 16px; background: ${isCorrect ? '#10B981' : '#e2e8f0'}; transition: background 0.3s ease;"></div>
                        <div class="option-btn-face ${faceClass} ${hasText}" style="position: relative; background: ${isCorrect ? '#f0fdf4' : '#ffffff'}; border-radius: 16px; border: 2px solid ${isCorrect ? '#10B981' : '#e2e8f0'}; min-height: 60px; padding: 12px; display: flex; flex-direction: column; align-items: center; justify-content: center; transition: all 0.1s ease; overflow: hidden;">
                            ${answerImageUrl ? `<img src="${answerImageUrl}" style="width: 50px; height: 50px; object-fit: contain; margin-bottom: 6px; border-radius: 6px;">` : ''}
                            ${answerBody ? `<div style="font-size: 15px; color: #334155; text-align: center; line-height: 1.4; font-family: 'Rubik', sans-serif;">${styledText}</div>` : ''}
                            ${answerAudioUrl ? `<div class="audio-indicator" style="width: 32px; height: 32px; border-radius: 50%; background: #e8f4f8; display: flex; align-items: center; justify-content: center; margin-top: ${answerImageUrl || answerBody ? '6px' : '0'};"><i class="fa-solid fa-volume-high" style="color: #2E76B6; font-size: 14px;"></i></div>` : ''}
                        </div>
                    </div>
                `;
            });
        }

        // Render mobile quiz style card with fixed height
        return `
            <div class="question-card" style="background: white; border: 2px solid #e2e8f0; border-radius: 16px; overflow: hidden; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1); display: block; position: relative; padding-bottom: 100px;" data-index="${index}">
                <!-- Header -->
                <div style="padding: 16px; background: #f8fafc; border-bottom: 1px solid #e2e8f0;">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 12px;">
                        <div style="font-size: 12px; color: #64748b; font-weight: 600;">Question ${index + 1}${totalQuestions ? ' of ' + totalQuestions : ''}</div>
                        ${totalQuestions ? `<button class="btn btn-sm btn-secondary btn-edit-ai-question" data-index="${index}" style="padding: 4px 12px; font-size: 12px;">
                            <i class="fa-solid fa-edit"></i> Edit
                        </button>` : ''}
                    </div>
                    <div style="color: #475569; font-size: 18px; font-weight: 700; text-align: right; direction: rtl; font-family: 'Rubik', sans-serif; line-height: 1.5; max-height: 54px; overflow: hidden; text-overflow: ellipsis; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical;">
                        ${instruction || 'No instruction'}
                    </div>
                </div>

                <!-- Question Part (fixed 300px height, centered) -->
                <div style="height: 300px; padding: 20px; display: flex; flex-direction: column; justify-content: center; overflow-y: auto; background: #fafafa;">
                    <!-- Question Image (only if exists) -->
                    ${questionImageUrl ? `
                    <div style="text-align: center; margin-bottom: 16px; flex-shrink: 0;">
                        <img src="${questionImageUrl}" style="max-width: 100%; max-height: 200px; border-radius: 12px; object-fit: contain; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
                    </div>
                    ` : ''}
                    
                    <!-- Question Audio (only if exists) -->
                    ${questionAudioUrl ? `
                    <div style="display: flex; justify-content: center; margin-bottom: 16px; flex-shrink: 0;">
                        <button class="btn-audio-play" data-src="${questionAudioUrl}" title="Play Audio" style="width: 60px; height: 60px; border-radius: 50%; border: 2px solid #2E76B6; background: white; cursor: pointer; display: flex; align-items: center; justify-content: center; box-shadow: 0 2px 8px rgba(46, 118, 182, 0.2);">
                            <i class="fa-solid fa-volume-high" style="color: #2E76B6; font-size: 24px;"></i>
                        </button>
                    </div>
                    ` : ''}
                    
                    <!-- Question Body (only if exists) -->
                    ${body ? `
                    <div style="margin-bottom: 16px; text-align: center; flex-shrink: 0;">
                        <div style="font-size: 18px; color: #1e293b; font-weight: 600; line-height: 1.4; font-family: 'Rubik', sans-serif;">
                            ${renderStyledText(body)}
                        </div>
                    </div>
                    ` : ''}
                </div>
                
                <!-- Answers Part (separate from question) -->
                <div style="padding: 20px; background: white; border-top: 2px solid #e2e8f0; flex-shrink: 0;">
                    <div style="display: flex; flex-wrap: wrap; justify-content: center; gap: 12px; width: 100%;">
                        ${optionsHtml}
                    </div>
                </div>
                
                ${totalQuestions ? `
                <!-- Footer with Validate Button -->
                <div style="padding: 20px; background: #f8fafc; border-top: 1px solid #e2e8f0; position: absolute; bottom: 0; width: 100%; height: 100px; box-sizing: border-box; display: flex; flex-direction: row; gap: 12px; align-items: center;">
                    ${duplicateInfo && duplicateInfo.is_published ? `
                    <button class="btn btn-secondary" disabled style="flex: 1; padding: 16px; font-size: 16px; font-weight: 700; border-radius: 12px; background: #94a3b8; color: white; border: none; cursor: not-allowed; transition: all 0.2s;">
                        <i class="fa-solid fa-check"></i> Published
                    </button>
                    ` : `
                    <button class="btn btn-success btn-validate-publish-question" data-index="${index}" style="flex: 1; padding: 16px; font-size: 16px; font-weight: 700; border-radius: 12px; background: #10B981; color: white; border: none; cursor: pointer; transition: all 0.2s;">
                        <i class="fa-solid fa-check-circle"></i> Publish
                    </button>
                    `}
                    <button class="btn btn-unaccept-question" data-index="${index}" style="flex: 1; padding: 16px; font-size: 16px; font-weight: 700; border-radius: 12px; background: #EF4444; color: white; border: none; cursor: pointer; transition: all 0.2s;">
                        <i class="fa-solid fa-xmark"></i> Unaccept
                    </button>
                </div>
                ` : ''}
            </div>
        `;
    }

    // Global function to handle preview answer clicks
    window.handlePreviewAnswerClick = function (element, audioUrl, isCorrect) {
        const $container = $(element);
        const $shadow = $container.find('.option-btn-shadow');
        const $face = $container.find('.option-btn-face');

        // Play audio if available
        if (audioUrl) {
            const audio = new Audio(audioUrl);
            audio.play().catch(e => console.error('Audio playback failed:', e));
        }

        // Visual feedback based on correctness
        if (isCorrect) {
            // Correct answer - green feedback
            $shadow.css('background', '#10B981');
            $face.css({
                'background': '#f0fdf4',
                'border-color': '#10B981'
            });
        } else {
            // Incorrect answer - red feedback (soft)
            $shadow.css('background', '#EF4444');
            $face.css({
                'background': '#fef2f2',
                'border-color': '#EF4444'
            });
        }

        // Reset after 1.5 seconds
        setTimeout(() => {
            $shadow.css('background', isCorrect ? '#10B981' : '#e2e8f0');
            $face.css({
                'background': isCorrect ? '#f0fdf4' : '#ffffff',
                'border-color': isCorrect ? '#10B981' : '#e2e8f0'
            });
        }, 1500);
    };

    // Publish individual question
    $(document).on('click', '.btn-validate-publish-question', function () {
        const index = $(this).data('index');
        const question = window.parsedAIQuestions[index];

        if (!question) {
            alert('Question not found');
            return;
        }

        const btn = $(this);
        const card = btn.closest('[data-index]');
        btn.prop('disabled', true).html('<i class="fa-solid fa-spinner fa-spin"></i> Publishing...');

        // Process question data to auto-color French articles
        const processedData = processQuestionDataForUpload(question.data, question.data?.type);

        // Remove type from data block as requested
        const dataToSend = JSON.parse(JSON.stringify(processedData));
        if (dataToSend && typeof dataToSend === 'object') {
            delete dataToSend.type;
        }

        // Call backend API to publish
        $.ajax({
            url: `/questions/${index}/publish`,
            type: 'POST',
            contentType: 'application/json',
            data: JSON.stringify({
                local_question_id: index,
                concept_id: String(question.concept_id),
                name: question.name,
                type: question.type || question.data?.type || 'universal',
                status: 'published',
                data: dataToSend
            })
        }).done(function (response) {
            btn.html('<i class="fa-solid fa-check"></i> Published');
            btn.removeClass('btn-success').addClass('btn-secondary');

            // Visual feedback on the card
            card.css('border-color', '#86efac');
            card.css('background', '#f0fdf4');

            // Disable unaccept button
            card.find('.btn-unaccept-question').prop('disabled', true);

            alert(`Question "${question.name}" published successfully!\nRevizy ID: ${response.revizy_question_id}`);
        }).fail(function (xhr) {
            btn.prop('disabled', false).html('<i class="fa-solid fa-check-circle"></i> Publish');
            const errorMsg = xhr.responseJSON?.detail || 'Unknown error';
            alert(`Failed to publish: ${errorMsg}`);
        });
    });

    // Unaccept individual question
    $(document).on('click', '.btn-unaccept-question', function () {
        const index = $(this).data('index');
        const question = window.parsedAIQuestions[index];

        if (!question) {
            alert('Question not found');
            return;
        }

        const btn = $(this);
        const card = btn.closest('[data-index]');

        if (!confirm('Are you sure you want to mark this question as unaccepted?')) {
            return;
        }

        btn.prop('disabled', true).html('<i class="fa-solid fa-spinner fa-spin"></i> Processing...');

        // Process question data to auto-color French articles
        const processedData = processQuestionDataForUpload(question.data, question.data?.type);

        // Call backend API to unaccept
        $.ajax({
            url: `/questions/${index}/unaccept`,
            type: 'POST',
            contentType: 'application/json',
            data: JSON.stringify({
                local_question_id: index,
                concept_id: String(question.concept_id),
                name: question.name,
                data: processedData
            })
        }).done(function (response) {
            btn.html('<i class="fa-solid fa-xmark"></i> Unaccepted');
            btn.css('background', '#9CA3AF');

            // Visual feedback on the card
            card.css('border-color', '#FCA5A5');
            card.css('background', '#FEF2F2');

            // Disable publish button
            card.find('.btn-validate-publish-question').prop('disabled', true);

            alert(`Question "${question.name}" marked as unaccepted.`);
        }).fail(function (xhr) {
            btn.prop('disabled', false).html('<i class="fa-solid fa-xmark"></i> Unaccept');
            const errorMsg = xhr.responseJSON?.detail || 'Unknown error';
            alert(`Failed to unaccept: ${errorMsg}`);
        });
    });

    // Edit AI question (placeholder for future)
    $(document).on('click', '.btn-edit-ai-question', function () {
        const index = $(this).data('index');
        const question = window.parsedAIQuestions[index];
        if (question) {
            // For now, just log it. In future, open an edit modal
            console.log('Edit question:', question);
            alert('Edit functionality coming soon!\n\nQuestion: ' + question.name);
        }
    });

    // Cancel generate questions
    $('#btn-cancel-generate').click(function () {
        $('#generate-questions-modal').hide();
        selectedAssetForQuestion = null;
        window.parsedAIQuestions = [];
        // Remove all question cards but keep the section structure
        $('#ai-questions-preview-section .question-card').remove();
        $('#ai-questions-preview-section').hide();
    });

    // Delete question
    $(document).on('click', '.btn-delete-question', function () {
        const id = $(this).data('id');
        if (!confirm('Are you sure you want to delete this question?')) return;

        $.ajax({
            url: `/questions/${id}`,
            type: 'DELETE'
        }).done(function () {
            loadQuestions();
        }).fail(function () {
            alert('Failed to delete question');
        });
    });

    // Load questions when view is shown
    $('.nav-item[data-view="questions-studio"], button[data-view="questions-studio"]').click(function () {
        console.log('Questions Studio view clicked');
        loadQuestions();
        // Also load vocabulary if filters are already set from localStorage
        const grade = $('#questions-grade').val();
        const period = $('#questions-period').val();
        const week = $('#questions-week').val();
        if (grade && period && week) {
            loadQuestionsVocab();
        }
    });

    // Export CSV button
    $('#btn-export-csv').click(function () {
        if (currentVocabItems.length === 0) {
            alert('No data to export');
            return;
        }

        // CSV Header
        let csvContent = 'concept_id,name,image_secret_id,audio_secret_id\n';

        // CSV Rows - only export items with concept_id
        currentVocabItems.forEach(item => {
            if (item.concept_id) {
                const conceptId = item.concept_id || '';
                const name = (item.name || '').replace(/,/g, ' ').replace(/"/g, '""');
                const imageSecretId = item.revizy_image_file_id || '';
                const audioSecretId = item.revizy_audio_file_id || '';
                csvContent += `"${conceptId}","${name}","${imageSecretId}","${audioSecretId}"\n`;
            }
        });

        // Create download
        const grade = $('#questions-grade').val() || 'X';
        const period = $('#questions-period').val() || 'X';
        const week = $('#questions-week').val() || 'X';
        const filename = `N${grade}_P${period}_SEM${week}.csv`;

        const blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' });
        const link = document.createElement('a');
        if (link.download !== undefined) {
            const url = URL.createObjectURL(blob);
            link.setAttribute('href', url);
            link.setAttribute('download', filename);
            link.style.visibility = 'hidden';
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
        }
    });

    // Batch Auto-Generate
    $('#btn-batch-generate').click(function () {
        $('#batch-generate-modal').css('display', 'flex');
        $('#batch-progress-container').addClass('hidden');
        $('#batch-results').addClass('hidden').empty();
        $('#btn-confirm-batch-generate').prop('disabled', false).html('<i class="fa-solid fa-play"></i> Start Batch Generation');
    });

    $('.close-modal, .close-modal-btn').click(function () {
        $('#batch-generate-modal').hide();
    });

    $('#btn-confirm-batch-generate').click(function () {
        const btn = $(this);
        btn.prop('disabled', true).html('<i class="fa-solid fa-spinner fa-spin"></i> Processing...');

        $('#batch-progress-container').removeClass('hidden');
        $('#batch-progress-bar').css('width', '5%');
        $('#batch-status-text').text('Analyzing vocabulary items...');
        $('#batch-results').removeClass('hidden').html('<div class="text-secondary">Starting process...</div>');

        // Call batch endpoint
        $.post('/batch-generate-publish')
            .done(function (response) {
                $('#batch-progress-bar').css('width', '100%');
                $('#batch-status-text').text('Completed!');
                btn.html('<i class="fa-solid fa-check"></i> Done');

                let html = `
                    <div class="alert alert-success">
                        <strong>Success!</strong> ${response.message}<br>
                        Generated: ${response.generated} questions<br>
                        Published: ${response.published}<br>
                        Failed: ${response.failed}<br>
                        Skipped: ${response.skipped}
                    </div>
                    <table class="table table-sm table-striped" style="margin-top:10px;">
                        <thead><tr><th>Word</th><th>Status</th><th>Details</th></tr></thead>
                        <tbody>
                `;

                if (response.details && response.details.length > 0) {
                    response.details.forEach(item => {
                        let statusColor = item.status === 'done' ? 'text-success' : (item.status === 'error' ? 'text-error' : 'text-secondary');
                        let details = item.errors.length > 0 ? item.errors.join(', ') : `Generated ${item.questions_generated}, Published ${item.questions_published}`;
                        html += `
                            <tr>
                                <td>${item.word} <small class="text-secondary">(${item.grade})</small></td>
                                <td class="${statusColor}"><strong>${item.status.toUpperCase()}</strong></td>
                                <td>${details}</td>
                            </tr>
                        `;
                    });
                } else {
                    html += `<tr><td colspan="3" class="text-center text-secondary">No items needed processing.</td></tr>`;
                }

                html += '</tbody></table>';
                $('#batch-results').html(html);

                // Refresh table if filtered view is open
                if (currentVocabItems.length > 0) {
                    loadQuestionsVocab();
                }
            })
            .fail(function (xhr) {
                $('#batch-progress-bar').css('background', 'var(--error)');
                $('#batch-status-text').text('Error occurred');
                btn.prop('disabled', false).text('Retry');
                $('#batch-results').html(`<div class="alert alert-error"><strong>Error:</strong> ${xhr.responseText || 'Server error'}</div>`);
            });
    });

    handleRouting();
});
