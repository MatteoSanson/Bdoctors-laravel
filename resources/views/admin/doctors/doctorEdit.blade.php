@extends('layouts.admin')

@section('content')
    <div id="doctor-edit" class="container pt-5">
        <div class="d-flex align-items-center justify-content-between mb-5">
            <h1>Modifica Profilo</h1>
        </div>

        <form action="{{ route('admin.doctors.update', $doctor) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="row">

                {{-- Campi testuali --}}
                <div class="col-md-8">

                    {{-- nome  --}}
                    <div class="form-group mb-3">
                        <label for="name" class="mb-2">Nome *</label>
                        <input type="text" name="name" id="name" class="val-name form-control"
                            value="{{ old('name', $user->name) }}">
                        @error('name')
                            <div class="alert alert-danger">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- cognome  --}}
                    <div class="form-group mb-3">
                        <label for="surname" class="mb-2">Cognome *</label>
                        <input type="text" name="surname" id="surname" class="val-surname form-control"
                            value="{{ old('surname', $user->surname) }}">
                        @error('surname')
                            <div class="alert alert-danger">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- indirizzo  --}}
                    <div class="form-group mb-3">
                        <label for="address" class="mb-2">Indirizzo *</label>
                        <input type="text" name="address" id="address" class="val-address form-control"
                            value="{{ old('address', $user->doctor->address) }}">
                        @error('address')
                            <div class="alert alert-danger">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- numero di telefono  --}}
                    <div class="form-group mb-3">
                        <label for="phone_number" class="mb-2">Numero di telefono</label>
                        <input type="text" name="phone_number" id="phone_number" class="val-phone-number form-control"
                            value="{{ old('phone_number', $user->doctor->phone_number) }}">
                        @error('phone_number')
                            <div class="alert alert-danger">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- disponibilità --}}
                    <div class="form-group mb-2">
                        <label class="mb-2">Disponibilità</label>
                        <div class="form-check">
                            <input type="radio" id="available" name="is_available" value="1"
                                class="val-avaiable form-check-input"
                                {{ $user->doctor->is_available == 1 ? 'checked' : '' }}>
                            <label for="available" class="form-check-label">Disponibile</label>
                        </div>
                        <div class="form-check">
                            <input type="radio" id="not_available" name="is_available" value="0"
                                class="val-not-avaiable form-check-input"
                                {{ $user->doctor->is_available == 0 ? 'checked' : '' }}>
                            <label for="not_available" class="form-check-label">Non Disponibile</label>
                        </div>
                    </div>

                    {{-- disponibilità 
                    <div class="form-group pb-2">
                        <label for="is_available">Disponibilità</label>
                        <select name="is_available" id="is_available" class="form-control">
                            <option value="1" {{ $user->doctor->is_available == 1 ? 'selected' : '' }}>Available
                            </option>
                            <option value="0" {{ $user->doctor->is_available == 0 ? 'selected' : '' }}>Not Available
                            </option>
                        </select>
                    </div> --}}

                    {{-- specializzazione  --}}
                    <div class="form-group mb-3">
                        <label for="specializations"
                            class="col-md-4 col-form-label text-md-right mb-2">{{ __('Specializzazioni') }}</label>
                        <select id="specializations" class="form-select  @error('specializations') is-invalid @enderror"
                            aria-label="Default select example" name="specializations" required
                            autocomplete="specializations" autofocus>
                            <option selected value="">Nessuna Specializzazione</option>
                            @foreach ($specializations as $specialization)
                                {
                                <option value="{{ $specialization->id }}"
                                    {{ old('specialization->id', $user->doctor->specializations[0]['id']) == $specialization->id ? 'selected' : '' }}>
                                    {{ $specialization->title }}</option>
                                }
                            @endforeach
                        </select>

                        @error('specializations')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>

                    {{-- Prestazioni  --}}
                    <div class="form-group pb-2">
                        <label for="services" class="mb-2">Prestazioni</label>
                        <textarea type="text" name="services" id="services" class="form-control" cols="30" rows="10">{{ old('services', $user->doctor->services) }}</textarea>
                    </div>

                </div>

                {{-- Foto e CV --}}
                <div class="col-md-4">

                    {{-- Foto Profilo  --}}
                    <div class="card mb-4">

                        <div class="card-header">Foto</div>

                        <div class="card-body">
                            {{-- Immagine --}}
                            <div class="mb-3">
                                @if ($user->doctor->doctor_img)
                                    <img src="{{ asset('storage/' . $user->doctor->doctor_img) }}" class="img-fluid"
                                        alt="Doctor Image">
                                @endif
                            </div>

                            {{-- Testo "Change/Add image" --}}
                            <div class="mb-3">
                                <label for="doctor-img-edit" class="form-label d-flex justify-content-between">
                                    @if ($user->doctor->doctor_img)
                                        Cambia Immagine
                                    @else
                                        Aggiungi Immagine
                                    @endif
                                    {{-- errore url immagine --}}
                                    @error('doctor_img')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </label>
                            </div>

                            {{-- Input "Scegli file" --}}
                            <div class="input-group">
                                <input class="upload-image my-input form-control @error('doctor_img') is-invalid @enderror"
                                    type="file" id="doctor-img-edit" name="doctor_img"
                                    value="{{ old('doctor_img', $user->doctor->doctor_img) }}">
                            </div>
                        </div>
                    </div>


                    {{-- CV --}}
                    <div class="card mb-4 cv">

                        <div class="card-header">CV</div>

                        <div class="card-body">
                            <div class="mb-3">
                                @if ($user->doctor->doctor_cv)
                                    <iframe src="{{ asset('storage/' . $user->doctor->doctor_cv) }}" alt="Doctor CV"
                                        title="Doctor CV"></iframe>
                                @endif
                            </div>

                            {{-- Testo "Change/Add image" --}}
                            <div class="mb-3">
                                <label for="doctor-cv-edit" class="form-label d-flex justify-content-between">
                                    @if ($user->doctor->doctor_cv)
                                        Cambia CV
                                    @else
                                        Aggiungi un CV
                                    @endif
                                    {{-- errore url immagine --}}
                                    @error('doctor_cv')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </label>
                            </div>

                            {{-- Input "Scegli file" --}}
                            <div class="input-group">
                                <input class="upload-image my-input form-control @error('doctor_cv') is-invalid @enderror"
                                    type="file" id="doctor-cv-edit" name="doctor_cv"
                                    value="{{ old('doctor_cv', $user->doctor->doctor_cv) }}">
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Bottone di invio --}}
                <div class="py-5">
                    <button type="submit" class="send btn btn-link">Modifica</button>
                </div>
        </form>
    </div>
@endsection
