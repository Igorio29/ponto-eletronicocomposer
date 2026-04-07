<?php

namespace App\Http\Controllers;

use App\Models\Ponto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PontoController extends Controller
{

    public function index()
    {
        if (Auth::user()->nivel_acesso == 2) {
            $pontos = Ponto::latest()->get();
        } else {
            $pontos = Ponto::where('user_id', Auth::id())
                ->latest()
                ->get();
        }
        return view('ponto', compact('pontos'));
    }

    public function entrada()
    {
        Ponto::create([
            'user_id' => Auth::id(),
            'entrada' => now()
        ]);

        return back();
    }

    public function saida($id)
    {
        // Busca o registro de ponto pelo ID
        $ponto = Ponto::findOrFail($id);

        if ($ponto->user_id !== Auth::id() && Auth::user()->nivel_acesso != 2) {
            abort(403, 'Você não tem permissão para finalizar este ponto.');
        }

        $ponto->update([
            'saida' => now()
        ]);

        return back()->with('success', 'Ponto finalizado com sucesso!');
    }

    public function destroy($id)
    {
        if (Auth::user()->nivel_acesso != 2) {
            abort(403);
        }

        $ponto = Ponto::findOrFail($id);
        $ponto->delete();

        return redirect()->back()->with('error', 'Registro deletado!');
    }
}
