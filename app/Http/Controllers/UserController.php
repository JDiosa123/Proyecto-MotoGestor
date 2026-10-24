<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Models\User;
use App\Models\Cliente;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class UserController extends Controller
{
    public function index()
    {
        $users = User::all();
        return view('admin.users.index', compact('users'));
    }

    public function create()
    {
        $clientes = Cliente::whereNull('user_id')->orderBy('nombre')->get();

        return view('admin.users.create', compact('clientes'));
    }

    public function store(StoreUserRequest $request)
    {
        $data = $request->validated();
        $clienteId = $data['cliente_id'] ?? null;
        unset($data['cliente_id']);

        DB::transaction(function () use ($data, $clienteId): void {
            $user = User::create($data);

            if ($user->role === User::ROLE_CLIENT) {
                Cliente::whereKey($clienteId)->update(['user_id' => $user->id]);
            }
        });

        return redirect()->route('admin.users.index')->with('success', 'Usuario creado correctamente.');
    }

    public function edit(User $user)
    {
        $clientes = Cliente::whereNull('user_id')
            ->orWhere('id', $user->cliente?->id)
            ->orderBy('nombre')
            ->get();

        return view('admin.users.edit', compact('user', 'clientes'));
    }

    public function update(UpdateUserRequest $request, User $user)
    {
        $data = $request->validated();
        $clienteId = $data['cliente_id'] ?? null;
        unset($data['cliente_id']);

        DB::transaction(function () use ($data, $clienteId, $user): void {
            $user->update($data);

            if ($user->role === User::ROLE_CLIENT) {
                Cliente::where('user_id', $user->id)->update(['user_id' => null]);
                Cliente::whereKey($clienteId)->update(['user_id' => $user->id]);
            } else {
                Cliente::where('user_id', $user->id)->update(['user_id' => null]);
            }
        });

        return redirect()->route('admin.users.index')->with('success', 'Usuario actualizado correctamente.');
    }

    public function destroy(User $user)
    {
        $user->delete();
        return redirect()->route('admin.users.index')->with('success', 'Usuario eliminado correctamente.');
    }

    public function toggleStatus(User $user)
    {
        $user->update([
            'status' => $user->status === 'activo' ? 'inactivo' : 'activo',
        ]);

        return back()->with('success', 'Estado del usuario actualizado correctamente.');
    }
}
