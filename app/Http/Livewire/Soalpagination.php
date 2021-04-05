<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Soal;
use Livewire\WithPagination;
use Illuminate\Http\Request;

class Soalpagination extends Component
{
    use WithPagination;

    public $soal;

    public function render(Request $request)
    {
        $tema = $request->q;
        $this->soal = Soal::where('tema_id', $tema)->inRandomOrder()->paginate(5);

        return view('livewire.soalpagination', compact('soal'));
    }
}
