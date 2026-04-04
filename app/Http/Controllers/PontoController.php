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
        $ponto = Ponto::where('id', $id)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        $ponto->update([
            'saida' => now()
        ]);

        return back();
    }

    public function destroy($id)
    {
        if (Auth::user()->nivel_acesso != 2) {
            abort(403);
        }

        $ponto = Ponto::findOrFail($id);
        $ponto->delete();

        return redirect()->back()->with('success', 'Registro deletado!');
    }
}
