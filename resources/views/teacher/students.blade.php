@extends('teacher.layout')
@section('title', 'Mes élèves')

@section('content')
<div class="main">
    <!-- Stats -->
    <div class="stats-row">
        <div class="stat-card">
            <div class="stat-value">{{ $students->count() }}</div>
            <div class="stat-label">Élèves inscrits</div>
        </div>
        <div class="stat-card">
            <div class="stat-value" style="font-size: 1rem; -webkit-text-fill-color: var(--text-muted); margin-top: 8px;">
                {{ $teacher->name }}
            </div>
            <div class="stat-label">Enseignant connecté</div>
        </div>
    </div>

    <!-- Add student form -->
    <div class="card">
        <div class="card-title">
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="var(--accent)" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><line x1="19" y1="8" x2="19" y2="14"/><line x1="22" y1="11" x2="16" y2="11"/></svg>
            Ajouter un élève
        </div>

        @if($errors->any())
            <div class="alert alert-error">
                @foreach($errors->all() as $error)
                    <div>{{ $error }}</div>
                @endforeach
            </div>
        @endif

        <form method="POST" action="{{ route('teacher.students.store') }}" id="addStudentForm">
            @csrf
            <div class="form-row">
                <div class="form-group">
                    <label for="student_code">Code de l'élève <span style="color: var(--red);">*</span></label>
                    <input type="text"
                           id="student_code"
                           name="student_code"
                           class="form-input {{ $errors->has('student_code') ? 'is-invalid' : '' }}"
                           value="{{ old('student_code') }}"
                           placeholder="Ex: M123456789"
                           required>
                    @error('student_code')
                        <div class="validation-error">{{ $message }}</div>
                    @enderror
                </div>
                <div class="form-group">
                    <label for="student_name">Nom de l'élève <span class="text-muted" style="font-weight: 400; text-transform: none;">(optionnel)</span></label>
                    <input type="text"
                           id="student_name"
                           name="student_name"
                           class="form-input"
                           value="{{ old('student_name') }}"
                           placeholder="Prénom Nom">
                </div>
            </div>
            <button type="submit" class="btn btn-accent btn-sm">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                Ajouter l'élève
            </button>
        </form>
    </div>

    <!-- Students table -->
    <div class="card">
        <div class="card-title">
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="var(--primary)" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
            Liste des élèves
            <span class="badge badge-count" style="margin-left: auto;">{{ $students->count() }}</span>
        </div>

        @if($students->isEmpty())
            <div class="table-empty">
                <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><line x1="19" y1="8" x2="19" y2="14"/><line x1="22" y1="11" x2="16" y2="11"/></svg>
                <p style="font-weight: 500;">Aucun élève pour le moment</p>
                <p class="text-sm mt-2">Utilisez le formulaire ci-dessus pour ajouter votre premier élève.</p>
            </div>
        @else
            <div class="table-wrapper">
                <table class="table">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Code</th>
                            <th>Nom</th>
                            <th>Ajouté le</th>
                            <th style="text-align: right;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($students as $index => $student)
                        <tr>
                            <td style="color: var(--text-muted);">{{ $index + 1 }}</td>
                            <td>
                                <strong style="font-family: 'SF Mono', 'Fira Code', monospace; font-size: 0.875rem;">
                                    {{ $student->student_code }}
                                </strong>
                            </td>
                            <td>{{ $student->student_name ?: '—' }}</td>
                            <td style="color: var(--text-muted); font-size: 0.875rem;">
                                {{ $student->created_at->format('d/m/Y') }}
                            </td>
                            <td style="text-align: right;">
                                <form method="POST"
                                      action="{{ route('teacher.students.destroy', $student->id) }}"
                                      onsubmit="return confirm('Êtes-vous sûr de vouloir retirer cet élève ?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm">Retirer</button>
                                </form>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</div>
@endsection
