<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreDoctorsRequest;
use App\Http\Requests\Admin\UpdateDoctorsRequest;
use App\Models\Admin\Doctor;
use App\Models\User;
use App\Models\Admin\Specialization;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class DoctorsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $doctors = Doctor::all();
        return view('admin.doctors.doctorIndex', compact('doctor'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.doctors.doctorCreate');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreDoctorsRequest $request)
    {
        $doctor = new Doctor();
        $user = Auth::user();
        $data = $request->validated();

        do {
            $uniqueSlugId = microtime(true) * 10000;
            $doctorSlug = Str::of("{$user->name}-{$user->surname}-{$uniqueSlugId}")->slug('-');
        } while (Doctor::where('slug', $doctorSlug)->exists());
        $doctor->slug = $doctorSlug;

        $doctor->fill($data);

        // gestione immagini 
        if (isset($data['doctor_doctor_img'])) {
            $doctor->doctor_doctor_img = Storage::put('uploads', $data['doctor_doctor_img']);
        }

        $doctor->save();

        // prendo specializations se settato
        if (isset($data['specializations'])) {
            $doctor->specializations()->sync($data['specializations']);
        }
        return redirect()->route('admin.doctors.show', $doctor->slug);
    }

    /**
     * Display the specified resource.
     */
    public function show(Doctor $doctor)
    {
        $user = Auth::user();
        return view('admin.doctors.doctorShow', compact('doctor', 'user'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Doctor $doctor)
    {
        $user = Auth::user();
        $specializations = Specialization::all();
        return view('admin.doctors.doctorEdit', compact('doctor','user','specializations'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateDoctorsRequest $request, Doctor $doctor)
    {
        $user = Auth::user();
        $data = $request->validated();



        // Aggiornamento dei dati dell'utente associato al dottore
        $user->update([
            'name' => $data['name'],
            'surname' => $data['surname'],
        ]);

        
        do {
            $uniqueSlugId = microtime(true) * 10000;
            $doctorSlug = Str::of("{$user->name}-{$user->surname}-{$uniqueSlugId}")->slug('-');
        } while (Doctor::where('slug', $doctorSlug)->exists());
        $doctor->slug = $doctorSlug;

        // gestione immagini 
        // if (isset($data['doctor_img'])) {
        //     $doctor->doctor_img = Storage::put('uploads', $data['doctor_img']);
        // } else {
        //     $doctor->doctor_img = 'doctor_img';
        // }
        // $doctor->update($data);

        // aggiorno elemento specializations
        // if (isset($data['specializations'])) {
        //     $doctor->specializations()->sync($data['specializations']);
        // } else {
        //     $doctor->specializations()->sync([]);
        // }

    
        // Aggiornamento delle specializzazioni solo se sono state selezionate
        if (!empty($data['specializations'])) {
            // Rimuoviamo eventuali valori vuoti e duplicati dall'array di specializzazioni
            $specializations = array_filter(array_unique($data['specializations']));
            // Verifica che l'array di specializzazioni non sia vuoto prima di sincronizzarlo
            if (!empty($specializations)) {
                $doctor->specializations()->sync($specializations);
            } else {
                // Se l'array di specializzazioni è vuoto, rimuovi tutte le specializzazioni associate al dottore
                $doctor->specializations()->detach();
            }
        } else {
            // Se non sono state selezionate specializzazioni, rimuovi tutte le specializzazioni associate al dottore
            $doctor->specializations()->detach();
        }

        // Aggiornamento dei restanti dati del dottore
        $doctor->update($data);
       
        return redirect()->route('admin.doctors.show', $doctor);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Doctor $doctor)
    {
        // elimino elemento technologies
        $doctor->specializations()->sync([]);

        // elimino progetto
        $doctor->delete();
        return redirect()->route('admin');
    }
}
