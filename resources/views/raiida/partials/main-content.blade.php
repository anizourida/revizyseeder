        <main class="main-content">

            <!-- Dashboard View -->


            <section id="dashboard-view" class="view active">
                <header class="view-header">
                    <h2>Tableau de Bord</h2>
                    <div class="header-actions">
                        <button id="btn-sync" class="btn btn-primary">
                            <i class="fa-solid fa-sync"></i> Synchroniser
                        </button>
                        <button id="btn-inspect" class="btn btn-warning">
                            <i class="fa-solid fa-shield-halved"></i> Vérifier
                        </button>
                    </div>
                </header>

                <div class="stats-grid">
                    <div class="stat-card">
                        <div class="icon-wrapper blue"><i class="fa-solid fa-file"></i></div>
                        <div class="stat-info">
                            <h3>Total Fichiers</h3>
                            <p id="stat-total">0</p>
                        </div>
                    </div>
                    <div class="stat-card">
                        <div class="icon-wrapper green"><i class="fa-solid fa-check"></i></div>
                        <div class="stat-info">
                            <h3>Téléchargés</h3>
                            <p id="stat-downloaded">0</p>
                            <span class="sub-text" id="stat-percent">0%</span>
                        </div>
                    </div>
                    <div class="stat-card">
                        <div class="icon-wrapper purple"><i class="fa-solid fa-hard-drive"></i></div>
                        <div class="stat-info">
                            <h3>Taille Totale</h3>
                            <p id="stat-size">0 GB</p>
                        </div>
                    </div>
                    <div class="stat-card">
                        <div class="icon-wrapper red"><i class="fa-solid fa-triangle-exclamation"></i></div>
                        <div class="stat-info">
                            <h3>Corrompus</h3>
                            <p id="stat-corrupt" class="text-error">0</p>
                        </div>
                    </div>
                </div>

                <div class="recent-logs-container">
                    <h3>Activités Récentes</h3>
                    <div id="activity-log" class="activity-log">
                        <div class="log-item info">Prêt à synchroniser...</div>
                    </div>
                </div>
            </section>

            <!-- Files List View -->
            <section id="files-view" class="view">
                <header class="view-header">
                    <h2>Liste des Fichiers</h2>
                    <div class="search-box">
                        <i class="fa-solid fa-search"></i>
                        <input type="text" id="table-search" placeholder="Rechercher...">
                    </div>
                </header>

                <div class="filters-bar">
                    <select id="filter-grade" class="filter-select">
                        <option value="">Tous les Niveaux</option>
                        <!-- Populated by JS -->
                    </select>
                    <select id="filter-subject" class="filter-select">
                        <option value="">Toutes les Matières</option>
                    </select>
                    <select id="filter-period" class="filter-select">
                        <option value="">Toutes les Périodes</option>
                    </select>
                    <select id="filter-week" class="filter-select">
                        <option value="">Toutes les Semaines</option>
                    </select>
                    <select id="filter-status" class="filter-select">
                        <option value="">Tous les Statuts</option>
                        <option value="downloaded">Téléchargé</option>
                        <option value="missing">Non Téléchargé</option>
                        <option value="corrupt">Corrompu</option>
                    </select>

                    <div class="column-selector dropdown">
                        <button class="btn btn-secondary dropdown-toggle" type="button" id="columnDropdown">
                            <i class="fa-solid fa-columns"></i> Colonnes
                        </button>
                        <div class="dropdown-menu">
                            <label><input type="checkbox" checked data-col="id"> ID</label>
                            <label><input type="checkbox" checked data-col="filename"> Fichier</label>
                            <label><input type="checkbox" checked data-col="grade"> Niveau</label>
                            <label><input type="checkbox" checked data-col="subject"> Matière</label>
                            <label><input type="checkbox" checked data-col="period"> Période</label>
                            <label><input type="checkbox" checked data-col="week"> Semaine</label>
                            <label><input type="checkbox" checked data-col="size"> Taille</label>
                            <label><input type="checkbox" checked data-col="status"> Statut</label>
                            <label><input type="checkbox" checked data-col="vocab"> Vocab</label>
                            <label><input type="checkbox" checked data-col="session"> Séance</label>
                            <label><input type="checkbox" checked data-col="actions"> Actions</label>
                        </div>
                    </div>

                    <select id="group-by" class="filter-select">
                        <option value="">Pas de regroupement</option>
                        <option value="grade">Regrouper par Niveau</option>
                        <option value="subject">Regrouper par Matière</option>
                        <option value="week">Regrouper par Semaine</option>
                    </select>
                </div>

                <div class="table-container">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th data-sort="id">ID <i class="fa-solid fa-sort"></i></th>
                                <th data-sort="filename">Fichier <i class="fa-solid fa-sort"></i></th>
                                <th data-sort="grade">Niveau <i class="fa-solid fa-sort"></i></th>
                                <th data-sort="subject">Matière <i class="fa-solid fa-sort"></i></th>
                                <th data-sort="period">Période <i class="fa-solid fa-sort"></i></th>
                                <th data-sort="week">Semaine <i class="fa-solid fa-sort"></i></th>
                                <th data-sort="size">Taille <i class="fa-solid fa-sort"></i></th>
                                <th data-sort="status">Statut <i class="fa-solid fa-sort"></i></th>
                                <th data-sort="vocab">Vocab <i class="fa-solid fa-sort"></i></th>
                                <th data-sort="session">Séance <i class="fa-solid fa-sort"></i></th>
                                <th class="text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody id="files-table-body">
                            <!-- JS will populate -->
                        </tbody>
                    </table>
                    <div id="table-loading" class="loading"><i class="fa-solid fa-spinner fa-spin"></i> Chargement...
                    </div>
                </div>
            </section>

            <!-- Browser View -->
            <section id="browser-view" class="view">
                <header class="view-header">
                    <h2>Navigateur de Fichiers</h2>
                    <div class="search-box">
                        <i class="fa-solid fa-search"></i>
                        <input type="text" id="file-search" placeholder="Rechercher...">
                    </div>
                </header>

                <div class="file-browser-container">
                    <div id="file-tree" class="file-tree">
                        <div class="loading"><i class="fa-solid fa-spinner fa-spin"></i> Chargement...</div>
                    </div>
                </div>
            </section>

            <!-- Vocabulary View -->
            <section id="vocab-view" class="view">
                <header class="view-header">
                    <h2>Vocabulaire</h2>
                    <div class="header-actions">
                        <div class="view-toggle btn-group">
                            <button id="btn-view-grid" class="btn btn-secondary" title="Vue Grille"><i
                                    class="fa-solid fa-th-large"></i></button>
                            <button id="btn-view-table" class="btn btn-secondary active" title="Vue Tableau"><i
                                    class="fa-solid fa-list"></i></button>
                        </div>
                        <button id="btn-extract-vocab" class="btn btn-success">
                            <i class="fa-solid fa-bolt"></i> Extraire
                        </button>
                    </div>
                </header>

                <div class="filters-bar">
                    <select id="vocab-filter-grade" class="filter-select">
                        <option value="">Tous les Niveaux</option>
                        <option value="1">Niveau 1</option>
                        <option value="2">Niveau 2</option>
                        <option value="3">Niveau 3</option>
                        <option value="4">Niveau 4</option>
                        <option value="5">Niveau 5</option>
                        <option value="6">Niveau 6</option>
                    </select>
                    <select id="vocab-filter-period" class="filter-select">
                        <option value="">Toutes les Périodes</option>
                        <option value="1">Période 1</option>
                        <option value="2">Période 2</option>
                        <option value="3">Période 3</option>
                        <option value="4">Période 4</option>
                        <option value="5">Période 5</option>
                    </select>
                    <select id="vocab-filter-week" class="filter-select">
                        <option value="">Toutes les Semaines</option>
                        <option value="1">Semaine 1</option>
                        <option value="2">Semaine 2</option>
                        <option value="3">Semaine 3</option>
                        <option value="4">Semaine 4</option>
                        <option value="5">Semaine 5</option>
                        <option value="6">Semaine 6</option>
                    </select>
                    <div class="search-box">
                        <i class="fa-solid fa-search"></i>
                        <input type="text" id="vocab-search" placeholder="Rechercher un mot...">
                    </div>
                </div>

                <div id="vocab-grid" class="vocab-grid">
                    <div class="loading"><i class="fa-solid fa-spinner fa-spin"></i> Chargement...</div>
                </div>

                <div id="vocab-table-container" class="table-container hidden">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Image</th>
                                <th>Mot</th>
                                <th>Arabe</th>
                                <th>Niveau</th>
                                <th>Période</th>
                                <th>Semaine</th>
                                <th>Leçon</th>
                            </tr>
                        </thead>
                        <tbody id="vocab-table-body">
                            <!-- Populated by JS -->
                        </tbody>
                    </table>
                </div>
            </section>

            <!-- Audios View -->
            <section id="audios-view" class="view">
                <header class="view-header">
                    <h2>Générateur Audio</h2>
                    <div class="header-actions">
                        <div id="audio-progress" class="audio-status-box hidden">
                            <i class="fa-solid fa-spinner fa-spin"></i>
                            <span id="audio-status-text">En attente...</span>
                        </div>
                        <button id="btn-toggle-audio" class="btn btn-success">
                            <i class="fa-solid fa-play"></i> Start
                        </button>
                    </div>
                </header>

                <div class="table-container">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>ID</th>

                                <th>Image</th>
                                <th>Mot</th>
                                <th>Audio Preview</th>
                                <th>Créé le</th>
                            </tr>
                        </thead>
                        <tbody id="audios-table-body">
                            <!-- JS will populate -->
                        </tbody>
                    </table>
                    <div id="audios-loading" class="loading hidden"><i class="fa-solid fa-spinner fa-spin"></i>
                        Chargement...</div>
                </div>
            </section>

            </section>

            <!-- Assets View -->
            <section id="assets-view" class="view">
                <header class="view-header">
                    <h2>Vocabulary Assets</h2>
                    <div class="header-actions">
                        <button id="btn-auto-sync" class="btn btn-secondary">
                            <i class="fa-solid fa-robot"></i> Start Auto-Sync
                        </button>
                        <span id="auto-sync-log" style="font-size: 0.85em; color: #aaa; margin-left: 8px;"></span>
                        <button id="btn-sync-assets" class="btn btn-warning">
                            <i class="fa-solid fa-database"></i> Sync Data
                        </button>
                        <button id="btn-refresh-assets" class="btn btn-secondary">
                            <i class="fa-solid fa-sync"></i> Refresh
                        </button>
                    </div>
                </header>

                <div class="table-container">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>ID</th>

                                <th>N</th>
                                <th>Image</th>
                                <th>Audio</th>
                                <th>Name</th>
                                <th>Name AR</th>
                                <th>Revizy Image ID</th>
                                <th>Revizy Audio ID</th>
                                <th>Walidoi Image ID</th>
                                <th style="width: 150px;">Actions</th>
                            </tr>
                        </thead>
                        <tbody id="assets-table-body">
                            <!-- JS will populate -->
                        </tbody>
                    </table>
                    <div id="assets-loading" class="loading hidden"><i class="fa-solid fa-spinner fa-spin"></i>
                        Chargement...</div>

                    <div id="assets-pagination" class="pagination-container">
                        <!-- JS will populate -->
                    </div>
                </div>
            </section>

            <!-- Flashcards Uploader View -->
            <section id="flashcards-uploader-view" class="view">
                <header class="view-header">
                    <h2>Flashcards Uploader</h2>
                </header>

                <div class="filters-bar" style="justify-content: flex-start; gap: 10px; margin-bottom: 10px;">
                    <div style="display: flex; gap: 10px; align-items: center; width: 100%;">
                        <label style="font-weight: 500;">Category:</label>
                        <input type="number" id="uploader-category-id" class="filter-select" placeholder="ID"
                            style="width: 80px;">
                        <input type="text" id="uploader-category-name" class="filter-select" placeholder="Category Name"
                            disabled style="width: 250px; background-color: #f5f5f5; color: #333;">
                        <button id="btn-check-category" class="btn btn-secondary" title="Check Category">
                            <i class="fa-solid fa-check"></i>
                        </button>
                    </div>
                </div>

                <div class="filters-bar" style="justify-content: flex-start; gap: 10px;">
                    <select id="uploader-grade" class="filter-select">
                        <option value="">Select N</option>
                        <option value="1">1</option>
                        <option value="2">2</option>
                        <option value="3">3</option>
                        <option value="4">4</option>
                        <option value="5">5</option>
                        <option value="6">6</option>
                    </select>
                    <select id="uploader-period" class="filter-select">
                        <option value="">Select P</option>
                        <option value="1">1</option>
                        <option value="2">2</option>
                        <option value="3">3</option>
                        <option value="4">4</option>
                        <option value="5">5</option>
                    </select>
                    <select id="uploader-week" class="filter-select">
                        <option value="">Select SEM</option>
                        <option value="1">1</option>
                        <option value="2">2</option>
                        <option value="3">3</option>
                        <option value="4">4</option>
                        <option value="5">5</option>
                        <option value="6">6</option>
                    </select>
                    <button id="btn-uploader-upload" class="btn btn-primary" disabled>
                        <i class="fa-solid fa-cloud-arrow-up"></i> Upload
                    </button>
                </div>

                <div class="table-container">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Image</th>
                                <th>Audio</th>
                                <th>Name</th>
                                <th>Name AR</th>
                                <th>N</th>
                                <th>P</th>
                                <th>SEM</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody id="uploader-table-body">
                            <!-- JS will populate -->
                        </tbody>
                    </table>
                    <div id="uploader-loading" class="loading hidden"><i class="fa-solid fa-spinner fa-spin"></i>
                        Chargement...</div>
                </div>
            </section>

            <!-- Concept Creator View -->
            <section id="concept-creator-view" class="view">
                <header class="view-header">
                    <h2>Concept creator</h2>
                </header>

                <!-- Skill Check -->
                <div class="filters-bar" style="justify-content: flex-start; gap: 10px; margin-bottom: 10px;">
                    <div style="display: flex; gap: 10px; align-items: center; width: 100%;">
                        <label style="font-weight: 500; min-width: 80px;">Skill ID:</label>
                        <input type="number" id="concept-skill-id" class="filter-select" placeholder="ID"
                            style="width: 80px;">
                        <input type="text" id="concept-skill-name" class="filter-select" placeholder="Skill Name / Info"
                            disabled style="flex: 1; background-color: #f5f5f5; color: #333;">
                        <button id="btn-check-skill" class="btn btn-secondary" title="Check Skill">
                            <i class="fa-solid fa-check"></i>
                        </button>
                    </div>
                </div>

                <!-- Unit Check -->
                <div class="filters-bar" style="justify-content: flex-start; gap: 10px; margin-bottom: 15px;">
                    <div style="display: flex; gap: 10px; align-items: center; width: 100%;">
                        <label style="font-weight: 500; min-width: 80px;">Unit ID:</label>
                        <input type="number" id="concept-unit-id" class="filter-select" placeholder="ID"
                            style="width: 80px;">
                        <input type="text" id="concept-unit-name" class="filter-select" placeholder="Unit Name / Info"
                            disabled style="flex: 1; background-color: #f5f5f5; color: #333;">
                        <button id="btn-check-unit" class="btn btn-secondary" title="Check Unit">
                            <i class="fa-solid fa-check"></i>
                        </button>
                    </div>
                </div>

                <div class="filters-bar"
                    style="justify-content: flex-start; gap: 10px; margin-bottom: 20px; border-top: 1px solid var(--border); padding-top: 15px;">
                    <select id="concept-grade" class="filter-select">
                        <option value="">Select N</option>
                        <option value="1">1</option>
                        <option value="2">2</option>
                        <option value="3">3</option>
                        <option value="4">4</option>
                        <option value="5">5</option>
                        <option value="6">6</option>
                    </select>
                    <select id="concept-period" class="filter-select">
                        <option value="">Select P</option>
                        <option value="1">1</option>
                        <option value="2">2</option>
                        <option value="3">3</option>
                        <option value="4">4</option>
                        <option value="5">5</option>
                    </select>
                    <select id="concept-week" class="filter-select">
                        <option value="">Select SEM</option>
                        <option value="1">1</option>
                        <option value="2">2</option>
                        <option value="3">3</option>
                        <option value="4">4</option>
                        <option value="5">5</option>
                        <option value="6">6</option>
                        <option value="7">7</option>
                        <option value="8">8</option>
                    </select>
                    <button id="btn-concept-create" class="btn btn-primary" disabled>
                        <i class="fa-solid fa-plus"></i> Create
                    </button>
                </div>

                <div class="table-container">
                    <div id="concept-loading" class="loading hidden">
                        <i class="fa-solid fa-spinner fa-spin fa-2x"></i>
                        <p class="mt-2 text-sm">Loading vocabulary...</p>
                    </div>
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Image</th>
                                <th>Audio</th>
                                <th>Name</th>
                                <th>Arabic</th>
                                <th>N</th>
                                <th>P</th>
                                <th>SEM</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody id="concept-table-body">
                            <tr>
                                <td colspan="8" class="text-center text-secondary">Please select filters to view items.
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </section>

            <!-- Conjugaison View -->
            <section id="conjugaison-view" class="view">
                <header class="view-header">
                    <h2>Conjugaison Studio</h2>
                </header>

                <!-- Create Conjugaison Concept Form -->
                <div
                    style="background: #f8fafc; padding: 25px; border-radius: 12px; border: 1px solid #e2e8f0; margin-bottom: 25px; max-width: 600px;">
                    <h3 style="margin-bottom: 20px; font-size: 1.2rem; color: #1e293b;"><i
                            class="fa-solid fa-plus-circle"></i> Create Conjugaison Concept</h3>

                    <div style="display: flex; flex-direction: column; gap: 20px;">
                        <!-- Row 1: Skill ID -->
                        <div style="display: flex; flex-direction: column; gap: 8px;">
                            <label style="font-weight: 600; font-size: 0.95rem; color: #475569;">Skill ID:</label>
                            <div style="display: flex; gap: 10px;">
                                <input type="number" id="create-conj-skill-id" class="filter-select" placeholder="ID"
                                    style="width: 100px; height: 42px;">
                                <input type="text" id="create-conj-skill-name" class="filter-select"
                                    placeholder="Skill Name / Info" disabled
                                    style="flex: 1; background: #fff; height: 42px;">
                                <button id="btn-check-conj-skill" class="btn btn-secondary"
                                    style="height: 42px; width: 42px; padding: 0;" title="Check Skill"><i
                                        class="fa-solid fa-check"></i></button>
                            </div>
                        </div>

                        <!-- Row 2: Unit ID -->
                        <div style="display: flex; flex-direction: column; gap: 8px;">
                            <label style="font-weight: 600; font-size: 0.95rem; color: #475569;">Unit ID:</label>
                            <div style="display: flex; gap: 10px;">
                                <input type="number" id="create-conj-unit-id" class="filter-select" placeholder="ID"
                                    style="width: 100px; height: 42px;">
                                <input type="text" id="create-conj-unit-name" class="filter-select"
                                    placeholder="Unit Name / Info" disabled
                                    style="flex: 1; background: #fff; height: 42px;">
                                <button id="btn-check-conj-unit" class="btn btn-secondary"
                                    style="height: 42px; width: 42px; padding: 0;" title="Check Unit"><i
                                        class="fa-solid fa-check"></i></button>
                            </div>
                        </div>

                        <!-- Row 3: Concept Name -->
                        <div style="display: flex; flex-direction: column; gap: 8px;">
                            <label style="font-weight: 600; font-size: 0.95rem; color: #475569;">Concept Name:</label>
                            <input type="text" id="create-conj-name" class="filter-select"
                                placeholder="e.g. Le verbe être au présent" style="width: 100%; height: 42px;">
                        </div>

                        <!-- Row 4: Week -->
                        <div style="display: flex; flex-direction: column; gap: 8px;">
                            <label style="font-weight: 600; font-size: 0.95rem; color: #475569;">Week:</label>
                            <select id="create-conj-week" class="filter-select" style="width: 100%; height: 42px;">
                                <option value="1">Week 1</option>
                                <option value="2">Week 2</option>
                                <option value="3">Week 3</option>
                                <option value="4">Week 4</option>
                            </select>
                        </div>

                        <!-- Row 5: Action Button -->
                        <div style="margin-top: 10px;">
                            <button id="btn-create-conj-concept" class="btn btn-primary"
                                style="width: 100%; height: 48px; font-weight: 600; font-size: 1rem;">
                                <i class="fa-solid fa-magic"></i> Create Conjugaison Concept
                            </button>
                        </div>
                    </div>
                </div>

                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px;">
                    <h3 style="margin: 0; font-size: 1.1rem;">Conjugaison Items</h3>
                    <div style="display: flex; gap: 10px;">
                        <input type="text" id="conj-search" class="filter-select" placeholder="Search verb..."
                            style="width: 200px;">
                        <select id="conj-grade-filter" class="filter-select">
                            <option value="">All Grades</option>
                            <option value="N1">N1</option>
                            <option value="N2">N2</option>
                            <option value="N3">N3</option>
                            <option value="N4">N4</option>
                            <option value="N5">N5</option>
                            <option value="N6">N6</option>
                        </select>
                    </div>
                </div>

                <!-- Conjugaison Items Table -->
                <div class="table-container">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Verbe</th>
                                <th>Temps</th>
                                <th>Lecon / Info</th>
                                <th>Concept ID</th>
                                <th>Questions</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody id="conjugaison-table-body">
                            <tr>
                                <td colspan="7" class="text-center text-secondary">No conjugaison items loaded yet.</td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Conjugaison Questions Modal (Hidden by default) -->
                <div id="conj-questions-modal"
                    style="display: none; background: #f9fafb; padding: 20px; border-radius: 12px; margin-top: 20px; border: 1px solid #e5e7eb;">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px;">
                        <h4 style="margin: 0;"><i class="fa-solid fa-pen-to-square"></i> Create Conjugaison Questions</h4>
                        <button id="btn-close-conj-questions" class="btn btn-secondary btn-sm">
                            <i class="fa-solid fa-times"></i> Close
                        </button>
                    </div>

                    <!-- Concept ID (auto-filled from row) -->
                    <div style="display: flex; gap: 15px; margin-bottom: 20px; align-items: flex-end;">
                        <div style="flex: 1; max-width: 300px;">
                            <label style="display: block; margin-bottom: 8px; font-weight: 500;">Concept ID:</label>
                            <div style="display: flex; gap: 10px;">
                                <input type="number" id="conj-q-concept-id" class="filter-select"
                                    placeholder="Concept ID" style="flex: 1;">
                                <button id="btn-check-conj-q-concept" class="btn btn-secondary"
                                    title="Verify Concept">
                                    <i class="fa-solid fa-check-double"></i>
                                </button>
                            </div>
                        </div>
                        <div style="flex: 2;">
                            <input type="text" id="conj-q-concept-name" class="filter-select"
                                placeholder="Concept Name (verified)" disabled
                                style="width: 100%; background: #f8fafc;">
                        </div>
                    </div>

                    <!-- JSON Input Section -->
                    <div style="margin-bottom: 20px;">
                        <label style="display: block; margin-bottom: 8px; font-weight: 500;">Paste Questions JSON:</label>
                        <textarea id="conj-questions-json" placeholder="Paste JSON array here..."
                            style="width: 100%; min-height: 200px; padding: 10px; border: 1px solid #ddd; border-radius: 6px; font-family: monospace; font-size: 12px;"></textarea>
                    </div>

                    <!-- Parse Button -->
                    <div style="margin-bottom: 20px;">
                        <button id="btn-parse-conj-questions" class="btn btn-primary">
                            <i class="fa-solid fa-code"></i> Parse JSON
                        </button>
                    </div>

                    <!-- Preview Section -->
                    <div id="conj-questions-preview-section" style="display: none; margin-bottom: 20px;">
                        <h5 style="margin-bottom: 15px; color: #333;">Preview (<span
                                id="conj-questions-count">0</span> questions)</h5>
                        <!-- Question cards will be inserted here -->
                    </div>
                </div>
            </section>

            <!-- Questions Studio View -->
            <section id="questions-studio-view" class="view">
                <header class="view-header">
                    <h2>Questions Studio</h2>
                </header>

                <!-- Filters Bar -->
                <div class="filters-bar" style="justify-content: space-between; gap: 10px; margin-bottom: 20px;">
                    <div style="display: flex; gap: 10px; align-items: center;">
                        <select id="questions-grade" class="filter-select">
                            <option value="">Select N</option>
                            <option value="1">1</option>
                            <option value="2">2</option>
                            <option value="3">3</option>
                            <option value="4">4</option>
                            <option value="5">5</option>
                            <option value="6">6</option>
                        </select>
                        <select id="questions-period" class="filter-select">
                            <option value="">Select P</option>
                            <option value="1">1</option>
                            <option value="2">2</option>
                            <option value="3">3</option>
                            <option value="4">4</option>
                            <option value="5">5</option>
                        </select>
                        <select id="questions-week" class="filter-select">
                            <option value="">Select SEM</option>
                            <option value="1">1</option>
                            <option value="2">2</option>
                            <option value="3">3</option>
                            <option value="4">4</option>
                            <option value="6">6</option>
                        </select>
                        <button id="btn-load-questions-vocab" class="btn btn-primary">
                            <i class="fa-solid fa-filter"></i> Filter
                        </button>
                    </div>
                    <button class="btn btn-outline" id="btn-export-csv" disabled>
                        <i class="fa-solid fa-file-csv"></i> Export CSV
                    </button>
                    <button class="btn btn-primary" id="btn-batch-generate" style="margin-left: 10px;">
                        <i class="fa-solid fa-wand-magic-sparkles"></i> Batch Auto-Generate
                    </button>
                </div>

                <!-- Batch Generation Modal -->
                <div id="batch-generate-modal" class="modal">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h3><i class="fa-solid fa-wand-magic-sparkles"></i> Batch Auto-Generate Questions</h3>
                            <span class="close-modal">&times;</span>
                        </div>
                        <div class="modal-body">
                            <p>This will automatically generate and <strong>PUBLISH</strong> questions for all
                                vocabulary items that:</p>
                            <ul>
                                <li>Have a <strong>Concept ID</strong></li>
                                <li>Have <strong>0 published questions</strong></li>
                                <li>Have required image/lexical data</li>
                            </ul>
                            <p>This process may take a while. Expected 3-5 seconds per item.</p>
                            <div id="batch-progress-container" class="hidden" style="margin-top: 20px;">
                                <div class="progress-bar-container"
                                    style="background: #eee; height: 10px; border-radius: 5px; overflow: hidden;">
                                    <div id="batch-progress-bar"
                                        style="width: 0%; height: 100%; background: var(--primary); transition: width 0.3s;">
                                    </div>
                                </div>
                                <p id="batch-status-text" class="text-secondary"
                                    style="font-size: 13px; margin-top: 5px;">Starting...</p>
                            </div>
                            <div id="batch-results" class="hidden"
                                style="margin-top: 20px; max-height: 200px; overflow-y: auto; font-size: 12px; background: #f8f9fa; padding: 10px; border-radius: 4px;">
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button class="btn btn-secondary close-modal-btn">Cancel</button>
                            <button class="btn btn-primary" id="btn-confirm-batch-generate">
                                <i class="fa-solid fa-play"></i> Start Batch Generation
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Vocabulary Assets Table -->
                <div class="table-container">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>ID</th>

                                <th>N</th>
                                <th>Image</th>
                                <th>Audio</th>
                                <th>Name</th>
                                <th>Arabic</th>
                                <th>P</th>
                                <th>SEM</th>
                                <th>Concept ID</th>
                                <th>Questions</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody id="questions-vocab-table-body">
                            <tr>
                                <td colspan="11" class="text-center text-secondary">
                                    <i class="fa-solid fa-arrow-up"></i> Please select N, P, and SEM to view vocabulary.
                                </td>
                            </tr>
                        </tbody>
                    </table>
                    <div id="questions-vocab-loading" class="loading hidden">
                        <i class="fa-solid fa-spinner fa-spin"></i> Chargement...
                    </div>
                </div>

                <!-- Generate Questions Modal (Hidden by default) -->
                <div id="generate-questions-modal"
                    style="display: none; background: #f9fafb; padding: 20px; border-radius: 12px; margin-top: 20px; border: 1px solid #e5e7eb;">
                    <h4 style="margin-bottom: 15px;">Generate Questions (AI)</h4>

                    <!-- JSON Input Section -->
                    <div style="margin-bottom: 20px;">
                        <label style="display: block; margin-bottom: 8px; font-weight: 500;">Paste AI-generated
                            JSON:</label>
                        <textarea id="ai-questions-json" placeholder="Paste JSON array here..."
                            style="width: 100%; min-height: 200px; padding: 10px; border: 1px solid #ddd; border-radius: 6px; font-family: monospace; font-size: 12px;"></textarea>
                    </div>

                    <!-- Parse Button -->
                    <div style="margin-bottom: 20px;">
                        <button id="btn-parse-ai-questions" class="btn btn-primary">
                            <i class="fa-solid fa-code"></i> Parse JSON
                        </button>
                    </div>

                    <!-- Preview Section -->
                    <div id="ai-questions-preview-section" style="display: none; margin-bottom: 20px;">
                        <h5 style="margin-bottom: 15px; color: #333;">Preview (<span id="ai-questions-count">0</span>
                            questions)</h5>
                        <!-- Question cards will be inserted here directly in main page flow -->
                    </div>

                    <!-- Action Buttons -->
                    <div style="display: flex; gap: 10px; justify-content: flex-end; margin-top: 20px;">
                        <button id="btn-cancel-generate" class="btn btn-secondary">
                            <i class="fa-solid fa-times"></i> Close
                        </button>
                    </div>
                </div>

                <!-- Questions Table -->
                <div
                    style="background: white; padding: 20px; border-radius: 12px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); margin-top: 30px;">
                    <h3 style="margin-bottom: 15px;">Created Questions</h3>
                    <div id="questions-empty" style="color: #666; text-align: center; padding: 40px;">
                        No questions created yet.
                    </div>
                    <div id="questions-table-container" style="display: none;">
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Question Name</th>
                                    <th>Concept ID</th>
                                    <th>Status</th>
                                    <th>Revizy ID</th>
                                    <th>Created At</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody id="questions-table-body">
                            </tbody>
                        </table>
                    </div>
                </div>
            </section>

            <!-- Roadmap View -->
            <section id="roadmap-view" class="view">
                <header class="view-header">
                    <h2>Roadmap Pédagogique</h2>
                    <div class="header-actions">
                        <button id="roadmap-refresh-btn" class="btn btn-secondary">
                            <i class="fa-solid fa-sync"></i> Refresh
                        </button>
                    </div>
                </header>

                <div class="table-container">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Semaine</th>
                                <th>Vocabulaire</th>
                                <th>Conjugaison</th>
                                <th>Grammaire</th>
                            </tr>
                        </thead>
                        <tbody id="roadmap-body">
                            <tr>
                                <td colspan="4" class="text-center text-secondary">
                                    <i class="fa-solid fa-spinner fa-spin"></i> Chargement...
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </section>

            <!-- Grammaire View -->
            <section id="grammaire-view" class="view">
                <header class="view-header">
                    <h2>Leçons de Grammaire</h2>
                    <div class="header-actions">
                        <button id="grammaire-refresh-btn" class="btn btn-secondary">
                            <i class="fa-solid fa-sync"></i> Refresh
                        </button>
                    </div>
                </header>

                <div class="filters-bar">
                    <select id="filter-n" class="filter-select">
                        <option value="">Tous les Niveaux</option>
                        <option value="N1">N1</option>
                        <option value="N2">N2</option>
                        <option value="N3">N3</option>
                        <option value="N4">N4</option>
                        <option value="N5">N5</option>
                        <option value="N6">N6</option>
                    </select>
                    <div class="search-box">
                        <i class="fa-solid fa-search"></i>
                        <input type="text" id="search-grammar" placeholder="Rechercher un objectif...">
                    </div>
                </div>

                <div class="table-container">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Unité</th>
                                <th>Objectif / Point de Langue</th>
                                <th>Titre de la leçon</th>
                                <th>Détails</th>
                            </tr>
                        </thead>
                        <tbody id="grammaire-body">
                            <tr>
                                <td colspan="4" class="text-center text-secondary">
                                    <i class="fa-solid fa-spinner fa-spin"></i> Chargement...
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </section>

        </main>
