@extends('layouts.app')

@section('title', 'Editar Cliente - Sistema de Turnos')
@section('page-title', 'Editar Cliente')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <i class="fas fa-user-edit mr-2"></i>Editar: {{ $client->full_name }}
            </div>
            <div class="card-body">
                <form action="{{ route('admin.clients.update', $client) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="first_name">Nombres <span class="text-danger">*</span></label>
                                <input type="text" name="first_name" id="first_name"
                                       class="form-control @error('first_name') is-invalid @enderror"
                                       value="{{ old('first_name', $client->first_name) }}" required>
                                @error('first_name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="last_name">Apellidos <span class="text-danger">*</span></label>
                                <input type="text" name="last_name" id="last_name"
                                       class="form-control @error('last_name') is-invalid @enderror"
                                       value="{{ old('last_name', $client->last_name) }}" required>
                                @error('last_name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="document_type">Tipo Documento <span class="text-danger">*</span></label>
                                <select name="document_type" id="document_type" class="form-control @error('document_type') is-invalid @enderror" required>
                                    <option value="dni" {{ old('document_type', $client->document_type) === 'dni' ? 'selected' : '' }}>DNI</option>
                                    <option value="passport" {{ old('document_type', $client->document_type) === 'passport' ? 'selected' : '' }}>Pasaporte</option>
                                    <option value="ce" {{ old('document_type', $client->document_type) === 'ce' ? 'selected' : '' }}>Carné Extranjería</option>
                                    <option value="ruc" {{ old('document_type', $client->document_type) === 'ruc' ? 'selected' : '' }}>RUC</option>
                                    <option value="other" {{ old('document_type', $client->document_type) === 'other' ? 'selected' : '' }}>Otro</option>
                                </select>
                                @error('document_type')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-8">
                            <div class="form-group">
                                <label for="document_number">Número de Documento <span class="text-danger">*</span></label>
                                <input type="text" name="document_number" id="document_number"
                                       class="form-control @error('document_number') is-invalid @enderror"
                                       value="{{ old('document_number', $client->document_number) }}" required>
                                @error('document_number')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="email">Email</label>
                                <input type="email" name="email" id="email"
                                       class="form-control @error('email') is-invalid @enderror"
                                       value="{{ old('email', $client->email) }}">
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="phone">Teléfono</label>
                                <input type="text" name="phone" id="phone"
                                       class="form-control @error('phone') is-invalid @enderror"
                                       value="{{ old('phone', $client->phone) }}">
                                @error('phone')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="birth_date">Fecha de Nacimiento</label>
                                <input type="date" name="birth_date" id="birth_date"
                                       class="form-control @error('birth_date') is-invalid @enderror"
                                       value="{{ old('birth_date', $client->birth_date?->format('Y-m-d')) }}">
                                @error('birth_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="gender">Género</label>
                                <select name="gender" id="gender" class="form-control @error('gender') is-invalid @enderror">
                                    <option value="">No especificado</option>
                                    <option value="male" {{ old('gender', $client->gender) === 'male' ? 'selected' : '' }}>Masculino</option>
                                    <option value="female" {{ old('gender', $client->gender) === 'female' ? 'selected' : '' }}>Femenino</option>
                                    <option value="other" {{ old('gender', $client->gender) === 'other' ? 'selected' : '' }}>Otro</option>
                                </select>
                                @error('gender')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Condiciones Especiales (Atención Prioritaria)</label>
                        <div class="row mt-2">
                            <div class="col-md-4">
                                <div class="custom-control custom-checkbox">
                                    <input type="checkbox" class="custom-control-input" id="is_elderly" name="is_elderly"
                                           {{ old('is_elderly', $client->is_elderly) ? 'checked' : '' }}>
                                    <label class="custom-control-label" for="is_elderly">
                                        <i class="fas fa-user-clock text-info"></i> Adulto Mayor
                                    </label>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="custom-control custom-checkbox">
                                    <input type="checkbox" class="custom-control-input" id="is_pregnant" name="is_pregnant"
                                           {{ old('is_pregnant', $client->is_pregnant) ? 'checked' : '' }}>
                                    <label class="custom-control-label" for="is_pregnant">
                                        <i class="fas fa-baby" style="color: #e83e8c;"></i> Embarazada
                                    </label>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="custom-control custom-checkbox">
                                    <input type="checkbox" class="custom-control-input" id="has_disability" name="has_disability"
                                           {{ old('has_disability', $client->has_disability) ? 'checked' : '' }}>
                                    <label class="custom-control-label" for="has_disability">
                                        <i class="fas fa-wheelchair text-warning"></i> Discapacidad
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="notes">Notas</label>
                        <textarea name="notes" id="notes" class="form-control @error('notes') is-invalid @enderror"
                                  rows="2">{{ old('notes', $client->notes) }}</textarea>
                        @error('notes')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <hr>

                    <div class="d-flex justify-content-between">
                        <a href="{{ route('admin.clients.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left mr-1"></i>Cancelar
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save mr-1"></i>Actualizar Cliente
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
